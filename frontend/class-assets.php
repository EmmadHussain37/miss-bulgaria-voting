<?php

if (!defined('ABSPATH')) {
    exit;
}


class MBV_Assets
{


    public function __construct()
    {

        add_action(
			'wp_enqueue_scripts',
			array($this,'load'),
			999
		);

    }




    public function load()
    {


        /*
        |--------------------------------------------------------------------------
        | Frontend CSS
        |--------------------------------------------------------------------------
        */

        wp_enqueue_style(

			'mbv-frontend',

			MBV_URL .
			'assets/css/frontend.css',

			array(),

			filemtime(
				MBV_PATH . 'assets/css/frontend.css'
			)

		);





        /*
        |--------------------------------------------------------------------------
        | Frontend JS
        |--------------------------------------------------------------------------
        */

        wp_enqueue_script(

            'mbv-frontend',

            MBV_URL .
            'assets/js/frontend.js',

            array('jquery'),

            MBV_VERSION,

            true

        );






        /*
        |--------------------------------------------------------------------------
        | JS Variables
        |--------------------------------------------------------------------------
        */

        wp_localize_script(

            'mbv-frontend',

            'mbvData',

            array(

                // Free voting API

                'api' => rest_url(
                    'mbv/v1/vote'
                ),



                // Stripe checkout API

                'checkout' => rest_url(
                    'mbv/v1/create-checkout'
                ),



                // Security nonce

                'nonce' => wp_create_nonce(
                    'wp_rest'
                ),



                // Site URL

                'site_url' => home_url(),



                // WordPress AJAX URL

                'ajax_url' => admin_url(
                    'admin-ajax.php'
                )

            )

        );


    }


}