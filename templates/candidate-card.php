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



$raw_region = get_post_meta(
    $candidate_id,
    '_mbv_region',
    true
);

$region = trim((string) $raw_region);

if (empty($region)) {
    $region = 'Other Candidates';
}

$normalized_region = mb_strtolower($region, 'UTF-8');



$votes = intval(
    get_post_meta(
        $candidate_id,
        '_mbv_votes',
        true
    )
);

$max_votes_setting = get_option('mbv_max_votes_per_candidate', '');
$has_limit = ($max_votes_setting !== '' && $max_votes_setting !== false && is_numeric($max_votes_setting) && intval($max_votes_setting) > 0);
$limit = $has_limit ? intval($max_votes_setting) : 0;
$is_closed = ($has_limit && $votes >= $limit);



$instagram = get_post_meta(
    $candidate_id,
    '_mbv_instagram',
    true
);



$countdown_end_date = class_exists('MBV_Candidate_Fields')
    ? MBV_Candidate_Fields::get_active_countdown($candidate_id)
    : get_post_meta($candidate_id, '_mbv_countdown_end_date', true);

$final_message = get_post_meta(
    $candidate_id,
    '_mbv_final_message',
    true
);

if (empty($final_message)) {

    if ($has_limit) {
        $final_message = 'Help talented candidates from every region reach ' . number_format_i18n($limit) . ' votes, complete the mandatory Bootcamp and secure their place in the Grand Final.';
    } else {
        $final_message = 'Help talented candidates from every region gain votes, complete the mandatory Bootcamp and secure their place in the Grand Final.';
    }

}

$percentage = 0;

if ($has_limit && $limit > 0) {

    $percentage = round(
        ($votes / $limit) * 100
    );

    if($percentage > 100){

        $percentage = 100;

    }

}


?>



<div class="mbv-card" 
data-candidate="<?php echo esc_attr($candidate_id); ?>"
data-region="<?php echo esc_attr($normalized_region); ?>">





<?php

$rank = get_query_var(
    'mbv_rank',
    0
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





<?php if(!empty($region) && $region !== 'Other Candidates'): ?>


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
<?php echo intval($votes) . ($has_limit ? ' / ' . esc_html($limit) : '') . ' Votes'; ?>
</div>








<?php if ($has_limit): ?>

<div class="mbv-percentage">

<?php echo esc_html($percentage); ?>%

</div>








<div class="mbv-progress">


<div 

class="mbv-progress-bar"

style="width:<?php echo esc_attr($percentage); ?>%">

</div>


</div>

<?php endif; ?>





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








<?php if($is_closed): ?>

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
		
data-candidate-message="<?php echo esc_attr($final_message); ?>"

>

VOTE

</button>


<?php endif; ?>
	
	
<div class="mbv-final-path-text">


      <p>
        <?php echo esc_html($final_message); ?>
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