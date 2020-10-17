<?php
/****************************
 *  PUMP PACKAGE SETTINGS   *
 ****************************/
/**
 * Page pump settings
 * @param array $pages
 * @return array $pages
 * @since 2.0
 * @author tatthien
 */
if(!function_exists('qa_add_package_settings')) {
    function qa_add_package_settings($pages) {
        $sections = array();
        $options = AE_Options::get_instance();

        // Pump General Settings section
        $sections[] = array(
            'args' => array(
                'title' => __( 'General', ET_DOMAIN ),
                'id' => 'et-pump-settings',
                'icon' => 'y',
                'class' => ''
            ),
            'groups' => array(
                array(
                    'args' => array(
                        'title' => __("Set up the database for pumping", ET_DOMAIN) ,
                        'id' => 'pump_setup_data',
                        'class' => 'pump_setup_data',
                        'desc' => __("The new feature Pump Question is available on QAEngine from version 2.0. Run the update <strong>only one time and you have it for forever</strong>.", ET_DOMAIN)
                    ) ,

                    'fields' => array(
                        array(
                            'id' => 'pump_setup_button',
                            'type' => 'button',
                            'title' => __("set up the database", ET_DOMAIN) ,
                            'name' => 'pump_setup_data',
                            'value' => __('Update Database', ET_DOMAIN),
                            'class' => 'gt_zero',
                        )
                    )
                ),

                array(
                    'args' => array(
                        'title' => __("Pump Action", ET_DOMAIN) ,
                        'id' => 'pump-action',
                        'class' => '',
                        'desc' => __("Enabling this feature will allow users to pump on your site.", ET_DOMAIN)
                    ) ,

                    'fields' => array(
                        array(
                            'id'        => 'pump-action-field',
                            'type'      => 'switch',
                            'title'     => __("Pump Action", ET_DOMAIN) ,
                            'name'      => 'pump_action',
                            'class'     => '',
                            'default'   => 1
                        )
                    ),
                ),

                array(
                    'args' => array(
                        'title' => __("Time delay for free pump", ET_DOMAIN) ,
                        'id' => 'free_time_delay',
                        'class' => '',
                        'desc' => __("Set up the time delay for free pump", ET_DOMAIN)
                    ) ,

                    'fields' => array(
                        array(
                            'id' => 'free_time_delay',
                            'type' => 'text',
                            'title' => __("Time delay for free pump", ET_DOMAIN) ,
                            'name' => 'free_time_delay',
                            'placeholder' => __("e.g. 60", ET_DOMAIN) ,
                            'class' => 'gt_zero',
                            'default'=> 3600
                        )
                    )
                ),

                array(
                    'args' => array(
                        'title' => __("Time delay for premium pump", ET_DOMAIN) ,
                        'id' => 'premium_time_delay',
                        'class' => '',
                        'desc' => __("Set up the time delay for premium pump", ET_DOMAIN)
                    ) ,

                    'fields' => array(
                        array(
                            'id' => 'premium_time_delay',
                            'type' => 'text',
                            'title' => __("Time delay for free pump", ET_DOMAIN) ,
                            'name' => 'premium_time_delay',
                            'placeholder' => __("e.g. 2", ET_DOMAIN) ,
                            'class' => 'gt_zero',
                            'default'=> 120
                        )
                    )
                ),

                array(
                    'args' => array(
                        'title' => __("Time format", ET_DOMAIN) ,
                        'id' => 'time_format',
                        'class' => '',
                        'desc' => __("Select the format for time delay. Default is second.", ET_DOMAIN)
                    ) ,

                    'fields' => array(
                        array(
                            'id' => 'time_delay_format',
                            'type' => 'select',
                            'title' => __("time format", ET_DOMAIN) ,
                            'name' => 'time_delay_format',
                            'class' => '',
                            'data' => array(
                                'second' => __("Second", ET_DOMAIN),
                                'minute' => __("Minute", ET_DOMAIN),
                                'hour' => __('Hour', ET_DOMAIN),
                                'day' => __('Day', ET_DOMAIN)
                            ),
                        )
                    )
                ),
            ),
        );

        //Payment setting
        $sections['payment_settings'] = array(
            'args' => array(
                'title' => __("Payment", ET_DOMAIN) ,
                'id' => 'payment-settings',
                'icon' => '%',
                'class' => ''
            ) ,

            'groups' => array(

                array(
                    'args' => array(
                        'title' => __("Payment Currency", ET_DOMAIN) ,
                        'id' => 'payment-currency',
                        'class' => 'list-package',
                        'desc' => __("Enter currency code and sign ....", ET_DOMAIN) ,
                        'name' => 'currency'
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'currency-code',
                            'type' => 'text',
                            'title' => __("Code", ET_DOMAIN) ,
                            'name' => 'code',
                            'placeholder' => __("Code", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input '
                        ) ,
                        array(
                            'id' => 'currency-sign',
                            'type' => 'text',
                            'title' => __("Sign", ET_DOMAIN) ,
                            'name' => 'icon',
                            'placeholder' => __("Sign", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input '
                        ) ,
                        array(
                            'id' => 'currency-align',
                            'type' => 'switch',
                            'title' => __("Align", ET_DOMAIN) ,
                            'name' => 'align',

                            // 'label' => __("Code", ET_DOMAIN),
                            'class' => 'option-item bg-grey-input ',
                            'label_1' => __("Left", ET_DOMAIN) ,
                            'label_2' => __("Right", ET_DOMAIN) ,
                        ) ,
                    )
                ),

                array(
                    'args' => array(
                        'title' => __("Number Format", ET_DOMAIN) ,
                        'id' => 'number-format',
                        'class' => 'list-package',
                        'desc' => __("Format a number with grouped thousands", ET_DOMAIN) ,
                        'name' => 'number_format'
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'decimal-point',
                            'type' => 'text',
                            'title' => __("Decimal point", ET_DOMAIN) ,
                            'label' => __("Decimal point", ET_DOMAIN) ,
                            'name' => 'dec_point',
                            'placeholder' => __("Decimal point", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input '
                        ),
                        array(
                            'id' => 'thousand_sep',
                            'type' => 'text',
                            'label' => __("Thousand separator", ET_DOMAIN) ,
                            'title' => __("Thousand separator", ET_DOMAIN) ,
                            'name' => 'thousand_sep',
                            'placeholder' => __("Thousand separator", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input '
                        ),
                        array(
                            'id' => 'et_decimal',
                            'type' => 'text',
                            'label' => __("Number of decimal points", ET_DOMAIN) ,
                            'title' => __("Number of decimal points", ET_DOMAIN) ,
                            'name' => 'et_decimal',
                            'placeholder' => __("Sets the number of decimal points.", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input positive_int',
                            'default' => 2
                        ),
                    )
                ),

                /* payment test mode settings */
                array(
                    'args' => array(
                        'title' => __("Payment Test Mode", ET_DOMAIN) ,
                        'id' => 'payment-test-mode',
                        'class' => 'payment-test-mode',
                        'desc' => __("Enabling this will allow you to test payment without charging your account.", ET_DOMAIN) ,

                        // 'name' => 'currency'


                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'test-mode',
                            'type' => 'switch',
                            'title' => __("Align", ET_DOMAIN) ,
                            'name' => 'test_mode',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                // payment test mode

                /* payment gateways settings */
                array(
                    'args' => array(
                        'title' => __("Payment Gateways", ET_DOMAIN) ,
                        'id' => 'payment-gateways',
                        'class' => 'payment-gateways',
                        'desc' => __("Set payment plans your users can choose when posting new project.", ET_DOMAIN) ,

                        // 'name' => 'currency'

                    ) ,
                    'fields' => array()
                ) ,

                array(
                    'args' => array(
                        'title' => __("Paypal", ET_DOMAIN) ,
                        'id' => 'Paypal',
                        'class' => 'payment-gateway',
                        'desc' => __("Enabling this will allow your users to pay through PayPal", ET_DOMAIN) ,

                        'name' => 'paypal'
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'paypal',
                            'type' => 'switch',
                            'title' => __("Align", ET_DOMAIN) ,
                            'name' => 'enable',
                            'class' => 'option-item bg-grey-input '
                        ) ,
                        array(
                            'id' => 'paypal_mode',
                            'type' => 'text',
                            'title' => __("Align", ET_DOMAIN) ,
                            'name' => 'api_username',
                            'class' => 'option-item bg-grey-input ',
                            'placeholder' => __('Enter your PayPal email address', ET_DOMAIN)
                        )
                    )
                ) ,

                array(
                    'args' => array(
                        'title' => __("2Checkout", ET_DOMAIN) ,
                        'id' => '2Checkout',
                        'class' => 'payment-gateway',
                        'desc' => __("Enabling this will allow your users to pay through 2Checkout", ET_DOMAIN) ,

                        'name' => '2checkout'
                    ) ,
                    'fields' => array(
                        array(
                            'id' => '2Checkout_mode',
                            'type' => 'switch',
                            'title' => __("2Checkout mode", ET_DOMAIN) ,
                            'name' => 'enable',
                            'class' => 'option-item bg-grey-input '
                        ) ,
                        array(
                            'id' => 'sid',
                            'type' => 'text',
                            'title' => __("Sid", ET_DOMAIN) ,
                            'name' => 'sid',
                            'class' => 'option-item bg-grey-input ',
                            'placeholder' => __('Your 2Checkout Seller ID', ET_DOMAIN)
                        ) ,
                        array(
                            'id' => 'secret_key',
                            'type' => 'text',
                            'title' => __("Secret Key", ET_DOMAIN) ,
                            'name' => 'secret_key',
                            'class' => 'option-item bg-grey-input ',
                            'placeholder' => __('Your 2Checkout Secret Key', ET_DOMAIN)
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Cash", ET_DOMAIN) ,
                        'id' => 'Cash',
                        'class' => 'payment-gateway',
                        'desc' => __("Enabling this will allow your user to send cash to your bank account.", ET_DOMAIN) ,

                        'name' => 'cash'
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'cash_message_enable',
                            'type' => 'switch',
                            'title' => __("Align", ET_DOMAIN) ,
                            'name' => 'enable',
                            'class' => 'option-item bg-grey-input '
                        ) ,
                        array(
                            'id' => 'cash_message',
                            'type' => 'editor',
                            'title' => __("Align", ET_DOMAIN) ,
                            'name' => 'cash_message',
                            'class' => 'option-item bg-grey-input ',

                            // 'placeholder' => __('Enter your PayPal email address', ET_DOMAIN)

                        )
                    )
                ) ,
                /**
                 * Pump package
                 */
                array(
                    'type' => 'list',
                    'args' => array(
                        'title' => __("Pump Packages", ET_DOMAIN) ,
                        'id' => 'list-package',
                        'class' => 'list-package',
                        'desc' => '',
                        'name' => 'pump_pack',
                        'custom_field' => 'pupm_pack'
                    ) ,

                    'fields' => array(
                        'form' => '/admin-template/pump-pack-form.php',
                        'form_js' => '/admin-template/pump-pack-form-js.php',
                        'js_template' => '/admin-template/pump-pack-js-item.php',
                        'template' => '/admin-template/pump-pack-item.php'
                    )
                ) ,
            )
        );

        // Pump Package section
//        $sections[] = array(
//            'args' => array(
//                'title' => __( 'Packages', ET_DOMAIN ),
//                'id' => 'et-pump-package-settings',
//                'icon' => 'b',
//                'class' => ''
//            ),
//            'groups' => array(
//
//            ),
//        );

        $temp = array();
        foreach ( $sections as $key => $section ) {
            $temp[] = new AE_Section( $section['args'], $section['groups'], $options );
        }

        $container = new AE_Container( array(
            'class' => 'field-settings',
            'id' => 'settings'
        ), $temp, $options );

        /**
         * order list view
         */
        $orderlist = new AE_OrderList(array());
        $pages['payments'] = array(
            'args' => array(
                'parent_slug' => 'et-overview',
                'page_title' => __('Payments', ET_DOMAIN) ,
                'menu_title' => __('PAYMENTS', ET_DOMAIN) ,
                'cap' => 'administrator',
                'slug' => 'et-payments',
                'icon' => '%',
                'desc' => __("Overview of all payments", ET_DOMAIN)
            ) ,
            'container' => $orderlist
        );

        $pages[] = array(
            'args' => array(
                'parent_slug' => 'et-overview',
                'page_title'  => __('Pump Question', ET_DOMAIN) ,
                'menu_title'  => __('PUMP QUESTION', ET_DOMAIN) ,
                'cap'  => 'administrator',
                'slug' => 'et-pump',
                'icon' => '{',
                'desc' => __("All settings for pump question", ET_DOMAIN)
            ),
            'container' => $container
        );

        return $pages;
    }
}

add_filter('ae_admin_menu_pages', 'qa_add_package_settings');