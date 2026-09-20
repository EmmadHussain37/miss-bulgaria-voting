<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}


global $wpdb;


/*
|--------------------------------------------------------------------------
| Remove Custom Tables
|--------------------------------------------------------------------------
*/


$tables = array(

    $wpdb->prefix . 'mbv_votes',

    $wpdb->prefix . 'mbv_transactions'

);



foreach($tables as $table){


    $wpdb->query(

        "DROP TABLE IF EXISTS $table"

    );


}




/*
|--------------------------------------------------------------------------
| Remove Candidate Posts
|--------------------------------------------------------------------------
*/


$candidates = get_posts(array(

    'post_type'=>'mbv_candidate',

    'numberposts'=>-1,

    'post_status'=>'any'

));



foreach($candidates as $candidate){


    wp_delete_post(

        $candidate->ID,

        true

    );


}




/*
|--------------------------------------------------------------------------
| Remove Vote Packages
|--------------------------------------------------------------------------
*/


$packages = get_posts(array(

    'post_type'=>'mbv_vote_package',

    'numberposts'=>-1,

    'post_status'=>'any'

));



foreach($packages as $package){


    wp_delete_post(

        $package->ID,

        true

    );


}




/*
|--------------------------------------------------------------------------
| Remove Settings
|--------------------------------------------------------------------------
*/


delete_option(
    'mbv_stripe_secret_key'
);


delete_option(
    'mbv_stripe_webhook_secret'
);