<?php

if (!defined('ABSPATH')) {
    exit;
}


class MBV_Candidate_Fields
{


    public function __construct()
    {

        add_action(
            'add_meta_boxes',
            array($this,'add_fields')
        );


        add_action(
            'save_post_mbv_candidate',
            array($this,'save')
        );

    }



    public function add_fields()
    {

        add_meta_box(

            'mbv_candidate_details',

            'Candidate Details',

            array($this,'render'),

            'mbv_candidate',

            'normal',

            'high'

        );


    }



    public function render($post)
    {


        wp_nonce_field(
            'mbv_candidate_save',
            'mbv_candidate_nonce'
        );



        $country = get_post_meta(
            $post->ID,
            '_mbv_country',
            true
        );


        $city = get_post_meta(
            $post->ID,
            '_mbv_city',
            true
        );


        $age = get_post_meta(
            $post->ID,
            '_mbv_age',
            true
        );


        $instagram = get_post_meta(
            $post->ID,
            '_mbv_instagram',
            true
        );


        $countdown_end_date = get_post_meta(
            $post->ID,
            '_mbv_countdown_end_date',
            true
        );



        ?>

        <p>
            <label>
                Country
            </label>

            <input 
            type="text"
            name="mbv_country"
            value="<?php echo esc_attr($country); ?>"
            style="width:100%">
        </p>



        <p>
            <label>
                City
            </label>

            <input 
            type="text"
            name="mbv_city"
            value="<?php echo esc_attr($city); ?>"
            style="width:100%">
        </p>



        <p>
            <label>
                Age
            </label>

            <input 
            type="number"
            name="mbv_age"
            value="<?php echo esc_attr($age); ?>"
            style="width:100%">
        </p>



        <p>
            <label>
                Instagram URL
            </label>

            <input 
            type="url"
            name="mbv_instagram"
            value="<?php echo esc_attr($instagram); ?>"
            style="width:100%">
        </p>



        <p>
            <label>
                Voting Countdown End Date
            </label>

            <input 
            type="datetime-local"
            name="mbv_countdown_end_date"
            value="<?php echo esc_attr($countdown_end_date); ?>"
            style="width:100%">
        </p>


        <?php


    }





    public function save($post_id)
    {
        if (
            !isset($_POST['mbv_candidate_nonce'])
            || !wp_verify_nonce(
                sanitize_text_field(wp_unslash($_POST['mbv_candidate_nonce'])),
                'mbv_candidate_save'
            )
        ) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $fields = array(
            'mbv_country'            => '_mbv_country',
            'mbv_city'               => '_mbv_city',
            'mbv_age'                => '_mbv_age',
            'mbv_instagram'          => '_mbv_instagram',
            'mbv_countdown_end_date' => '_mbv_countdown_end_date',
        );

        foreach ($fields as $input => $meta) {
            if (isset($_POST[$input])) {
                $raw_val = wp_unslash($_POST[$input]);
                if ($input === 'mbv_instagram') {
                    $clean_val = esc_url_raw($raw_val);
                } elseif ($input === 'mbv_age') {
                    $clean_val = absint($raw_val);
                } else {
                    $clean_val = sanitize_text_field($raw_val);
                }

                update_post_meta($post_id, $meta, $clean_val);
            }
        }
    }


}