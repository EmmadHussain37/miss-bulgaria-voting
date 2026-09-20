<?php

if (!defined('ABSPATH')) {
    exit;
}


class MBV_REST_API
{


    public function __construct()
    {

        add_action(
            'rest_api_init',
            array($this,'register_routes')
        );

    }




    public function register_routes()
    {


        register_rest_route(

            'mbv/v1',

            '/vote',

            array(

                'methods'=>'POST',

                'callback'=>array(
                    $this,
                    'create_vote'
                ),

                'permission_callback'=>'__return_true'

            )

        );



        register_rest_route(

            'mbv/v1',

            '/leaderboard',

            array(

                'methods'=>'GET',

                'callback'=>array(
                    $this,
                    'leaderboard'
                ),

                'permission_callback'=>'__return_true'

            )

        );


    }





    public function create_vote(
        WP_REST_Request $request
    )
    {


        global $wpdb;

		/*
Honeypot bot check
*/

$honeypot = $request->get_param('website');

if(!empty($honeypot)){

    return new WP_Error(

        'bot_detected',

        'Bot detected',

        array(
            'status'=>403
        )

    );

}

        /*
        Verify REST nonce
        */

        $nonce = $request->get_header(
            'X-WP-Nonce'
        );


        if(
            !wp_verify_nonce(
                $nonce,
                'wp_rest'
            )
        ){

            return new WP_Error(

                'invalid_nonce',

                'Security check failed',

                array(
                    'status'=>403
                )

            );

        }







        $candidate_id =
        intval(
            $request->get_param(
                'candidate_id'
            )
        );



        $votes =
        intval(
            $request->get_param(
                'votes'
            )
        );






        /*
        Validate data
        */

        if(
            !$candidate_id ||
            !$votes
        ){

            return new WP_Error(

                'invalid_data',

                'Invalid voting data',

                array(
                    'status'=>400
                )

            );

        }






        /*
        Only allow one free vote
        */

        if($votes > 1){

            return new WP_Error(

                'vote_limit',

                'Invalid vote amount',

                array(
                    'status'=>400
                )

            );

        }







        /*
        Check candidate exists
        */

        $candidate = get_post($candidate_id);



        if(
            !$candidate ||
            $candidate->post_type !== 'mbv_candidate'
        ){

            return new WP_Error(

                'invalid_candidate',

                'Candidate not found',

                array(
                    'status'=>404
                )

            );

        }


		/*Stop voting when candidate reaches 5000 votes*/

			$current_votes = intval(
				get_post_meta(
					$candidate_id,
					'_mbv_votes',
					true
				)
			);


			if($current_votes >= 5000){

				return new WP_Error(

					'voting_closed',

					'Voting has ended for this candidate',

					array(
						'status'=>403
					)

				);

			}




        /*
        Prevent duplicate free votes
        */

        $ip = sanitize_text_field(
            wp_unslash($_SERVER['REMOTE_ADDR'] ?? '')
        );



        $table =
        $wpdb->prefix.'mbv_votes';



        $already_voted = $wpdb->get_var(

            $wpdb->prepare(

                "SELECT id
                FROM $table
                WHERE ip_address=%s
                AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
                LIMIT 1",

                $ip

            )

        );



        if($already_voted){


            return new WP_Error(

                'already_voted',

                'You have already voted today',

                array(
                    'status'=>429
                )

            );


        }








        /*
        Save vote
        */


        $inserted = $wpdb->insert(

            $table,

            array(

                'candidate_id'=>$candidate_id,

                'vote_amount'=>$votes,

                'ip_address'=>$ip

            ),

            array(

                '%d',

                '%d',

                '%s'

            )

        );





       if(!$inserted){

            error_log('MBV DB Error in vote insert: ' . $wpdb->last_error);

            return new WP_Error(

                'database_error',

                'Vote could not be saved. Please try again later.',

                array(
                    'status'=>500
                )

            );


}








        /*
        Update candidate votes atomically
        */

        if(!metadata_exists('post', $candidate_id, '_mbv_votes')){
            add_post_meta($candidate_id, '_mbv_votes', 0, true);
        }

        $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$wpdb->postmeta}
                SET meta_value = CAST(meta_value AS UNSIGNED) + %d
                WHERE post_id = %d AND meta_key = '_mbv_votes'",
                $votes,
                $candidate_id
            )
        );

        clean_post_cache($candidate_id);
        wp_cache_delete($candidate_id, 'post_meta');

        $total_votes = intval(
            get_post_meta(
                $candidate_id,
                '_mbv_votes',
                true
            )
        );

        return array(

            'success'=>true,

            'message'=>'Vote added successfully',

            'total_votes'=>$total_votes

        );


    }







    public function leaderboard()
    {


        $query =
        new WP_Query(array(

            'post_type'=>'mbv_candidate',

            'posts_per_page'=>10,

            'meta_key'=>'_mbv_votes',

            'orderby'=>'meta_value_num',

            'order'=>'DESC'

        ));



        $data=array();



        foreach($query->posts as $candidate){


            $data[]=array(

                'id'=>$candidate->ID,

                'name'=>$candidate->post_title,

                'votes'=>
                intval(
                    get_post_meta(
                        $candidate->ID,
                        '_mbv_votes',
                        true
                    )
                )

            );


        }



        return $data;


    }



}