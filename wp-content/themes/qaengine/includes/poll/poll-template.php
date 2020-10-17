<?php
if(!function_exists('qa_poll_tag_template')) {
    /**
     * Re-define poll tag template in order to use witt AE_Posts
     * @param void
     * @return void
     * @since 2.0
     * @author tatthien
     */
    function qa_poll_tag_template() {
        ?>
        <script type="text/template" id="poll_tag_item">

            <input type="hidden" name="qa_tag[][name]" value="{{= stripHTML(name) }}" />
            {{= stripHTML(name) }} <a href="javascript:void(0)" class="delete"><i class="fa fa-times"></i></a>

        </script>
        <script type="text/javascript">
            function stripHTML(html)
            {
                var tmp = document.createElement("DIV");
                tmp.innerHTML = html;
                return tmp.textContent||tmp.innerText;
            }
        </script>
        <script type="text/template" id="edit_poll_answer_item">
            <input type="text" class="input-answer" placeholder="{{= placeholder }}" name="poll_answers[]" value="{{= post_title }}">
            <input type="hidden" class="answer-color-picker" value="#e6e6e6">
            <div class="function-right">
                <span class="color-box" style="background: {{= poll_answer_color }}"></span>
                <span class="remove-box"><i class="fa fa-trash"></i></span>
            </div>
        </script>
        <?php
    }

    add_action('wp_footer', 'qa_poll_tag_template');
}

if(!function_exists('qa_poll_multi_choice_answer_render')) {
    /**
     * Render the poll answers that user can choose many answers on a time
     * @param int $poll_id
     * @param string $is_multi_choice
     * @return void
     * @since 2.0
     * @author tatthien
     */
    function qa_poll_answer_render($poll_id, $is_multi_choice) {
        $answers = get_posts(array(
            'post_type' => 'poll_answer',
            'posts_per_page' => -1,
            'post_parent' => $poll_id,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'ASC',
        ));

        echo '<form class="form-option-answer">';
        if($is_multi_choice) {
            foreach($answers as $answer) {
                ?>
                <div class="checkbox clearfix">
                    <label>
                        <div class="check-box-checkbox"><input type="checkbox" class="answer-selected" name="answer_id[]" value="<?php echo $answer->ID ?>"></div>
                        <span class="content-answer"><?php echo $answer->post_title; ?></span>
                    </label>
                </div>
                <?php
            }
        } else {
            foreach($answers as $answer) {
                ?>
                <div class="radio clearfix">
                    <label>
                        <div class="check-box-checkbox"><input type="radio" name="answer_id" class="answer-selected" value="<?php echo $answer->ID ?>"></div>
                        <span class="content-answer"><?php echo $answer->post_title; ?></span>
                    </label>
                </div>
                <?php
            }
        }

        ?>
        <div class="buttons">
            <button class="btn-submit-answer"><?php _e("Submit answer",ET_DOMAIN) ?></button>
            <span class="items-text">
                <?php
                    printf( __('By submit your answer, you agree to the <a target="_blank" href="%s">privacy policy</a> and <a target="_blank" href="%s">terms of service.</a>', ET_DOMAIN), et_get_page_link('privacy'), et_get_page_link('term') );
                ?>
            </a></span>
        </div>
        <?php
        echo '</form>';
    }
}

