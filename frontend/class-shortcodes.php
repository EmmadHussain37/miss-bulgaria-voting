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





public function candidates()
{


    ob_start();



    $query = new WP_Query(array(

        'post_type'=>'mbv_candidate',

        'posts_per_page'=>-1,

        'post_status'=>'publish'

    ));




    $regions = array();



    if($query->have_posts()){


        while($query->have_posts()){


            $query->the_post();


            $candidate_id = get_the_ID();


            $region = get_post_meta(

                $candidate_id,

                '_mbv_region',

                true

            );


            if(empty($region)){

                $region = 'Other Candidates';

            }


            $regions[] = $region;


        }


    }



    $regions = array_unique($regions);



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



    foreach($regions as $region){


        echo '<button 
        class="mbv-region-filter"
        data-region="'.esc_attr(strtolower($region)).'">
        '.esc_html($region).'
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

        while($query->have_posts()){


            $query->the_post();



            include MBV_PATH .
            'templates/candidate-card.php';



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