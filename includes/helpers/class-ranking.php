<?php

if (!defined('ABSPATH')) {
    exit;
}


class MBV_Ranking
{


    public function __construct()
    {


        add_action(

            'save_post_mbv_candidate',

            array(
                $this,
                'update_rankings'
            )

        );


    }





    public function update_rankings()
    {


        $candidates = new WP_Query(array(

            'post_type'=>'mbv_candidate',

            'posts_per_page'=>-1,

            'meta_key'=>'_mbv_votes',

            'orderby'=>'meta_value_num',

            'order'=>'DESC'

        ));



        $rank=1;



        foreach($candidates->posts as $candidate){


            update_post_meta(

                $candidate->ID,

                '_mbv_rank',

                $rank

            );


            $rank++;


        }


    }



}