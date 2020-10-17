<?php
/**
 * Template: QUESTIONS LISTING
 * version 1.0
 * @author: ThaiNT
 **/
	et_get_mobile_header();
    global $post;
?>
<!-- CONTAINER -->
<div class="wrapper-mobile">
	<!-- TOP BAR -->
	<section class="top-bar bg-white">
    	<div class="container">
            <div class="row">
                <div class="col-md-4 col-xs-4">
                    <span class="top-bar-title"><?php _e('All Question',ET_DOMAIN);?></span>
                </div>
                <div class="col-md-8 col-xs-8">
                    <div class="select-categories-wrapper">
                        <div class="select-categories">
                            <select class="select-grey-bg" id="move_to_category">
                                <option value="<?php echo home_url(); ?>"><?php _e("Select Categories",ET_DOMAIN) ?></option>
                                <?php qa_option_categories_redirect() ?>
                            </select>
                        </div>
                    </div>
                    <a href="javascript:void(0)" class="icon-search-top-bar"><i class="fa fa-search"></i></a>
                </div>
            </div>
        </div>
    </section>
    <!-- TOP BAR / END -->

    <!-- MIDDLE BAR -->
    <section class="middle-bar bg-white">
    	<?php qa_mobile_filter_search_questions() ?>
    </section>
    <!-- MIDDLE BAR / END -->
    <?php if ( is_active_sidebar( 'qa-ads-mobile-home-top' ) ) : ?>
        <div class="widget-ads">
            <?php dynamic_sidebar( 'qa-ads-mobile-home-top' ); ?>
        </div>
    <?php endif; ?>

    <!-- LIST QUESTION -->
    <section class="list-question-wrapper">
    	<div class="container">
            <div class="row">
            	<div class="col-md-12">
                	<ul class="list-question">
                        <?php

                            if(get_query_var( 'page' )){
                                $paged = get_query_var( 'page' ) ? get_query_var( 'page' ) : 1;
                            } else {
                                $paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
                            }

                            $args = array(
                                'paged'     => $paged
                            );

                            //Filter question list by poll, question, all
                            $args['post_type'] = qa_poll_filter();
                            if ( isset($_GET['sort']) && $_GET["sort"] == "unanswer" ) {
                                $args['meta_query'] = array(
                                    'relation' => 'OR',
                                    array(
                                        'key'     => 'et_answers_count',
                                        'compare' => 'NOT EXISTS'
                                    ),
                                    array(
                                        'key'   => 'et_answers_count',
                                        'value' => 0
                                    )
                                );
                            }
                            $query = QA_Questions::get_questions($args);
                            if($query->have_posts()){
                                while($query->have_posts()){
                                    $query->the_post();
                                    if( isset($_GET["sort"]) && $_GET["sort"] == "unanswer" && qa_poll_get_total_vote($post->ID) && $post->post_type == 'poll')
                                        continue;
                                    get_template_part( 'mobile/template/question', 'loop' );
                                }
                            }
                            wp_reset_query();
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </section>
     <?php if(!ae_get_option( 'qa_infinite_scroll' )){ ?>
        <!-- LIST QUESTION / END -->
        <section class="list-pagination-wrapper">
            <?php
                qa_template_paginations($query, $paged);
            ?>
        </section>
        <!-- PAGINATIONS QUESTION / END -->
    <?php } else{ 
        // Inifinite Scroll
        echo qae_infinite_scroll(); 
    } ?>
</div>
<!-- CONTAINER / END -->
<?php
	et_get_mobile_footer();
?>