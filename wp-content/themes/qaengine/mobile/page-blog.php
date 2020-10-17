<?php
/**
 * Template: Blog Template
 * version 2.0.10
 * @author: Tuandq
 **/
	et_get_mobile_header();
    global $wp_query;
?>
<!-- CONTAINER -->
<div class="wrapper-mobile">
	<!-- TAGS BAR -->
    <section class="tag-bar bg-white">
    	<div class="container">
            <div class="row">
            	<div class="col-md-4 col-xs-4">
                	<h1 class="title-page"><?php _e('Blog', ET_DOMAIN) ?></h1>
                </div>
                <div class="col-md-8 col-xs-8 collapse">
                	<form class="find-tag-form">
                    	<i class="fa fa-chevron-circle-right"></i>
                    	<input type="text" name="" id="" placeholder="Find a blog">
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- TAGS BAR / END -->

	<!-- MIDDLE BAR -->
    <section class="blog-wrapper">
    	<div class="container">
            <div class="row">
            	<ul class="blog-list">
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
                                echo '<li>';
                                get_template_part( 'mobile/template/post', 'loop' );
                                echo '</li>';
                            endwhile;
                        }
                        else {
                            _e('No posts yet.', ET_DOMAIN);
                        }
                        wp_reset_query();
                    ?>
                </ul>
    		</div>
        </div>
    </section>
	<!-- MIDDLE BAR / END -->
    <section class="list-pagination-wrapper">
        <?php
            qa_template_paginations($query, $paged);
        ?>
    </section>
    <!-- PAGINATIONS QUESTION / END -->
</div>
<!-- CONTAINER / END -->

<?php
	et_get_mobile_footer();
?>