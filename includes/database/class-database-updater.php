<?php

if (!defined('ABSPATH')) {
    exit;
}


class MBV_Database_Updater
{


    public function __construct()
    {


        add_action(
            'plugins_loaded',
            array(
                $this,
                'update_database'
            )
        );


    }







    public function update_database()
    {


        $current_version = get_option(
            'mbv_db_version'
        );



        if(
            $current_version !== MBV_VERSION
        ){

            $this->run_updates();


            update_option(

                'mbv_db_version',

                MBV_VERSION

            );

        }


    }







    private function run_updates()
    {


        global $wpdb;



        $transactions_table =
        $wpdb->prefix .
        'mbv_transactions';



        /*
        Check table exists
        */

        if(
            $wpdb->get_var(
                "SHOW TABLES LIKE '$transactions_table'"
            ) !== $transactions_table
        ){

            return;

        }







        /*
        Add package_id column
        */

        $column = $wpdb->get_results(

            "SHOW COLUMNS FROM 
            $transactions_table 
            LIKE 'package_id'"

        );


        if(empty($column)){


            $wpdb->query(

                "ALTER TABLE $transactions_table

                ADD package_id BIGINT UNSIGNED NOT NULL

                AFTER candidate_id"

            );


        }








        /*
        Add customer_name column
        */

        $column = $wpdb->get_results(

            "SHOW COLUMNS FROM 
            $transactions_table 
            LIKE 'customer_name'"

        );


        if(empty($column)){


            $wpdb->query(

                "ALTER TABLE $transactions_table

                ADD customer_name VARCHAR(255)

                AFTER stripe_payment_id"

            );


        }








        /*
        Add customer_email column
        */

        $column = $wpdb->get_results(

            "SHOW COLUMNS FROM 
            $transactions_table 
            LIKE 'customer_email'"

        );


        if(empty($column)){


            $wpdb->query(

                "ALTER TABLE $transactions_table

                ADD customer_email VARCHAR(255)

                AFTER customer_name"

            );


        }








        /*
        Add personal_message column
        */

        $column = $wpdb->get_results(

            "SHOW COLUMNS FROM 
            $transactions_table 
            LIKE 'personal_message'"

        );


        if(empty($column)){


            $result = $wpdb->query(

                "ALTER TABLE $transactions_table

                ADD personal_message TEXT

                AFTER customer_email"

            );


            if($result === false){

                error_log(
                    'MBV DB UPDATE ERROR: ' . $wpdb->last_error
                );

            }


        }








        /*
        Add votes column
        */

        $column = $wpdb->get_results(

            "SHOW COLUMNS FROM 
            $transactions_table 
            LIKE 'votes'"

        );


        if(empty($column)){


            $result = $wpdb->query(

                "ALTER TABLE $transactions_table

                ADD votes INT DEFAULT 0

                AFTER personal_message"

            );


            if($result === false){

                error_log(
                    'MBV DB UPDATE ERROR: ' . $wpdb->last_error
                );

            }


        }





        /*
        Add indexes for high traffic performance
        */

        $votes_table = $wpdb->prefix . 'mbv_votes';

        if(
            $wpdb->get_var("SHOW TABLES LIKE '$votes_table'") === $votes_table
        ){

            $has_idx = $wpdb->get_results("SHOW INDEX FROM $votes_table WHERE Key_name = 'idx_ip_created'");
            if(empty($has_idx)){
                $wpdb->query("ALTER TABLE $votes_table ADD INDEX idx_ip_created (ip_address(45), created_at)");
            }

            $has_cand_idx = $wpdb->get_results("SHOW INDEX FROM $votes_table WHERE Key_name = 'idx_candidate'");
            if(empty($has_cand_idx)){
                $wpdb->query("ALTER TABLE $votes_table ADD INDEX idx_candidate (candidate_id)");
            }

        }

        $has_stripe_idx = $wpdb->get_results("SHOW INDEX FROM $transactions_table WHERE Key_name = 'idx_stripe_payment'");
        if(empty($has_stripe_idx)){
            $wpdb->query("ALTER TABLE $transactions_table ADD INDEX idx_stripe_payment (stripe_payment_id(100))");
        }

        $has_cand_status_idx = $wpdb->get_results("SHOW INDEX FROM $transactions_table WHERE Key_name = 'idx_candidate_status'");
        if(empty($has_cand_status_idx)){
            $wpdb->query("ALTER TABLE $transactions_table ADD INDEX idx_candidate_status (candidate_id, status)");
        }


    }



}