<?php
define("ET_UPDATE_PATH", "http://update.enginethemes.com/?do=product-update");

define('ET_VERSION', '2.0.12');

if(!defined('ET_URL'))
	define('ET_URL', 'http://www.enginethemes.com/');

if(!defined('ET_CONTENT_DIR'))
	define('ET_CONTENT_DIR', WP_CONTENT_DIR.'/et-content/');

define( 'TEMPLATEURL', get_template_directory_uri() );
define( 'THEME_NAME' , 'qaengine');
define( 'ET_DOMAIN'  , 'enginetheme');

if(!defined('THEME_CONTENT_DIR ')) 	define('THEME_CONTENT_DIR', WP_CONTENT_DIR . '/et-content' . '/qaengine' );
if(!defined('THEME_CONTENT_URL'))	define('THEME_CONTENT_URL', content_url() . '/et-content' . '/qaengine' );

// theme language path
if(!defined('THEME_LANGUAGE_PATH') ) define('THEME_LANGUAGE_PATH', THEME_CONTENT_DIR.'/lang/');

if(!defined('ET_LANGUAGE_PATH') )
 define('ET_LANGUAGE_PATH', THEME_CONTENT_DIR . '/lang');

if(!defined('ET_CSS_PATH') )
 define('ET_CSS_PATH', THEME_CONTENT_DIR . '/css');

// if (!defined('USE_SOCIAL')) define('USE_SOCIAL', 1);

require_once TEMPLATEPATH.'/includes/index.php';
require_once TEMPLATEPATH.'/mobile/functions.php';

try {
	// if(USE_SOCIAL){
 //        init_social_login();
 //    }

	if ( is_admin() ){
		new QA_Admin();
	} else {
		new QA_Front();
	}
} catch (Exception $e) {
	echo $e->getMessage();
}
add_filter('gettext', 'et_translate_text' , 99, 3);
function et_translate_text ( $translated_text, $text, $domain ) {
	$translation = array (
		'YOU MUST <a class="authenticate" href="%s">LOGIN</a> TO SUBMIT A REVIEW' => 'YOU HAVE TO <a class="authenticate" href="%s">SIGNIN</a> TO CREATE A REVIEW',
	);
	if( isset( $translation[$text] ) ) {
		return $translation[$text];
	}
	return $translated_text;
}
//remove_action('admin_init', 'block_dashboard');

// Redirect to page Intro when register by the "Back door" (/wp-login.php?action=register)
if(is_multisite()){
	add_filter( 'wp_signup_location', 'my_custom_signup_location' );
	function my_custom_signup_location( $url ) {
	    return et_get_page_link('intro');
	}
}else{
	add_filter( 'registration_redirect', 'wpse_46848_hijack_the_back' );
	function wpse_46848_hijack_the_back( $redirect_to ) {
	    wp_redirect( et_get_page_link('intro') );
	    return exit();
	}
}

/**
 * Add custom metadata to user
 * @param array $meta_data
 * @return array $meta_data
 *
 * @since 1.5.4
 * @author tatthien
 */
function add_user_metakey( $meta_data ) {
	$meta_data = wp_parse_args( array( 'qa_point' ), $meta_data );
	return $meta_data;
}

add_filter( 'ae_define_user_meta', 'add_user_metakey' );

/**
 * Add styles for user point input in admin setting
 * @param void
 * @return void
 *
 * @since 1.5.4
 * @author tatthien
 */
function add_styles_for_user_point_setting() {
	$current_screen = get_current_screen();

	// Instert styles for memeber setting page only
	if( $current_screen->base == 'engine-settings_page_et-users' ) {
		?>
		<style>
			.et-act {
				width: auto;
			}

			.user-points {
				display: inline-block;
	    		width: 120px;
			}

			.user-points input {
				max-width: 50%;
			    text-align: center;
			    width: auto;
			    height: 28px;
			    position: relative;
			    top: 1px;
			}
		</style>
		<?php
	}
}

add_action( 'admin_head', 'add_styles_for_user_point_setting' );

/**
 * Add js template to members section of admin setting
 * @param void
 * @return void
 *
 * @since 1.5.4
 * @author tatthien
 */
function add_admin_user_js_template() {
	?>
	<span class="user-points">
		<input type="text" value="{{= qa_point }}" class="regular-input" name="qa_point" /> <?php _e('Points', ET_DOMAIN) ?>
	</span>
	<?php
}

add_action( 'ae_admin_user_js_template_action', 'add_admin_user_js_template' );

/**
 * Add schema for thumbnail - itemprop="image"
 * @param array $attr
 * @param int $attachment
 * @param string $size
 * @return array $attr
 *
 * @since 1.5.6
 * @author tatthien
 */
function add_itemprop_to_post_thumbnail($attr, $attachment, $size) {
	$attr = wp_parse_args($attr, array('itemprop'=>'image'));
	return $attr;
}

add_filter( 'wp_get_attachment_image_attributes', 'add_itemprop_to_post_thumbnail', 10, 3);

/**
 * Add schema itemlist for questions list
 * @param void
 * @return void
 *
 * @since 1.5.6
 * @author tatthien
 */
if(!function_exists('add_schema_for_question_list')) {
	function add_schema_for_question_list() {
		$current_url = qa_get_current_url();
		echo '<link itemprop="url" href="'. $current_url .'" style="display: none;"/>';
	}
}

add_action('qa_top_questions_listing', 'add_schema_for_question_list');

