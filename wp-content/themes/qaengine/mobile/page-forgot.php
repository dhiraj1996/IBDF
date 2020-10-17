<?php
/*
 * Template name: Forgot Password
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
                            <h2 class="title-blog"><?php _e("Forgot Password", ET_DOMAIN); ?></h2>
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
                                                <form id="form_forgot_password_mobile" method="post" action="">
                                                    <div class="social-form">
                                                        <input type="hidden" name="et_nonce" value="<?php echo wp_create_nonce( 'authentication' ) ?>">
                                                        <input type="text" name="user_email" autocomplete="off" placeholder="<?php _e('Email', ET_DOMAIN) ?>">
                                                        <input type="submit" class="btn-submit" value="Submit">
                                                    </div>
                                                </form>
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
?>