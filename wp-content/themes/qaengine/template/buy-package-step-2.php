<?php
    /* STEP: AUTHENTICATION */
?>
<div id="authentication" class="step-wrapper step-auth authencation-step hide">
    <div id="login-form-buy-package">
        <div class="title-login"><?php _e("Don't have an account?", ET_DOMAIN); ?><a href="#" class="login-link btn-register"><?php _e('Register here', ET_DOMAIN); ?></a></div>
        <form id="pump_login_form" class="form-submit-author">
            <div class="form-group">
                <label for="username">
                    <?php _e("Username", ET_DOMAIN) ?>
                    <span class="note-title"><?php _e('Your username', ET_DOMAIN) ?></span>
                </label>
                <input type="text" class="email_user"  name="username" id="username" placeholder="<?php _e('Enter your username or email', ET_DOMAIN) ?>"/>
            </div>

            <div class="form-group">
                <label for="password">
                    <?php _e("Password", ET_DOMAIN) ?>
                    <span class="note-title"><?php _e('Enter password', ET_DOMAIN); ?></span>
                </label>
                <input type="password" class="password_user" id="password" name="password" placeholder="<?php _e('Enter your password', ET_DOMAIN) ?>" >
            </div>

            <div class="form-group">
                <input type="submit" name="submit" value="<?php _e("Login", ET_DOMAIN) ?>" class="btn-submit">
            </div>
        </form>
    </div>
    <div id="register-form-buy-package" class="hide">
        <div class="title-login">Already have an account ? <a href="#" class="login-link btn-login">Login</a></div>
        <form class="auth form-submit-author">
            <div class="form-group">
                <label>
                    Username
                    <span class="note-title"><?php _e('Your username', ET_DOMAIN) ?></span>
                </label>
                <input type="text" id="user_login" name="user_login" class="input-item" placeholder="<?php _e('Enter your username', ET_DOMAIN); ?>">
            </div>
            <div class="form-group">
                <label>
                    Email
                    <span class="note-title"><?php _e('Your email', ET_DOMAIN); ?></span>
                </label>
                <input type="email" id="user_email" name="user_email" class="input-item" placeholder="<?php _e('Enter your email', ET_DOMAIN); ?>">
            </div>
            <div class="form-group">
                <label>
                    password
                    <span class="note-title"><?php _e('Enter password', ET_DOMAIN); ?></span>
                </label>
                <input type="password" id="user_pass" name="user_pass" class="input-item" placeholder="<?php _e('Your password', ET_DOMAIN); ?>">
            </div>
            <div class="form-group">
                <label>
                    retype password
                    <span class="note-title"><?php _e('Retype password', ET_DOMAIN); ?></span>
                </label>
                <input type="password" id="repeat_password" name="repeat_password" class="input-item" placeholder="<?php _e('Your password', ET_DOMAIN); ?>">
            </div>

            <?php if(ae_get_option('gg_captcha')) { ?>
            <div class="form-group">
                <label for=""></label>
                <div class="gg-captcha">
                    <?php ae_gg_recaptcha(); ?>
                </div>
            </div>
            <?php } ?>

            <div class="form-group clearfix">
                <input type="submit" name="submit" value="<?php _e("Sign up", ET_DOMAIN) ?>" class="btn-submit">
            </div>

            <div class="clearfix"></div>

            <div class="form-group">
                <p class="policy-sign-up">
                    <?php
                    printf( __('By clicking "Sign up" you indicate that you have read and agree to the <a target="_blank" href="%s">privacy policy</a> and <a target="_blank" href="%s">terms of service.</a>', ET_DOMAIN), et_get_page_link('term'), et_get_page_link('term') );
                    ?>
                </p>
            </div>
        </form>
    </div>
</div>