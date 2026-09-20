<?php

if (!defined('ABSPATH')) {
    exit;
}


class MBV_Candidate_CPT
{


    public function __construct()
    {

        add_action(
            'init',
            array($this,'register')
        );
	
	add_action(
    'add_meta_boxes',
    array($this,'add_region_box')
);

add_action(
    'save_post_mbv_candidate',
    array($this,'save_region')
);		

    }



    public function register()
    {


        $labels = array(

            'name' => 'Candidates',

            'singular_name' => 'Candidate',

            'add_new' => 'Add Candidate',

            'add_new_item' => 'Add New Candidate',

            'edit_item' => 'Edit Candidate',

            'menu_name' => 'Candidates'

        );



        register_post_type(

            'mbv_candidate',

            array(

                'labels'=>$labels,

                'public'=>true,

                'menu_icon'=>'dashicons-groups',

                'supports'=>array(

                    'title',

                    'editor',

                    'thumbnail'

                ),

                'has_archive'=>true,

                'rewrite'=>array(

                    'slug'=>'candidates'

                ),

                'show_in_rest'=>true


            )

        );


    }

public function add_region_box()
{

    add_meta_box(
        'mbv_candidate_region',
        'Candidate Region',
        array($this,'region_field'),
        'mbv_candidate',
        'side'
    );

}



public function region_field($post)
{
    wp_nonce_field('mbv_save_candidate_region', 'mbv_candidate_region_nonce');

    $region = get_post_meta(
        $post->ID,
        '_mbv_region',
        true
    );

    ?>

    <input
        type="text"
        name="mbv_region"
        value="<?php echo esc_attr($region); ?>"
        style="width:100%;"
        placeholder="<?php esc_attr_e('Enter region', 'miss-bulgaria-voting'); ?>"
    >

    <?php

}



public function save_region($post_id)
{
    if (
        !isset($_POST['mbv_candidate_region_nonce'])
        || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mbv_candidate_region_nonce'])), 'mbv_save_candidate_region')
    ) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['mbv_region'])) {
        update_post_meta(
            $post_id,
            '_mbv_region',
            sanitize_text_field(wp_unslash($_POST['mbv_region']))
        );
    }
}
	
}