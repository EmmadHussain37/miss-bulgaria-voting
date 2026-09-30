<?php

if (!defined('ABSPATH')) {
    exit;
}


class MBV_Candidate_Stats_API
{


    public function __construct()
    {


        add_action(
            'rest_api_init',
            array(
                $this,
                'register_routes'
            )
        );


    }






    public function register_routes()
    {


        register_rest_route(

            'mbv/v1',

            '/candidate-stats',

            array(

                'methods'=>'GET',

                'callback'=>array(
                    $this,
                    'get_stats'
                ),

                'permission_callback'=>'__return_true'

            )

        );


    }









    public function get_stats(
        WP_REST_Request $request
    )
    {


        $candidate_id = intval(

            $request->get_param(
                'candidate_id'
            )

        );




        if(!$candidate_id){


            return new WP_Error(

                'missing_candidate',

                'Candidate ID missing',

                array(
                    'status'=>400
                )

            );


        }







        global $wpdb;

        /*
        Get current candidate votes
        */

        $current_votes = intval(
            get_post_meta(
                $candidate_id,
                '_mbv_votes',
                true
            )
        );

        /*
        Calculate rank and top votes using efficient direct SQL with tie handling
        */

        $candidate_post = get_post($candidate_id);
        $candidate_date = $candidate_post ? $candidate_post->post_date : '1970-01-01 00:00:00';

        $higher_voted_count = intval($wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT p.ID) 
             FROM {$wpdb->posts} p
             LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_mbv_votes'
             WHERE p.post_type = 'mbv_candidate' 
               AND p.post_status = 'publish' 
               AND (
                   COALESCE(CAST(pm.meta_value AS UNSIGNED), 0) > %d
                   OR (
                       COALESCE(CAST(pm.meta_value AS UNSIGNED), 0) = %d
                       AND (p.post_date > %s OR (p.post_date = %s AND p.ID > %d))
                   )
               )",
            $current_votes,
            $current_votes,
            $candidate_date,
            $candidate_date,
            $candidate_id
        )));

        $rank = $higher_voted_count + 1;

        $top_votes = intval($wpdb->get_var(
            "SELECT MAX(COALESCE(CAST(pm.meta_value AS UNSIGNED), 0)) 
             FROM {$wpdb->posts} p
             LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_mbv_votes'
             WHERE p.post_type = 'mbv_candidate' 
               AND p.post_status = 'publish'"
        ));

        if ($top_votes < $current_votes) {
            $top_votes = $current_votes;
        }

        /*
        Votes needed to reach #1
        */

        $needed = 0;

        if($rank > 1){

            $needed = ($top_votes - $current_votes) + 1;

        }

        return array(

            'success'=>true,

            'candidate_id'=>$candidate_id,

            'total_votes'=>$current_votes,

            'rank'=>$rank,

            'top_votes'=>$top_votes,

            'needed'=>$needed

        );



    }



}