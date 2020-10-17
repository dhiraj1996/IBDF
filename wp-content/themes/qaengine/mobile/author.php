<?php
/**
 * Template: BLOG LISTING AUTHOR
 * version 1.0
 * @author: ThaiNT
 **/
et_get_mobile_header();
global $wp_query, $wp_rewrite, $current_user;

$user = get_user_by( 'id', get_query_var( 'author' ) );
$user = QA_Member::convert($user);
$user_profile_url = get_author_posts_url($user->ID);
if(isset($_GET['edit'])):
?>
    <!-- CONTAINER -->
    <div class="wrapper-mobile">
        <!-- MIDDLE BAR -->
        <section class="middle-bar bg-white">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <ul class="menu-middle-bar">
                            <li class="<?php if(isset($_GET['edit']) && $_GET['edit'] == "change-profile") echo 'active'; ?>" >
                                <a href="<?php echo esc_url(add_query_arg('edit', 'change-profile', get_author_posts_url( $current_user->ID ))); ?>"><?php _e('Change Profile',ET_DOMAIN) ?></a>
                            </li>
                            <li class="<?php if(isset($_GET['edit']) && $_GET['edit'] == "change-password") echo 'active'; ?>" >
                                <a href="<?php echo esc_url(add_query_arg('edit', 'change-password', get_author_posts_url( $current_user->ID ))); ?>"><?php _e('Change Password',ET_DOMAIN) ?></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <!-- MIDDLE BAR / END -->
        <section class="change-password-question-wrapper">
            <?php 

            if(isset($_GET['edit']) && $_GET['edit'] == 'change-profile'){ ?>
                <!-- CHANGE PROFILE -->user-name-profile
                <form id="change_profile"  class="form_update" action="">
                    <div class="col-md-12 change_profile">
                        <div class="qa-edit-avatar qa-container" id="user_avatar_container">
                            <div class="avatar-container">
                                <div id="user_logo_container">
                                    <span class="img-profile" id="user_avatar_thumbnail" >
                                        <span class="btn-profile" id="user_avatar_browse_button"><a href="#" class="no-underline">
                                        <?php echo  et_get_avatar($current_user->ID);?>
                                        </a></span>
                                    </span>
                                    <span class="et_ajaxnonce" id="<?php echo wp_create_nonce('user_avatar_et_uploader'); ?>"></span>
                                    <span id="profile_thumb_browse_button" class="bg-grey-button btn-button" style="z-index: 0;">
                                        <i class="fa fa-upload"></i> <span data-icon="o" class="icon"></span>
                                    </span>
                                    <span class="hide" id="user_id" user-data="<?php echo $current_user->ID; ?>"></span>
                                </div>
                                <?php //echo et_get_avatar($current_user->ID) ?>
                                <!-- <a href="#" data-toggle="modal-edit" class="fe-icon-b-edit fe-icon-b" data-role="none"></a> -->
                            </div>
                        </div>
                        <div class="form-post">
                            <label class="form-title"><?php _e('Full name',ET_DOMAIN);?></label>
                            <input class="submit-input" type="text" name="display_name" id="display_name" value="<?php echo $user->display_name;?>"/>
                        </div>
                        <div class="form-post">
                            <label class="form-title"><?php _e('Location',ET_DOMAIN);?></label>
                            <input class="submit-input" type="text" name="user_location" id="user_location" value="<?php echo $user->user_location;?>"/>
                        </div>
                        <div class="form-post">
                            <label class="form-title"><?php _e('Facebook',ET_DOMAIN);?></label>
                            <input class="submit-input" type="text" name="user_facebook" id="user_facebook" value="<?php echo $user->user_facebook;?>"/>
                        </div>
                        <div class="form-post">
                            <label class="form-title"><?php _e('Twitter',ET_DOMAIN);?></label>
                            <input class="submit-input" type="text" name="user_twitter" id="user_twitter" value="<?php echo $user->user_twitter;?>"/>
                        </div>
                        <div class="form-post">
                            <label class="form-title"><?php _e('Google+',ET_DOMAIN);?></label>
                            <input class="submit-input" type="text" name="user_gplus" id="user_gplus" value="<?php echo $user->user_gplus;?>"/>
                        </div>
                        <div class="form-post">
                            <label class="form-title"><?php _e('Email',ET_DOMAIN);?></label>
                            <input class="submit-input" type="text" name="user_email" id="user_email" placeholder="<?php _e('admin@local.com',ET_DOMAIN);?>" value="<?php echo $user->user_email;?>"/>
                        </div>
                        <div class="form-post">
                            <input type="checkbox" name="show_email" id="show_email" <?php echo ($user->show_email == "on") ? 'checked':'' ;?>>
                            <label for="for="show_email""><?php _e('Make this email public.',ET_DOMAIN);?></label>
                        </div>
                        <div class="form-post">
                            <label class="form-title"><?php _e('Description',ET_DOMAIN);?></label>
                            <textarea name="description" id="description" cols="30" rows="10" placeholder="Your Description"> <?php echo $user->description;?></textarea>
                        </div>
                        <div class="form-post">
                            <input type="submit" name="submit" value="<?php _e('Change Profile', ET_DOMAIN);?>" class="btn-submit update-profile"/>
                        </div>
                    </div>
                </form>
                 <!-- CHANGE PROFILE / END -->
            <?php } else if(isset($_GET['edit']) && $_GET['edit'] == 'change-password') { ?>
                 <!-- CHANGE PASSWORD -->
                <form id="change_password" class="form_update" action="">
                    <div class="col-md-12 change_password">
                        <div class="form-post">
                            <label class="form-title"><?php _e('Old Password',ET_DOMAIN);?></label>
                            <input class="submit-input" type="password" name="old_password" id="old_password"/>
                        </div>
                        <div class="form-post">
                            <label class="form-title"><?php _e('New Password',ET_DOMAIN);?></label>
                            <input class="submit-input" type="password" name="new_password" id="new_password"/>
                        </div>
                        <div class="form-post">
                            <label class="form-title"><?php _e('Repeat New Password',ET_DOMAIN);?></label>
                            <input class="submit-input" type="password" name="re_password" id="re_password"/>
                        </div>
                        <div class="form-post">
                            <input type="submit" name="submit" value="<?php _e('Change Password', ET_DOMAIN); ?>" class="btn-submit update-profile"/>
                        </div>
                    </div>
                </form>
                 <!-- CHANGE PASSWORD / END -->
            <?php } ?>
        </section>
    </div>
    <!-- CONTAINER / END -->
