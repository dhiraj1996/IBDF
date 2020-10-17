<?php

class QA_Engine extends AE_Base{
	// declare post_types, scripts, styles ... which are uses in theme
	function __construct(){
		//parent::__construct();
		global $current_user;

		// disable admin bar if user can not manage categories
		if (!current_user_can('manage_categories')){
			show_admin_bar(false);
		};

		// register tag
		$this->add_action('init', 'init_theme');
		//block dashboard
		$this->add_action('admin_init', 'block_dashboard');

		//filter email message template
		$this->add_filter('et_reset_password_link'			, 'reset_password_link', 10, 3);
		$this->add_filter('et_retrieve_password_message'	, 'retrieve_password_message', 10, 3);
		$this->add_action('et_after_register'				, 'user_register_mail', 20 , 2);
		if(ae_get_option('qa_new_member_email'))
			$this->add_action('et_after_register'			, 'user_register_mail_admin', 20 , 2);
		$this->add_action('qa_accept_answer'				, 'qa_after_accept_answer', 10, 2);
		$this->add_action('et_after_register'				, 'default_user_meta', 10 , 2);
		//hook after create user to set default meta data
		$this->add_action('user_register'					, 'default_user_meta', 10, 2);
		$this->add_action('et_password_reset'				, 'password_reset_mail', 10, 2);
		$this->add_action('widgets_init'					, 'et_widgets_init');
		$this->add_action('after_switch_theme'				, 'set_default_theme', 500);
		$this->add_filter('user_search_columns'				, 'user_search_columns_bd' , 10, 3);

		$this->add_filter('excerpt_length'					, 'qa_excerpt_length' );
		$this->add_filter('excerpt_more'					, 'qa_excerpt_more' );

		if( ae_get_option('qa_live_notifications') ){

		$this->add_filter( 'heartbeat_settings'			, 'change_hearbeat_rate');
		$this->add_filter( 'heartbeat_send'				, 'send_data_to_heartbeat', 10, 2 );
		$this->add_action( 'et_insert_question'			, 'store_new_question_to_DB');

		}
		if(is_admin()){
			// update postmeta in backend
			$this->add_action( 'save_post_question' 		, 'save_post_meta_backend', 11, 2);
		}
		$this->add_action( 'et_insert_question'			, 'alert_pending_question_to_admin' );

		$this->add_action( 'et_insert_question'			, 'save_following_questions');
		$this->add_action( 'et_insert_answer'			, 'save_following_questions' );
		//$this->add_action( 'ae_admin_user_action' 		, 'add_user_actions_backend');
		$this->add_action( 'qa_send_following_mail' 	, 'mail_to_following_users' );
		$this->add_action( 'add_meta_boxes'				, 'add_post_meta_box' );
		$this->add_action( 'et_after_reported'			, 'et_reported_email', 10, 2 );
		$this->add_filter( 'wp_link_query_args'			, 'qa_tinymce_filter_link_query' );

		//add return field for user
		$this->add_filter( 'ae_convert_user'			, 'ae_convert_user' );
		$this->add_filter( 'image_send_to_editor'		, 'ae_give_linked_images_class', 10, 8);
		if(ae_get_option('qa_send_following_mail' ) && !ae_get_option("pending_answers"))
			$this->add_action( 'et_insert_answer'  ,'qa_questions_new_answer' );

		//short codes
		new QA_Shortcodes();

		// enqueue script and styles
		if ( is_admin() ){
			$this->add_action('admin_enqueue_scripts', 'on_add_scripts');
			$this->add_action('admin_print_styles', 'on_add_styles');
		} else {
			$this->add_action('wp_enqueue_scripts', 'on_add_scripts');
			$this->add_action('wp_print_styles', 'on_add_styles');
		}
		/* === filter bad words === */
		$this->add_filter( 'the_content', 'ae_filter_badword' );
		$this->add_filter( 'the_title', 'ae_filter_badword' );
		$this->add_filter( 'comment_text', 'ae_filter_badword' );
		$this->add_filter( 'qa_wp_title', 'ae_filter_badword' );
		/**
		 * bind ajax to get tag json for autocomplete tag in modal add/edit question
		*/
		$this->add_ajax('qa_get_tags' , 'qa_get_tags');

		/**
		 * load text domain
		*/
		add_action('after_setup_theme', array ( 'AE_Language' ,'load_text_domain' ) );

		$this->add_filter( 'ae_globals', 'ae_add_qa_globals' );
	}
	function save_post_meta_backend ($post_ID, $post){
		add_post_meta($post_ID, 'et_pump_time', time(), true);
		add_post_meta($post_ID, 'et_vote_count', 0, true);
		add_post_meta($post_ID, 'et_view_count', 0, true);
		add_post_meta($post_ID, 'et_users_follow', 0, true);
		add_post_meta($post_ID, 'et_new_post', 0, true);
		add_post_meta($post_ID, 'et_answers_count', 0, true);
	}
	/**
	 * Attach a class to linked images' parent anchors
	 * e.g. a img => a.img img
	 */
	function ae_give_linked_images_class($html, $id, $caption, $title, $align, $url, $size, $alt = '' ){
	  $classes = 'qa-blog-zoom'; // separated by spaces, e.g. 'img image-link'

	  // check if there are already classes assigned to the anchor
	  if ( preg_match('/<a.*? class=".*?">/', $html) ) {
	    $html = preg_replace('/(<a.*? class=".*?)(".*?>)/', '$1 ' . $classes . '$2', $html);
	  } else {
	    $html = preg_replace('/(<a.*?)>/', '$1 class="' . $classes . '" >', $html);
	  }
	  return $html;
	}
	function ae_filter_badword($content){
        // filter badwords
		$filter_word     = ae_get_option('filter_keywords');
		$filter_keywords = explode(',', $filter_word);

        if(!empty($filter_keywords)){
        	foreach ($filter_keywords as $word) {
        		if($word){
        			$partern = '/\b' . trim($word) . '\b/i';
        			$content = preg_replace($partern, " ***", $content);
        		}
        	}
        }
		return $content;
	}
	/*
	* Send email to answer author when the answer is the best
	*
	*/
	function qa_after_accept_answer($answerID, $action){
		//get post data
		$answer       = get_post( $answerID );
		$question     = get_post( $answer->post_parent );
		$author       = get_user_by( 'id', $answer->post_author );

		$author_email = $author->user_email;

		$message = ae_get_option('accept_answer_mail_template');
		$message = stripslashes($message);
		$message = str_ireplace('[action]', $action == "accept-answer" ? __("marked", ET_DOMAIN) : __("unmarked", ET_DOMAIN), $message);
		$message = str_ireplace('[display_name]', $author->display_name, $message);
		$message = str_ireplace('[question_link]', get_permalink( $question->ID ), $message);
		$message = str_ireplace('[blogname]', get_option('blogname'), $message);

		$subject =	sprintf(__("[%s] Your answer has been marked as the best.",ET_DOMAIN),get_option('blogname'));

		$headers = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= "From: ".get_option('blogname')." < ".ae_get_option('send_mail_from', 'no-reply@enginethemes.com')."> \r\n";

		if($author_email)
			wp_mail($author_email, $subject , $message, $headers) ;
	}
	function ae_convert_user($user){
		$user_instance           = QA_Member::get_instance();
		$user->register_status   = get_user_meta($user->ID, 'register_status', true) == "unconfirm" ? "unconfirm" : '';
		//$user->banned            = $user_instance->is_ban($user->ID) ? true : false;
		$user->qa_point          = get_user_meta($user->ID, 'qa_point', true) ? get_user_meta($user->ID, 'qa_point', true) : 0;
		$user->et_question_count = et_count_user_posts($user->ID, 'question');
		$user->et_answer_count   = et_count_user_posts($user->ID, 'answer');
		return $user;
	}
	function qa_tinymce_filter_link_query($query){
		$query['post_type']   =	'question';
		$query['post_status'] =	array('publish','closed');
		return $query;
	}
	/**
	 *  Send email to admin after new pending question created
	 */
	function alert_pending_question_to_admin($id){
		if(ae_get_option( 'pending_questions' ) && get_post_status( $id ) == "pending"){
			$admin_email = apply_filters( 'email_alert_pending_question', ae_get_option('new_admin_email') );

			$message =	ae_get_option('pending_question_mail_template');
			$message = 	stripslashes($message);
			$message =	str_ireplace('[question_title]', get_the_title( $id ), $message);
			$message =	str_ireplace('[pending_question_link]', et_get_page_link("pending"), $message);
			$message =	str_ireplace('[blogname]', get_option('blogname'), $message);

			$subject =	sprintf(__("[%s] New pending question has been created.",ET_DOMAIN),get_option('blogname'));

			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$headers .= "From: ".get_option('blogname')." < ".ae_get_option('send_mail_from', 'no-reply@enginethemes.com')."> \r\n";

			wp_mail($admin_email, $subject , $message, $headers) ;
		}
	}
	/**
	 * Update option question has a new answer
	 */
	static public function qa_questions_new_answer($id){
		$answer    = get_post( $id );
		$id        = $answer->post_parent;
		$questions = (array)get_option( 'qa_questions_new_answer' );
		if(is_array($questions))
			array_push( $questions , $id);
		update_option( 'qa_questions_new_answer' , array_filter(array_unique( $questions )) );
	}
	/**
	 * Email to following user when thread has new reply
	 */
 	public function mail_to_following_users(){
		$questions = get_option( 'qa_questions_new_answer' );
		global $current_user;

		if(!empty($questions)){
			foreach ($questions as $id) {

				$question_title = get_the_title($id);
				$last_author    = get_post_meta( $id, 'et_last_author', true );
				$users_follow   = explode(',', get_post_meta($id, 'et_users_follow', true) );

				foreach ($users_follow as $userid) {

					$user    = get_user_by('id', $userid);

					$headers = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
					$headers .= "From: ".get_option('blogname')." < ".ae_get_option('send_mail_from', 'no-reply@enginethemes.com')."> \r\n";
					$subject = sprintf(__("[New Answer] Your question %s has a new reply.",ET_DOMAIN), $question_title);

					$message = ae_get_option('new_answer_mail_template');
					$message = stripslashes($message);
					/* ============ filter placeholder ============ */
					$message =	str_ireplace('[display_name]', $user->display_name, $message);
					$message =	str_ireplace('[question_title]', $question_title, $message);
					$message =	str_ireplace('[question_link]', get_permalink($id), $message);
					$message =	str_ireplace('[blogname]', get_option('blogname'), $message);
					/* ============ filter placeholder ============ */

					// user email exist & user id != last author
					if($user->user_email && $userid != $last_author){
						wp_mail($user->user_email, $subject , $message, $headers);
					}
				}
			}
			update_option( 'qa_questions_new_answer' , array() );
		}
	}
	/**
	 * Save thread id to following to usermeta
	 */
	public function save_following_questions($id){
		global $user_ID;

		if(get_post_type($id) != "answer" && get_post_type($id) != "question") {
			return;
		}

		if(get_post_type($id) == "answer"){
			$answer = get_post( $id );
			$id     = $answer->post_parent;
			//update last author to question
			update_post_meta( $id, 'et_last_author', $answer->post_author );
		}

		$users_follow = explode(',', get_post_meta($id,'et_users_follow',true) );

		if(!in_array($user_ID, $users_follow)){
			$users_follow[] = $user_ID;
		}

		$users_follow = array_unique(array_filter($users_follow));
		$users_follow = implode(',', $users_follow);
		QA_Questions::update_field($id, 'et_users_follow', $users_follow);
	}
	/* ==================== LIVE NOTIFICATION ==================== */
	public function send_data_to_heartbeat($response, $data){

		global $wpdb, $current_user;

		$sql = $wpdb->prepare(
			"SELECT * FROM $wpdb->options WHERE option_name LIKE %s",
			'_transient_qa_notify_%'
		);

		$notifications = $wpdb->get_results( $sql );

		if(!empty($notifications)){
			foreach ( $notifications as $db_notification ) {

				$id = str_replace( '_transient_', '', $db_notification->option_name );

				if(ae_get_option( 'pending_questions' )){
					if ( false !== ( $notification = get_transient( $id ) )  && $notification['user'] != md5( $current_user->user_login ) && current_user_can( 'administrator' ) )
						$response['message'][ $id ] = $notification;
				} else {
					if ( false !== ( $notification = get_transient( $id ) )  && $notification['user'] != md5( $current_user->user_login ) )
						$response['message'][ $id ] = $notification;
				}

			}
		}

		return $response;
	}
	public function store_new_question_to_DB($post_id){

		global $current_user;

		if( get_post_type( $post_id ) != 'question')
			return $post_id;

		//if( get_option( 'pending_questions' ) && !current_user_can( 'administrator' ))
			//return $post_id;

		set_transient( 'qa_notify_' . mt_rand( 100000, 999999 ), array(
			'title'		=>		__( 'New Question', ET_DOMAIN ),
			'content'	=>	 	__( 'There\'s a new post, why don\'t you give a look at', ET_DOMAIN ) .
								' <a href="' . get_permalink( $post_id ) . '">' . get_the_title( $post_id ) . '</a>',
			'type'		=>		'update',
			'user'		=>	md5( $current_user->user_login )
		), 20 );

		return $post_id;
	}
	public function change_hearbeat_rate($settings){

		$settings['interval'] = 20;

		return $settings;
	}
	/* ==================== LIVE NOTIFICATION ==================== */
	public function add_user_actions_backend($user)	{
		$user = QA_Member::convert($user);
			if($user->register_status == "unconfirm"){
		?>
		<a class="action et-act-confirm" data-act="confirm" href="javascript:void(0)" title="<?php _e( 'Confirm this user', ET_DOMAIN ) ?>">
			<span class="icon" data-icon="3"></span>
		</a>
		<?php
			}
	}
	public function qa_excerpt_length(){
		return 20;
	}
	public function qa_excerpt_more( $more ) {
		return ' ...';
	}
	public function qa_wp_title( $title, $sep ) {
		global $paged, $page;

		if ( is_feed() )
			return $title;

		// Add the site name.
		$title .= get_bloginfo( 'name' );

		// Add the site description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) )
			$title = "$title $sep $site_description";

		// Add a page number if necessary.
		if ( $paged >= 2 || $page >= 2 )
			$title = "$title $sep " . sprintf( __( 'Page %s', ET_DOMAIN ), max( $paged, $page ) );

		return apply_filters( 'qa_wp_title', $title);
	}
	public function user_search_columns_bd($search_columns, $search, $vars){

	    if(!in_array('display_name', $search_columns)){
	        $search_columns[] = 'display_name';
	    }
	    return $search_columns;
	}

