<?php

if (!defined('ABSPATH')) {
    exit;
}


class MBV_Vote_Package_CPT
{


    public function __construct()
    {

        add_action(
            'init',
            array($this,'register')
        );


    }



    public function register()
    {


        register_post_type(

            'mbv_vote_package',

            array(

                'labels'=>array(

                    'name'=>'Vote Packages',

                    'singular_name'=>'Vote Package'

                ),


                'public'=>false,


                'show_ui'=>true,


                'menu_icon'=>'dashicons-cart',


                'supports'=>array(

                    'title'

                ),


                'show_in_rest'=>true


            )

        );


    }


}