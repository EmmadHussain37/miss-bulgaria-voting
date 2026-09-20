<?php

if (!defined('ABSPATH')) {
    exit;
}


class MBV_Leaderboard
{


    public function __construct()
    {

        add_shortcode(

            'mbv_leaderboard',

            array(
                $this,
                'render'
            )

        );

    }






    public function render()
    {


        ob_start();



        $query = new WP_Query(array(

            'post_type'=>'mbv_candidate',

            'posts_per_page'=>10,

            'post_status'=>'publish',

            'meta_key'=>'_mbv_votes',

            'orderby'=>'meta_value_num',

            'order'=>'DESC'

        ));




        $candidates = array();

        $leader_votes = 0;




        if($query->have_posts()){


            while($query->have_posts()){


                $query->the_post();



                $votes = intval(

                    get_post_meta(

                        get_the_ID(),

                        '_mbv_votes',

                        true

                    )

                );



                if($votes > $leader_votes){

                    $leader_votes = $votes;

                }




                $candidates[] = array(

                    'id'=>get_the_ID(),

                    'name'=>get_the_title(),

                    'votes'=>$votes,

                    'image'=>get_post_thumbnail_id()

                );


            }


        }



        wp_reset_postdata();




        ?>



<div class="mbv-leaderboard">



<?php


$position = 1;



foreach($candidates as $candidate){



    $percentage = 0;


    if($leader_votes > 0){

        $percentage = round(
    ($candidate['votes'] / $leader_votes) * 100
);


if($position == 1){

    $percentage = 100;

}

    }




    $status = '';



    if($position == 1){

        $status = 'leader';

    }



?>



<div class="mbv-ranking-card <?php echo esc_attr($status); ?>"
data-candidate-id="<?php echo esc_attr($candidate['id']); ?>">


<span class="mbv-your-vote-badge">
    ✨ Your Vote
</span>



    <div class="mbv-rank-number">

        #<?php echo esc_html($position); ?>

    </div>





    <div class="mbv-rank-image">


        <?php

        if($candidate['image']){


            echo wp_get_attachment_image(

                $candidate['image'],

                'thumbnail'

            );


        }

        ?>


    </div>






    <div class="mbv-rank-info">


        <h3>

            <?php echo esc_html($candidate['name']); ?>

        </h3>



        <span class="mbv-vote-count">

		<?php echo esc_html($candidate['votes']); ?>

		Votes
	
		</span>



        <div class="mbv-progress">


            <div class="mbv-progress-bar"
            style="width:<?php echo esc_attr($percentage); ?>%">


            </div>


        </div>




        <?php if($position == 1): ?>


            <small>
                🏆 Leader
            </small>


        <?php else: ?>


            <small>
                <?php echo esc_html($percentage); ?>% of leader votes
            </small>


        <?php endif; ?>




    </div>



</div>




<?php


$position++;


}



?>



</div>





<?php if (isset($_GET['payment']) && sanitize_text_field(wp_unslash($_GET['payment'])) === 'success'): ?>


<div id="mbv-payment-success" class="mbv-success-popup">


<div class="mbv-success-box">


<button class="mbv-success-close">
×
</button>


<h2>
🎉 Payment Successful
</h2>


<p>
Your votes have been added successfully.
</p>



<div class="mbv-success-details">


<strong id="mbv-success-name"></strong>


<br>


<span id="mbv-success-votes"></span>


<div id="mbv-success-total"></div>


<div id="mbv-success-rank"></div>


<div id="mbv-success-needed"></div>

<a 
href="/vote-now"
class="mbv-vote-again">

    Vote Again

</a>
	
</div>


</div>


</div>


<?php endif; ?>



<?php


return ob_get_clean();


    }


}