	public function set_default_theme(){

		$pages = array("profile","tags","users","search","badges","intro","categories");
		global $pagenow;

		if( is_admin() && isset($_GET['activated'] ) && $pagenow == 'themes.php' ){
			if( !get_option( 'qa_first_time_active' ) ){
				//add default page:
				foreach ($pages as $key => $page) {
					$id = wp_insert_post(array(
						'post_status' => "publish",
						'post_type'   => 'page',
						'post_title'  => ucfirst($page)
					));
					update_post_meta( $id, '_wp_page_template', 'page-'.$page.'.php' );
				}

				//set static front page
				$front_id  = get_option('page_on_front');
				if ( empty($front_id) ){
					$front = wp_insert_post(array(
						'post_status' => "publish",
						'post_type'   => 'page',
						'post_title'  => 'Questions Listing'
					));
					update_option( 'page_on_front' , $front );
					update_post_meta( $front, '_wp_page_template', 'page-questions.php' );
				}

				$posts_id  = get_option('page_for_posts');
				if (empty( $posts_id )){
					$post = wp_insert_post(array(
						'post_status' => "publish",
						'post_type'   => 'page',
						'post_title'  => 'Blog'
					));
					update_option( 'page_for_posts' , $post );
				}

				update_option( 'show_on_front' , "page" );
				update_option( 'qa_first_time_active', 1 );
			}
		}
	}