/**
 * Get current url
 * @param void
 * @return string $current_url
 *
 * @since 1.5.6
 * @author tatthien
 */
if(!function_exists('qa_get_current_url')) {
	function qa_get_current_url() {
		global $wp;
		$current_url = home_url(add_query_arg(array(),$wp->request));
		return $current_url;
	}
}

if (!function_exists('et_get_customization')) {

	/**
	 * Get and return customization values for
	 * @since 2.0
	 */
	function et_get_customization() {
		$style = get_option('ae_theme_customization', true);
		$style = wp_parse_args($style, array(
			'background' => '#ffffff',
			'header' => '#2980B9',
			'heading' => '#37393a',
			'text' => '#7b7b7b',
			'action_1' => '#8E44AD',
			'action_2' => '#3783C4',
			'project_color' => '#3783C4',
			'profile_color' => '#3783C4',
			'footer' => '#F4F6F5',
			'footer_bottom' => '#fff',
			'font-heading-name' => 'Raleway,sans-serif',
			'font-heading' => 'Raleway',
			'font-heading-size' => '15px',
			'font-heading-style' => 'normal',
			'font-heading-weight' => 'normal',
			'font-text-name' => 'Raleway, sans-serif',
			'font-text' => 'Raleway',
			'font-text-size' => '15px',
			'font-text-style' => 'normal',
			'font-text-weight' => 'normal',
			'font-action' => 'Open Sans, Arial, Helvetica, sans-serif',
			'font-action-size' => '15px',
			'font-action-style' => 'normal',
			'font-action-weight' => 'normal',
			'layout' => 'content-sidebar'
		));
		return $style;
	}
}

//Add wp color picker
function qa_add_wp_color_picker() {
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script(
		'iris',
		admin_url( 'js/iris.min.js' ),
		array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ),
		false,
		1
	);
	wp_enqueue_script(
		'wp-color-picker',
		admin_url( 'js/color-picker.min.js' ),
		array( 'iris' ),
		false,
		1
	);
	$colorpicker_l10n = array(
		'clear' => __( 'Clear' ),
		'defaultString' => __( 'Default' ),
		'pick' => __( 'Select Color' )
	);
	wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n );

}
add_action( 'wp_enqueue_scripts', 'qa_add_wp_color_picker', 100 );

if(!function_exists('qa_format_number')) {
	/**
	 * Convert long number to K, M. Eg: 1000 -> 1K, 1000.000 -> 1M
	 * @param type $number
	 * @return type
	 * @since 2.0
	 * @author tatthien
	 */
	function qa_format_number($number) {
		$number_fomart = 0;

		if(!empty($number)) {
			if($number < 1000) { // Anything less than 1 thousand
				$number_fomart = number_format($number);
			} else if($number < 1000000) { // Anything less than 1 milion
				$number_fomart = number_format($number / 1000) . 'K';
			} else if($number < 1000000000) { // Anything less than 1 billion
				$number_fomart = number_format($number / 1000000) . 'M';
			} else {
				$number_fomart = number_format($number / 1000000000) . 'B';
			}
		}

		return $number_fomart;
	}
}

if(!function_exists('qa_email_notify')) {
	/**
	 * Send email notify to user
	 * @param ojbject $user
	 * @param string $subject
	 * @param string $message
	 * @param string $header
	 * @return void
	 */
	function qa_email_notify($to_user, $subject, $message) {
		$user_email = $to_user->user_email;

		$message = str_ireplace('[display_name]', $to_user->display_name, $message);
		$message = str_ireplace('[blogname]', html_entity_decode(get_option('blogname')), $message);
		$message = str_ireplace('<br>', 'replace', $message);

		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= "From: ".html_entity_decode(get_option('blogname'))." < ".ae_get_option('send_mail_from', 'no-reply@enginethemes.com')."> \r\n";
		// Send email
		wp_mail($user_email, $subject , $message, $headers) ;
	}
}
/*Content width*/
if ( ! isset( $content_width ) ) $content_width = 970;
/*After setup*/
if ( ! function_exists( 'et_theme_setup' ) ) :
function et_theme_setup(){
	add_theme_support( 'title-tag' );
}
endif;
add_action( 'after_setup_theme', 'et_theme_setup' );

/*  Add responsive container to embeds
/* ------------------------------------ */ 
function alx_embed_html( $html ) {
    return '<div class="video-container">' . $html . '</div>';
}
 
add_filter( 'embed_oembed_html', 'alx_embed_html', 10, 3 );
add_filter( 'video_embed_html', 'alx_embed_html' ); // Jetpack

function ae_verify_captcha($captcha){
    if(ae_get_option('gg_captcha', false) && ae_get_option('gg_secret_key')){
        //check google recaptcha
        $response = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret=".ae_get_option('gg_secret_key')."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
        $response = json_decode(wp_remote_retrieve_body($response));
        if(!$response->success){
            throw new Exception(__('Please enter a valid captcha!', ET_DOMAIN) , 401);
        }
    }
}

/**
 * Set pre get post if page author
 * @param $query
 * @author Dang Bui
 */

add_action( 'pre_get_posts','custom_author_archive' );
function custom_author_archive( $query ) {
	global $flag_query;
	if(is_author()) {
		if(empty($flag_query))
			$query->set('post_status', array('publish', 'pending'));

		if((isset($_GET['type']) && $_GET['type'] == 'following') && empty($flag_query)) {
			$query->set('post_type', array('question', 'poll'));
			$query->set('post_status', 'publish');
		}
	}

	return $query;
}