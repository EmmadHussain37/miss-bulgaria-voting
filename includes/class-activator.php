<?php

if (!defined('ABSPATH')) {
    exit;
}


class MBV_Activator
{


    public static function activate()
    {


        require_once MBV_PATH .
        'includes/database/class-database.php';


        MBV_Database::create_tables();


        flush_rewrite_rules();


    }


}