	public function et_widgets_init(){
		unregister_widget('WP_Widget_Recent_Comments');
		register_widget('QA_Recent_Comments');
		register_widget('QA_Hot_Questions_Widget');
		register_widget('QA_Statistic_Widget');
		register_widget('QA_Tags_Widget');
		register_widget('QA_Recent_Activity');
		register_widget('QA_Top_Users_Widget');
		register_widget('QA_Related_Questions_Widget');
		register_widget('QA_Latest_Answers_Widget');
	}
	public function retrieve_password_message($message , $active_key , $user_data) {
		$user_login 	=   $user_data->user_login;
		$forgot_message =	ae_get_option('forgotpass_mail_template');
		$forgot_message = 	stripslashes($forgot_message);
		$activate_url	= 	apply_filters('et_reset_password_link',  network_site_url("wp-login.php?action=rp&key=$active_key&login=" . rawurlencode($user_login), 'login'), $active_key, $user_login );
		$activate_url = '<a href="'. $activate_url .'">'. $activate_url .'</a>';
		$forgot_message	=	et_filter_authentication_placeholder ( $forgot_message, $user_data->ID );
		$forgot_message	=	str_ireplace('[activate_url]', $activate_url, $forgot_message);

		return $forgot_message;
	}
	public function password_reset_mail ( $user, $new_pass ) {
		$new_pass_msg	=	ae_get_option('resetpass_mail_template');
		$new_pass_msg   = 	stripslashes($new_pass_msg);
		$new_pass_msg	=	et_filter_authentication_placeholder($new_pass_msg, $user->ID);

		$subject 		=	apply_filters('et_reset_pass_mail_subject',__('Password updated successfully!', ET_DOMAIN));

		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= "From: ".get_option('blogname')." < ".ae_get_option('send_mail_from', 'no-reply@enginethemes.com')."> \r\n";

		wp_mail($user->user_email, $subject , $new_pass_msg, $headers);
	}
	public function user_register_mail( $user_id, $role = false) {

		$user			=   new WP_User($user_id);
		$user_email		=	$user->user_email;

		if(ae_get_option( 'user_confirm' )){
			$message		=	ae_get_option('confirm_mail_template');
		} else {
			$message		=	ae_get_option('register_mail_template');
		}
		$message   = 	stripslashes($message);
		$message		=	et_filter_authentication_placeholder ( $message, $user_id );
		$subject		=	sprintf(__("Congratulations! You have successfully registered to %s.",ET_DOMAIN),html_entity_decode(get_option('blogname')) );

		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= "From: ".html_entity_decode(get_option('blogname'))." < ".ae_get_option('send_mail_from', 'no-reply@enginethemes.com')."> \r\n";

		wp_mail($user_email, $subject , $message, $headers) ;

	}
	public function user_register_mail_admin($user_id, $role = false) {
		$user = get_user_by('email', get_option('admin_email'));
		$new_user = new WP_User($user_id);
		if($user){
			$message		=	ae_get_option('register_mail_admin_template');
			$message        = 	stripslashes($message);
			$message 		=   str_ireplace('[display_name]', ucfirst($user->display_name) , $message);
			$message 		=   str_ireplace('[blogname]', get_option('blogname') , $message);
			$message 		=   str_ireplace('[user_login]', $new_user->user_login , $message);
			$message 		=   str_ireplace('[user_email]', $new_user->user_email , $message);
			$message 		=   str_ireplace(' here ', ' <a href="' .get_author_posts_url($user_id). '">here</a> ' , $message);
			$subject		=	sprintf(__("New Member Registration",ET_DOMAIN));

			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$headers .= "From: ".html_entity_decode(get_option('blogname'))." < ".ae_get_option('send_mail_from', 'no-reply@enginethemes.com')."> \r\n";
			wp_mail($user->user_email, $subject , $message, $headers) ;
		}
	}
	public function default_user_meta( $user_id, $role = false) {
		$user = get_user_by( 'id',$user_id );

		update_user_meta( $user_id, 'qa_point', apply_filters('qa_default_points_after_register', 1) );
		update_user_meta( $user_id, 'et_question_count', 0 );
		update_user_meta( $user_id, 'et_answer_count', 0 );
		update_user_meta( $user_id, 'key_confirm', md5($user->user_email) );

		if(ae_get_option( 'user_confirm' ))
			update_user_meta( $user_id, 'register_status', 'unconfirm' );
	}
	public function block_dashboard() {
		if ( ! current_user_can( 'manage_categories' ) && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
			wp_redirect( home_url() );
			exit;
		}
	}
	public function init_theme(){
		global $wp_rewrite, $max_file_size;
		// post type
		QA_Questions::init();
		QA_Answers::init();
		QA_Member::init();

		if(ae_get_option('twitter_login', false))
			new ET_TwitterAuth();
		if(ae_get_option('facebook_login', false)){
			new ET_FaceAuth();
		}
		if(ae_get_option('gplus_login', false)){
			new ET_GoogleAuth();
		}

		/**
		 * new class QA_PackAction to control all action do with user badge
		*/
		$qa_pack = new QA_PackAction();

		// register footer menu
		register_nav_menus ( array(
			'et_header' => __('Menu display on Header',ET_DOMAIN),
			'et_left'	=>	__('Menu display on Left Sidebar',ET_DOMAIN)
		));

		//sidebars
		register_sidebar( array(
			'name' 			=> __('Left Sidebar', ET_DOMAIN),
			'id' 			=> 'qa-left-sidebar',
			'description' 	=> __("Display widgets in left sidebar", ET_DOMAIN)
		) );
		register_sidebar( array(
			'name' 			=> __('Right Sidebar', ET_DOMAIN),
			'id' 			=> 'qa-right-sidebar',
			'description' 	=> __("Display widgets in right sidebar", ET_DOMAIN)
		) );

		//header sidebars
		register_sidebar( array(
			'name' 			=> __('Header Sidebar', ET_DOMAIN),
			'id' 			=> 'qa-header-sidebar',
			'description' 	=> __("Display widgets in header sidebar", ET_DOMAIN)
		) );

		//blog sidebars
		register_sidebar( array(
			'name' 			=> __('Blog\'s Left Sidebar', ET_DOMAIN),
			'id' 			=> 'qa-blog-left-sidebar',
			'description' 	=> __("Display widgets in blog's left sidebar", ET_DOMAIN)
		) );
		register_sidebar( array(
			'name' 			=> __('Blog\'s Right Sidebar', ET_DOMAIN),
			'id' 			=> 'qa-blog-right-sidebar',
			'description' 	=> __("Display widgets in blog's right sidebar", ET_DOMAIN)
		) );

		//single question sidebars
		register_sidebar( array(
			'name' 			=> __('Single Question Sidebar', ET_DOMAIN),
			'id' 			=> 'qa-question-right-sidebar',
			'description' 	=> __("Display widgets in single question sidebar", ET_DOMAIN)
		) );

		register_sidebar( array(
			'name' 			=> __('Top Questions Listing Ads Banner Sidebar', ET_DOMAIN),
			'id' 			=> 'qa-top-questions-banner-sidebar',
			'description' 	=> __("Display ad banners widgets in top questions listing sidebar", ET_DOMAIN)
		) );

		register_sidebar( array(
			'name' 			=> __('Bottom Ads Banner Sidebar', ET_DOMAIN),
			'id' 			=> 'qa-btm-questions-banner-sidebar',
			'description' 	=> __("Display ad banners widgets in bottom of website", ET_DOMAIN)
		) );

		register_sidebar( array(
			'name' 			=> __('Content Question Ad Banner Sidebar', ET_DOMAIN),
			'id' 			=> 'qa-content-question-banner-sidebar',
			'description' 	=> __("Display ad banners widgets in bottom questions listing sidebar", ET_DOMAIN)
		) );

		register_sidebar( array(
			'name' 			=> __('Below Answers Listing Ad Banner Sidebar', ET_DOMAIN),
			'id' 			=> 'qa-btm-single-question-banner-sidebar',
			'description' 	=> __("Display ad banners widgets in bottom questions listing sidebar", ET_DOMAIN)
		) );
//		Mobile sidebar
		register_sidebar( array(
			'name' 			=> __('Mobile Top Questions Listing Banner Ads', ET_DOMAIN),
			'id' 			=> 'qa-ads-mobile-home-top',
			'description' 	=> __("Banner Ads will display at the top questions listing page. It's better if you only insert banner ads in the widgets.", ET_DOMAIN),
			'before_title' => '<span class="hidden">',
			'after_title' => '</span>',
			'before_widget' => '<div id="%1$s" style="margin: 0 10px">',
			'after_widget'  => '</div>',
		) );

		register_sidebar( array(
			'name' 			=> __('Mobile Content Question Banner Ads', ET_DOMAIN),
			'id' 			=> 'qa-ads-mobile-single-question',
			'description' 	=> __("Banner Ads will display under the first question in the questions listing page. It's better if you only insert banner ads in the widgets.", ET_DOMAIN),
			'before_title' => '<span class="hidden">',
			'after_title' => '</span>',
			'before_widget' => '<div id="%1$s" style="margin: 0 10px">',
			'after_widget'  => '</div>',
		) );
		register_sidebar( array(
			'name' 			=> __('Mobile Bottom Banner Ads', ET_DOMAIN),
			'id' 			=> 'qa-ads-mobile-footer',
			'description' 	=> __("Banner Ads will display at the bottom of the website, above the footer widgets. It's better if you only insert banner ads in the widgets.", ET_DOMAIN),
			'before_title' => '<span class="hidden">',
			'after_title' => '</span>',
			'before_widget' => '<div id="%1$s" style="margin: 0 10px">',
			'after_widget'  => '</div>',
		) );

		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'post-thumbnails', array('post') );

