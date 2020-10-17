<?php
/**
 * Template Name: Blog Template
 * version 2.0.10
 * @author: Tuandq
 **/
global $wp_query;
get_header();
?>
    <?php get_sidebar( 'left' ); ?>
    <div itemtype="http://schema.org/ItemList" class="col-md-8 main-blog-fix">
        <div class="row">
            <div class="col-md-12">
                <div class="blog-classic-top">
                    <h2 itemprop="name"><?php _e("Blog",ET_DOMAIN) ?></h2>
                    <form id="search-bar" action="<?php echo home_url() ?>">
                        <i class="fa fa-search"></i>
                        <input type="text" name="s" id="" placeholder="<?php _e("Search at blog",ET_DOMAIN) ?>">
                    </form>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12">
                <ul id="main_posts_list">
                    <?php
                        if(get_query_var( 'page' ))
                            $paged = get_query_var( 'page' ) ? get_query_var( 'page' ) : 1;
                        else
                            $paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
                        $args  = array(
                            'post_type'      => 'post',
                            'paged'          => $paged,
                        );
                        $query = new WP_Query($args);
                        if($query-> have_posts()){
                            while ( $query->have_posts() ) : $query->the_post();
                                //the_post();
                                echo '<li>';
                                get_template_part( 'template/post', 'loop' );
                                echo '</li>';
                            endwhile;
                        }
                        wp_reset_query();
                    ?>
                </ul>
            </div><!-- END MAIN-QUESTIONS-LIST -->
        </div><!-- END SELECT-CATEGORY -->
        <div class="row paginations home blog-template">
            <div class="col-md-12">
                <?php
                    qa_template_paginations($query, $paged);
                ?>
            </div>
        </div><!-- END MAIN-PAGINATIONS -->
    </div>
    <?php get_sidebar( 'right' ); ?>
<?php get_footer() ?>