<?php
    /* STEP: SELECT PAYMENTS  */
?>
<div id="payment" class="step-wrapper step-payment select-payment-step hide">
    <ul>
        <form method="post" action="" id="checkout_form">
            <div class="payment_info"></div>
            <div style="position:absolute; left : -7777px; " >
                <input type="submit" id="payment_submit" />
            </div>
        </form>

        <?php
            $paypal = ae_get_option('paypal');
            if($paypal['enable']) {
        ?>
            <li>
                <span class="item-package" data-type="paypal"><?php _e("Paypal", ET_DOMAIN); ?></span>
                <p class="functions-package"><?php _e("Send your payment via Paypal.", ET_DOMAIN); ?></p>
                <button class="btn-select btn-submit-price-plan select-payment" data-type="paypal"><?php _e("Select", ET_DOMAIN); ?></button>
            </li>
        <?php } ?>
        <?php
            $co = ae_get_option('2checkout');
            if($co['enable']) {
        ?>
            <li>
                <span class="item-package" data-type="2checkout">
					<?php _e("2Checkout", ET_DOMAIN); ?>
                </span>
                <p class="functions-package"><?php _e("Send your payment via 2Checkout.", ET_DOMAIN); ?></p>
                <button class="btn-select btn-submit-price-plan select-payment" data-type="2checkout"><?php _e("Select", ET_DOMAIN); ?></button></li>
            <?php
        }
        $cash = ae_get_option('cash');
        if($cash['enable']) {
            ?>
            <li>
                <span class="item-package" data-type="cash">
                    <?php _e("Cash", ET_DOMAIN); ?>
                </span>
                <p class="functions-package">
                    <?php _e("Send your cash payment to our bank account", ET_DOMAIN); ?>
                </p>
                <button class="btn-select btn-submit-price-plan select-payment" data-type="cash"><?php _e("Select", ET_DOMAIN); ?></button>
            </li>
        <?php }
        do_action( 'after_payment_list' );
        ?>
    </ul>
</div>