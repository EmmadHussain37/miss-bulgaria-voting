<?php

if (!defined('ABSPATH')) {
    exit;
}


class MBV_Database
{


    public function __construct()
    {

        // Future database hooks

    }



    public static function create_tables()
    {

        global $wpdb;


        $charset = $wpdb->get_charset_collate();


        $votes_table = $wpdb->prefix . 'mbv_votes';

        $transactions_table = $wpdb->prefix . 'mbv_transactions';



        $sql_votes = "
        CREATE TABLE $votes_table (

            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

            candidate_id BIGINT UNSIGNED NOT NULL,

            ip_address VARCHAR(100),

            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

            PRIMARY KEY  (id),

            KEY idx_ip_created (ip_address(45), created_at),

            KEY idx_candidate (candidate_id)

        ) $charset;
        ";




        $sql_transactions = "
        CREATE TABLE $transactions_table (

            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

            candidate_id BIGINT UNSIGNED NOT NULL,

            package_id BIGINT UNSIGNED NOT NULL,

            stripe_payment_id VARCHAR(255),

            customer_name VARCHAR(255),

            customer_email VARCHAR(255),
			
			personal_message TEXT,

            votes INT DEFAULT 0,

            amount DECIMAL(10,2),

            status VARCHAR(50),

            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

            PRIMARY KEY  (id),

            KEY idx_stripe_payment (stripe_payment_id(100)),

            KEY idx_candidate_status (candidate_id, status)

        ) $charset;
        ";




        require_once ABSPATH . 'wp-admin/includes/upgrade.php';



        dbDelta($sql_votes);

        dbDelta($sql_transactions);


    }


}