	    $author_slug = ae_get_option( 'members_slug', 'member' ); // change slug name
	    $wp_rewrite->author_base = $author_slug;
	    $max_file_size = get_theme_mod('max_file_size', 5);

	    /**
		 * create post type report
		*/
		$args = array(
			'labels' => array(
				'name'               => __('Reports', ET_DOMAIN ),
				'singular_name'      => __('Report', ET_DOMAIN ),
				'add_new'            => __('Add New', ET_DOMAIN ),
				'add_new_item'       => __('Add New Report', ET_DOMAIN ),
				'edit_item'          => __('Edit Report', ET_DOMAIN ),
				'new_item'           => __('New Report', ET_DOMAIN ),
				'all_items'          => __('All Reports', ET_DOMAIN ),
				'view_item'          => __('View Report', ET_DOMAIN ),
				'search_items'       => __('Search Reports', ET_DOMAIN ),
				'not_found'          => __('No Reports found', ET_DOMAIN ),
				'not_found_in_trash' => __('No Reports found in Trash', ET_DOMAIN ),
				'parent_item_colon'  => '',
				'menu_name'          => __('Reports', ET_DOMAIN )
			),
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'query_var'           => true,
			'rewrite'             => array( 'slug' => 'report'),
			'capability_type'     => 'post',
			'has_archive'         => 'reports',
			'hierarchical'        => false,
			'menu_position'       => null,
			'supports'            => array( 'title', 'editor', 'author'),
			'taxonomies'          => array('report-taxonomy')
		);
		register_post_type( 'report', $args );

