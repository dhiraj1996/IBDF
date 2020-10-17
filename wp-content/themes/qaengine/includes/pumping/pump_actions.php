<?php
/****************************
 *       PUMP ACTIONS       *
 ****************************/
class QA_Pump_Actions extends AE_Base
{
    public static $instance;

    /**
     * Get instance method
     * @param void
     * @return object $instance
     * @since 2.0
     * @author tatthien
     */
    public static function get_instance() {
        if(!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Init method
     * @param void
     * @return void
     * @since 2.0
     * @author tatthien
     */
    public function init() {
        $this->add_ajax('qa_pump_sync', 'qa_pump_sync');

        //$this->add_action('pre_get_posts', 'qa_pump_reset_time');
        $this->add_action('pre_get_posts', 'qa_pump_orderby_time', 9, 1);
        $this->add_action('ae_select_process_payment', 'qa_setup_pump_package_for_user', 10, 2);
        $this->add_action('save_post', 'qa_cash_approve', 10, 2);

        //Pump filter
        $this->add_filter('posts_orderby', 'qa_order_pump_pack_by_menu_order', 10, 2);
        $this->add_filter('question_meta_fields', 'qa_add_pump_meta');
    }

    /**
     * Function handle all actions of pumping
     */
    public function qa_pump_sync() {
        $request = $_REQUEST;
        switch($request['method']) {
            case 'pumping':
                $this->qa_pump_question($request);
                break;
            case 'setup':
                $this->qa_setup_pump();
        }
    }

    /**
     * Pump a question
     * @param array $request
     * @return void
     * @since 2.0
     * @author tatthien
     */
    public function qa_pump_question($request) {
        global $current_user;
        $question = get_post($request['id']);
        if(!is_wp_error($question)) {
            $author = $question->post_author;

            if($author == $current_user->ID) {
                //Get current timestamp
                $timestamp = time();

                //Get question timestamp
                $item_pump_time = get_post_meta($question->ID, 'et_pump_time', true);
                $is_new_post = get_post_meta($question->ID, 'et_new_post', true);

                //Get user pump number
                $user_pump_number = get_user_meta($author, 'et_pump_number', true);

                //Set time delay depend on user pump number
                $time_delay = !empty($user_pump_number) ? PREMIUM_TIME_DELAY : FREE_TIME_DELAY;
                $time_delay_type = !empty($user_pump_number) ? 'premium' : 'free';

                //Convert time delay to second and countdown format
                $data = qa_pump_data_convert($time_delay);

                $item_pump_time = $item_pump_time + $data['second_convert'];

                // Check if question can pump or not
                if(empty($item_pump_time) || $timestamp >= $item_pump_time || $is_new_post) {
                    //Update question timestamp with current timestamp
                    update_post_meta($question->ID, 'et_pump_time', $timestamp);
                    update_post_meta($question->ID, 'et_pump_type', $time_delay_type);
                    update_post_meta($question->ID, 'et_new_post', false);

                    // Decrease user pump number by 1
                    if(!empty($user_pump_number)) {
                        update_user_meta($author, 'et_pump_number', --$user_pump_number);

                        if($user_pump_number == 0) {
                            $posts = get_posts(array(
                                'post_type' => array('question', 'poll'),
                                'posts_per_page' => -1,
                                'author' => $author,
                                'meta_key' => 'et_pump_type',
                                'meta_value' => 'premium'
                            ));

                            $premium_data = qa_pump_data_convert(PREMIUM_TIME_DELAY);
                            $free_data = qa_pump_data_convert(FREE_TIME_DELAY);

                            foreach($posts as $post) {
                                $pump_time = get_post_meta($post->ID, 'et_pump_time', true);
                                $pump_time = $pump_time - $premium_data['second_convert'] - $free_data['second_convert'];
                                update_post_meta($post->ID, 'et_pump_time', $pump_time);
                            }
                        }
                    }

                    wp_send_json(array(
                        'data' => array(
                            'pump_number' => $user_pump_number,
                            'countdown_time' => $data['second_convert'],
                            'countdown_format' => $data['countdown_format']
                        ),
                        'success' => true,
                        'msg' => __('Your question has been pumped.', ET_DOMAIN)
                    ));
                } else {
                    $remain_time = abs($item_pump_time - $timestamp); //(second)
                    wp_send_json(array(
                        'success' => false,
                        'remain_time' => $remain_time,
                        'msg' => sprintf(__('Please wait in %s', ET_DOMAIN), gmdate($data['countdown_format'], $remain_time))
                    ));
                }
            } else {
                wp_send_json(array(
                    'success' => false,
                    'msg' => __('You are not the author of this question.', ET_DOMAIN)
                ));
            }
        } else {
            wp_send_json(array(
                'success' => false,
                'msg' => __('Post not found!', ET_DOMAIN)
            ));
        }
    }

    /**
     * Add pump meta for each question after active theme
     * @param void
     * @return void
     * @since 2.0
     * @author tatthien
     */
    function qa_setup_pump() {
        global $current_user;
        $role = get_user_role($current_user->ID);

        if( current_user_can( 'manage_options' ) ){
            $posts = get_posts(array(
                'post_type' => 'question',
                'posts_per_page' => -1
            ));

            // Update question with new database
            foreach($posts as $post) {
                $pump_time = get_post_meta($post->ID, 'et_pump_time', true);
                if(empty($pump_time)) {
                    update_post_meta($post->ID, 'et_pump_time', 0 );
                }
            }

            // Update badge
            $qa_badges = get_posts(array(
                'post_type' => 'pack',
                'posts_per_page' => -1
            ));

            $data = array();
            foreach ($qa_badges as $badge) {
                $post = wp_update_post(array(
                    'ID' => $badge->ID,
                    'post_type' => 'qa_level'
                ));

                $data[] = $post;
            }
            update_option('qa_level', $data);

            // Mark updated
            update_option('qa_pump_is_update', true);

            wp_send_json(array(
                'success' => true,
                'msg' => __('Finish your update.', ET_DOMAIN)
            ));
        } else {
            wp_send_json(array(
                'success' => false,
                'msg' => __('Only administrator can run the update.', ET_DOMAIN)
            ));
        }
    }

    /**
     * Allowing sort pump package in settings
     * @param string $orderby
     * @param object $query
     * @return string $orderby
     * @since 2.0
     * @author tatthien
     */
    public function qa_order_pump_pack_by_menu_order($orderby, $query) {
        global $wpdb;
        if ($query->query_vars['post_type'] != 'pump_pack') return $orderby;
        $orderby = "{$wpdb->posts}.menu_order ASC";
        return $orderby;
    }

    /**
     * Add meta for pump action
     * 'et_pump_time'
     * @param array $meta
     * @return array $meta
     * @since 2.0
     * @author tatthien
     */
    public function qa_add_pump_meta($meta) {
        $meta = wp_parse_args($meta, array('et_pump_time'));
        return $meta;
    }

    /**
     * Reset pump time
     * @param $query
     * @return $query
     * @since 2.0
     * @author tatthien
     */
    public function qa_pump_reset_time($query) {
        if(!is_admin() && isset($query->query_vars['post_type']) && $query->query_vars['post_type'] == 'question') {
            global $wpdb;
            $sql = "
                    SELECT * FROM $wpdb->posts AS p
                    INNER JOIN $wpdb->postmeta pm ON pm.post_id = p.ID
                    WHERE pm.meta_key = 'et_pump_time' AND pm.meta_value != 0
                ";
            $posts = $wpdb->get_results($sql);

            foreach($posts as $post) {
                $item_pump_time = (int)get_post_meta($post->ID, 'et_pump_time', true);
            }
        }
        return $query;
    }

    /**
     * Order question list by pump time and date
     * @param object $query
     * @return object $query
     * @since 2.0
     * @author tatthien
     */
    public function qa_pump_orderby_time($query) {
        if(!is_admin() &&  isset($query->query_vars['post_type'])) {
            $post_type = $query->query_vars['post_type'];
            if($post_type == 'question' || $post_type == 'poll' || is_array($post_type)) {
                $meta_query = array(
                    'relation' => 'OR',
                    'second_key' => array(
                        'key' => 'et_pump_time',
                        'type'    => 'NUMERIC',
                        'compare' => 'EXISTS'
                    )
                );
                if(isset($_REQUEST['sort']) && $_REQUEST['sort'] == 'unanswer'){
                    $meta_query = array(
                        'relation' => 'OR',
                        array(
                            'key'     => 'et_answers_count',
                            'compare' => 'NOT EXISTS'
                        ),
                        array(
                            'key'     => 'et_answers_count',
                            'value' => '0'
                        )
                    );
                }
                
                $query->set('meta_query', $meta_query);
                $query->set('orderby', array(
                    'second_key' => 'DESC',
                    'date' => 'DESC',
                ));
                $query->set('meta_key', 'et_pump_time');
            }
        }

        return $query;
    }

    /**
     * Update pump package for user after process payment
     * @param object $payment_return
     * @param array $data
     * @since 2.0
     * @author tatthien
     */
    public function qa_setup_pump_package_for_user($payment_return, $data) {
        if(!$payment_return['ACK']) return false;
        if(isset($payment_return['payment_status'])) {
            if($payment_return['payment_status'] == 'Completed' && $payment_return['payment'] != 'cash') {
                global $user_ID;
                $sku = $data['order']->payment_plan;
                $pack = qa_get_package_by_sku($sku);
                if($user_ID && isset($pack->et_pump_number) && (int)$pack->et_pump_number > 0) {
                    $this->qa_update_pump_number($pack->et_pump_number, $user_ID);
                } else {
                    return false;
                }
            }
        }
        return $payment_return;
    }

    /**
     * Update pump number
     * @param string|int $number
     * @param int $user_id
     * @return void;
     * @since 2.0
     * @author tatthien
     */
    public function qa_update_pump_number($number, $user_id) {
        $user_pump_number = get_user_meta($user_id, 'et_pump_number', true);
        if(!empty($number)) {
            $user_pump_number = (int)$user_pump_number + (int)$number;
            update_user_meta($user_id, 'et_pump_number', $user_pump_number);
        } else {
            update_user_meta($user_id, 'et_pump_number', $number);
        }
    }

    /**
     * Update user pump number when approve cash
     * @param int $post_id
     * @param object $post
     * @return void
     * @since 2.0
     * @author tatthien
     */
    public function qa_cash_approve($post_id, $post) {
        if(current_user_can('manage_options')) {
            if($post->post_type == 'order' && $post->post_status == 'publish' ) {
                $order = new AE_Order($post_id);
                $order_pay = $order->get_order_data();
                if(isset($order_pay['payment']) && $order_pay['payment'] == 'cash') {
                    $sku = $order_pay['payment_package'];
                    $pack = qa_get_package_by_sku($sku);
                    if($pack->et_pump_number && (int)$pack->et_pump_number > 0) {
                        $this->qa_update_pump_number($pack->et_pump_number, $post->post_author);
                    } else {
                        return false;
                    }
                }
            }
        }
    }
}