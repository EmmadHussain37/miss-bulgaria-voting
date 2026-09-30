<?php

if (!defined('ABSPATH')) {
    exit;
}


class MBV_Shortcodes
{


    public function __construct()
    {


        add_shortcode(

            'miss_bulgaria_candidates',

            array($this,'candidates')

        );


        add_action(
            'wp_ajax_mbv_load_paid_vote_packages',
            array($this,'load_paid_vote_packages_ajax')
        );


        add_action(
            'wp_ajax_nopriv_mbv_load_paid_vote_packages',
            array($this,'load_paid_vote_packages_ajax')
        );


    }


public function force_vote_order($orderby)
{
    return $orderby;
}


    public function candidates($atts = array())
    {

        $atts = shortcode_atts(
            array(
                'ids' => '',
            ),
            $atts,
            'miss_bulgaria_candidates'
        );

        $extra_args = array();

        $ids_string = is_array($atts['ids']) ? implode(',', $atts['ids']) : trim((string) $atts['ids']);

        if ($ids_string !== '') {
            $raw_ids = is_array($atts['ids']) ? $atts['ids'] : explode(',', $ids_string);

            // Sanitize IDs to positive integers
            $candidate_ids = array_filter(
                array_map('absint', array_map('trim', $raw_ids)),
                function ($id) {
                    return $id > 0;
                }
            );
            $candidate_ids = array_values(array_unique($candidate_ids));

            // Prime cache for post objects to optimize post type validation
            if (function_exists('_prime_post_caches') && !empty($candidate_ids)) {
                _prime_post_caches($candidate_ids, false, false);
            }

            // Validate that IDs belong to candidate posts
            $valid_candidate_ids = array();
            foreach ($candidate_ids as $candidate_id) {
                if (get_post_type($candidate_id) === 'mbv_candidate') {
                    $valid_candidate_ids[] = $candidate_id;
                }
            }

            // Apply WordPress query argument:
            // If valid candidate IDs exist, filter by them.
            // If IDs were provided but none matched valid candidate posts, ensure 0 results are returned.
            if (!empty($valid_candidate_ids)) {
                $extra_args['post__in'] = $valid_candidate_ids;
            } else {
                $extra_args['post__in'] = array(0);
            }
        }

        ob_start();

        $query = class_exists('MBV_Ranking')
            ? MBV_Ranking::get_ranked_candidates(-1, $extra_args)
            : new WP_Query(array_merge(array(
                'post_type'      => 'mbv_candidate',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
            ), $extra_args));


    $regions = array();

    if($query->have_posts()){

        while($query->have_posts()){

            $query->the_post();

            $candidate_id = get_the_ID();

            $raw_region = get_post_meta(
                $candidate_id,
                '_mbv_region',
                true
            );

            $trimmed_region = trim((string)$raw_region);

            if(empty($trimmed_region)){
                $display_region = 'Other Candidates';
            } else {
                $display_region = $trimmed_region;
            }

            $normalized_key = mb_strtolower($display_region, 'UTF-8');

            if(!isset($regions[$normalized_key])){
                $regions[$normalized_key] = $display_region;
            } else {
                if($regions[$normalized_key] === strtolower($regions[$normalized_key]) && $display_region !== strtolower($display_region)){
                    $regions[$normalized_key] = $display_region;
                }
            }

        }

    }

    wp_reset_postdata();

    /*
    |--------------------------------------------------------------------------
    | Region Filters
    |--------------------------------------------------------------------------
    */

    echo '<div class="mbv-region-filters">';

    echo '<button 
    class="mbv-region-filter active"
    data-region="all">
    ALL
    </button>';

    foreach($regions as $normalized_key => $display_region){

        echo '<button 
        class="mbv-region-filter"
        data-region="'.esc_attr($normalized_key).'">
        '.esc_html($display_region).'
        </button>';

    }

    echo '</div>';






    /*
    |--------------------------------------------------------------------------
    | Candidates Grid
    |--------------------------------------------------------------------------
    */


    echo '<div class="mbv-candidates-grid">';



	  if($query->have_posts()){

		$query->rewind_posts();

		$mbv_rank = 1;

		while($query->have_posts()){


			$query->the_post();

		
			echo '<!-- DEBUG: ' . get_the_title() . ' = ' . get_post_meta(get_the_ID(), '_mbv_votes', true) . ' -->';
			
			set_query_var(
				'mbv_rank',
				$mbv_rank
			);


			include MBV_PATH .
			'templates/candidate-card.php';


			$mbv_rank++;


		}

	}



    echo '</div>';



    wp_reset_postdata();







    $output = ob_get_clean();




    ob_start();

    include MBV_PATH . 'templates/paid-vote-popup.php';

    $output .= ob_get_clean();




    return $output;


}




			public function load_paid_vote_packages_ajax()
	{
		$candidate_id = absint(wp_unslash($_POST['candidate_id'] ?? 0));

		if (!$candidate_id) {
			wp_die();
		}

		ob_start();

		echo do_shortcode(
			'[mbv_vote_packages candidate="' . $candidate_id . '"]'
		);

		$output = ob_get_clean();

		echo $output;

		wp_die();
	}



}