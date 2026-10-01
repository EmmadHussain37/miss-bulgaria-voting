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


        $countdown_enable = get_post_meta(
            $post->ID,
            '_mbv_countdown_enable',
            true
        );


        $countdown_end_date = get_post_meta(
            $post->ID,
            '_mbv_countdown_end_date',
            true
        );
		
		$final_message = get_post_meta(
			$post->ID,
			'_mbv_final_message',
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
            <input type="hidden" name="mbv_countdown_enable" value="0">
            <label>
                <input 
                type="checkbox"
                name="mbv_countdown_enable"
                value="1"
                <?php checked('1', $countdown_enable); ?>>
                <strong>Enable Individual Counter</strong>
            </label>
            <br>
            <small>
                Default is OFF. If enabled, this individual countdown overrides the Universal Counter for this candidate.
            </small>
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

		<p>
			<label>
				Final Message
			</label>

			<textarea
				name="mbv_final_message"
				rows="4"
				style="width:100%;"><?php echo esc_textarea($final_message); ?></textarea>

			<small>
				Message displayed in candidate voting popup.
			</small>
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
            'mbv_countdown_enable'   => '_mbv_countdown_enable',
            'mbv_countdown_end_date' => '_mbv_countdown_end_date',
			 'mbv_final_message'      => '_mbv_final_message',

        );

        foreach ($fields as $input => $meta) {
            if (isset($_POST[$input])) {
                $raw_val = wp_unslash($_POST[$input]);
                if ($input === 'mbv_instagram') {

    $clean_val = esc_url_raw($raw_val);

} elseif ($input === 'mbv_age') {

    $clean_val = absint($raw_val);

} elseif ($input === 'mbv_final_message') {

    $clean_val = sanitize_textarea_field($raw_val);

} else {

    $clean_val = sanitize_text_field($raw_val);

}

                update_post_meta($post_id, $meta, $clean_val);
            }
        }
    }


    /**
     * Determine active countdown expiry date based on candidate-specific priority logic:
     * 1. If Candidate Individual Counter is ON: return Candidate Individual Closing Date.
     * 2. Else If Universal Counter is ON: return Universal Closing Date.
     * 3. Else: return empty string (hide countdown).
     *
     * @param int $candidate_id
     * @return string Expiry datetime string or empty string
     */
    public static function get_active_countdown($candidate_id)
    {
        $individual_enable = get_post_meta($candidate_id, '_mbv_countdown_enable', true);
        $is_individual_on = ($individual_enable === '1' || $individual_enable === 1 || $individual_enable === true || $individual_enable === 'on');

        if ($is_individual_on) {
            $individual_end_date = get_post_meta($candidate_id, '_mbv_countdown_end_date', true);
            return !empty($individual_end_date) ? $individual_end_date : '';
        }

        $universal_enable = get_option('mbv_universal_countdown_enable', '1');
        $is_universal_on = ($universal_enable === '1' || $universal_enable === 1 || $universal_enable === true || $universal_enable === 'on');

        if ($is_universal_on) {
            $universal_end_date = get_option('mbv_universal_countdown_end_date', '');
            return !empty($universal_end_date) ? $universal_end_date : '';
        }

        return '';
    }


}