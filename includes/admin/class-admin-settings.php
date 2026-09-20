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



                </table>



                <?php submit_button(); ?>


            </form>



        </div>


        <?php


    }



}