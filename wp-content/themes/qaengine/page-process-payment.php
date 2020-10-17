<?php
/**
 * Template Name: Payment Success Template
 * version 1.0
 * @author: enginethemes
 **/
global $current_user, $ae_post_factory;

if(!$current_user->ID) {
    wp_redirect(home_url());
}

$payment_type = $_GET['paymentType'];
$session	=	et_read_session ();
$payment_return = ae_process_payment($payment_type , $session );
global $ad , $payment_return;
$payment_return	=	wp_parse_args( $payment_return, array('ACK' => false, 'payment_status' => '' ));
extract( $payment_return );

get_header();
?>
<?php get_sidebar( 'left' ); ?>
<div class="col-md-8 main-blog-fix buy-package">
    <div class="col-md-12">
        <?php
        if(isset($ACK) && $ACK) {
            get_template_part('template/payment', 'success');
        } else {
            get_template_part('template/payment', 'fail');
        }
         et_destroy_session();
        ?>
    </div>
</div>
<?php get_sidebar( 'right' ); ?>
<?php get_footer() ?>
