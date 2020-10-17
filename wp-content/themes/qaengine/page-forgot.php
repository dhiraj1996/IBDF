<?php
if(!et_load_mobile()) {
    wp_redirect(home_url());
    return exit();
}