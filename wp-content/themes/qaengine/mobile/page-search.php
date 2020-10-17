<?php
/**
 * Template: SEARCH QUESTIONS
 * version 1.0
 * @author: ThaiNT
 **/
	et_get_mobile_header();
    global $post;
    $keyword = urldecode(get_query_var( 'keyword' ));
?>
<!-- CONTAINER -->
<div class="wrapper-mobile">
	<!-- TOP BAR -->
	<section class="top-bar bg-white">
    	<div class="container">
            <div class="row">
                <div class="col-md-4 col-xs-4">
                    <span class="top-bar-title"><?php _e('Search Question',ET_DOMAIN);?></span>
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
                    <a href="javascript:void(0)" class="icon-search-top-bar active">
                        <i class="fa fa-search"></i>
                    </a>
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

    <!-- LIST QUESTION -->
    <section class="list-question-wrapper">
    	<div class="container">
            <div class="row">
            	<div class="col-md-12">
                	<ul class="list-question">
                        <?php
                            $paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
                            $args  = array(
                                    'post_type' => array('question','poll'),
                                    'paged'     => $paged,
                                    's'         => $keyword
                                );

                            if( isset($_GET['numbers']) && $_GET['numbers'])
                                $args['posts_per_page'] = $_GET['numbers'];

                            $search_query = new WP_Query($args);
                            if($search_query->have_posts()){
                                while($search_query->have_posts()){
                                    $search_query->the_post();
                                    get_template_part( 'mobile/template/question', 'loop' );
                                }
                            } else {
                                echo '<h3>';
                                _e('No results for keyword:', ET_DOMAIN);
                                echo '<strong><em>'.esc_attr( $keyword ).'</em></strong>';
                                echo '</h3>';
                            }
                            wp_reset_query();
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!-- LIST QUESTION / END -->
    <?php if(!ae_get_option( 'qa_infinite_scroll' )){ ?>
    <section class="list-pagination-wrapper">
        <?php
            qa_template_paginations($search_query, $paged);
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