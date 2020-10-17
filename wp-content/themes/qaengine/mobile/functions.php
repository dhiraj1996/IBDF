<?php
define( 'MOBILE_PATH', dirname(__FILE__) );
/**
*
* TEMPLATE FILTER QUESTIONS FOR MOBILE
* @author ThaiNT
* @since 1.0
*
**/
if( !function_exists('qa_mobile_submit_questions_form')){
function qa_mobile_submit_questions_form(){
	global $current_user;
	$role = get_user_role($current_user->ID);
	$privi  =   qa_get_privileges();
	?>
	<section class="post-question-form-wrapper">
		<div class="container no-padding">
        	<div class="tabs-title">
            	<ul>
            		<li><span class="title-bar-tab active" id="tab-question"><?php _e('Ask a Question', ET_DOMAIN);?></span></li>
            		<?php if(ae_get_option('poll_maker') !=="0") { ?>
            		<li><span class="title-bar-tab" id="tab-poll"><?php _e('Create a Poll', ET_DOMAIN);?></span></li>
            		<?php } ?>
            	</ul>
            </div>
            <div class="form-active-question">
            	<div class="body-question" data-name="question">
					<form id="submit_question" action="">
						<input type="hidden" id="qa_nonce" name="qa_nonce" value="<?php echo wp_create_nonce( 'insert_question' ); ?>">
						<input id="add_tag_text" type="hidden" value="<?php printf(__("You must have %d points to add tag. Current, you have to select existing tags.", ET_DOMAIN), $privi->create_tag  ); ?>" />
						<div class="form-post">
							<input type="text" name="post_title" id="question_title" placeholder="<?php _e("Your Question", ET_DOMAIN) ?>">
							<span id="charNum"><?php echo ae_get_option('max_width_title', 150);?></span>
						</div>
						<div class="form-post">
							<div class="select-categories-wrapper">
								<div class="select-categories">
									<select class="select-grey-bg" id="" name="question_category">
										<option value=""><?php _e("Select Category",ET_DOMAIN) ?></option>
										<?php
											$terms = get_terms( 'question_category', array('hide_empty' => 0, 'orderby'    => 'term_group' ) );
											$value_type = 'slug';
											foreach ($terms as $term) {
												if($term->parent == 0){
													if($value_type == 'slug') {
														echo "<option value='".$term->slug."'>";
													} elseif($value_type == 'id') {
														echo "<option value='".$term->term_id."'>";
													}
													echo $term->name;
													echo "</option>";
													foreach ($terms as $value) {
														if($term->term_id == $value->parent){
															if($value_type == 'slug') {
																echo "<option value='".$value->slug."'>";
															} elseif($value_type == 'id') {
																echo "<option value='".$value->term_id."'>";
															}
																echo "--".$value->name;
															echo "</option>";
														}
													}
												}
											}
										?>
									</select>
								</div>
							</div>
						</div>
						<?php if(ae_get_option('ae_upload_images')){ ?>
						<div class="form-post container_upload">
							<div class="upload_image" id="mobile_images_upload_container">
								<a href="javascript:void(0)" id="mobile_images_upload_browse_button"><i class="fa fa-image"></i><?php _e("Insert Image", ET_DOMAIN); ?></a>
								<span class="et_ajaxnonce" id="<?php echo wp_create_nonce( 'et_upload_images' ); ?>"></span>
								<span id="mobile_images_upload_text">
								<?php
								global $max_file_size;
								printf(__("Size must be less than < %sMB.", ET_DOMAIN),$max_file_size);
								?>
								</span>
							</div>
						</div>
						<?php } ?>
						<div class="form-post">
							 <?php wp_editor( '', 'post_content', editor_settings_mobile() );?>
							<!-- <textarea name="post_content" id="" cols="30" rows="10" placeholder="<?php _e("Your Description", ET_DOMAIN) ?>"></textarea> -->
						</div>
						<div class="form-post">
							<input  data-provide="typeahead" type="text" name="" id="question_tags" placeholder="<?php _e('Tag(max 5 tags)',ET_DOMAIN) ?>" />
						</div>
						<ul class="post-question-tags" id="tag_list"></ul>
						<?php if(ae_get_option('gg_question_captcha') && $role != 'administrator'){ ?>
							<div class="clearfix"></div>
							<div class="container_captcha">
								<div class="gg-captcha form-group">
								<?php ae_gg_recaptcha(); ?>
								</div>
							</div>
						<?php } ?>
						<div class="group-btn-post">
							<div class="container">
								<div class="row">
									<div class="col-xs-5 no-pad-left"><span class="text"><?php _e("Ask a Question", ET_DOMAIN) ?></span></div>
									<div class="col-xs-7 text-right">
										<button type="submit" class="submit-post-question"><?php _e("Submit", ET_DOMAIN) ?></button>
										<a href="javascript:void(0)" class="cancel-post-question"><?php _e("Cancel", ET_DOMAIN) ?></a>
									</div>
								</div>
							</div>
						</div>
					</form>
            	</div>
				<?php
				/**
				 * Render submit form for poll
				 */
					qa_poll_submit_form();
				?>
            	</div>
            </div>
	    </div>
	</section>
	<?php
}
}
if( !function_exists('editor_settings_mobile')){
	function editor_settings_mobile(){
		return array(
	        'quicktags' => false,
	        'media_buttons' => false,
	        'wpautop' => false,

	        //'tabindex'    =>  '2',
	        'teeny' => true,
	        'tinymce' => array(
	            'content_css' => ae_get_url() . '/assets/css/tinyeditor-content.css',
	            'height' => 200,
	            'editor_class' => 'input-item',
	            'autoresize_min_height' => 250,
	            'autoresize_max_height' => 550,
	            'theme_advanced_buttons1' => 'bold,|,italic,|,underline,|,bullist,numlist,|,link,unlink,|,wp_fullscreen',
	            'theme_advanced_buttons2' => '',
	            'theme_advanced_buttons3' => '',
	            'theme_advanced_statusbar_location' => 'none',
	            'theme_advanced_resizing' => true,
	            'paste_auto_cleanup_on_paste' => true,
	            'setup' => "function(ed){
	                ed.onChange.add(function(ed, l) {
	                    var content = ed.getContent();
	                    if(ed.isDirty() || content === '' ){
	                        ed.save();
	                        jQuery(ed.getElement()).blur(); // trigger change event for textarea
	                    }

	                });

	                // We set a tabindex value to the iframe instead of the initial textarea
	                ed.onInit.add(function() {
	                    var editorId = ed.editorId,
	                        textarea = jQuery('#'+editorId);
	                    jQuery('#'+editorId+'_ifr').attr('tabindex', textarea.attr('tabindex'));
	                    textarea.attr('tabindex', null);
	                });
	            }"
	        )
	    );
	}
}
/**
*
* TEMPLATE FILTER QUESTIONS FOR MOBILE
* @param array
* @author ThaiNT
* @since 1.0
*
**/
if( !function_exists('qa_mobile_filter_search_questions')){
function qa_mobile_filter_search_questions(){
	$current_url = "http".(isset($_SERVER['HTTPS']) ? 's' : '')."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$queried_object = get_queried_object();
	if(is_front_page())
		$current_url = get_home_url();
	else if(is_tax('question_category'))
		$current_url = get_term_link(get_query_var('question_category'),'question_category');
	else if(is_tax('qa_tag'))
		$current_url = get_term_link(get_query_var('qa_tag'),'qa_tag');
	else if($queried_object->has_archive=="questions")
		$current_url = "questions";
	else if($queried_object->has_archive=="polls")
		$current_url = "polls";
	// else
	// 	$current_url = $current;
	?>
	<div class="container">
	    <div class="row">
	    	<div class="col-md-12">
	        	<ul class="menu-middle-bar">
	                <li class="<?php echo !isset($_GET['sort']) ? 'active' : ''; ?>">
	                    <a href="<?php echo remove_query_arg( array('sort') ,$current_url); ?>">
	                    	<?php _e("Latest",ET_DOMAIN) ?>
	                    </a>
	                </li>
	                <li class="<?php echo isset($_GET['sort']) && $_GET['sort'] == 'vote' ? 'active' : ''; ?>" >
	                    <a href="<?php echo $current_url."/?sort=vote"; ?>">
	                    	<?php _e("Votes",ET_DOMAIN) ?>
	                    </a>
	                </li>
	                <li class="<?php echo isset($_GET['sort']) && $_GET['sort'] == 'unanswer' ? 'active' : ''; ?>">
	                    <a href="<?php echo $current_url."/?sort=unanswer"; ?>">
	                    	<?php _e("Unanswered",ET_DOMAIN) ?>
	                    </a>
	                </li>
	            </ul>
	        </div>
		</div>
	</div>
	<div class="form-search-wrapper" <?php if(is_page_template( 'page-search.php' )){ ?>style="display:block;"<?php } ?>>
	    <?php
	        $keyword = get_query_var( 'keyword' ) ? urldecode(get_query_var( 'keyword' )) : '';
	        $keyword = str_replace('+', ' ', $keyword);
	    ?>
		<form id="form-search" method="POST" action="<?php echo home_url(); ?>">
	        <input type="text" class="form-input-search" required autocomplete="off" name="keyword" value="<?php echo esc_attr($keyword) ?>" placeholder="<?php _e("Enter Keywords",ET_DOMAIN) ?>" />
	    </form>
	</div>
	<?php
}
}
/**
*
* TEMPLATE FORM COMMENTS FOR MOBILE
* @param array $comments
* @author ThaiNT
* @since 1.0
*
**/
function qa_mobile_comment_form( $post, $type = 'question' ){
	global $current_user;
?>
<form class="form-post-answers create-comment collapse">
    <input type="hidden" name="qa_nonce"        value="<?php echo wp_create_nonce( 'insert_comment' );?>" />
    <input type="hidden" name="comment_post_ID" value="<?php echo $post->ID ?>" />
    <input type="hidden" name="comment_type"    value="<?php echo $type ?>" />
    <input type="hidden" name="user_id"         value="<?php echo $current_user->ID ?>" />
	<textarea name="post_content" id="post_content" rows="4" placeholder="<?php _e("Type your comment", ET_DOMAIN)?> "></textarea>
	<input type="submit" class="btn-submit" name="submit" id="" value="<?php _e("Add comment", ET_DOMAIN)?>">
	<a href="javascript:void(0)" class="close-form-post-answers"><?php _e("Cancel", ET_DOMAIN)?></a>
</form>
<?php
}
/**
*
* TEMPLATE LOOP FOR MOBILE COMMENTS
* @param array $comments
* @author ThaiNT
* @since 1.0
*
**/
function qa_mobile_comments_loop($child){
	global $qa_comment;
	$qa_comment = QA_Comments::convert($child);
	get_template_part( 'mobile/template/item' , 'comment' );
}

