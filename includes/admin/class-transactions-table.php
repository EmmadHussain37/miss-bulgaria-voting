<?php

if (!defined('ABSPATH')) {
    exit;
}


class MBV_Transactions_Table
{


    public function __construct()
    {


        add_action(

            'admin_menu',

            array(
                $this,
                'register_page'
            )

        );


    }







    public function register_page()
    {


        add_submenu_page(

            'mbv-dashboard',

            'Transactions',

            'Transactions',

            'manage_options',

            'mbv-transactions',

            array(
                $this,
                'render'
            )

        );


    }








    public function render()
    {


        global $wpdb;

        $table =
        $wpdb->prefix .
        'mbv_transactions';

        $per_page = 50;
        $current_page = isset($_GET['paged']) ? max(1, absint(wp_unslash($_GET['paged']))) : 1;
        $offset = ($current_page - 1) * $per_page;

        $total_items = intval($wpdb->get_var("SELECT COUNT(*) FROM $table"));
        $total_pages = $total_items > 0 ? ceil($total_items / $per_page) : 1;

        $transactions =
        $wpdb->get_results(
            $wpdb->prepare(
                "SELECT *
                FROM $table
                ORDER BY created_at DESC
                LIMIT %d OFFSET %d",
                $per_page,
                $offset
            )
        );

        ?>


        <div class="wrap">


            <h1>
                Payment Transactions
            </h1>



            <table class="widefat fixed striped">


                <thead>

                    <tr>

                        <th>
                            ID
                        </th>


                        <th>
                            Candidate
                        </th>


                        <th>
                            Package
                        </th>


                        <th>
							Buyer Name
						</th>


						<th>
							Email
						</th>
						
						<th>
    						Personal Message
						</th>


						<th>
							Votes
						</th>


						<th>
							Stripe Payment ID
						</th>


                        <th>
                            Amount
                        </th>


                        <th>
                            Status
                        </th>


                        <th>
                            Date
                        </th>


                    </tr>

                </thead>




                <tbody>



                <?php if(!empty($transactions)): ?>



                    <?php foreach($transactions as $transaction): ?>



                    <tr>


                        <td>

                            <?php echo esc_html(
                                $transaction->id
                            ); ?>

                        </td>




                        <td>


                            <?php

                            echo esc_html(

                                get_the_title(

                                    $transaction->candidate_id

                                )

                            );

                            ?>


                        </td>





                        <td>


                            <?php

                            echo esc_html(

                                get_the_title(

                                    $transaction->package_id

                                )

                            );

                            ?>


                        </td>






                        <td>

							<?php echo esc_html(

								$transaction->customer_name ?? ''

							); ?>

							</td>



							<td>

							<?php echo esc_html(

								$transaction->customer_email ?? ''

							); ?>

							</td>

							<td>

							<?php echo esc_html(

								$transaction->personal_message ?? ''

							); ?>

							</td>

							<td>

							<?php echo esc_html(

								$transaction->votes ?? 0

							); ?>

							</td>



							<td>

							<?php echo esc_html(

								$transaction->stripe_payment_id

							); ?>

							</td>






                        <td>

                             €<?php echo esc_html(number_format(
                                $transaction->amount,
                                2
                            )); ?>


                        </td>






                        <td>


                            <strong>

                            <?php echo esc_html(

                                ucfirst(
                                    $transaction->status
                                )

                            ); ?>


                            </strong>


                        </td>







                        <td>

                            <?php echo esc_html(

                                $transaction->created_at

                            ); ?>


                        </td>



                    </tr>




                    <?php endforeach; ?>



                <?php else: ?>


                    <tr>

                        <td colspan="10">

                            No transactions found.

                        </td>

                    </tr>



                <?php endif; ?>



                </tbody>


            </table>

            <?php if ($total_pages > 1): ?>
                <div class="tablenav bottom">
                    <div class="tablenav-pages">
                        <span class="displaying-num">
                            <?php echo sprintf(
                                esc_html__('%s items', 'miss-bulgaria-voting'),
                                number_format_i18n($total_items)
                            ); ?>
                        </span>
                        <?php
                        echo paginate_links(array(
                            'base'      => add_query_arg('paged', '%#%'),
                            'format'    => '',
                            'prev_text' => '&laquo;',
                            'next_text' => '&raquo;',
                            'total'     => $total_pages,
                            'current'   => $current_page
                        ));
                        ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>



        <?php


    }



}