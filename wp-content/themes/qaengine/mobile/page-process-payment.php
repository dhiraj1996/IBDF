<?php
/**
 * Template Name: Process Payment
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

et_get_mobile_header();
?>
<div class="wrapper-mobile">
    <div class="top-bar bg-white">
        <div class="container">
            <span class="bar-title"><?php _e('Proccess Payment', ET_DOMAIN); ?></span>
        </div>
    </div>
    <div class="content-buy-package">
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
<?php
et_get_mobile_footer();
?>

