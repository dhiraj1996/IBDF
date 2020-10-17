<?php
    global $post, $current_user;
    $question      = QA_Questions::convert($post);
    $et_post_date  = et_the_time(strtotime($question->post_date));
    $title         = $post->post_status == "pending" ? 'title="'.__('Pending Question', ET_DOMAIN).'"' : '';
    if ( isset($_GET['sort']) && $_GET["sort"] == "unanswer" ){
        $count_comment = wp_count_comments($question->ID);
        if($count_comment->approved)
            return;
    }
?>
<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" <?php post_class( 'question-item' );?> data-id="<?php echo $post->ID ?>" <?php echo $title ?>>
    <div itemprop="item" itemscope itemtype="http://schema.org/Question">
        <div class="col-md-8 col-xs-8 q-left-content">
            <div class="q-ltop-content">
                <h2 itemprop="name">
                    <span class="mask-poll"><?php _e('Poll', ET_DOMAIN); ?></span>
                    <a itemprop="url" href="<?php the_permalink(); ?>" class="question-title"><?php the_title() ?></a>
                </h2>
            </div>
            <div class="q-lbtm-content">
                <div itemprop="text" class="question-excerpt">
                    <?php the_excerpt(); ?>
                </div>
                <div class="question-cat">
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
                    <div class="clearfix"></div>
                    <a itemprop="author" itemscope itemtype="http://schema.org/Person" href="<?php echo get_author_posts_url($question->post_author); ?>">
                    <span class="author-avatar">
                        <?php echo et_get_avatar( $question->post_author, 30 ); ?>
                    </span>
                        <span itemprop="name" class="author-name"><?php echo $question->author_name; ?></span>
                    </a>
                    <?php  qa_user_badge( $question->post_author ); ?>
                    <span itemprop="dateCreated" datetime="<?php echo $et_post_date; ?>" class="question-time">
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
            </div>
        </div><!-- end left content -->
        <div class="col-md-4 col-xs-4 q-right-content">
            <ul class="question-statistic">
                <li>
                <span class="question-views">
                    <?php echo qa_format_number($question->et_view_count); ?>
                </span>
                    <?php _e("views",ET_DOMAIN) ?>
                </li>
                <li class="<?php if($question->et_best_answer) echo 'active'; ?>">
                <span itemprop="answerCount" class="poll-answers">
                    <?php echo qa_poll_get_total_vote($post->ID) ?>
                </span>
                    <?php _e("answers",ET_DOMAIN) ?>
                </li>
                <li>
                <span itemprop="upvoteCount" class="question-votes">
                    <?php echo qa_format_number($question->et_vote_count); ?>
                </span>
                    <?php _e("votes",ET_DOMAIN) ?>
                </li>
            </ul>
            <div class="pumping">
                <?php qa_render_pump_button($question); ?>
            </div>
        </div><!-- end right content -->
        <div class="clearfix"></div>
    </div>
</li>