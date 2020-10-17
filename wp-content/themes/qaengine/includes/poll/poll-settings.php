<?php
/**
 * Poll settings
 * @since 2.0
 * @author tatthien
 */
if(!function_exists('qa_add_poll_settings')) {
    function qa_add_poll_settings($pages) {
        $sessions = array();
        $options = AE_Options::get_instance();

        // Create sections
        $sections['poll_general'] = array(
            'args' => array(
                'title' => __( 'General', ET_DOMAIN ),
                'id' => 'et-pump-settings',
                'icon' => 'y',
                'class' => ''
            ),

            'groups' => array(
                array(
                    'args' => array(
                        'title' => __("Poll Maker", ET_DOMAIN) ,
                        'id' => 'poll-maker',
                        'class' => '',
                        'desc' => __("Enabling this feature will allow users to create polls on your site.", ET_DOMAIN)
                    ) ,

                    'fields' => array(
                        array(
                            'id'        => 'poll-maker-field',
                            'type'      => 'switch',
                            'title'     => __("Poll Maker", ET_DOMAIN) ,
                            'name'      => 'poll_maker',
                            'class'     => '',
                            'default'   => 1
                        )
                    ),
                ),

                array(
                    'args' => array(
                        'title' => __("Maximum poll answers", ET_DOMAIN) ,
                        'id' => 'poll_max_answer',
                        'class' => '',
                        'desc' => __("Set maximum number of anwers for a poll. Default is 5", ET_DOMAIN)
                    ) ,

                    'fields' => array(
                        array(
                            'id' => 'poll_max_answer',
                            'type' => 'text',
                            'title' => __("Max answer number of a poll", ET_DOMAIN) ,
                            'name' => 'poll_max_answer',
                            'placeholder' => __("e.g. 5", ET_DOMAIN) ,
                            'class' => 'gt_zero',
                            'default'=> 5
                        )
                    )
                ),

                array(
                    'args' => array(
                        'title' => __('Check user voted by'),
                        'id' => 'poll_checked_user_voted',
                        'class' => '',
                        'desc' => __("Check user who has already voted by IP or Cookie. Default is IP", ET_DOMAIN)
                    ),

                    'fields' => array(
                        array(
                            'id' => 'poll_checked_user_voted',
                            'type' => 'select',
                            'title' => __("user voted", ET_DOMAIN) ,
                            'name' => 'poll_checked_user_voted',
                            'class' => '',
                            'data' => array(
                                'ip' => 'IP',
                                'cookie' => 'Cookie',
                            )
                        )
                    )
                ),

                array(
                    'args' => array(
                        'title' => __('Select chart', ET_DOMAIN),
                        'id' => 'poll_chart_type',
                        'class' => '',
                        'desc' => __("Select the chart that you want to show the result of poll answers", ET_DOMAIN)
                    ),

                    'fields' => array(
                        array(
                            'id' => 'poll_chart_type',
                            'type' => 'radio',
                            'title' => __("chart type", ET_DOMAIN) ,
                            'name' => 'poll_chart_type',
                            'class' => '',
                            'data' => array(
                                'donut_chart' => '<img src="'. get_template_directory_uri(). '/img/type_1.jpg"/>',
                                'column_chart' => '<img src="'. get_template_directory_uri(). '/img/type_2.jpg"/>',
                                'pie_chart' => '<img src="'. get_template_directory_uri(). '/img/type_3.jpg"/>',
                                'bar_chart' => '<img src="'. get_template_directory_uri(). '/img/type_4.jpg"/>'
                            )
                        )
                    )
                )
            )
        );

        // Create page
        $temp = array();
        foreach ( $sections as $key => $section ) {
            $temp[] = new AE_Section( $section['args'], $section['groups'], $options );
        }

        $container = new AE_Container( array(
            'class' => 'field-settings',
            'id' => 'settings'
        ), $temp, $options );

        $pages[] = array(
            'args' => array(
                'parent_slug' => 'et-overview',
                'page_title'  => __('Poll', ET_DOMAIN) ,
                'menu_title'  => __('POLL', ET_DOMAIN) ,
                'cap'  => 'administrator',
                'slug' => 'et-poll',
                'icon' => '2',
                'desc' => __("All settings for poll", ET_DOMAIN)
            ),
            'container' => $container
        );

        return $pages;
    }

    add_filter('ae_admin_menu_pages', 'qa_add_poll_settings');
}