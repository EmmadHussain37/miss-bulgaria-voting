<?php

if (!defined('ABSPATH')) {
    exit;
}


class MBV_Migration
{


    public function __construct()
    {

        add_action(
            'admin_init',
            array(
                $this,
                'maybe_migrate'
            )
        );

    }





    public function maybe_migrate()
    {
        if (
            !isset($_GET['mbv_migrate'])
            ||
            sanitize_text_field(wp_unslash($_GET['mbv_migrate'])) !== '1'
        ) {
            return;
        }

        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'miss-bulgaria-voting'));
        }

        $this->run_migration();

        wp_die(
            esc_html__('Miss Bulgaria migration completed successfully.', 'miss-bulgaria-voting')
        );
    }







    private function run_migration()
    {


        global $wpdb;



        $table =
        $wpdb->prefix . 'mb_finalists';




        $exists = $wpdb->get_var(

            $wpdb->prepare(

                "SHOW TABLES LIKE %s",

                $table

            )

        );



        if(!$exists){

            return;

        }







        $candidates = $wpdb->get_results(

            "SELECT * FROM $table"

        );






        foreach($candidates as $candidate){



            $existing = get_page_by_title(

                $candidate->name,

                OBJECT,

                'mbv_candidate'

            );



            if($existing){

                continue;

            }







            $post_id = wp_insert_post(

                array(

                    'post_title'=>
                    sanitize_text_field(
                        $candidate->name
                    ),


                    'post_type'=>
                    'mbv_candidate',


                    'post_status'=>
                    'publish'


                )

            );






            if($post_id){



                update_post_meta(

                    $post_id,

                    '_mbv_city',

                    sanitize_text_field(
                        $candidate->city
                    )

                );





                update_post_meta(

                    $post_id,

                    '_mbv_votes',

                    intval(

                        $candidate->free_votes

                        +

                        $candidate->paid_votes

                    )

                );





                update_post_meta(

                    $post_id,

                    '_mbv_old_id',

                    intval(
                        $candidate->id
                    )

                );



            }



        }



    }



}