/**
*
* JS TEMPLATE ANSWER
* @param array $comments
* @author ThaiNT
* @since 1.0
*
**/
function qa_mobile_answer_template(){
	// get template-js/item-answer.php
	get_template_part( 'mobile/template-js/item', 'answer' );
}
function qa_mobile_comment_template(){
	// get template-js/item-answer.php
	get_template_part( 'mobile/template-js/item', 'comment' );
}

function qa_mobile_member_contact_template(){
	// get template-js/item-answer.php
	get_template_part( 'mobile/template/modal', 'contact' );
}

function qa_mobile_report_template(){
	// get template-js/item-answer.php
	get_template_part( 'mobile/template/modal', 'report' );
}
/**
 * New Script in Mobile Footer
 */
if ( et_load_mobile()  ){
add_action( 'wp_footer', 'scripts_in_footer_mobile', 100);
}
function scripts_in_footer_mobile(){
?>
<script type="text/javascript" id="frontend_scripts">
	(function ($) {
		$(document).ready(function(){
			if(typeof QAEngine.Views.MobileFront != 'undefined') {
				QAEngine.MobileApp = new QAEngine.Views.MobileFront();
			}
			if(typeof QAEngine.Views.MobileSingleQuestion != 'undefined') {
				QAEngine.MobileSingleQuestion = new QAEngine.Views.MobileSingleQuestion();
			}
			if(typeof QAEngine.Views.MobileSingleAnswer != 'undefined') {
				QAEngine.MobileSingleAnswer = new QAEngine.Views.MobileSingleAnswer();
			}
			if(typeof QAEngine.Views.ChangeProfileModal != 'undefined') {
				QAEngine.MobileApp = new QAEngine.Views.ChangeProfileModal();
			}
			if(typeof QAEngine.Views.MobileSinglePoll != 'undefined') {
				QAEngine.MobileSinglePoll = new QAEngine.Views.MobileSinglePoll();
			}
		});
	})(jQuery);
</script>
<?php
}
/**
 * Enqueue Styles & Scripts
 */
