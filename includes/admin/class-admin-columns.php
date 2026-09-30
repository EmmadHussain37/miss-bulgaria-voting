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

        $new_columns = array();

        $date = isset($columns['date']) ? $columns['date'] : null;
        unset($columns['date']);

        foreach ($columns as $key => $title) {
            $new_columns[$key] = $title;
            if ($key === 'title') {
                $new_columns['id'] = 'ID';
            }
        }

        if (!isset($new_columns['id'])) {
            $new_columns['id'] = 'ID';
        }

        $new_columns['country'] = 'Country';

        $new_columns['votes'] = 'Votes';

        if ($date !== null) {
            $new_columns['date'] = $date;
        }

        return $new_columns;

    }




    public function column_data($column, $post_id)
    {

        if ($column == 'id' || $column == 'candidate_id') {

            echo esc_html($post_id);

        }


        if ($column == 'country') {


            echo esc_html(
                get_post_meta(
                    $post_id,
                    '_mbv_country',
                    true
                )
            );


        }



        if ($column == 'votes') {


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