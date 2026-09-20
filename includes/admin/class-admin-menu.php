<?php

if (!defined('ABSPATH')) {
    exit;
}


class MBV_Admin_Menu
{


    private $settings;



    public function __construct()
    {


        $this->settings = new MBV_Admin_Settings();



        add_action(
            'admin_menu',
            array(
                $this,
                'register_menu'
            )
        );


    }






    public function register_menu()
    {


        add_menu_page(

            'Miss Bulgaria',

            'Miss Bulgaria',

            'manage_options',

            'mbv-dashboard',

            array(
                $this,
                'dashboard'
            ),

            'dashicons-awards',

            25

        );






        add_submenu_page(

            'mbv-dashboard',

            'Dashboard',

            'Dashboard',

            'manage_options',

            'mbv-dashboard',

            array(
                $this,
                'dashboard'
            )

        );






        add_submenu_page(

            'mbv-dashboard',

            'Candidates',

            'Candidates',

            'manage_options',

            'edit.php?post_type=mbv_candidate'

        );






        add_submenu_page(

            'mbv-dashboard',

            'Vote Packages',

            'Vote Packages',

            'manage_options',

            'edit.php?post_type=mbv_vote_package'

        );






        add_submenu_page(

            'mbv-dashboard',

            'Settings',

            'Settings',

            'manage_options',

            'mbv-settings',

            array(
                $this->settings,
                'render'
            )

        );


    }








    public function dashboard()
    {


        $candidates = wp_count_posts(
            'mbv_candidate'
        );


        $packages = wp_count_posts(
            'mbv_vote_package'
        );


        ?>

        <div class="wrap">


            <h1>
                Miss Bulgaria Voting Dashboard
            </h1>



            <div style="
            display:grid;
            grid-template-columns:repeat(4,1fr);
            gap:20px;
            margin-top:30px;
            ">



                <div class="card">

                    <h2>
                        Candidates
                    </h2>

                    <h1>
                    <?php echo esc_html(
                        $candidates->publish
                    ); ?>
                    </h1>

                </div>





                <div class="card">

                    <h2>
                        Vote Packages
                    </h2>

                    <h1>
                    <?php echo esc_html(
                        $packages->publish
                    ); ?>
                    </h1>

                </div>





                <div class="card">

                    <h2>
                        Total Votes
                    </h2>

                    <h1>
                    <?php echo intval(
                        $this->total_votes()
                    ); ?>
                    </h1>

                </div>





                <div class="card">

                    <h2>
                        Revenue
                    </h2>

                    <h1>
                    $<?php echo number_format(
                        $this->total_revenue(),
                        2
                    ); ?>
                    </h1>

                </div>




            </div>



        </div>


        <?php


    }









    private function total_votes()
    {
        global $wpdb;

        $total = $wpdb->get_var(
            "SELECT COALESCE(SUM(CAST(pm.meta_value AS UNSIGNED)), 0) 
             FROM {$wpdb->postmeta} pm
             INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
             WHERE pm.meta_key = '_mbv_votes'
               AND p.post_type = 'mbv_candidate'"
        );

        return intval($total);
    }









    private function total_revenue()
    {


        global $wpdb;


        $table =
        $wpdb->prefix . 'mbv_transactions';



        $total =
        $wpdb->get_var(

            "SELECT SUM(amount)
            FROM $table
            WHERE status='completed'"

        );



        return $total ?: 0;


    }



}