		$tax_labels = array(
			'name'                       => _x( 'Reports taxonomy', ET_DOMAIN ),
			'singular_name'              => _x( 'Report taxonomys', ET_DOMAIN ),
			'search_items'               => __( 'Search Reports', ET_DOMAIN ),
			'popular_items'              => __( 'Popular Reports', ET_DOMAIN ),
			'all_items'                  => __( 'All Reports', ET_DOMAIN ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Report', ET_DOMAIN ),
			'update_item'                => __( 'Update Report', ET_DOMAIN ),
			'add_new_item'               => __( 'Add New Report', ET_DOMAIN  ),
			'new_item_name'              => __( 'New Report Name', ET_DOMAIN ),
			'separate_items_with_commas' => __( 'Separate Reports with commas', ET_DOMAIN ),
			'add_or_remove_items'        => __( 'Add or remove Reports', ET_DOMAIN ),
			'choose_from_most_used'      => __( 'Choose from the most used Reports', ET_DOMAIN ),
			'not_found'                  => __( 'No Reports found.', ET_DOMAIN ),
			'menu_name'                  => __( 'Reports taxonomy', ET_DOMAIN ),
		);
		$tax_args = array(
			'hierarchical'          => true,
			'labels'                => $tax_labels,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'Report-taxonomy' ),
		);
		register_taxonomy( 'report-taxonomy', 'report', $tax_args );
	}
	/**
	 * All about meta boxes in backend
	 */
	function add_post_meta_box(){
		add_meta_box( 'thread_info',
			__('Report Information', ET_DOMAIN),
			array($this, 'meta_box_view'),
			'report',
			'normal',
			'high' );
	}
	function meta_box_view($post){
		?>
		<p>Click this link below to view thread:</p>
		<p>
			<a href="<?php echo get_post_meta($post->ID, '_link_report', true) ?>">
				<?php echo get_post_meta($post->ID, '_link_report', true) ?>
			</a>
		</p>
		<?php
	}
	public function on_add_scripts(){
		global $current_user, $pagenow;

		if($pagenow == "customize.php") return;

		$isEditable = current_user_can( 'manage_questions' );
		$variables = array(
			'ajaxURL'           => apply_filters( 'ae_ajax_url', admin_url('admin-ajax.php') ),
			'imgURL'            => TEMPLATEURL.'/img/',
			'posts_per_page'    => get_option('posts_per_page'),
			'homeURL'           => home_url(),
			'user_confirm'      => ae_get_option('user_confirm') ? 1 : 0 ,
			'pending_questions' => ae_get_option('pending_questions') ? 1 : 0,
			'pending_answers'   => ae_get_option("pending_answers") ? 1 : 0,
			'introURL'          => et_get_page_link('intro'),
			'gplus_client_id'   => ae_get_option("gplus_client_id"),
			'plupload_config'   => array(
				'max_file_size'       => '3mb',
				'url'                 => admin_url('admin-ajax.php'),
				'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
				'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
		));

		?>
		<script type="text/javascript">
			ae_globals = <?php echo json_encode($variables) ?>
		</script>
		<?php
	}

	public function ae_add_qa_globals( $variable ) {
		global $current_user, $pagenow, $user_ID;

		if($pagenow == "customize.php") return;
		$isEditable = current_user_can( 'manage_questions' );
		$qa_variables = array(
			'ajaxURL'           => apply_filters( 'ae_ajax_url', admin_url('admin-ajax.php') ),
			'imgURL'            => TEMPLATEURL.'/img/',
			'posts_per_page'    => get_option('posts_per_page'),
			'homeURL'           => home_url(),
			'user_confirm'      => ae_get_option('user_confirm') ? 1 : 0 ,
			'pending_questions' => ae_get_option('pending_questions') ? 1 : 0,
			'pending_answers'   => ae_get_option("pending_answers") ? 1 : 0,
			'introURL'          => et_get_page_link('intro'),
			'buy_pump_link' 	=> et_get_page_link('buy-package'),
			'gplus_client_id'   => ae_get_option("gplus_client_id"),
			'plupload_config'   => array(
				'max_file_size'       => '3mb',
				'url'                 => admin_url('admin-ajax.php'),
				'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
				'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
			),
			'max_width_title'	=> ae_get_option('max_width_title', 150),
			'user_id'			=> $user_ID,
			'upload_images'		=> ae_get_option('ae_upload_images', 1),
			'is_infinite'		=> ae_get_option('qa_infinite_scroll')
		);


		$variable = wp_parse_args($qa_variables, $variable  );

		return $variable;
	}

	public function on_add_styles(){}

	/**
	 * Write some method specified for forumengine only ...
	 */

	public function reset_password_link($link, $key, $user_login){
		return add_query_arg(array('user_login' => $user_login, 'key' => $key), home_url());
	}

	public function qa_get_tags() {
		$terms	=	get_terms('qa_tag', array('hide_empty' => 0, 'fields' => 'names' )) ;
		wp_send_json($terms);
	}
	/**
	 * Send email after report success
	 */
	public function et_reported_email($thread_id, $report_message){
		global $current_user;
		if($thread_id && $report_message){
			$thread = get_post( $thread_id );
			//$user_send = get_users( 'role=administrator' );

			// Get author email
			$author_id = get_post_field('post_author', $thread_id);
			$author = get_user_by('id', $author_id);

			$user_send = array(
				// Author info
				array(
					'user_email' => $author->user_email,
					'display_name' => $author->display_name
				),
				// Admin info
				array(
					'user_email' => ae_get_option("new_admin_email"),
					'display_name' => __("Admin", ET_DOMAIN)
				)
			);

			foreach ( $user_send as $user ) {
				$user_email			=	$user['user_email'];

				$message =	ae_get_option('report_mail_template');

				/* ============ filter placeholder ============ */
				$message  	=	str_ireplace('[display_name]', $user['display_name'], $message);
				$message  	=	str_ireplace('[thread_title]', $thread->post_title, $message);
				$message  	=	str_ireplace('[thread_content]', $thread->post_content, $message);
				$message  	=	str_ireplace('[thread_link]', get_permalink($thread_id), $message);
				$message  	=	str_ireplace('[report_message]',$report_message, $message);
				$message  	=	str_ireplace('[blogname]', get_option('blogname'), $message);
				// $message	=	et_filter_authentication_placeholder ( $message, $user->ID);

				$subject	=	'[#'.$thread_id.']'.__("There's a new report ",ET_DOMAIN);

				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
				$headers .= "From: ".get_option('blogname')." < ".$current_user->user_email."> \r\n";

				if($user_email){
					$test = wp_mail($user_email, $subject , $message, $headers) ;
				}
			}
		}
	}
}