if(!function_exists('qa_poll_submit_form')) {
    /**
     * Render the poll submit form for mobile
     * @param void
     * @return void
     * @since 2.0
     * @author tatthien
     */
    function qa_poll_submit_form() {
        global $current_user;
        $role = get_user_role($current_user->ID);
        $privi  =   qa_get_privileges();
        ?>
        <div class="body-poll hide" data-name="poll">
        <form id="submit_poll" class="form_submit_poll">
            <!-- Poll title -->
            <div class="form-post">
                <input type="text" name="post_title" id="poll_question_title" placeholder="<?php _e("Your Question", ET_DOMAIN) ?>">
                <span id="charNumPoll"><?php echo ae_get_option('max_width_title', 150);?></span>
            </div>

            <!-- Poll category  -->
            <div class="form-post">
                <div class="select-categories-wrapper">
                    <div class="select-categories">
                        <select class="select-grey-bg chosen-select" id="question_category" name="question_category">
                            <option value=""><?php _e("Select Category",ET_DOMAIN) ?></option>
                            <?php
                            $terms = get_terms( 'question_category', array('hide_empty' => 0, 'orderby'    => 'term_group') );
                            $value_type = 'id';
                            foreach ($terms as $term) {
                                if($term->parent == 0){
                                    if($value_type == 'slug') {
                                        echo "<option value='".$term->slug."'>";
                                    } elseif($value_type == 'id') {
                                        echo "<option value='".$term->term_id."'>";
                                    }
                                    echo $term->name;
                                    echo "</option>";
                                    foreach ($terms as $value) {
                                        if($term->term_id == $value->parent){
                                            if($value_type == 'slug') {
                                                echo "<option value='".$value->slug."'>";
                                            } elseif($value_type == 'id') {
                                                echo "<option value='".$value->term_id."'>";
                                            }
                                                echo "--".$value->name;
                                            echo "</option>";
                                        }
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <?php if(ae_get_option('ae_upload_images')){ ?>
                <div class="form-post container_upload">
                </div>
            <?php } ?>
            <!-- Poll content -->
            <div class="form-post">
                <textarea name="post_content" id="insert_poll" cols="30" rows="10" placeholder="<?php _e("Your Description", ET_DOMAIN) ?>"></textarea>
            </div>

            <!-- Poll answers -->
            <div class="answer">
                <ul id="answer_list_poll_edit">
                    <li class="item_poll_answer_edit">
                        <input type="text" class="input-answer" placeholder="<?php _e('Your answer', ET_DOMAIN); ?>" name="poll_answers[]">
                        <input type="hidden" class="answer-color-picker" value="#e6e6e6">
                        <div class="function-right"><span class="color-box"></span></div>
                    </li>
                </ul>
            </div>
            <div class="btn-more-anwser">
                <span class="btn-add-more"><i class="fa fa-plus"></i><?php _e('More answer', ET_DOMAIN); ?></span>
                <span><?php printf(__('You can create %s answer(s).', ET_DOMAIN), POLL_MAX_ANSWER); ?></span>
            </div>

            <!-- Add tags -->
            <div class="form-post">
                <input  data-provide="typeahead" type="text" name="" id="poll_question_tags" placeholder="<?php _e('Tag(max 5 tags)',ET_DOMAIN) ?>" />
            </div>
            <ul class="post-question-tags" id="poll_tag_list"></ul>

            <!-- Date picker -->
            <div class="chose-date">
                <div class='input-group date' id='datetimepicker5'>
                    <input type='text' name="poll_end_date" class="form-control form-group" placeholder="<?php _e('End day', ET_DOMAIN); ?>" />
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                </div>
            </div>

            <div class="chose-multi">
                <!-- Check multi time -->
                <!-- <div class="form-group">
                    <div class="checkbox">
                        <input type="checkbox" id="time" /><label for="time"><?php _e('Multi time', ET_DOMAIN); ?></label>
                    </div>
                </div> -->

                <!-- Check multi choice -->
                <div class="form-group">
                    <div class="checkbox">
                        <div class="checkbox">
                            <input type="checkbox" id="choice" /><label for="choice"><?php _e('Multi choice', ET_DOMAIN); ?></label>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Google capcha -->
            <?php if(ae_get_option('gg_question_captcha') && $role != 'administrator'){ ?>
                <div class="clearfix"></div>
                <div class="container_captcha"> 
                    <div class="gg-captcha form-post">
                        <?php //ae_gg_recaptcha(); ?>
                    </div>
                </div>
            <?php } ?>
            <!-- Submit and cancel button -->
            <div class="group-btn-post">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-5 no-pad-left"><span class="text"><?php _e("Ask a Poll", ET_DOMAIN) ?></span></div>
                        <div class="col-xs-7 text-right">
                            <button type="submit" id="btn_submit_poll" class="submit-post-question"><?php _e("Submit", ET_DOMAIN) ?></button>
                            <a href="javascript:void(0)" class="cancel-post-question"><?php _e("Cancel", ET_DOMAIN) ?></a>
                        </div>
                    </div>
                </div>
            </div>
            <input id="add_poll_tag_text" type="hidden" value="<?php printf(__("You must have %d points to add tag. Current, you have to select existing tags.", ET_DOMAIN), $privi->create_tag  ); ?>" />
        </form>
        <?php
    }
}

/**
 * Render the result of poll answers
 * @param $poll_id
 * @return void
 * @since 2.0
 * @author tatthien
 */
function qa_poll_render_answer_result($poll_id) {
    // Get all answers by poll ID
    $answers = qa_get_poll_answers($poll_id);

    // Sort the answers by votes
    $answers = qa_poll_sort_answers($answers);

    // Get total votes of a poll question
    $total_votes = qa_poll_get_total_vote($poll_id);

    // Render to HTML
    foreach($answers as $answer) {
        ?>
            <div class="content-answer" id="<?php echo 'percent-item-' . $answer->ID ?>">
                <div class="box-color-vote" style="background: <?php echo $answer->poll_answer_color; ?>"></div>
                <span class="percent-number"style="color: <?php echo $answer->poll_answer_color; ?>">
                    <?php
                        if(qa_poll_get_answer_vote($answer->ID) != 0) {
                            echo round((qa_poll_get_answer_vote($answer->ID) / $total_votes * 100)) . '%';
                        } else {
                            echo '0%';
                        }

                    ?>
                </span>
                <span class="question-poll"><?php echo $answer->post_title; ?></span>
            </div>
        <?php
    }
}


if(!function_exists('qa_poll_comment_form')) {
    /**
     * Render comment form for single poll
     * @param object $post
     * @param string $type    Default is poll
     * @return  void
     * @since 2.0
     * @author tatthien
     */
    function qa_poll_comment_form($post, $type = 'poll') {
        global $current_user;
        ?>
        <form class="poll-comment" method="POST">
            <input type="hidden" name="qa_nonce"        value="<?php echo wp_create_nonce( 'insert_comment' );?>" />
            <input type="hidden" name="comment_post_ID" value="<?php echo $post->ID ?>" />
            <input type="hidden" name="comment_type"    value="<?php echo $type ?>" />
            <input type="hidden" name="user_id"         value="<?php echo $current_user->ID ?>" />
            <div id="editor_wrap" class="child-answer-wrap">
                <div class="wp-editor-container">
                    <textarea name="post_content" class="collapse" id="poll_comment_editor"></textarea>
                </div>
                <div class="row submit-wrapper">
                    <div class="col-md-2 col-xs-2">
                        <button id="submit_reply" class="btn-submit">
                            <?php _e("Add comment",ET_DOMAIN) ?>
                        </button>
                    </div>
                </div>
            </div>
        </form><!-- END SUBMIT FORM COMMENT -->
        <?php
    }
}