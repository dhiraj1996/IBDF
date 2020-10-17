<?php
/**
 * Template: QUESTIONS LISTING
 * version 1.0
 * @author: ThaiNT
 **/
et_get_mobile_header();
global $post,$wp_rewrite,$current_user, $qa_question, $ae_post_factory;
the_post();

// Get poll
$poll = $ae_post_factory->get('poll');
$poll = $poll->convert($post);

$question        = QA_Questions::convert($post);
$et_post_date    = et_the_time(strtotime($question->post_date));
$category        = !empty($question->question_category[0]) ? $question->question_category[0]->name : __('No Category',ET_DOMAIN);
$category_link   = !empty($question->question_category[0]) ? get_term_link( $question->question_category[0]->term_id, 'question_category' ) : '#';
$qa_question    =   $question;

$vote_up_class  =  'action vote vote-up ' ;
$vote_up_class  .= ($question->voted_up) ? 'active' : '';
$vote_up_class  .= ($question->voted_down) ? 'disabled' : '';

$vote_down_class = 'action vote vote-down ';
$vote_down_class .= ($question->voted_down) ? 'active' : '';
$vote_down_class .= ($question->voted_up) ? 'disabled' : '';

$parent_comments    = get_comments( array(
    'post_id'       => $post->ID,
    'parent'        => 0,
    'status'        => 'approve',
    'post_status'   => 'publish',
    'order'         => 'ASC',
    'type'          => 'comment'
) );
?>
    <!-- CONTAINER -->
    <div class="wrapper-mobile">
        <!-- CONTENT QUESTION -->
        <section class="list-question-wrapper" id="question_content">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="content-qna-wrapper">
                            <div class="avatar-user">
                                <a href="<?php echo get_author_posts_url( $question->post_author ); ?>">
                                    <?php echo et_get_avatar($question->post_author, 55) ?>
                                </a>
                            </div>
                            <div class="info-user">
                                <!-- <span title="1" class="user-badge">Newbie</span> -->
                                <?php qa_user_badge($question->post_author, true, true) ?>
                            </div>
                            <div class="content-question">
                                <h2 class="title-question">
                                    <a href="javascript:void(0)"><?php the_title() ?></a>
                                </h2>
                                <div class="details">
                                    <?php the_content(); ?>
                                </div>
                                <div class="info-tag-time">
                                    <ul class="list-tag">
                                        <?php
                                        foreach ($question->qa_tag as $tag) {
                                            ?>
                                            <li>
                                                <a href="<?php echo get_term_link($tag->term_id, 'qa_tag'); ?> ">
                                                    <?php echo $tag->name; ?>
                                                </a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                            	<span class="time-categories">
                                    <?php
                                    $author = '<a href="'.get_author_posts_url( $question->post_author ).'">'.$question->author_name.'</a>';
                                    printf( __( 'Asked by %s %s in', ET_DOMAIN ), $author, $et_post_date );
                                    ?>
                                    <a href="<?php echo $category_link ?>"><?php echo $category ?></a>.
                                </span>
                                </div>
                                <div class="vote-wrapper">

                                    <a href="javascript:void(0)" data-name="vote_up" class="<?php echo $vote_up_class ?>">
                                        <i class="fa fa-angle-up"></i>
                                    </a>

                                    <span class="number-vote"><?php echo qa_format_number($question->et_vote_count); ?></span>

                                    <a href="javascript:void(0)" data-name="vote_down" class="<?php echo $vote_down_class ?>">
                                        <i class="fa fa-angle-down"></i>
                                    </a>

                                    <?php
                                        $user_following = explode(',', $qa_question->et_users_follow);
                                        $is_followed    = in_array($current_user->ID, $user_following);

                                        if(!$is_followed) {
                                            ?>
                                            <a href="javascript:void(0)" class="btn-follow-question mobile-follow-question"><i class="fa fa-plus-square"></i><?php _e('Follow', ET_DOMAIN); ?></a>
                                            <a href="javascript:void(0)" class="btn-follow-question mobile-unfollow-question" style="display: none"><i class="fa fa-minus-square"></i><?php _e('Unfollow', ET_DOMAIN); ?></a>
                                            <?php
                                        } else {
                                            ?>
                                            <a href="javascript:void(0)" class="btn-follow-question mobile-unfollow-question"><i class="fa fa-minus-square"></i><?php _e('Unfollow', ET_DOMAIN); ?></a>
                                            <a href="javascript:void(0)" class="btn-follow-question mobile-follow-question" style="display: none"><i class="fa fa-plus-square"></i><?php _e('Follow', ET_DOMAIN); ?></a>
                                            <?php
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <!-- SHARE -->
                        <div class="share">
                            <ul class="list-share">
                                <li>
                                    <a class="share-social" href="javascript:void(0)" rel="popover" data-container="body" data-content='<?php echo qa_template_share($question->ID); ?>' data-html="true">
                                        <?php _e("Share",ET_DOMAIN) ?> <i class="fa fa-share"></i>
                                    </a>
                                </li>
                                <!-- <li class="collapse">
                                <a href="javascript:void(0)"><?php _e("Report", ET_DOMAIN) ?><i class="fa fa-flag"></i></a>
                            </li> -->
                                <li>
                                    <a href="#comments" class="">
                                        <?php _e("Comment", ET_DOMAIN) ?>(<?php echo count($parent_comments) ?>)&nbsp;<i class="fa fa-comment"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- SHARE / END -->
                        <!-- COMMENT IN COMMENT -->
                        <div class="cmt-in-cmt-wrapper">
                            <ul class="mobile-comments-list">
                                <?php
                                /**
                                 * render comment loop
                                 */
                                if(!empty($parent_comments)){
                                    foreach ($parent_comments as $comment) {
                                        qa_mobile_comments_loop( $comment );
                                    }
                                }
                                ?>
                            </ul>
                            <?php qa_mobile_comment_form($post) ?>
                            <a href="javascript:void(0)" class="add-cmt-in-cmt"><?php _e("Add comment", ET_DOMAIN) ?></a>
                        </div>
                        <!-- COMMENT IN COMMENT / END -->
                    </div>
                </div>
            </div>
        </section>
        <!-- CONTENT QUESTION / END -->

        <!-- LABEL -->
        <!-- Poll answer -->
        <div class="label-vote-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                            // Check if user has voted or not
                            $is_voted = qa_poll_check_user_vote($poll->ID);
                        ?>
                        <div class="multi-choice">
                            <ul>
                                <ul>
                                    <li class="vote-link"><span class="<?php echo $is_voted ? '' : 'active'; ?>"><?php _e('Vote', ET_DOMAIN); ?></span></li>
                                    <li class="vote-result"><span class="<?php echo !$is_voted ? '' : 'active'; ?>"><?php _e('Result', ET_DOMAIN); ?></span></li>
                                </ul>
                            </ul>
                        </div>
                        <?php
                        if(isset($poll->poll_multi_choice) && $poll->poll_multi_choice) {
                            echo '<span>'.__('Select multiple answer', ET_DOMAIN).'</span>';
                        } else {
                            echo '<span>'.__('Select one answer', ET_DOMAIN).'</span>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="container select-answer">
            <div class="vote-answer <?php echo $is_voted ? 'hide' : ''; ?>">
                <div class="option-answer">
                    <?php
                    if(isset($poll->poll_multi_choice) && $poll->poll_multi_choice) {
                        qa_poll_answer_render($poll->ID, true);
                    } else {
                        qa_poll_answer_render($poll->ID, false);
                    }
                    ?>
                </div>
            </div>
            <!--Chart vote poll-->
            <div class="result-answer <?php echo !$is_voted ? 'hide' : ''; ?>">
                <div class="answer-chart-area <?php if(POLL_CHART_TYPE == 'type_1') { echo 'donut-chart'; } ?>">
                    <div class="inner-chart">
                        <div id="piechart"></div>
                        <div class="total-vote">
                            <?php
                                $total_vote = qa_poll_get_total_vote($poll->ID);
                                if(empty($total_vote) || $total_vote == 0) {
                                    printf(__('<span class="number">%s</span> Vote', ET_DOMAIN), $total_vote);
                                } else {
                                    printf(__('<span class="number">%s</span> Votes', ET_DOMAIN), $total_vote);
                                }
                            ?>
                        </div>
                    </div>
                    <div class="result-vote">
                        <?php qa_poll_render_answer_result($poll->ID); ?>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!--End chart vote poll-->
        </div>
        <!--End Poll answer -->
        <?php if ( is_active_sidebar( 'qa-ads-mobile-single-question' ) ) : ?>
            <div class="widget-ads">
                <?php dynamic_sidebar( 'qa-ads-mobile-single-question' ); ?>
            </div>
        <?php endif; ?>

        <div class="clearfix" style="height:20px;"></div>
        <!-- CONTENT ANSWERS LOOP / END -->

        <div class="poll-comment form-reply">
            <?php comments_template(); ?>
        </div>

    </div>
    <!-- CONTAINER / END -->
    <script type="text/javascript">
        currentPoll = <?php echo json_encode($poll); ?>;
    </script>
<?php
et_get_mobile_footer();
?>