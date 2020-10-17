<?php
/**
 * The Template for displaying all single polls
 *
 * @package: QAEngine
 * @since: QnA Engine 1.0
 * @author: enginethemes
 */
global $post, $wp_rewrite, $current_user, $qa_question, $wp_query, $ae_post_factory;
the_post();
$question        = QA_Questions::convert($post);
$et_post_date    = et_the_time(strtotime($question->post_date));
/**
 * global qa_question
 */
$qa_question    =   $question;

// Get poll
$poll = $ae_post_factory->get('poll');
$poll = $poll->convert($post);

get_header();

$parent_comments       = get_comments( array(
    'post_id'       => $post->ID,
    'parent'        => 0,
    'status'        => 'approve',
    'post_status'   => 'publish',
    'order'         => 'ASC',
    'type'          => 'comment'
) );
?>
<?php get_sidebar( 'left' ); ?>
    <div itemscope itemtype="http://schema.org/Question" class="col-md-8 main-content single-content">
        <div class="row select-category single-head">
            <div class="col-md-2 col-xs-2">
                <span class="back">
                    <i class="fa fa-angle-double-left"></i> <a href="<?php echo home_url(); ?>"><?php _e("Home", ET_DOMAIN); ?></a>
                </span>
            </div>
            <div class="col-md-8 col-xs-8">
                <h1 itemprop="name"><?php the_title(); ?></h1>
            </div>
        </div><!-- END SELECT-CATEGORY -->
        <div id="question_content" class="row question-main-content question-item" data-id="<?php echo $post->ID; ?>">
            <!-- Vote section -->
            <?php get_template_part( 'template/item', 'vote' ); ?>
            <!--// Vote section -->
            <div class="col-md-9 col-xs-9 q-right-content">

                <!-- admin control -->
                <ul class="post-controls">
                    <?php if($current_user->ID == $qa_question->post_author || qa_user_can('edit_question')) { ?>
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-original-title="<?php _e("Edit", ET_DOMAIN) ?>" data-name="edit" class="post-edit action">
                                <i class="fa fa-pencil"></i>
                            </a>
                        </li>
                    <?php } ?>
                    <?php if( current_user_can( 'manage_options' ) ){ ?>
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-original-title="<?php _e("Delete", ET_DOMAIN) ?>" data-name="delete" class="post-delete action" >
                                <i class="fa fa-trash-o"></i>
                            </a>
                        </li>
                    <?php } ?>
                    <!-- Follow Action -->
                    <?php
                    $user_following = explode(',', $question->et_users_follow);
                    $is_followed    = in_array($current_user->ID, $user_following);
                    if(!$is_followed){
                        ?>
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-original-title="<?php _e("Follow", ET_DOMAIN) ?>" data-name="follow" class="action follow" >
                                <i class="fa fa-plus-square"></i>
                            </a>
                        </li>
                    <?php } else { ?>
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-original-title="<?php _e("Unfollow", ET_DOMAIN) ?>" data-name="unfollow" class="action followed" >
                                <i class="fa fa-minus-square"></i>
                            </a>
                        </li>
                    <?php } ?>
                    <!-- // Follow Action -->
                    <!-- report Action -->
                    <?php if(is_user_logged_in() && !$question->reported && $question->post_status != "pending"){ ?>
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-original-title="<?php _e("Report", ET_DOMAIN) ?>" data-name="report" class="action report" >
                                <i class="fa fa-exclamation-triangle"></i>
                            </a>
                        </li>
                    <?php } else if( current_user_can( 'manage_options' ) ) { ?>
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-original-title="<?php _e("Approve", ET_DOMAIN) ?>" data-name="approve" class="action approve" >
                                <i class="fa fa-check"></i>
                            </a>
                        </li>
                    <?php } ?>
                    <!--// Report Action -->
                </ul>
                <!--// admin control -->
                <!-- question tag -->
                <div class="top-content">
                    <?php if($question->et_best_answer){ ?>
                        <span class="answered"><i class="fa fa-check"></i> <?php _e("Answered", ET_DOMAIN) ?></span>
                    <?php } ?>
                    <ul class="question-tags">
                        <?php
                        foreach ($question->qa_tag as $tag) {
                            ?>
                            <li>
                                <a class="q-tag" href="<?php echo get_term_link($tag->term_id, 'qa_tag'); ?> ">
                                    <?php echo $tag->name; ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>

                </div>
                <!--// question tag -->
                <div class="clearfix"></div>

                <div itemprop="text" class="question-content">
                    <?php the_content() ?>
                </div>

                <div class="row">
                    <div class="col-md-8 col-xs-8 question-cat">
                        <a href="<?php echo get_author_posts_url($question->post_author); ?>">
                            <span itemprop="author" itemscope itemtype="http://schema.org/Person">
                                <span class="author-avatar">
                                    <?php echo et_get_avatar( $question->post_author, 30 ); ?>
                                </span>
                                <span itemprop="name" class="author-name"><?php echo $question->author_name; ?></span>
                            </span>
                        </a>
                        <?php  qa_user_badge( $question->post_author ); ?>

                        <span class="question-time" itemprop="dateCreated" datetime="<?php echo $et_post_date; ?>">
                            <?php printf( __( 'Asked %s in', ET_DOMAIN ),$et_post_date); ?>
                        </span>
                        <?php
                        if(count($question->question_category) > 1){
                            $num = 1;
                            foreach ($question->question_category as $key => $value) {
                                $category = $value->name;
                                $category_link = get_term_link($value->term_id,'question_category');
                                if($num == count($question->question_category)){
                                    echo '<span class="question-category">
                                            <a href="'.$category_link.'">'.$category.'.</a>
                                        </span>';
                                }else{
                                    echo '<span class="question-category">
                                            <a href="'.$category_link.'">'.$category.',</a> &nbsp
                                        </span>';
                                }
                                $num++;
                            }
                        }else{
                            $category      = !empty($question->question_category[0]) ? $question->question_category[0]->name : __('No Category',ET_DOMAIN);
                            $category_link = !empty($question->question_category[0]) ? get_term_link( $question->question_category[0]->term_id, 'question_category' ) : '#';
                            echo '<span class="question-category">
                                            <a href="'.$category_link.'">'.$category.'.</a>
                                        </span>';
                        } ?>
                    </div>
                    <div class="col-md-4 col-xs-4 question-control">
                        <ul>
                            <li>
                                <a class="share-social" href="javascript:void(0)" data-toggle="popover" data-placement="top"  data-container="body" data-content='<?php echo qa_template_share($question->ID); ?>' data-html="true">
                                    <?php _e("Share",ET_DOMAIN) ?> <i class="fa fa-share"></i>
                                </a>
                            </li>
                            <!-- <li class="collapse">
                                <a href="javascript:void(0)">
                                    <?php _e("Report",ET_DOMAIN) ?> <i class="fa fa-flag"></i>
                                </a>
                            </li> -->
                            <li>
                                <span href="javascript:void(0)" class="<?php if(count($parent_comments) > 0) echo 'active'; ?>">
                                    <?php
                                    printf( __( 'Comment(%d) ', ET_DOMAIN ), count($parent_comments));
                                    ?> <i class="fa fa-comment"></i>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>

            <!-- POLL ANSWERS -->
            <div class="select-answer">
                <?php
                    // Check if user has voted or not
                    $is_voted = qa_poll_check_user_vote($poll->ID);
                ?>
                <div class="multi-choice">
                    <ul>
                        <li class="vote-link"><span class="<?php echo $is_voted ? '' : 'active'; ?>"><?php _e('Vote', ET_DOMAIN); ?></span></li>
                        <li class="vote-result"><span class="<?php echo !$is_voted ? '' : 'active'; ?>"><?php _e('Result', ET_DOMAIN); ?></span></li>
                    </ul>
                </div>
                <div class="vote-answer <?php echo $is_voted ? 'hide' : ''; ?>">
                    <div class="top-bar-answer">
                        <?php
                            $end_date =  DateTime::createFromFormat('d/m/Y', $poll->poll_end_date)->format(get_option('date_format'));
                        ?>
                        <p><?php printf(__('Select your answer - End in %s', ET_DOMAIN), $end_date); ?></p>
                    </div>
                    <div class="option-answer">
                        <?php
                            if(isset($post->poll_multi_choice) && $poll->poll_multi_choice) {
                                qa_poll_answer_render($poll->ID, true);
                            } else {
                                qa_poll_answer_render($poll->ID, false);
                            }
                        ?>
                    </div>
                </div>
                <div class="result-answer <?php echo !$is_voted ? 'hide' : ''; ?>">
                    <div class="top-bar-answer">
                        <p>
                            <?php
                                _e('Results of this poll', ET_DOMAIN);

                                if(qa_poll_check_end_date($poll->ID)) {
                                    echo '<span style="font-style: italic;">'. __(' (This poll was expired)', ET_DOMAIN) .'<span>';
                                }
                            ?>
                        </p>
                    </div>
                    <div class="percent-vote <?php if(POLL_CHART_TYPE == 'donut_chart') { echo 'donut-chart'; } ?>">
                        <div class="row">

                            <div class="col-md-4 col-sm-12 col-xs-12 answer-chart-area">
                                <div class="chart-cus">
                                    <div id="chart_wrap">
                                        <div id="piechart"></div>

                                    </div>
                                </div>
                                <div class="total-vote">
                                    <?php
                                        $total_vote = qa_poll_get_total_vote($poll->ID);
                                        if(empty($total_vote) || $total_vote == 0 || $total_vote == 1) {
                                            printf(__('<span class="number">%s</span> Vote', ET_DOMAIN), $total_vote);
                                        } else {
                                            printf(__('<span class="number">%s</span> Votes', ET_DOMAIN), $total_vote);
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-8 col-sm-12 col-xs-12 answer-percent-area">
                                <?php qa_poll_render_answer_result($poll->ID); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END POLL ANSWERS -->

        </div><!-- END QUESTION-MAIN-CONTENT -->

        <?php if( is_active_sidebar( 'qa-content-question-banner-sidebar' ) ){ ?>
            <div class="row">
                <div class="col-md-12 ads-wrapper">
                    <?php dynamic_sidebar( 'qa-content-question-banner-sidebar' ); ?>
                </div>
            </div><!-- END WIDGET BANNER -->
        <?php } ?>

        <div class="row form-reply">
            <?php comments_template(); ?>
        </div>

        <?php do_action( 'qa_btm_questions_listing' ); ?>

    </div>
<?php get_sidebar( 'right' ); ?>
    <script type="text/javascript">
        currentPoll = <?php echo json_encode($poll); ?>;
    </script>
<?php get_footer() ?>