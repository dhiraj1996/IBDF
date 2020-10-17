<?php
    global $post;
?>
<div  itemprop="itemListElement" itemscope itemType="http://schema.org/ListItem" class="blog-wrapper">
    <div itemprop="item" itemscope itemtype="http://schema.org/BlogPosting" class="row">
        <div class="col-md-3 col-xs-3">
            <div class="post-thumbnail">
                <a href="<?php the_permalink(); ?>">
                    <?php if(has_post_thumbnail()) {
                        the_post_thumbnail( 'full' );
                    } else { ?>
                        <img src="<?php echo get_template_directory_uri() . '/img/default-thumbnail.jpg' ?>" alt="default thumbnail" itemprop="image">
                    <?php } ?>
                </a>
            </div>
        </div>
        <div class="col-md-8 col-xs-8">
            <div class="blog-content">
                <!-- Post Info -->
                <div class="blog-info">
                    <span itemprop="author" itemscope itemtype="http://schema.org/Person">
                        <span itemprop="name" class="author" ><?php the_author();?></span>
                    </span>
                    <span itemprop="datePublished" content="<?php the_time(get_option('date_format')); ?>"><?php the_time('M j');  ?> <sup><?php the_time('S');?></sup>, <?php the_time('Y');?></span>
                    <span class="tag">
                        <?php the_category( '-' ); ?>
                    </span>
                    <span class="cmt">
                        <i class="fa fa-comments"></i><?php comments_number(); ?>
                    </span>
                </div>
                <!-- End / Post Info -->
                <h2 itemprop="headline" class="title-blog"><a itemprop="url" href="<?php the_permalink(); ?>"><?php the_title() ?></a></h2>
                <?php
                    if(is_single()){
                        echo '<div itemprop="articleBody">';
                        the_content();
                        wp_link_pages( array(
                            'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', ET_DOMAIN ) . '</span>',
                            'after'       => '</div>',
                            'link_before' => '<span>',
                            'link_after'  => '</span>',
                        ) );
                        echo '</div>';
                    } else {
                        echo '<div itemprop="description">';
                        the_excerpt();
                        echo '</div>';
                ?>
                <a href="<?php the_permalink(); ?>" class="read-more">
                    <?php _e("READ MORE",ET_DOMAIN) ?><i class="fa fa-arrow-circle-o-right"></i>
                </a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>