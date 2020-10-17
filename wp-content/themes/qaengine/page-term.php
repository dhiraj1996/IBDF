<?php
/**
 * Template Name: Term & Conditions Template
 * version 1.0
 * @author: enginethemes
 **/
global $wp_query, $post;
get_header();
the_post();
?>
    <?php get_sidebar( 'left' ); ?>
    <div class="col-md-8 main-blog-fix">
        <div class="row">
            <div class="col-md-12">
                <div class="blog-classic-top">
                    <h2 class="title-blog"><a href="<?php the_permalink(); ?>"><?php the_title() ?></a></h2>
                    <form id="search-bar" action="<?php echo home_url() ?>">
                        <i class="fa fa-search"></i>
                        <input type="text" name="s" id="" placeholder="<?php _e("Search at blog",ET_DOMAIN) ?>">
                    </form>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12">
				<div class="blog-wrapper">
				    <div class="row">
				        <div class="col-md-10 col-xs-10" id="page_content">
				            <div class="blog-content">
				                <?php
				                    the_content();
				                ?>
				            </div>
				        </div>
				    </div>
				</div>
            </div>
        </div>
    </div>
    <?php get_sidebar( 'right' ); ?>
<?php get_footer() ?>