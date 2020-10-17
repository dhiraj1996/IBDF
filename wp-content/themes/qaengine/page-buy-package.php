<?php
/**
 * Template Name: Buy Package Template
 * version 1.0
 * @author: enginethemes
 **/
global $user_ID;
get_header();
?>
<?php get_sidebar( 'left' ); ?>
<div class="col-md-8 main-blog-fix buy-package">
    <div class="col-md-12 inner-package">
        <h1 class="title-header"><?php _e('Buy Package', ET_DOMAIN); ?></h1>
        <div class="tabs-buy-package <?php echo ($user_ID) ? 'is_login' : ''; ?>">
            <ul style="position: relative">
                <li class="active step-heading" data-id="plan"><a href="#plan"><?php _e('Select plan', ET_DOMAIN) ?></a></li>
                <?php
                    if(!$user_ID) {
                        ?>
                        <li class="step-heading" data-id="authentication"><a href="#authentication"><?php _e('Authencation', ET_DOMAIN); ?></a></li>
                        <?php
                    }
                ?>
                <li class="step-heading" data-id="payment"><a href="#payment"><?php _e('Select payment method', ET_DOMAIN); ?></a></li>
                <li class="progress-bars"></li>
                <li class="finish-progress-bar"></li>
            </ul>
        </div>
        <div class="warpper-buy-package">
            <?php
                // Step 1
                get_template_part('template/buy-package', 'step-1');

                // Step 2
                if(!$user_ID) {
                    get_template_part('template/buy-package', 'step-2');
                }

                // Step 3
                get_template_part('template/buy-package', 'step-3');
            ?>
        </div>
    </div>
</div>
<?php get_sidebar( 'right' ); ?>
<?php get_footer() ?>