<?php else:?>
    <!-- CONTAINER -->
    <div class="wrapper-mobile">
    	<!-- TOP BAR -->
    	<section class="profile-user-wrapper">
        	<div class="container">
                <div class="row">
                    <div class="col-md-3 col-xs-3 padding-right-0">
                        <a href="javascript:void(0);" class="profile-avatar">
                            <?php echo et_get_avatar( $user->ID, 65); ?>
                        </a>
                    </div>
                    <div class="col-md-9 col-xs-9">
                        <div class="profile-wrapper">
                        	<span class="user-name-profile"><?php echo esc_attr( $user->display_name );  ?></span>
                        	<span class="address-profile">
                                <?php
                                    if( $user->user_location ) {
                                        echo '<i class="fa fa-map-marker"></i>' .esc_attr( $user->user_location )  ;
                                    } else {
                                        echo '<i class="fa fa-globe"></i>' . __("Earth", ET_DOMAIN)  ;
                                    }
                                ?>
                            </span>
                            <span class="email-profile">
                                <i class="fa fa-envelope"></i>
                                <?php echo $user->show_email == "on" ? $user->user_email : __('Email is hidden.', ET_DOMAIN); ?>
                            </span>
                            <?php
                                if($user->user_facebook)
                                {
                                    ?>
                                    <span class="facebook-profile">
                                        <i class="fa fa-facebook"></i>
                                        <a target="_blank" href="<?php echo $user->user_facebook ?>"><?php echo esc_attr($user->user_facebook) ?></a>
                                    </span>
                                    <?php
                                }
                                if($user->user_twitter)
                                {
                                    ?>
                                    <span class="twitter-profile">
                                        <i class="fa fa-twitter"></i>
                                        <a target="_blank" href="<?php echo $user->user_twitter ?>"><?php echo esc_attr($user->user_twitter) ?></a>
                                    </span>
                                    <?php
                                }
                                if($user->user_gplus)
                                {
                                    ?>
                                    <span class="google-profile">
                                        <i class="fa fa-google"></i>
                                        <a target="_blank" href="<?php echo $user->user_gplus ?>"><?php echo esc_attr($user->user_gplus) ?></a>
                                    </span>
                                    <?php
                                }
                            ?>         
                            <div class="clearfix"></div>
                            <?php if($current_user->ID != $user->ID){ ?>
                                <a class="<?php echo is_user_logged_in() ? 'inbox' : 'login-url'; ?>" id="inbox"><?php _e("Contact", ET_DOMAIN);?></a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top: 15px;">
                    <div class="col-md-3 col-xs-3 padding-right-0">
                        <div class="badges"><?php qa_user_badge($user->ID); ?></div>
                    </div>
                    <div class="col-md-5 col-xs-5 padding-right-0">
                    	<div class="list-bag-profile-wrapper">
                            <span class="point-profile">
                                <span>
                                    <?php echo qa_get_user_point($user->ID) ? qa_get_user_point($user->ID) : 0 ?>
                                    <i class="fa fa-star"></i>
                                </span><?php _e("points", ET_DOMAIN) ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-4 col-xs-4 padding-left-0">
                    	 <div class="list-bag-profile-wrapper" style="text-align: right; padding-top 2px;">
                             <span class="question-profile">
                                <?php $questionTotal = et_count_user_posts($user->ID);
                                        if($questionTotal > 1000){
                                            $questionTotal = round(($questionTotal/1000), 1) . "K+";
                                        } 
                                    echo $questionTotal;
                                ?>
                                <i class="fa fa-question-circle"></i>
                             </span>
                             <span class="answers-profile">
                                <?php $answerTotal = et_count_user_posts($user->ID, "answer") ;
                                    if($answerTotal > 1000){
                                        $answerTotal = rount(($answerTotal/1000), 1) . "K+";
                                    }
                                    echo $answerTotal;
                                ?>
                                <i class="fa fa-comments"></i>
                             </span>
                         </div>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-md-12 col-xs-12">
                        <div class="description">
                            <?php echo nl2br(esc_attr($user->description)); ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <?php
                        if($current_user->ID == $user->ID && is_user_logged_in() && ae_get_option('pump_action') !=="0") {
                            $user_pump_number = get_user_meta($user->ID, 'et_pump_number', true);
                            if(!empty($user_pump_number)) {
                                $premium_time_delay = ae_get_option('premium_time_delay');
                                ?>
                                <div class="packaged">
                                    <div class="col-md-5 col-xs-5 padding-right-0">
                                        <span class="text-pump"><?php echo $user_pump_number ?><span class="text-normal"><?php _e('pump', ET_DOMAIN); ?></span></span>
                                    </div>
                                    <div class="col-md-7 col-xs-7 padding-left-0">
                                        <span class="text-pump"><?php echo gmdate("H:i:s", $premium_time_delay * 60); ?> <span class="text-normal"><?php _e('cooldown', ET_DOMAIN); ?></span></span>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-12">
                                        <a class="btn-buy" href="javascript:void(0)"><?php _e('Buy more pump', ET_DOMAIN); ?></a>
                                    </div>
                                </div>
                                <?php
                            } else {
                                $free_time_delay = ae_get_option('free_time_delay');
                                ?>
                                <div class="packaged">
                                    <div class="col-md-5 col-xs-5 padding-right-0">
                                        <span class="text-pump" style="text-align: left;">0<span class="text-normal"><?php _e('pump', ET_DOMAIN); ?></span></span>
                                    </div>
                                    <div class="col-md-7 col-xs-7 padding-left-0">
                                        <span class="text-pump" style="text-align: right;"><?php echo gmdate("H:i:s", $free_time_delay * 60); ?> <span class="text-normal"><?php _e('cooldown', ET_DOMAIN); ?></span></span>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-12">
                                        <a class="btn-buy" href="javascript:void(0)"><?php _e('Buy more pump', ET_DOMAIN); ?></a>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                    ?>
                </div>
            </div>
        </section>
        <!-- TOP BAR / END -->

        <!-- MIDDLE BAR -->
        <section class="middle-bar bg-white">
        	<div class="container">
                <div class="row">
                	<div class="col-md-12">
                    	<ul class="menu-middle-bar">
                            <li class="<?php if(!isset($_GET['type'])) echo 'active'; ?>" >
                                <a href="<?php echo $user_profile_url; ?>"><?php _e('Questions',ET_DOMAIN) ?></a>
                            </li>
                            <?php
                                if(ae_get_option('poll_maker') !=="0"){
                                    ?>
                                    <li class="<?php if (isset($_GET['type']) && $_GET['type'] == "poll") echo 'active'; ?>">
                                        <a href="<?php echo esc_url(add_query_arg(array('type' => 'poll', 'paged' => 1), $user_profile_url)); ?>"><?php _e('Polls', ET_DOMAIN) ?></a>
                                    </li>
                                    <?php
                                }
                            ?>
                            <li class="<?php if(isset($_GET['type']) && $_GET['type'] == "answer") echo 'active'; ?>" >
                                <a href="<?php echo esc_url(add_query_arg(array('type'=>'answer', 'paged' => 1), $user_profile_url)); ?>"><?php _e('Answers',ET_DOMAIN) ?></a>
                            </li>
                            <?php if($current_user->ID == $user->ID){ ?>
                            <li class="<?php if(isset($_GET['type']) && $_GET['type'] == "following") echo 'active'; ?>">
                                <a href="<?php echo esc_url(add_query_arg(array('type'=>'following'), $user_profile_url)); ?>"><?php _e('Following',ET_DOMAIN) ?></a>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
        		</div>
            </div>
            <div class="form-search-wrapper">
            	<form id="form-search" class="collapse">
                	<a href="javascript:void(0);" class="clear-text-search"><i class="fa fa-times-circle"></i></a>
                    <a href="javascript:void(0);" class="close-form-search"><?php _e('Cancel', ET_DOMAIN) ?></a>
                	<input type="text" name="" id="" required placeholder="<?php _e('Enter keyword',ET_DOMAIN) ?>" class="form-input-search">
                </form>
            </div>
        </section>
        <!-- MIDDLE BAR / END -->
        <?php
            $confirm = get_user_meta($current_user->ID, "register_status", true);
            if($confirm != "unconfirm") {
        ?>
                <!-- LIST QUESTION -->
                <section class="list-question-wrapper">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <ul class="list-question <?php if (isset($_GET['type']) && $_GET['type'] == "post") echo 'list-posts'; ?>">
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
                                    }
                                    //tab following questions
                                    if (isset($_GET['type']) && $_GET['type'] == "following") {
                                        $follow_questions = array_filter((array)get_user_meta($user->ID, 'qa_following_questions', true));
                                        $args['post_type'] = array('question', 'poll');
                                        $args['post__in'] = !empty($follow_questions) ? $follow_questions : array(0);
                                        unset($args['author']);
                                    }

                                    $query = QA_Questions::get_questions($args);

                                    if ($query->have_posts()) {
                                        while ($query->have_posts()) {
                                            $query->the_post();
                                            if($type == 'following') {
                                                if($post->post_type == 'question') {
                                                    get_template_part('mobile/template/question', 'loop');
                                                } else {
                                                    get_template_part('mobile/template/poll', 'loop');
                                                }
                                            } else {
                                                get_template_part('mobile/template/' . $type, 'loop');
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
                            </div>
                        </div>
                    </div>
                </section>
                <!-- LIST QUESTION / END -->
                <section class="list-pagination-wrapper">
                    <?php
                    qa_template_paginations($query, $paged);
                    ?>
                </section>
                <!-- PAGINATIONS QUESTION / END -->
        <?php
            } else {
                ?>
                <div class="row">
                    <div class="inner resend-email-area">
                        <div class="col-md-12">
                            <h3><i class="fa fa-ban"></i><?php _e("Please check your mail box to confirm your email address.", ET_DOMAIN); ?></h3>
                            <p><span><?php _e("or", ET_DOMAIN); ?></span> <a href="#" class="resend-confirm-link"><?php _e("Resend confirm email.", ET_DOMAIN); ?></a></p>
                        </div>
                    </div>
                </div><!-- END MAIN-QUESTIONS-LIST -->
                <?php
            }
       ?>
    </div>
    <!-- CONTAINER / END -->
<?php
endif;
	et_get_mobile_footer();
?>
