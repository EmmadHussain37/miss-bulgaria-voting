<?php

if (!defined('ABSPATH')) {
    exit;
}


class MBV_Stripe_Webhook
{


    private $webhook_secret;



    public function __construct()
    {


        $this->webhook_secret = get_option(
            'mbv_stripe_webhook_secret'
        );


        add_action(

            'rest_api_init',

            array(
                $this,
                'register_route'
            )

        );


    }







    public function register_route()
    {


        register_rest_route(

            'mbv/v1',

            '/stripe-webhook',

            array(

                'methods' => 'POST',

                'callback' => array(
                    $this,
                    'handle'
                ),

                'permission_callback' => '__return_true'

            )

        );


    }







    public function handle(
        WP_REST_Request $request
    )
    {


        $payload = $request->get_body();


        $signature =
        wp_unslash($_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '');



        require_once MBV_PATH .
        'vendor/autoload.php';





        \Stripe\Stripe::setApiKey(

            get_option(
                'mbv_stripe_secret_key'
            )

        );





        try {


            $event =
            \Stripe\Webhook::constructEvent(

                $payload,

                $signature,

                $this->webhook_secret

            );


        }

        catch(Exception $e){


            return new WP_Error(

                'invalid_signature',

                $e->getMessage(),

                array(
                    'status'=>400
                )

            );


        }







        if(
            $event->type === 'checkout.session.completed'
        ){


            $session =
            $event->data->object;


            $this->process_payment(
                $session
            );


        }






        return array(

            'success'=>true

        );


    }









    private function process_payment(
        $session
    )
    {


        global $wpdb;









        if(
            $session->payment_status !== 'paid'
        ){
            return;
        }







        $candidate_id =
        intval(
            $session->metadata->candidate_id ?? 0
        );



        $package_id =
        intval(
            $session->metadata->package_id ?? 0
        );



        $votes =
        intval(
            $session->metadata->votes ?? 0
        );



        $personal_message =
        sanitize_text_field(

            $session->metadata->personal_message ?? ''

        );





     






        if(
            !$candidate_id ||
            !$votes
        ){
            return;
        }







        $candidate =
        get_post($candidate_id);



        if(
            !$candidate ||
            $candidate->post_type !== 'mbv_candidate'
        ){

            return;

        }







        $transaction_table =
        $wpdb->prefix .
        'mbv_transactions';







        $exists =
        $wpdb->get_var(

            $wpdb->prepare(

                "SELECT id 
                 FROM $transaction_table
                 WHERE stripe_payment_id=%s",

                $session->id

            )

        );



        if($exists){

            return;

        }








        /*
        Calculate allowed votes
        */


        $current_votes =
        intval(

            get_post_meta(

                $candidate_id,

                '_mbv_votes',

                true

            )

        );



        $allowed_votes =
        $votes;



        $max_votes_setting = get_option('mbv_max_votes_per_candidate', '');

        if ($max_votes_setting !== '' && $max_votes_setting !== false && is_numeric($max_votes_setting)) {

            $limit = intval($max_votes_setting);

            if ($limit > 0) {

                if (($current_votes + $votes) > $limit) {

                    $allowed_votes = $limit - $current_votes;

                }

                if ($allowed_votes <= 0) {

                    return;

                }

            }

        }









        /*
        Save transaction
        */


        $inserted =
        $wpdb->insert(

            $transaction_table,

            array(

                'candidate_id' =>
                $candidate_id,


                'package_id' =>
                $package_id,


                'stripe_payment_id' =>
                $session->id,


                'customer_name' =>
                $session->customer_details->name ?? '',


                'customer_email' =>
                $session->customer_details->email ?? '',


                'personal_message' =>
                $personal_message,


                'votes' =>
                $allowed_votes,


                'amount' =>
                ($session->amount_total / 100),


                'status' =>
                'completed'

            ),


            array(

                '%d',

                '%d',

                '%s',

                '%s',

                '%s',

                '%s',

                '%d',

                '%f',

                '%s'

            )

        );







        if(!$inserted){


            error_log(

                'MBV DB ERROR: ' .
                $wpdb->last_error

            );


            return;


        }







        /*
        Update candidate votes atomically
        */

        if(!metadata_exists('post', $candidate_id, '_mbv_votes')){
            add_post_meta($candidate_id, '_mbv_votes', 0, true);
        }

        $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$wpdb->postmeta}
                SET meta_value = CAST(meta_value AS UNSIGNED) + %d
                WHERE post_id = %d AND meta_key = '_mbv_votes'",
                $allowed_votes,
                $candidate_id
            )
        );

        clean_post_cache($candidate_id);
        wp_cache_delete($candidate_id, 'post_meta');

        if (class_exists('MBV_Ranking')) {
            MBV_Ranking::update_rankings();
        }

    }



}