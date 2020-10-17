<?php
global $payment_return, $current_user;
extract($payment_return);
$payment_type = $_GET['paymentType'];
$pack = qa_get_package_by_sku($order->payment_plan);
$package_name = $pack->post_title;
$permalink = get_author_posts_url($current_user->ID);
?>
<div class="payment-success">
    <p class="thanks"><?php _e('Your payment has been successful. Thank you!', ET_DOMAIN); ?></p>
    <p class="sucess"><i class="fa fa-check"></i><?php printf(__('You have successfully purchased the package: %s', ET_DOMAIN), $package_name); ?></p>
    <?php
    if($payment_status == 'Pending')
        printf(__("Your payment has been sent successfully but is currently set as 'pending' by %s. <br/>You will be notified when your listing is approved.", ET_DOMAIN), $payment_type);
    if($payment_type == 'cash'){
        printf(__("%s ", ET_DOMAIN) , $response['L_MESSAAGE']);
    }
    ?>
    <p class="link"><?php printf(__('click <a href="%s">here</a> to see you pump package.', ET_DOMAIN), $permalink); ?></p>
</div>