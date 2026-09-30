<?php

if (!defined('ABSPATH')) {
    exit;
}


class MBV_Admin_Settings
{


    public function __construct()
    {


        add_action(

            'admin_init',

            array(
                $this,
                'register_settings'
            )

        );


    }





    public function register_settings()
    {


        register_setting(

            'mbv_settings_group',

            'mbv_stripe_secret_key',

            array(
                'sanitize_callback' => 'sanitize_text_field'
            )

        );



        register_setting(

            'mbv_settings_group',

            'mbv_stripe_webhook_secret',

            array(
                'sanitize_callback' => 'sanitize_text_field'
            )

        );



        register_setting(

            'mbv_settings_group',

            'mbv_universal_countdown_enable',

            array(
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => '1'
            )

        );



        register_setting(

            'mbv_settings_group',

            'mbv_universal_countdown_end_date',

            array(
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => ''
            )

        );



        register_setting(

            'mbv_settings_group',

            'mbv_max_votes_per_candidate',

            array(
                'sanitize_callback' => array($this, 'sanitize_max_votes'),
                'default'           => ''
            )

        );


    }





    public function render()
    {


        ?>


        <div class="wrap">


            <h1>
                Miss Bulgaria Settings
            </h1>



            <form method="post" action="options.php">


                <?php

                settings_fields(
                    'mbv_settings_group'
                );


                do_settings_sections(
                    'mbv_settings_group'
                );

                ?>



                <table class="form-table">


                    <tr>


                        <th>
                            Stripe Secret Key
                        </th>


                        <td>

                            <input

                            type="password"

                            name="mbv_stripe_secret_key"

                            value="<?php echo esc_attr(

                                get_option(
                                    'mbv_stripe_secret_key'
                                )

                            ); ?>"

                            style="width:500px">


                        </td>


                    </tr>





                    <tr>


                        <th>
                            Stripe Webhook Secret
                        </th>


                        <td>


                            <input

                            type="password"

                            name="mbv_stripe_webhook_secret"

                            value="<?php echo esc_attr(

