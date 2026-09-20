<?php

if (!defined('ABSPATH')) {
    exit;
}


class MBV_Vote_Packages
{


    public function __construct()
    {

        add_shortcode(
            'mbv_vote_packages',
            array(
                $this,
                'display_packages'
            )
        );

    }







    public function display_packages($atts)
    {


        $candidate_id = isset($atts['candidate'])
            ? intval($atts['candidate'])
            : 0;



        ob_start();



        $packages = new WP_Query(array(

            'post_type'      => 'mbv_vote_package',

            'posts_per_page' => -1,

            'post_status'    => 'publish',

            'orderby'        => 'meta_value_num',

            'meta_key'       => '_mbv_package_votes',

            'order'          => 'ASC'

        ));





        if($packages->have_posts()){


            echo '<div class="mbv-packages">';




            while($packages->have_posts()){


                $packages->the_post();



                $package_id = get_the_ID();




                $votes = intval(

                    get_post_meta(

                        $package_id,

                        '_mbv_package_votes',

                        true

                    )

                );




                $price = floatval(

                    get_post_meta(

                        $package_id,

                        '_mbv_package_price',

                        true

                    )

                );



                /*
                Calculate price per vote
                */

                $price_per_vote = 0;


                if($votes > 0){

                    $price_per_vote = round(
                        ($price / $votes),
                        2
                    );

                }





                /*
                Premium package classes
                */

                $package_class = '';

               
                ?>



                <div

                class="mbv-package-card mbv-buy-votes <?php echo esc_attr($package_class); ?>"

                data-candidate="<?php echo esc_attr($candidate_id); ?>"

                data-package="<?php echo esc_attr($package_id); ?>">



                    <div class="mbv-package-info">



 <h3>

<?php echo esc_html(get_the_title()); ?>

</h3>





                        <div class="mbv-package-price">


                            €<?php echo esc_html(number_format($price, 2)); ?>


                        </div>





                        <div class="mbv-price-per-vote">


                            €<?php echo esc_html(number_format($price_per_vote, 2)); ?>

                            / vote


                        </div>




                    </div>







                    <?php if($votes >= 5000): ?>


                    <div class="mbv-ultimate-label">

                        ULTIMATE PACKAGE

                    </div>


                    <?php endif; ?>



                </div>




                <?php


            }




            echo '</div>';


        }



        wp_reset_postdata();



        return ob_get_clean();


    }



}