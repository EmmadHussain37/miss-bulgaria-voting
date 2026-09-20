<?php

if (!defined('ABSPATH')) {
    exit;
}


class MBV_Stripe
{


    private $secret_key;



    public function __construct()
    {


        $this->secret_key = get_option(
            'mbv_stripe_secret_key'
        );


        add_action(
            'rest_api_init',
            array(
                $this,
                'register_routes'
            )
        );


    }







    public function register_routes()
    {


        register_rest_route(

            'mbv/v1',

            '/create-checkout',

            array(

                'methods'=>'POST',

                'callback'=>array(
                    $this,
                    'create_checkout'
                ),

                'permission_callback'=>'__return_true'

            )

        );


    }









    public function create_checkout(
        WP_REST_Request $request
    )
    {


        /*
        Verify nonce
        */


        if(
            !wp_verify_nonce(
                $request->get_header('X-WP-Nonce'),
                'wp_rest'
            )
        ){

            return new WP_Error(

                'invalid_nonce',

                'Security check failed',

                array(
                    'status'=>403
                )

            );

        }







        $candidate_id = intval(

            $request->get_param(
                'candidate_id'
            )

        );



        $package_id = intval(

            $request->get_param(
                'package_id'
            )

        );






        if(
            !$candidate_id ||
            !$package_id
        ){

            return new WP_Error(

                'missing_data',

                'Candidate or package missing',

                array(
                    'status'=>400
                )

            );

        }







        /*
        Validate Candidate
        */


        $candidate = get_post(
            $candidate_id
        );



        if(
            !$candidate ||
            $candidate->post_type !== 'mbv_candidate'
        ){

            return new WP_Error(

                'invalid_candidate',

                'Candidate not found',

                array(
                    'status'=>404
                )

            );

        }








        /*
        Validate Package
        */


        $package = get_post(
            $package_id
        );



        if(
            !$package ||
            $package->post_type !== 'mbv_vote_package'
        ){

            return new WP_Error(

                'invalid_package',

                'Package not found',

                array(
                    'status'=>404
                )

            );

        }








        /*
        Get package data
        */


        $votes = intval(

            get_post_meta(

                $package_id,

                '_mbv_package_votes',

                true

            )

        );



        $price = floatval(

            get_post_meta(

                $package_id,

                '_mbv_package_price',

                true

            )

        );








        if(
            $votes <= 0 ||
            $price <= 0
        ){

            return new WP_Error(

                'invalid_package_data',

                'Invalid package values',

                array(
                    'status'=>400
                )

            );

        }








        if(empty($this->secret_key)){


            return new WP_Error(

                'stripe_missing',

                'Stripe key missing',

                array(
                    'status'=>500
                )

            );


        }








        require_once MBV_PATH .
        'vendor/autoload.php';






        \Stripe\Stripe::setApiKey(

            $this->secret_key

        );









        try {



            $session = \Stripe\Checkout\Session::create(array(



                'payment_method_types'=>array(

                    'card'

                ),



                'line_items'=>array(

                    array(

                        'price_data'=>array(

                            'currency'=>'usd',


                            'product_data'=>array(

                                'name'=>

                                get_the_title($package_id)
                                .
                                ' - '
                                .
                                $votes .
                                ' Votes for '
                                .
                                get_the_title($candidate_id)

                            ),



                            'unit_amount'=>

                            intval(
                                round($price * 100)
                            )


                        ),



                        'quantity'=>1

                    )

                ),




                'mode'=>'payment',




                'metadata'=>array(

    'candidate_id'=>$candidate_id,

    'package_id'=>$package_id,

    'votes'=>$votes,

    'personal_message'=>
        sanitize_text_field(
            $request->get_param('personal_message')
        )

),




                'success_url'=>

                add_query_arg(

                    array(

                        'payment'=>'success',

                        'candidate_id'=>$candidate_id

                    ),

                    home_url('/leaderboard/')

                ),




                'cancel_url'=>

                home_url()



            ));







            return array(

                'success'=>true,

                'url'=>$session->url

            );





        }


        catch(Exception $e){



            return new WP_Error(

                'stripe_error',

                $e->getMessage(),

                array(
                    'status'=>500
                )

            );


        }



    }



}