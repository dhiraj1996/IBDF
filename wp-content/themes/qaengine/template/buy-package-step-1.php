<?php
    /* STEP: SELECT PLANS */

    global $user_ID, $ae_post_factory;
    $package =  $ae_post_factory->get('pump_pack');
    $packs = $package->fetch('pump_pack');
?>
<div id="plan" class="step-wrapper step-plan select-plan-step">
    <ul class="list-price">
        <?php foreach($packs as $pack) { ?>
        <li data-id="<?php echo $pack->ID; ?>"  data-sku="<?php echo $pack->sku; ?>" data-price="<?php echo $pack->et_price; ?>">
            <span class="price">
                <?php
                    if($pack->et_price) {
                        ae_price($pack->et_price);
                    } else {
                        _e('Free', ET_DOMAIN);
                    }
                ?>
            </span>
            <span class="item-package"><?php echo $pack->post_title; ?></span>
            <?php echo $pack->post_content; ?>
            <button class="btn-select select-plan"><?php _e('Select', ET_DOMAIN); ?></button>
        </li>
        <?php } ?>
        <?php
            echo '<script type="data/json" id="package_plans">'. json_encode($packs) .'</script>';
        ?>
    </ul>
</div>