<?php /*
et_get_mobile_header();
global $wp_query, $wp_rewrite, $current_user;

$user = QA_Member::convert($current_user);

?>
<!-- CONTAINER -->
<div class="wrapper-mobile">

    <!-- MIDDLE BAR -->
    <section class="middle-bar bg-white">
    	<div class="container">
            <div class="row">
            	<div class="col-md-12">
                	<ul class="menu-middle-bar">
                        <li class="<?php if(!isset($_GET['type'])) echo 'active'; ?>" >
                            <a href="<?php echo  et_get_page_link('author-setting'); ?>"><?php _e('Change Profile',ET_DOMAIN) ?></a>
                        </li>
                        <li class="<?php if(isset($_GET['type']) && $_GET['type'] == "change-password") echo 'active'; ?>" >
                            <a href="<?php echo esc_url(add_query_arg(array('type'=>'change-password'))); ?>"><?php _e('Change Password',ET_DOMAIN) ?></a>
                        </li>
                    </ul>
                </div>
    		</div>
        </div>
    </section>
    <!-- MIDDLE BAR / END -->

   
    <section class="change-password-question-wrapper">
    	<?php if(!isset($_GET['type'])){ ?>
    		<!-- CHANGE PROFILE -->
			<form id="change_profile"  class="form_update" action="">
		    	<div class="col-md-12 change_profile">
		    		<div class="form-post">
		    			<label class="form-title"><?php _e('Full name',ET_DOMAIN);?></label>
		    			<input class="submit-input" type="text" name="display_name" id="display_name" value="<?php echo $user->display_name;?>"/>
		    		</div>
		    		<div class="form-post">
		    			<label class="form-title"><?php _e('Location',ET_DOMAIN);?></label>
		    			<input class="submit-input" type="text" name="user_location" id="user_location" value="<?php echo $user->user_location;?>"/>
		    		</div>
		    		<div class="form-post">
		    			<label class="form-title"><?php _e('Facebook',ET_DOMAIN);?></label>
		    			<input class="submit-input" type="text" name="user_facebook" id="user_facebook" value="<?php echo $user->user_facebook;?>"/>
		    		</div>
		    		<div class="form-post">
		    			<label class="form-title"><?php _e('Twitter',ET_DOMAIN);?></label>
		    			<input class="submit-input" type="text" name="user_twitter" id="user_twitter" value="<?php echo $user->user_twitter;?>"/>
		    		</div>
		    		<div class="form-post">
		    			<label class="form-title"><?php _e('Google+',ET_DOMAIN);?></label>
		    			<input class="submit-input" type="text" name="user_gplus" id="user_gplus" value="<?php echo $user->user_gplus;?>"/>
		    		</div>
		    		<div class="form-post">
		    			<label class="form-title"><?php _e('Email',ET_DOMAIN);?></label>
		    			<input class="submit-input" type="text" name="user_email" id="user_email" placeholder="<?php _e('admin@local.com',ET_DOMAIN);?>" value="<?php echo $user->user_email;?>"/>
		    		</div>
		    		<div class="form-post">
			    		<input type="checkbox" name="show_email" id="show_email" <?php echo ($user->show_email == "on") ? 'checked':'' ;?>>
			    		<label for="for="show_email""><?php _e('Make this email public.',ET_DOMAIN);?></label>
			    	</div>
		    		<div class="form-post">
		    			<label class="form-title"><?php _e('Description',ET_DOMAIN);?></label>
		    			<textarea name="description" id="description" cols="30" rows="10" placeholder="Your Description"> <?php echo $user->description;?></textarea>
		    		</div>
		    		<div class="form-post">
		    			<input type="submit" name="submit" value="<?php _e('Change Profile', ET_DOMAIN);?>" class="btn-submit update-profile"/>
		    		</div>
		    	</div>
		    </form>
		     <!-- CHANGE PROFILE / END -->
    	<?php } else { ?>
    		 <!-- CHANGE PASSWORD -->
    		<form id="change_password" class="form_update" action="">
		    	<div class="col-md-12 change_password">
		    		<div class="form-post">
		    			<label class="form-title"><?php _e('Old Password',ET_DOMAIN);?></label>
		    			<input class="submit-input" type="password" name="old_password" id="old_password"/>
		    		</div>
		    		<div class="form-post">
		    			<label class="form-title"><?php _e('New Password',ET_DOMAIN);?></label>
		    			<input class="submit-input" type="password" name="new_password" id="new_password"/>
		    		</div>
		    		<div class="form-post">
		    			<label class="form-title"><?php _e('Repeat New Password',ET_DOMAIN);?></label>
		    			<input class="submit-input" type="password" name="re_password" id="re_password"/>
		    		</div>
		    		<div class="form-post">
		    			<input type="submit" name="submit" value="<?php _e('Change Password', ET_DOMAIN); ?>" class="btn-submit update-profile"/>
		    		</div>
		    	</div>
		    </form>
		     <!-- CHANGE PASSWORD / END -->
    	<?php } ?>
    </section>
   
</div>
<!-- CONTAINER / END -->
<?php
et_get_mobile_footer();