class QA_Shortcodes{
	public function __construct(){
		$this->add_shortcode( 'img', 'img' );
		//$this->add_shortcode( 'quote', 'quote' );
		$this->add_shortcode( 'code', 'code' );
		add_filter('comment_text', 'do_shortcode');
		do_action('et_add_shortcodes');
	}

	function img($atts, $content = ""){
		global $post;
		$title = "";

		// Set default attributes
		$atts = shortcode_atts(array(
			'alt_text' => isset( $atts['alt_text'] ) ? $atts['alt_text'] : '',
			'description' => isset( $atts['description'] ) ? $atts['description'] : '',
		), $atts);

		extract($atts);

		if(isset($post->post_title)){
			$title = $post->post_title;
		}

		$output = '<p style="text-align: center;">';
		if(!empty($alt_text) && !empty($description)) {
			$output .= '<a href="' . $content . '" class="qa-zoom" title="'. $title .'">';
			$output .= '<img alt="'. $alt_text .'" class="img-responsive" src="' . $content . '">';
			$output .= '<span class="img-description">' . $description . '</span>';
			$output .= '</a>';
		} elseif(!empty($alt_text) && empty($description)) {
			$output .= '<a href="' . $content . '" class="qa-zoom" title="'. $title .'">';
			$output .= '<img alt="'. $alt_text .'" class="img-responsive" src="' . $content . '">';
			$output .= '</a>';
		} elseif(empty($alt_text) && !empty($description)) {
			$output .= '<a href="' . $content . '" class="qa-zoom" title="'. $title .'">';
			$output .= '<img alt="'. $title .'" class="img-responsive" src="' . $content . '">';
			$output .= '<span class="img-description">' . $description . '</span>';
			$output .= '</a>';
		} else {
			$output .= '<a href="' . $content . '" class="qa-zoom" title="'. $title .'">';
			$output .= '<img alt="'. $title .'" class="img-responsive" src="' . $content . '">';
			$output .= '</a>';
		}
		$output .= '</p>';

		return $output;
	}

	function code($atts, $content = ''){
		extract( shortcode_atts( array(
				'type'      => 'php',
				'start'     => 1,
				'highlight' => ''
			), $atts ) );

		$content = preg_replace('#<br\s*/?>#i', "\n", $content);
		$content = str_replace("<br>", "\n", $content);
		$content = str_replace("<p></p>", "", $content);
		$content = str_replace("<p>", "", $content);
		$content = str_replace("</p>", "", $content);

		return '<pre class="ruler: true;brush: '.$type.';toolbar: false;highlight: ['.$highlight.'];first-line: '.$start.';">'.do_shortcode( $content ).'</pre>';
	}

