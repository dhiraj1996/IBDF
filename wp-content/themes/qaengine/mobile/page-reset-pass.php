<?php
/*
 * Template name: Reset Password
 */
global $wp_query, $wp_rewrite, $post, $et_data;

et_get_mobile_header();
global $post;
the_post();
?>
    <!-- CONTAINER -->
    <div class="wrapper-mobile bg-white">
        <!-- TAGS BAR -->
        <section class="blog-bar">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="blog-content">
                            <!-- <span class="tag"><?php //the_category( '-' ); ?></span><span class="cmt"><i class="fa fa-comments"></i><?php //comments_number(); ?></span> -->
                            <h2 class="title-blog"><?php _e("Reset Password", ET_DOMAIN); ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- TAGS BAR / END -->

        <!-- MIDDLE BAR -->
        <section class="blog-wrapper">
            <div class="container">
                <div class="row">
                    <div class="blog-list single-blog">
                        <!-- <div class="col-xs-2">
                        <a href="<?php echo get_author_posts_url( $post->post_author ); ?>" class="profile-avatar">
                            <?php echo et_get_avatar( $post->post_author, 65, array('class' => 'avatar img-responsive','alt' => '') ); ?>
                        </a>
                    </div> -->
                        <div class="col-xs-12" id="page_content">
                            <div class="blog-content">
                                <div class="container-fluid main-center">
                                    <div class="row">
                                        <div class="col-md-12 marginTop30">
                                            <div class="twitter-auth social-auth social-auth-step1">
                                                <p class="social-small"><?php the_content(); ?></p>
                                                <div class="social-form">
                                                    <form id="resetpass_form" class="form_modal_style">
                                                        <input type="hidden" id="user_login" name="user_login" value="<?php if(isset($_GET['user'])) echo $_GET['user'] ?>" />
                                                        <input type="hidden" id="user_key" name="user_key" value="<?php if(isset($_GET['key'])) echo $_GET['key'] ?>">
                                                        <label><?php _e("Enter your new password here", ET_DOMAIN) ?></label>
                                                        <input type="password" class="name_user" name="new_password" id="new_password" />
                                                        <input type="password" class="name_user" name="re_new_password" id="re_new_password" />
                                                        <input type="submit" name="submit" value="<?php _e("Reset", ET_DOMAIN) ?>" class="btn-submit">
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-xs-12">
                        <?php //comments_template(); ?>
                    </div>
                </div>
            </div>
        </section>
        <!-- MIDDLE BAR / END -->

    </div>
    <!-- CONTAINER / END -->
<?php
et_get_mobile_footer();
?><?php
if(!et_load_mobile()) {
    wp_redirect(home_url());
}