<?php
class QA_Poll_Actions extends AE_Base
{
    public static $instance;

    /**
     * Get instance
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

    public function __construct() {

    }

    /**
     * Init actions
     * @param void
     * @return void
     * @since 2.0
     * @author tatthien
     */
    public function init() {
        $this->add_ajax('qa-sync-poll', 'qa_poll_sync');
        $this->add_ajax('qa-sync-answer', 'qa_answer_sync');

        $this->add_action('pre_get_posts', 'qa_poll_filter_in_taxonomy');

        $this->add_filter('ae_convert_poll', 'qa_poll_convert');
    }

    /**
     * Sync poll: create, update...
     * @param void
     * @return void
     * @since 2.0
     * @author tatthien
     */
    public function qa_poll_sync() {
        global $current_user, $ae_post_factory;
        $request = $_REQUEST;

        // Check required fields
        if(empty($request['post_title']) || empty($request['post_content']) || empty($request['question_category']) || empty($request['poll_end_date'])) {
            wp_send_json(array(
                'success' => false,
                'msg' => __('Please fill in all required fields.', ET_DOMAIN)
            ));
        }

        // Check if user login
        if(!$current_user->ID) {
            wp_send_json(array(
                'success' => false,
                'msg' => __('Not authorized.', ET_DOMAIN)
            ));
        }

        /**
         * Verify captcha key
         * @author Tuandq
         */
        $captcha = isset($request['g-recaptcha-response']) ? $request['g-recaptcha-response'] : '';
        $is_captcha = isset($request['is_captcha']) ? $request['is_captcha'] : '';
        if(ae_get_option('gg_question_captcha', false) && !current_user_can( 'administrator' ) && $request['method'] == 'create' && ae_get_option('gg_secret_key')){           
            //check google recaptcha
            $response = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret=".ae_get_option('gg_secret_key')."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
            $response = json_decode(wp_remote_retrieve_body($response));           
            if(!$response->success){
                wp_send_json(array(
                    'success' => false,
                    'msg' => __("Please enter a valid captcha!", ET_DOMAIN)
                ));                
            }
        }
        
        // Check date format
        if (!preg_match("/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}$/", $request['poll_end_date'])) {
            wp_send_json(array(
                'success' => false,
                'msg' => __('Invalid date format. It should be dd/mm/yyyy', ET_DOMAIN)
            ));
        }

        // Check pending question
        if(ae_get_option('pending_questions')) {
            $request['post_status'] = 'pending';
        } else {
            $request['post_status'] = 'publish';
        }

        // Tax input
        if(isset($request['tax_input'])) {
            $request['tax_input']['qa_tag'] = $request['qa_tag'];
        }
        $request['tax_input'] = array(
            'question_category' => $request['question_category']
        );

        // Get answer
        $answer_count = isset($request['poll_answers']) ? count($request['poll_answers']) : 0;
        if(!isset($request['poll_answers']) || $answer_count < 2) {
            wp_send_json(array(
                'success' => false,
                'msg' => __('Please add at lease 2 answers for this poll.', ET_DOMAIN)
            ));
        }

        // Check max answers
        if($answer_count > POLL_MAX_ANSWER) {
            wp_send_json(array(
                'success' => false,
                'msg' => POLL_MAX_ANSWER_ERROR_TEXT
            ));
        }

        //Sync post
        $post = $ae_post_factory->get('poll');
        $result = $post->sync($request);

        if(!is_wp_error($result)) {
            if($request['method'] == 'create') {
                // Update pump time and new post flag for poll
                update_post_meta($result->ID, 'et_pump_time', time());
                update_post_meta($result->ID, 'et_new_post', true);
                
                wp_send_json(array(
                    'success' => true,
                    'redirect' => get_the_permalink($result->ID),
                    'ID' => $result->ID,
                    'msg' => __('You poll has been created successfully!', ET_DOMAIN)
                ));
            } else if($request['method'] == 'update') {
                //Update meta data
                if(!isset($request['poll_multi_choice']) || empty($request['poll_multi_choice'])) {
                    update_post_meta($result->ID, 'poll_multi_choice', "");
                }

                if(!isset($request['poll_multi_time']) || empty($request['poll_multi_time'])) {
                    update_post_meta($result->ID, 'poll_multi_time', "");
                }

                wp_send_json(array(
                    'success' => true,
                    'redirect' => get_the_permalink($result->ID),
                    'ID' => $result->ID,
                    'msg' => __('You poll has been saved successfully!', ET_DOMAIN)
                ));
            }
        } else {
            wp_send_json(array(
                'success' => false,
                'msg' => $result->get_error_message()
            ));
        }
    }

