<?php

if (!defined('ABSPATH')) {
    exit;
}


class MBV_Vote_Package_Fields
{


    public function __construct()
    {

        add_action(
            'add_meta_boxes',
            array($this,'add_fields')
        );


        add_action(
            'save_post_mbv_vote_package',
            array($this,'save')
        );

    }



    public function add_fields()
    {


        add_meta_box(

            'mbv_vote_package_details',

            'Vote Package Details',

            array(
                $this,
                'render'
            ),

            'mbv_vote_package',

            'normal',

            'high'

        );

    }





    public function render($post)
    {


        wp_nonce_field(
            'mbv_vote_package_save',
            'mbv_vote_package_nonce'
        );


        $votes = get_post_meta(
            $post->ID,
            '_mbv_package_votes',
            true
        );


        $price = get_post_meta(
            $post->ID,
            '_mbv_package_price',
            true
        );


        ?>


        <p>

        <label>
        Number of Votes
        </label>

        <input

        type="number"

        name="mbv_package_votes"

        value="<?php echo esc_attr($votes); ?>"

        style="width:100%">

        </p>



        <p>

        <label>
        Package Price ($)
        </label>

        <input

        type="number"

        step="0.01"

        name="mbv_package_price"

        value="<?php echo esc_attr($price); ?>"

        style="width:100%">

        </p>


        <?php


    }





    public function save($post_id)
    {
        if (
            !isset($_POST['mbv_vote_package_nonce'])
            || !wp_verify_nonce(
                sanitize_text_field(wp_unslash($_POST['mbv_vote_package_nonce'])),
                'mbv_vote_package_save'
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

        if (isset($_POST['mbv_package_votes'])) {
            update_post_meta(
                $post_id,
                '_mbv_package_votes',
                absint(wp_unslash($_POST['mbv_package_votes']))
            );
        }

        if (isset($_POST['mbv_package_price'])) {
            update_post_meta(
                $post_id,
                '_mbv_package_price',
                floatval(wp_unslash($_POST['mbv_package_price']))
            );
        }
    }


}