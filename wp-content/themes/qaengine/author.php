<?php
/**
 * Template: Author Page
 * version 1.0
 * @author: enginethemes
 **/
get_header();
global $current_user, $wp_rewrite, $wp_query, $user, $flag_query;;

$user = get_user_by( 'id', get_query_var( 'author' ) );
$user = QA_Member::convert($user);
$user_profile_url = get_author_posts_url($user->ID);
//echo '<h1>Author</h1>';
?>
    <?php get_sidebar( 'left' ); ?>
    <div itemscope itemtype="http://schema.org/Person" class="col-md-8 main-content">
        <div class="row select-category">
            <div class="col-md-6 col-xs-6 current-category">
                <span>
                    <?php
                        printf(__("%s's Profile",ET_DOMAIN), esc_attr($user->display_name) );
                    ?>
                </span>
            </div>
            <?php
                if($current_user->ID == $user->ID){
            ?>
            <div class="col-md-6 col-xs-6 user-controls">
                <ul>
                    <li>
                        <a href="javascript:void(0)" data-toggle="modal" class="show-edit-form">
                            <?php _e("Edit",ET_DOMAIN) ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo wp_logout_url(home_url()); ?>">
                            <?php _e("Logout",ET_DOMAIN) ?>
                        </a>
                    </li>
                    <li>
                        
                    </li>
                </ul>
            </div>
            <?php } ?>

            <?php if($current_user->ID != $user->ID){ ?>
                <div class="col-md-6 col-xs-6 user-controls">
                    <ul>
                        <li>
                            <a href="#" class="<?php echo is_user_logged_in() ? 'inbox' : 'login-url'; ?>" id="inbox"><?php _e("Contact", ET_DOMAIN);?></a>
                        </li>
                    </ul>
                </div>
            <?php } ?>
        </div><!-- END SELECT-CATEGORY -->
        <div class="row user-statistic highlight">
            <div class="col-md-5 col-xs-12 user-info">
                <span class="avatar-80">
                    <?php echo et_get_avatar( $user->ID, 80); ?>
                </span>
                <ul>
                    <li itemprop="name" class="name">
                        <?php echo esc_attr($user->display_name);  ?>
                    </li>
                    <li class="location">
                        <i class="fa fa-map-marker"></i>
                        <span itemprop="homeLocation" itemscope itemtype="http://schema.org/PostalAddress">
                            <span itemprop="addressLocality"><?php echo $user->user_location ? esc_attr($user->user_location) : __('Earth', ET_DOMAIN) ?></span>
                        </span>
                    </li>
                    <li itemprop="email" class="email">
                        <i class="fa fa-envelope"></i>
                        <?php echo $user->show_email == "on" ? esc_attr($user->user_email) : __('Email is hidden.', ET_DOMAIN); ?>
                    </li>
                    <?php if($user->user_facebook){ ?>
                    <li class="location">
                        <i class="fa fa-facebook"></i>
                        <a target="_blank" href="<?php echo $user->user_facebook ?>"><?php echo esc_attr($user->user_facebook) ?></a>
                    </li>
                    <?php } ?>
                    <?php if($user->user_twitter){ ?>
                    <li class="location">
                        <i class="fa fa-twitter"></i>
                        <a target="_blank" href="<?php echo $user->user_twitter ?>"><?php echo esc_attr($user->user_twitter) ?></a>
                    </li>
                    <?php } ?>
                    <?php if($user->user_gplus){ ?>
                    <li class="location">
                        <i class="fa fa-google"></i>
                        <a target="_blank" href="<?php echo $user->user_gplus ?>"><?php echo esc_attr($user->user_gplus) ?></a>
                    </li>
                    <?php } ?>
                </ul>
                <div itemprop="description" class="col-md-12 description">
                    <?php echo nl2br(esc_attr($user->description)); ?>
                </div>
            </div>
            <div class="col-md-7 col-xs-12 user-post-count">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6 statistics">
                        <div class="pull-top question-cat">
                            <?php qa_user_badge( $user->ID ); ?>
                            <br>
                            <span class="points-count">
                            <?php echo qa_get_user_point($user->ID) ? qa_get_user_point($user->ID) : 0 ?>
                            </span>
                            <span class="star">
                                <i class="fa fa-star"></i><br>
                                <?php _e("Points", ET_DOMAIN) ?>
                            </span>
                        </div>
                        <div class="pull-bottom">
                            <div class="col-md-6 col-sm-6 col-xs-6 num-question">
                                <p class="questions-count">
                                    <?php _e('Questions',ET_DOMAIN) ?><br>
                                    <span><?php echo et_count_user_posts($user->ID, 'question'); ?></span>
                                </p>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6 num-answer">
                                <p class="answers-count">
                                    <?php _e('Answers',ET_DOMAIN) ?><br>
                                    <span><?php echo et_count_user_posts($user->ID, 'answer'); ?></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <?php
                        // PUMP PACKAGE
                        if($current_user->ID == $user->ID && is_user_logged_in() && ae_get_option('pump_action') !=="0") {
                            $user_pump_number = get_user_meta($user->ID, 'et_pump_number', true);
                            if(!empty($user_pump_number)) {
                                $premium_time_delay = ae_get_option('premium_time_delay');
                                ?>
                                <div class="col-md-6 col-sm-6 col-xs-6 packaged">
                                    <p class="text-package"><?php _e('Your pump package', ET_DOMAIN); ?></p>
                                    <span class="number-pump"><span class="number"><?php echo $user_pump_number; ?></span><span class="cate-pump"> <?php _e('Pumps', ET_DOMAIN); ?></span></span>
                                    <span class="time-pump"><?php echo gmdate("H:i:s", $premium_time_delay * 60); ?> <span class="cate-pump"><?php _e('Cooldown', ET_DOMAIN); ?></span></span>
                                    <a class="btn-buy" href="javascript:void(0)"><?php _e('Buy more pump', ET_DOMAIN); ?></a>
                                </div>
                                <?php
                            } else {
                                $free_time_delay = ae_get_option('free_time_delay');
                                ?>
                                <div class="col-md-6 col-sm-6 col-xs-6 packaged">
                                    <p class="text-package"><?php _e('Your pump package', ET_DOMAIN); ?></p>
                                    <span class="number-pump">0<span class="cate-pump"> <?php _e('Pump', ET_DOMAIN); ?></span></span>
                                    <span class="time-pump"><?php echo gmdate("H:i:s", $free_time_delay * 60); ?> <span class="cate-pump"><?php _e('Cooldown', ET_DOMAIN); ?></span></span>
                                    <p style="font-style: italic; margin-top: 10px"><?php _e('You can buy more pump package to pump faster.', ET_DOMAIN); ?></p>
                                    <a class="btn-buy" href="javascript:void(0)"><?php _e('Buy more pump', ET_DOMAIN); ?></a>
                                </div>
                                <?php
                            }
                        }
                    //END PUMP PACKAGE
                    ?>
                </div>
            </div>
        </div><!-- END USER-STATISTIC -->

        <div class="row question-filter">
            <div class="col-md-12 sort-questions">
                <ul>
                    <li>
                        <a class="<?php if (!isset($_GET['type'])) echo 'active'; ?>"
                           href="<?php echo $user_profile_url; ?>">
                            <?php _e('Questions', ET_DOMAIN) ?>
                        </a>
                    </li>
                    <?php
                        if(ae_get_option('poll_maker') !=="0"){
                            ?>
                            <li>
                                <a class="<?php if (isset($_GET['type']) && $_GET['type'] == "poll") echo 'active'; ?>"
                                   href="<?php echo esc_url(add_query_arg(array('type' => 'poll', 'paged' => 1), $user_profile_url)); ?>">
                                    <?php _e('Polls', ET_DOMAIN) ?>
                                </a>
                            </li>
                            <?php
                        }
                    ?>
                    <li>
                        <a class="<?php if (isset($_GET['type']) && $_GET['type'] == "answer") echo 'active'; ?>"
                           href="<?php echo esc_url(add_query_arg(array('type' => 'answer', 'paged' => 1), $user_profile_url)); ?>">
                            <?php _e('Answers', ET_DOMAIN) ?>
                        </a>
                    </li>
                    <?php if ($current_user->ID == $user->ID) { ?>
                        <li>
                            <a class="<?php if (isset($_GET['type']) && $_GET['type'] == "following") echo 'active'; ?>"
                               href="<?php echo esc_url(add_query_arg(array('type' => 'following'), $user_profile_url)); ?>">
                                <?php _e('Following', ET_DOMAIN) ?>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div><!-- END QUESTIONS-FILTER -->

        <?php
            //Resend email action
            $confirm = get_user_meta($current_user->ID, "register_status", true);
            if($confirm != "unconfirm") {
        ?>
                <div class="main-questions-list">
                    <ul id="main_questions_list">
                        <?php
                        $paged = get_query_var('paged') ? get_query_var('paged') : 1;

                        $type = isset($_GET['type']) ? $_GET['type'] : 'question';

                        $args = array(
                            'post_type' => $type,
                            'paged' => $paged,
                            'author' => $user->ID
                        );

                        //show pending question if current is author
                        if ($current_user->ID == $user->ID) {
                            $args['post_status'] = array('publish', 'pending');
                        } else {
                            $args['post_status'] = 'publish';
                        }

                        //Set flag
                        $flag_query = 'query_author';

                        //tab following questions
                        if (isset($_GET['type']) && $_GET['type'] == "following") {
                            $follow_questions = array_filter((array)get_user_meta($user->ID, 'qa_following_questions', true));
                            $args['post__in'] = !empty($follow_questions) ? $follow_questions : array(0);
                            $args['post_type'] = array('question', 'poll');
                            $args['post_status'] = 'publish';
                            unset($args['author']);
                        }

                        $query = QA_Questions::get_questions($args);

                        if ($query->have_posts()) {
                            while ($query->have_posts()) {
                                $query->the_post();
                                if($type == 'following') {
                                    if($post->post_type == 'question') {
                                        get_template_part('template/question', 'loop');
                                    } else {
                                        get_template_part('template/poll', 'loop');
                                    }
                                } else {
                                    get_template_part('template/' . $type, 'loop');
                                }
                            }
                        } else {
                            echo '<li class="no-questions">';
                            switch ($type) {
                                case 'poll':
                                    echo '<h2>' . __('There are no polls yet.', ET_DOMAIN) . '</h2>';
                                    break;
                                case 'answer':
                                    echo '<h2>' . __('There are no answers yet.', ET_DOMAIN) . '</h2>';
                                    break;
                                case 'following':
                                    echo '<h2>' . __('There are no questions or polls yet.', ET_DOMAIN) . '</h2>';
                                    break;

                                default:
                                    echo '<h2>' . __('There are no questions yet.', ET_DOMAIN) . '</h2>';
                                    break;
                            }
                            echo '</li>';
                        }
                        wp_reset_query();

                        ?>
                    </ul>
                </div><!-- END MAIN-QUESTIONS-LIST -->
                <div class="row paginations home">
                    <div class="col-md-12">
                        <?php
                        qa_template_paginations($query, $paged);
                        ?>
                    </div>
                </div><!-- END MAIN-PAGINATIONS -->
        <?php
            } else {
                ?>
                <div class="row">
                   <div class="inner resend-email-area">
                       <div class="col-md-12">
                           <h3><i class="fa fa-ban"></i><?php _e("Please check your mailbox to confirm your email address.", ET_DOMAIN); ?></h3>
                           <p><span><?php _e("or", ET_DOMAIN); ?></span> <a href="#" class="resend-confirm-link"><?php _e("Resend the confirmation email.", ET_DOMAIN); ?></a></p>
                       </div>
                   </div>
                </div><!-- END MAIN-QUESTIONS-LIST -->
                <?php
            }
        ?>
    </div>
    <?php get_sidebar( 'right' ); ?>
<?php get_footer() ?>