<?php

if (!defined('ABSPATH')) {
    exit;
}


class MBV_Admin_Columns
{


    public function __construct()
    {


        add_filter(

            'manage_mbv_candidate_posts_columns',

            array($this,'columns')

        );


        add_action(

            'manage_mbv_candidate_posts_custom_column',

            array($this,'column_data'),

            10,

            2

        );


    }



    public function columns($columns)
    {


        $columns['country']='Country';

        $columns['votes']='Votes';


        return $columns;

    }




    public function column_data($column,$post_id)
    {


        if($column=='country'){


            echo esc_html(
                get_post_meta(
                    $post_id,
                    '_mbv_country',
                    true
                )
            );


        }



        if($column=='votes'){


            echo intval(
                get_post_meta(
                    $post_id,
                    '_mbv_votes',
                    true
                )
            );


        }



    }


}