    /**
     * Sync poll answers
     * @param void
     * @return void
     * @since 2.0
     * @author tatthien
     */
    public function qa_answer_sync() {
        global $ae_post_factory, $current_user;
        $post = $ae_post_factory->get('poll_answer');

        $request = $_REQUEST;

        //Submit answer
        switch ($request['method']) {
            case 'submit-answer':
                // If poll question is invalid
                if(!isset($request['question_id']) || empty($request['question_id'])) {
                    wp_send_json(array(
                        'success' => false,
                        'msg' => __('Invalid poll!', ET_DOMAIN)
                    ));
                }

                // If user does not choose any answer
                if(!isset($request['answer_id']) || empty($request['answer_id'])) {
                    wp_send_json(array(
                        'success' => false,
                        'msg' => __('Please choose the answer!', ET_DOMAIN)
                    ));
                }

                // Check poll expired
                if(qa_poll_check_end_date($request['question_id'])) {
                    wp_send_json(array(
                        'success' => false,
                        'msg' => __('This poll was expired!', ET_DOMAIN)
                    ));
                }

                $answer_comment = new AE_Comments('submit_poll_answer');
                $answer_comment->limit_time = 0;

                $question_comment = new AE_Comments('submit_poll_question');
                $question_comment->limit_time = 0;

                //If check user voted by cookie
                $poll_id = $request['question_id'];
                if(POLL_CHECK_USER_VOTED == 'cookie') {
                    if(!empty($current_user->ID)) {
                        $question_comment->duplicate_error = __('You have already voted for this poll.', ET_DOMAIN);
                    } else {
                        if(isset($_COOKIE["poll_id_$poll_id"]) && $_COOKIE["poll_id_$poll_id"] == $request['question_id']) {
                            wp_send_json(array(
                                'success' => false,
                                'msg' => __('You have already voted for this poll.', ET_DOMAIN)
                            ));
                        } else if(empty($current_user->ID)) {
                            // Allow duplication comment and set cookie if user is guest
                            $answer_comment->duplicate = true;
                            $question_comment->duplicate = true;

                            //Set cookie
                            setcookie("poll_id_$poll_id", $request['question_id'], time() + (30 * DAY_IN_SECONDS), '/');
                        }
                    }
                } else {
                    // If check user voted by IP
                    if(!empty($current_user->ID)) {
                        $question_comment->duplicate_error = __('You have already voted for this poll.', ET_DOMAIN);
                    } else {
                        $question_comment->duplicate_error = __('Your IP has been used to vote.', ET_DOMAIN);
                    }
                }

                // Save comment for a question help tracking user has voted or not
                $result = $question_comment->insert(array(
                    'comment_post_ID' => $request['question_id'],
                    'comment_content' => 'vote_poll_question',
                    'comment_approved' => 1
                ));

                // If user can vote
                if(!is_wp_error($result)) {
                    // Save answer comment for poll question has not multi choice
                    if(!is_array($request['answer_id'])) {
                        $this->qa_qoll_insert_answer_comment($answer_comment, $request['answer_id']);
                    } else {
                        // Save multi choices answer
                        foreach ($request['answer_id'] as $answer_id) {
                            $this->qa_qoll_insert_answer_comment($answer_comment, $answer_id);
                        }
                    }

                    // Response success messages
                    $percents = $this->qa_poll_get_answer_percent_vote($request['question_id']);
                    $total_votes = qa_poll_get_total_vote($request['question_id']);
                    wp_send_json(array(
                        'success' => true,
                        'data' => array(
                            'percents' => $percents,
                            'total_votes' => $total_votes
                        ),
                        'msg' => __('Your answer has been voted.', ET_DOMAIN)
                    ));
                } else {
                    // Response fail messages
                    $percents = $this->qa_poll_get_answer_percent_vote($request['question_id']);
                    $total_votes = qa_poll_get_total_vote($request['question_id']);
                    wp_send_json(array(
                        'success' => false,
                        'data' => array(
                            'percent' => $percents,
                            'total_votes' => $total_votes
                        ),
                        'msg' => $result->get_error_message()
                    ));
                }
                break;

            default:
                // Default methods are: create, update, remove
                $result = $post->sync($request);

                if(!is_wp_error($result)) {
                    wp_send_json(array(
                        'success' => true,
                        'data' => $result
                    ));
                } else {
                    wp_send_json(array(
                        'success' => false
                    ));
                }
                break;
        }
    }