if ( et_load_mobile()  ){
add_action( 'wp_enqueue_scripts', 'qa_mobile_scripts_styles' );
}
function qa_mobile_scripts_styles(){
	/* ==== PRINT SCRIPTS ==== */
	wp_enqueue_script('mobile-front', 	TEMPLATEURL . '/mobile/js/front.js', array('jquery', 'underscore', 'backbone', 'site-functions', 'appengine'));
	wp_enqueue_script('mobile-pumping', 		TEMPLATEURL . '/js/pumping.js', array('jquery', 'underscore', 'backbone', 'appengine', 'mobile-front'));
	wp_enqueue_script('mouseweel', 		TEMPLATEURL . '/mobile/js/jquery.mouseweel.js', array('jquery'));
	wp_enqueue_script('mobile-script', 	TEMPLATEURL . '/mobile/js/script-mobile.js', array('jquery'));
	wp_enqueue_script('mobile-script', 	TEMPLATEURL . '/js/libs/adjector.js', array('jquery'));

	wp_enqueue_script('date-picker-moment', 		TEMPLATEURL . '/mobile/js/moment.min.js', array('jquery', 'underscore', 'backbone', 'site-functions'));
	wp_enqueue_script('date-picker', 		TEMPLATEURL . '/mobile/js/bootstrap-datetimepicker.min.js', array('jquery', 'underscore', 'backbone', 'site-functions','date-picker-moment'));

	//localize scripts
	wp_localize_script( 'mobile-front', 'qa_front', qa_static_texts() );

	if(is_singular( 'question' )){
		wp_enqueue_script('mobile-single-question', 	TEMPLATEURL . '/mobile/js/single-question.js', array('jquery', 'underscore', 'backbone', 'site-functions','mobile-front'));
	}
	if(is_singular( 'answer' )){
		wp_enqueue_script('mobile-single-answer', 	TEMPLATEURL . '/mobile/js/single-answer.js', array('jquery', 'underscore', 'backbone', 'site-functions','mobile-front'));
	}
	if(is_singular( 'poll' )){
		wp_enqueue_script('mobile-single-poll', 	TEMPLATEURL . '/mobile/js/single-poll.js', array('jquery', 'underscore', 'backbone', 'site-functions','mobile-front'));
	}
	/* ==== PRINT STYLES ==== */
	wp_enqueue_style( 'mobile-style', 	TEMPLATEURL . '/mobile/css/main.css', array('bootstrap') );
	wp_enqueue_style( 'date-picker-style', 	TEMPLATEURL . '/mobile/css/bootstrap-datetimepicker.min.css', array('bootstrap') );
}

/**
 * Redirect to reset password page
 * @since 1.6
 * @author tatthien
 */
if(et_load_mobile()) {
	if(isset($_GET['user_login']) && isset($_GET['key']) && !is_user_logged_in()) {
		wp_redirect(et_get_page_link('reset-pass', array(
			'user' => $_GET['user_login'],
			'key' => $_GET['key']
		)));
		return exit();
	}
}