	function quote($atts, $content = ''){
		extract( shortcode_atts( array(
				'author' => '',
			), $atts ) );
		return '<blockquote>' . do_shortcode( $content ) . '</blockquote>';
	}

	private function add_shortcode($name, $callback){
		add_shortcode( $name, array($this, $callback) );
	}
}



/**
 * Print the content with shortcode
 */
function et_the_content($more_link_text = null, $stripteaser = false){
	$content = get_the_content($more_link_text, $stripteaser);
	$content = apply_filters( 'et_the_content', $content );
	$content = str_replace(']]>', ']]&gt;', $content);
	echo $content;
}

add_filter('et_the_content', 'et_the_content_filter');
function et_the_content_filter($content){
	add_filter('the_content', 'do_shortcode', 11);
	$content = apply_filters( 'the_content', $content );
	remove_filter('the_content', 'do_shortcode');
	return $content;
}

function et_the_content_edit($content){
	if(is_contain_ytd_vm($content)){
		return apply_filters( 'the_content', $content );
	} else {
		return wpautop(nl2br($content));
	}
}
function is_contain_ytd_vm($content){
	if ( strpos($content, "youtube.com") !== false || strpos($content, "youtu.be") !== false || strpos($content, "vimeo.com") !== false ) {
	    return true;
	} else {
	    return false;
	}
}

/**
 * Get editor default settings
 * @param array $args overwrite settings
 */
function editor_settings($args = array()){
	$buttons = apply_filters( 'qa_editor_buttons', 'bold,|,italic,|,underline,|,link,unlink,|,bullist,numlist,qaimage,qacode' );
	return array(
	'quicktags' 	=> false,
	'media_buttons' => false,
	'tabindex' 		=> 5,
	//'wpautop' => false,
	'textarea_name' => 'post_content',
	'tinymce' 		=> array(
		'content_css'           => get_template_directory_uri() . '/css/editor_content.css',
		'height'                => 150,
		'toolbar1'              => $buttons,
		'toolbar2'              => '',
		'toolbar3'              => '',
		'autoresize_min_height' => 150,
		'force_p_newlines'      => false,
		'statusbar'             => false,
		'force_br_newlines'     => false,
		//'forced_root_block'     => '',
		'setup'                 => "function(ed){
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
	));
}

function et_filter_authentication_placeholder ($content, $user_id, $key_confirm = "") {
		$user 		=	new WP_User ($user_id);

		$content =	str_ireplace('[user_login]'		, $user->user_login, $content);
		$content =	str_ireplace('[user_name]'		, $user->user_login, $content);
		$content =	str_ireplace('[user_nicename]'	, ucfirst( $user->user_nicename ), $content);
		$content =	str_ireplace('[user_email]'		, $user->user_email, $content);
		$content =	str_ireplace('[blogname]'		, get_bloginfo( 'name' ), $content);
		$content =	str_ireplace('[display_name]'	, ucfirst( $user->display_name ), $content);
		$content =	str_ireplace('[company]'		, ucfirst( $user->display_name ) , $content);
		$content =	str_ireplace('[dashboard]'		, et_get_page_link('dashboard'), $content);
		$content =	str_ireplace('[site_url]'		, home_url(), $content);
	//	$content =	str_ireplace('[confirm_link]'	, add_query_arg(array('act' => 'confirm', 'key'=>md5($user->user_email)),home_url()), $content);

		if(empty($key_confirm)) {
			$key_confirm = md5($user->user_email);
		}

		$confirm_link =  add_query_arg(array(
			'act' => 'confirm',
			'key'=> $key_confirm
		),home_url());
		$confirm_link = '<a href="' . $confirm_link . '" >' .$confirm_link . '</a>';
		$content = str_ireplace('[confirm_link]', $confirm_link, $content);
		return $content;
}

/**
 * Edit WP_NAV_MENUs HTML list of nav menu items.
 *
 * @since 1.0
 * @uses Walker
 */
class QA_Custom_Walker_Nav_Menu extends Walker_Nav_Menu {
	/**
	 * Start the element output.
	 *
	 * @see Walker::start_el()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 * @param int    $id     Current item ID.
	 */
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$custom_class = isset($item->classes[0]) ? $item->classes[0] : '';
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		/**
		 * Filter the CSS class(es) applied to a menu item's <li>.
		 *
		 * @since 3.0.0
		 *
		 * @see wp_nav_menu()
		 *
		 * @param array  $classes The CSS classes that are applied to the menu item's <li>.
		 * @param object $item    The current menu item.
		 * @param array  $args    An array of wp_nav_menu() arguments.
		 */
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		/**
		 * Filter the ID applied to a menu item's <li>.
		 *
		 * @since 3.0.1
		 *
		 * @see wp_nav_menu()
		 *
		 * @param string $menu_id The ID that is applied to the menu item's <li>.
		 * @param object $item    The current menu item.
		 * @param array  $args    An array of wp_nav_menu() arguments.
		 */
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $class_names .'>';

		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';