    /**
     * Convert poll object
     * @param object $result
     * @return object $result
     * @since 2.0
     * @author tatthien
     */
    public function qa_poll_convert($result) {
        $result->qa_tag = wp_get_object_terms($result->ID, 'qa_tag');
        return $result;
    }

    /**
     * Poll filter for taxonomy archive page
     * @param $query
     * @return $query
     * @since 2.0
     * @author tatthien
     */
    public function qa_poll_filter_in_taxonomy($query) {
        if(!is_admin() && $query->is_main_query() && ($query->is_tax('question_category') || $query->is_tax('qa_tag'))) {
            $post_type = qa_poll_filter();
            $query->set('post_type', $post_type);
        }
        return $query;
    }

    /**
     * Insert comment as vote for answer and update vote meta
     * @param object $answer_comment
     * @param int $answer_id
     * @return type
     */
    public function qa_qoll_insert_answer_comment($answer_comment, $answer_id) {
        $answer_vote = $answer_comment->insert(array(
            'comment_post_ID' => $answer_id,
            'comment_content' => 'vote_poll_answer',
            'comment_approved' => 1
        ));

        if(!is_wp_error($answer_vote)) {
            //Update answer votes
            $answer_vote_count = (int)get_post_meta($answer_id, 'poll_answer_vote', true);
            update_post_meta($answer_id, 'poll_answer_vote', ++$answer_vote_count);
            //Add count votes to question
            $question_id = wp_get_post_parent_id($answer_id);
            if($question_id){
                $et_answers_count = (int)get_post_meta($question_id, 'et_answers_count', true);
                if($et_answers_count)
                    update_post_meta($question_id, 'et_answers_count', ++$et_answers_count);
                else
                    add_post_meta($question_id, 'et_answers_count', 1);
            }
        }
    }

    /**
     * Get percent of all anwsers
     * @param int $poll_id
     * @return array $percents
     * @since 2.0
     * @author tatthien
     */
    public function qa_poll_get_answer_percent_vote($poll_id) {
        // Get all answers by poll ID
        $answers = qa_get_poll_answers($poll_id);

        // Sort the answers by votes
        $answers = qa_poll_sort_answers($answers);

        // Get total votes of a poll question
        $total_votes = qa_poll_get_total_vote($poll_id);

        // Store percent vote of each answer to array
        $percents = array();
        foreach($answers as $answer) {
            $percents[] = array(
                'answer_item' => "percent-item-$answer->ID",
                'value' => round((qa_poll_get_answer_vote($answer->ID) / $total_votes * 100))
            );
        }

        return $percents;
    }
}
