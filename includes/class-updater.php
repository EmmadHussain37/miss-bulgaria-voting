<?php

if (!defined('ABSPATH')) {
    exit;
}


class MBV_Updater
{


    public function __construct()
    {


        add_action(

            'plugins_loaded',

            array(
                $this,
                'check_version'
            )

        );


    }







    public function check_version()
    {


        $installed =
        get_option(
            'mbv_version'
        );



        if(
            $installed !== MBV_VERSION
        ){


            update_option(

                'mbv_version',

                MBV_VERSION

            );


        }


    }



}