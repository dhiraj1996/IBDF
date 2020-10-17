<?php
/**
 * Template Name: Buy Package Template
 * version 1.0
 * @author: enginethemes
 **/
et_get_mobile_header();
?>
    <div class="wrapper-mobile buy-package">
        <div class="top-bar bg-white">
            <div class="container">
                <span class="bar-title"><?php _e('Buy Package', ET_DOMAIN); ?></span>
            </div>
        </div>
        <div class="tabs-buy-package <?php echo ($user_ID) ? 'is_login' : ''; ?>">
            <ul style="position: relative">
                <li class="active step-heading" data-id="plan"><a href="#plan">Select plan</a></li>
                <?php
                if(!$user_ID) {
                    ?>
                    <li class="step-heading" data-id="authentication"><a href="#authentication">Authencation</a></li>
                    <?php
                }
                ?>
                <li class="step-heading" data-id="payment"><a href="#payment">Payment</a></li>
                <li class="progress-bars"></li>
                <li class="finish-progress-bar"></li>
            </ul>
        </div>
        <div class="content-buy-package warpper-buy-package">
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
<?php
et_get_mobile_footer();
?>