		/**
		 * Filter the HTML attributes applied to a menu item's <a>.
		 *
		 * @since 3.6.0
		 *
		 * @see wp_nav_menu()
		 *
		 * @param array $atts {
		 *     The HTML attributes applied to the menu item's <a>, empty strings are ignored.
		 *
		 *     @type string $title  Title attribute.
		 *     @type string $target Target attribute.
		 *     @type string $rel    The rel attribute.
		 *     @type string $href   The href attribute.
		 * }
		 * @param object $item The current menu item.
		 * @param array  $args An array of wp_nav_menu() arguments.
		 */
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$item_output = $args->before;
		$custom_icon = $custom_class ? '<i class="fa '.$custom_class.'"></i>' : '';
		$item_output .= '<a'. $attributes .'>'.$custom_icon;
		/** This filter is documented in wp-includes/post-template.php */
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		/**
		 * Filter a menu item's starting output.
		 *
		 * The menu item's starting output only includes $args->before, the opening <a>,
		 * the menu item's title, the closing </a>, and $args->after. Currently, there is
		 * no filter for modifying the opening and closing <li> for a menu item.
		 *
		 * @since 3.0.0
		 *
		 * @see wp_nav_menu()
		 *
		 * @param string $item_output The menu item's starting HTML output.
		 * @param object $item        Menu item data object.
		 * @param int    $depth       Depth of menu item. Used for padding.
		 * @param array  $args        An array of wp_nav_menu() arguments.
		 */
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}
function et_count_posts($status = 'publish', $type = 'question'){
	$count = wp_count_posts($type);
	return $count->$status;
}
/**
*
* Return the array of static texts
*
**/
function qa_static_texts(){
	$max_lengh = ae_get_option('max_width_title');
	return 	array(
		'form_auth'	=> array(
			'error_msg'      => __("Please fill out all fields required.", ET_DOMAIN),
			'error_user'     => __("Please enter your user name.", ET_DOMAIN),
			'error_email'    => __("Please enter a valid email address.", ET_DOMAIN),
			'error_username' => __("Please enter a valid username.", ET_DOMAIN),
			'error_repass'   => __("Please enter the same password as above.", ET_DOMAIN),
			'error_url'      => __("Please enter a valid URL.", ET_DOMAIN),
			'error_cb'       => __("You must accept the terms & privacy.", ET_DOMAIN),
			//'max_lengh'		 => sprintf(__('Please enter no more than %s characters', ET_DOMAIN), $max_lengh )
		),
		'texts' => array(
			'require_login'   => __("You must be logged in to perform this action.", ET_DOMAIN),
			'enought_points'  => __("You don't have enought points to perform this action.", ET_DOMAIN),
			'create_topic'    => __("Create Topic", ET_DOMAIN),
			'upload_images'   => __("Upload Images", ET_DOMAIN),
			'insert_codes'    => __("Insert Code", ET_DOMAIN),
			'no_file_choose'  => __("No file chosen.", ET_DOMAIN),
			'require_tags'    => __("Please insert at least one tag.", ET_DOMAIN),
			'add_comment'     => __("Add comment", ET_DOMAIN),
			'cancel'          => __("Cancel", ET_DOMAIN),
			'sign_up'         => __("Sign Up", ET_DOMAIN),
			'sign_in'         => __("Sign In", ET_DOMAIN),
			'accept_txt'      => __("Accept", ET_DOMAIN),
			'best_ans_txt'    => __("Best answer", ET_DOMAIN),
			'forgotpass'      => __("Forgot Password", ET_DOMAIN),
			'close_tab'       => __("You have made some changes which you might want to save.", ET_DOMAIN),
			'confirm_account' => __("You must activate your account first to create questions / answers!.", ET_DOMAIN),
			'cancel_auth'     => __("User cancelled login or did not fully authorize.", ET_DOMAIN),
			'banned_account'  => __('You account has been banned, you can\'t make this action!', ET_DOMAIN),
			'buy_pump'		  => __('You must activate your account first to buy pump package.', ET_DOMAIN),
			'uploading'		  => __('Uploading...', ET_DOMAIN),
			'insert'		  => __('Insert', ET_DOMAIN),
			'max_lengh_text'		 => sprintf(__('Please enter no more than %s characters', ET_DOMAIN), $max_lengh ),
			'max_lengh'		 => $max_lengh
		)
	);
}
/**
*
* Insert post link to listing report post type
*
**/
add_filter('manage_report_posts_columns' , 'report_cpt_columns');
add_action( 'manage_report_posts_custom_column' , 'custom_report_column', 10,2 );
function report_cpt_columns($columns) {

	$new_columns = array(
		'post_link' => __('Post link', ET_DOMAIN),
	);
    return array_merge($columns, $new_columns);
}
function custom_report_column( $column, $post_id ) {
    switch ( $column ) {

        case 'post_link' :
            $post_link = get_post_meta($post_id, '_link_report', true);
            if ($post_link)
                echo '<a target ="_blank" href ="'.$post_link.'" >'. $post_link.'</a>';
            else
                _e( 'Unable to get post link', ET_DOMAIN);
            break;
        default:

        	break;
    }
}
// insert rel nofollow to a link
add_filter( 'the_content', 'add_nofollow_blank_link');
function add_nofollow_blank_link( $content ) {

	$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>";
	if(preg_match_all("/$regexp/siU", $content, $matches, PREG_SET_ORDER)) {
		if( !empty($matches) ) {

			$srcUrl = get_option('siteurl');
			for ($i=0; $i < count($matches); $i++)
			{

				$tag = $matches[$i][0];
				$tag2 = $matches[$i][0];
				$url = $matches[$i][0];

				$noFollow = '';

				$pattern = '/target\s*=\s*"\s*_blank\s*"/';
				preg_match($pattern, $tag2, $match, PREG_OFFSET_CAPTURE);
				if( count($match) < 1 )
					$noFollow .= ' target="_blank" ';

				$pattern = '/rel\s*=\s*"\s*[n|d]ofollow\s*"/';
				preg_match($pattern, $tag2, $match, PREG_OFFSET_CAPTURE);
				if( count($match) < 1 )
					$noFollow .= ' rel="nofollow" ';

				$pos = strpos($url,$srcUrl);
				if ($pos === false) {
					$tag = rtrim ($tag,'>');
					$tag .= $noFollow.'>';
					$content = str_replace($tag2,$tag,$content);
				}
			}
		}
	}

	$content = str_replace(']]>', ']]&gt;', $content);
	return $content;
}
/**

* Shorten long numbers to K / M / B
* @author Thái NT
* @since 1.3
* @package QAEngine

*/
function custom_number_format($n, $precision = 1) {
    // first strip any formatting;
    $n = (0+str_replace(",","",$n));

    // is this a number?
    if(!is_numeric($n)) return false;

    // now filter it;
    if($n >= 1000000000000) return round(($n/1000000000000),1).'T';
    else if($n >= 1000000000) return round(($n/1000000000),1).'B';
    else if($n >= 1000000) return round(($n/1000000),1).'M';
    else if($n >= 1000) return round(($n/1000),1).'K';

    return number_format($n);
}
