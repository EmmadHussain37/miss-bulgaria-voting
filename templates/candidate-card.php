<?php

if (!defined('ABSPATH')) {
    exit;
}


$candidate_id = get_the_ID();



$country = get_post_meta(
    $candidate_id,
    '_mbv_country',
    true
);



$city = get_post_meta(
    $candidate_id,
    '_mbv_city',
    true
);



$region = get_post_meta(
    $candidate_id,
    '_mbv_region',
    true
);



$votes = intval(
    get_post_meta(
        $candidate_id,
        '_mbv_votes',
        true
    )
);


if($votes > 5000){

    $votes = 5000;

}



$instagram = get_post_meta(
    $candidate_id,
    '_mbv_instagram',
    true
);



$countdown_end_date = get_post_meta(
    $candidate_id,
    '_mbv_countdown_end_date',
    true
);



$total_votes = 5000;


$percentage = 0;


if ($total_votes > 0) {

    $percentage = round(
        ($votes / intval($total_votes)) * 100
    );

    if($percentage > 100){

        $percentage = 100;

    }

}


?>



<div class="mbv-card" 
data-candidate="<?php echo esc_attr($candidate_id); ?>"
data-region="<?php echo esc_attr(strtolower($region)); ?>">





<?php

$rank = intval(
    get_post_meta(
        $candidate_id,
        '_mbv_rank',
        true
    )
);


if($rank):

?>

<div class="mbv-badge <?php echo 'rank-' . esc_attr($rank); ?>">

    <?php echo esc_html($rank); ?>

</div>

<?php endif; ?>







<div class="mbv-image">


<?php


if(has_post_thumbnail()){


    the_post_thumbnail(

        'large',

        array(

            'class'=>'mbv-candidate-photo',

            'loading'=>'lazy'

        )

    );


}
else
{


?>


<img 

src="<?php echo esc_url(MBV_URL . 'assets/images/default-candidate.jpg'); ?>"

class="mbv-candidate-photo"

alt="<?php esc_attr_e('Candidate', 'miss-bulgaria-voting'); ?>">


<?php

}

?>


</div>








<div class="mbv-content">





<?php if($region): ?>


<div class="mbv-region-title">

    <?php echo esc_html($region); ?>

</div>


<?php endif; ?>





<h2>

<?php echo esc_html(get_the_title()); ?>

</h2>







<div class="mbv-location">


<?php if($country): ?>

<span>

<?php echo esc_html($country); ?>

</span>

<?php endif; ?>




<?php if($city): ?>

<span>

• <?php echo esc_html($city); ?>

</span>

<?php endif; ?>


</div>








<div class="mbv-votes">


<?php echo intval($votes); ?>

/

<?php echo esc_html($total_votes); ?>

Votes


</div>








<div class="mbv-percentage">

<?php echo esc_html($percentage); ?>%

</div>








<div class="mbv-progress">


<div 

class="mbv-progress-bar"

style="width:<?php echo esc_attr($percentage); ?>%">

</div>


</div>





<?php if($countdown_end_date): ?>

<div class="mbv-countdown" data-countdown="<?php echo esc_attr(date('Y-m-d\TH:i:s', strtotime($countdown_end_date))); ?>">

    <div>
        <span class="days">00</span>
        <small>DAYS</small>
    </div>

    <div>
        <span class="hours">00</span>
        <small>HOURS</small>
    </div>

    <div>
        <span class="minutes">00</span>
        <small>MIN</small>
    </div>

    <div>
        <span class="seconds">00</span>
        <small>SEC</small>
    </div>

</div>

<?php endif; ?>








<?php if($votes >= 5000): ?>

<button 

class="mbv-paid-vote-btn mbv-voting-closed"

disabled

>

VOTING CLOSED

</button>


<?php else: ?>


<button 

class="mbv-paid-vote-btn"

data-candidate-id="<?php echo esc_attr($candidate_id); ?>"

data-candidate-name="<?php echo esc_attr(get_the_title()); ?>"

data-candidate-image="<?php echo esc_url(get_the_post_thumbnail_url($candidate_id,'large')); ?>"

data-candidate-region="<?php echo esc_attr($region); ?>"

>

VOTE

</button>


<?php endif; ?>
	
	
<div class="mbv-final-path-text">

    <h3>A DIGITAL PATH TO THE MISS BULGARIA FINAL</h3>

    <p>
        Help talented candidates from every region reach 5,000 votes,
        complete the mandatory Bootcamp and secure their place in the Grand Final.
    </p>

</div>










<?php if($instagram): ?>


<a 

href="<?php echo esc_url($instagram); ?>"

target="_blank"

class="mbv-social">


Instagram


</a>


<?php endif; ?>







</div>


</div>