                                get_option(
                                    'mbv_stripe_webhook_secret'
                                )

                            ); ?>"

                            style="width:500px">


                        </td>


                    </tr>




                    <tr>


                        <th>
                            Enable Universal Counter
                        </th>


                        <td>

                            <input type="hidden" name="mbv_universal_countdown_enable" value="0">

                            <label>

                                <input
                                type="checkbox"
                                name="mbv_universal_countdown_enable"
                                value="1"
                                <?php checked('1', get_option('mbv_universal_countdown_enable', '1')); ?>
                                >

                                Enable Universal Voting Countdown for all candidates

                            </label>

                            <p class="description">
                                Default is ON. When enabled, this countdown applies to all candidates across the platform.
                            </p>

                        </td>


                    </tr>




                    <tr>


                        <th>
                            Universal Voting Closing Date &amp; Time
                        </th>


                        <td>

                            <input
                            type="datetime-local"
                            name="mbv_universal_countdown_end_date"
                            value="<?php echo esc_attr(
                                get_option(
                                    'mbv_universal_countdown_end_date',
                                    ''
                                )
                            ); ?>"
                            style="width:300px">

                            <p class="description">
                                Universal voting closing date and time used for all candidates when Universal Counter is ON.
                            </p>

                        </td>


                    </tr>




                    <tr>


                        <th>
                            Maximum Votes Per Candidate
                        </th>


                        <td>

                            <input
                            type="number"
                            min="1"
                            step="1"
                            name="mbv_max_votes_per_candidate"
                            value="<?php echo esc_attr(
                                get_option(
                                    'mbv_max_votes_per_candidate',
                                    ''
                                )
                            ); ?>"
                            placeholder="Leave empty for unlimited votes"
                            style="width:300px">

                            <p class="description">
                                Maximum allowed votes per candidate. If left empty, candidates can receive unlimited votes.
                            </p>

                        </td>


                    </tr>



                </table>



                <?php submit_button(); ?>


            </form>

            <hr style="margin: 40px 0 25px;">

            <h2><?php esc_html_e('Shortcode Reference', 'miss-bulgaria-voting'); ?></h2>
            <p class="description" style="margin-bottom: 20px;">
                <?php esc_html_e('Use the following shortcodes to display candidate listings, leaderboards, and voting packages anywhere on your website.', 'miss-bulgaria-voting'); ?>
            </p>

            <div style="display: flex; flex-direction: column; gap: 20px; max-width: 900px;">

                <!-- Candidate Listing -->
                <div class="card" style="max-width: 100%; margin: 0; padding: 18px 24px;">
                    <h3 style="margin-top: 0; margin-bottom: 8px;">
                        <?php esc_html_e('Candidate Listing', 'miss-bulgaria-voting'); ?>
                    </h3>
                    <p style="margin: 6px 0;">
                        <strong><?php esc_html_e('Shortcode:', 'miss-bulgaria-voting'); ?></strong><br>
                        <code style="font-size: 14px; padding: 4px 8px; display: inline-block; margin-top: 4px;">[miss_bulgaria_candidates]</code>
                    </p>
                    <p style="margin: 8px 0 0;">
                        <strong><?php esc_html_e('Description:', 'miss-bulgaria-voting'); ?></strong><br>
                        <?php esc_html_e('Displays all Miss Bulgaria candidates with voting functionality.', 'miss-bulgaria-voting'); ?>
                    </p>
                </div>

                <!-- Leaderboard -->
                <div class="card" style="max-width: 100%; margin: 0; padding: 18px 24px;">
                    <h3 style="margin-top: 0; margin-bottom: 8px;">
                        <?php esc_html_e('Leaderboard', 'miss-bulgaria-voting'); ?>
                    </h3>
                    <p style="margin: 6px 0;">
                        <strong><?php esc_html_e('Shortcode:', 'miss-bulgaria-voting'); ?></strong><br>
                        <code style="font-size: 14px; padding: 4px 8px; display: inline-block; margin-top: 4px;">[mbv_leaderboard]</code>
                    </p>
                    <p style="margin: 8px 0 0;">
                        <strong><?php esc_html_e('Description:', 'miss-bulgaria-voting'); ?></strong><br>
                        <?php esc_html_e('Displays candidate ranking leaderboard.', 'miss-bulgaria-voting'); ?>
                    </p>
                </div>

                <!-- Vote Packages -->
                <div class="card" style="max-width: 100%; margin: 0; padding: 18px 24px;">
                    <h3 style="margin-top: 0; margin-bottom: 8px;">
                        <?php esc_html_e('Vote Packages', 'miss-bulgaria-voting'); ?>
                    </h3>
                    <p style="margin: 6px 0;">
                        <strong><?php esc_html_e('Shortcode:', 'miss-bulgaria-voting'); ?></strong><br>
                        <code style="font-size: 14px; padding: 4px 8px; display: inline-block; margin-top: 4px;">[mbv_vote_packages]</code>
                    </p>
                    <p style="margin: 8px 0 0;">
                        <strong><?php esc_html_e('Description:', 'miss-bulgaria-voting'); ?></strong><br>
                        <?php esc_html_e('Displays available voting packages.', 'miss-bulgaria-voting'); ?>
                    </p>
                    <p style="margin: 8px 0 0;">
                        <strong><?php esc_html_e('Optional Candidate Attribute:', 'miss-bulgaria-voting'); ?></strong><br>
                        <code style="font-size: 13px; padding: 3px 6px; display: inline-block; margin-top: 4px;">[mbv_vote_packages candidate="101"]</code>
                        <span class="description" style="display: inline-block; margin-left: 6px;"><?php esc_html_e('Pre-selects the specified candidate ID for vote purchasing.', 'miss-bulgaria-voting'); ?></span>
                    </p>
                </div>

                <!-- Show Selected Candidates Only -->
                <div class="card" style="max-width: 100%; margin: 0; padding: 18px 24px; border-left: 4px solid #2271b1;">
                    <h3 style="margin-top: 0; margin-bottom: 8px;">
                        <?php esc_html_e('Show Selected Candidates Only', 'miss-bulgaria-voting'); ?>
                    </h3>
                    <p style="margin: 6px 0;">
                        <?php esc_html_e('You can display only selected candidates by adding candidate IDs.', 'miss-bulgaria-voting'); ?>
                    </p>
                    <p style="margin: 8px 0;">
                        <strong><?php esc_html_e('Example:', 'miss-bulgaria-voting'); ?></strong><br>
                        <code style="font-size: 14px; padding: 4px 8px; display: inline-block; margin-top: 4px;">[miss_bulgaria_candidates ids="101,103,106"]</code>
                    </p>
                    <p style="margin: 8px 0;">
                        <strong><?php esc_html_e('Explanation:', 'miss-bulgaria-voting'); ?></strong><br>
                        <?php esc_html_e('This will display only candidates with these IDs:', 'miss-bulgaria-voting'); ?>
                        <ul style="list-style-type: disc; margin-left: 20px; margin-top: 4px;">
                            <li><code>101</code></li>
                            <li><code>103</code></li>
                            <li><code>106</code></li>
                        </ul>
                    </p>
                    <div style="background: #f0f0f1; border-radius: 4px; padding: 10px 14px; margin-top: 10px;">
                        <strong><?php esc_html_e('To find candidate IDs:', 'miss-bulgaria-voting'); ?></strong><br>
                        <?php esc_html_e('Go to:', 'miss-bulgaria-voting'); ?> <strong>Miss Bulgaria Voting &rarr; Candidates</strong><br>
                        <?php esc_html_e('The Candidate ID column shows each candidate ID.', 'miss-bulgaria-voting'); ?>
                    </div>
                </div>

            </div>



        </div>


        <?php


    }




    public function sanitize_max_votes($val)
    {
        $trimmed = trim((string) $val);
        if ($trimmed === '') {
            return '';
        }
        $int = absint($trimmed);
        return $int > 0 ? $int : '';
    }


}