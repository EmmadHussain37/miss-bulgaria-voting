<?php

if (!defined('ABSPATH')) {
    exit;
}


class MBV_Plugin
{

    private static $instance = null;


    public static function instance()
    {

        if (self::$instance === null) {

            self::$instance = new self();

        }

        return self::$instance;

    }



    private function __construct()
    {

        $this->load_modules();

    }



    private function load_modules()
{

    new MBV_Candidate_CPT();

    new MBV_Vote_Package_CPT();

    new MBV_Candidate_Fields();

    new MBV_Vote_Package_Fields();

    new MBV_Admin_Menu();

    new MBV_Admin_Columns();

    new MBV_Admin_Settings();

    new MBV_Transactions_Table();

    new MBV_Migration();

    new MBV_Stripe();

    new MBV_Stripe_Webhook();

    new MBV_REST_API();
		
	new MBV_Candidate_Stats_API();	

    new MBV_Shortcodes();

    new MBV_Vote_Packages();

    new MBV_Ranking();

    new MBV_Leaderboard();

    new MBV_Assets();

    new MBV_Database();

    new MBV_Database_Updater();

    new MBV_Updater();

}


}