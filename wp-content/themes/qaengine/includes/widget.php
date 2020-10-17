<?php

class QA_Related_Questions_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget', 'description' => __( 'Drag this widget to single sidebars to display a list of related questions.',ET_DOMAIN) );
		$control_ops = array('width' => 250, 'height' => 100);
		parent::__construct('question_related_widget', __('QA Related Questions', ET_DOMAIN) , $widget_ops ,$control_ops );
	}

	function update ( $new_instance, $old_instance ) {
		if( $new_instance['number'] != $old_instance['number'] ){
			delete_transient( 'related_questions_query' );
		}
		return $new_instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array(
			'title'   => __('RELATED QUESTIONS', ET_DOMAIN) ,
			'number'  => '4',
			'base_on' => 'category',
			) );
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">
				<?php _e('Title:', ET_DOMAIN) ?>
			</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>">
				<?php _e('Number of questions to display:', ET_DOMAIN) ?>
			</label>
			<input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo esc_attr( $instance['number'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('base_on'); ?>">
				<?php _e('Questions base on:', ET_DOMAIN) ?>
			</label>
			<select id="<?php echo $this->get_field_id('base_on'); ?>" name="<?php echo $this->get_field_name('base_on'); ?>">
				<option <?php selected( $instance['base_on'], "category" ); ?> value="category">
					<?php _e('Category', ET_DOMAIN) ?>
				</option>
				<option <?php selected( $instance['base_on'], "tag" ); ?> value="tag">
					<?php _e('Tag', ET_DOMAIN) ?>
				</option>
			</select>
		</p>
	<?php
	}

	function widget( $args, $instance ) {

		global $wpdb, $post;
		if(is_singular( 'question' )){
			$arrSlug  = array();
			$taxonomy = $instance['base_on'] == "category" ? "question_category" : "qa_tag";
			$terms    = get_the_terms($post->ID, $taxonomy);
			if(!empty($terms)){
				foreach ($terms as $term) {
					$arrSlug[] = $term->slug;
				}
			}
			$args = array(
					'post_type'    => 'question',
					'showposts'    => $instance['number'],
					'post__not_in' => array($post->ID),
					'order'        => 'DESC'
				);
			if(!empty($arrSlug)){
				$args['tax_query'] = array(
						array(
							'taxonomy' => $taxonomy,
							'field'    => 'slug',
							'terms'    => $arrSlug,
						)
					);
			}
			$query = new WP_Query($args);
			ob_start();
			?>
		    <li class="widget widget-hot-questions">
		        <h3><?php echo esc_attr($instance['title']) ?></h3>
		        <ul>
					<?php
						if($query->have_posts()){
							while ( $query->have_posts() ) {
								$query->the_post();
					?>
		            <li>
		                <a href="<?php echo get_permalink( $post->ID );?>">
		                    <span class="topic-avatar">
		                    	<?php echo et_get_avatar($post->post_author, 30) ?>
		                    </span>
		                    <span class="topic-title"><?php echo $post->post_title ?></span>
		                </a>
		            </li>
		            <?php
		        			}
			        	} else {
			        		echo '<li class="no-related">'.__('There are no related questions!', ET_DOMAIN).'</li>';
			        	}
			        	wp_reset_query();
			        ?>
		        </ul>
		    </li><!-- END widget-related-tags -->
			<?php
			$questions = ob_get_clean();
			echo $questions;
			//delete_transient( 'related_questions_query' );
		} else {
		?>
		<li class="widget widget-hot-questions">
			<h3><?php echo esc_attr($instance['title']) ?></h3>
			<ul>
				<li>
					<?php _e('This widget should be placed in Single Question Sidebar', ET_DOMAIN) ?>
				</li>
			</ul>
		</li>
		<?php
		}
	}
}//End Related Questions

class QA_Hot_Questions_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget', 'description' => __( 'Drag this widget to any sidebars to display a list of hot questions.',ET_DOMAIN) );
		$control_ops = array('width' => 250, 'height' => 100);
		parent::__construct('question_hot_widget', __('QA Latest Questions / Hot Questions',ET_DOMAIN) , $widget_ops ,$control_ops );
	}

	function update ( $new_instance, $old_instance ) {
		if($new_instance['normal_question'] != $old_instance['normal_question'] || $new_instance['number'] != $old_instance['number']){
			delete_transient( 'hot_questions_query' );
			delete_transient( 'latest_questions_query' );
		}
		return $new_instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => __('HOT QUESTIONS',ET_DOMAIN) , 'number' => '8', 'date' => '', 'normal_question' => 0) );
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">
				<?php _e('Title:', ET_DOMAIN) ?>
			</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>">
				<?php _e('Number of questions to display:', ET_DOMAIN) ?>
			</label>
			<input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo esc_attr( $instance['number'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('normal_question'); ?>">
				<?php _e('Latest questions (sort by date)', ET_DOMAIN) ?>
			</label>
			<input class="widefat" id="<?php echo $this->get_field_id('normal_question'); ?>" name="<?php echo $this->get_field_name('normal_question'); ?>" value="1" type="checkbox" <?php checked( $instance['normal_question'], 1 ); ?> value="<?php echo esc_attr( $instance['normal_question'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('date'); ?>">
				<?php _e('Date range:', ET_DOMAIN) ?>
			</label>
			<select id="<?php echo $this->get_field_id('date'); ?>" name="<?php echo $this->get_field_name('date'); ?>">
				<option <?php selected( $instance['date'], "all" ); ?> value="all">
					<?php _e('All days', ET_DOMAIN) ?>
				</option>
				<option <?php selected( $instance['date'], "last7days" ); ?> value="last7days">
					<?php _e('Last 7 days', ET_DOMAIN) ?>
				</option>
				<option <?php selected( $instance['date'], "last30days" ); ?> value="last30days">
					<?php _e('Last 30 days', ET_DOMAIN) ?>
				</option>
			</select>
		</p>
	<?php
	}

	function widget( $args, $instance ) {

		global $wpdb;
		if(!isset($instance['normal_question'])){

			if(get_transient( 'hot_questions_query' ) === false){
				$hour       = 12;
				$today      = strtotime("$hour:00:00");
				$last7days  = strtotime('-7 day', $today);
				$last30days = strtotime('-30 day', $today);

				if($instance['date'] == "last7days"){
					$custom = "AND post_date >= '".date("Y-m-d H:i:s", $last7days)."' AND post_date <= '".date("Y-m-d H:i:s", $today)."' ";
				} elseif ($instance['date'] == "last30days") {
					$custom = "AND post_date >= '".date("Y-m-d H:i:s", $last30days)."' AND post_date <= '".date("Y-m-d H:i:s", $today)."' ";
				} else {
					$custom = "";
				}

				$query = "SELECT * FROM $wpdb->posts as post
						INNER JOIN $wpdb->postmeta as meta
						ON post.ID = meta.post_id
						AND meta.meta_key  = 'et_answers_count'
						WHERE post_status = 'publish'
						AND post_type = 'question'
					";

				$query .= $custom;
				$query .="	ORDER BY CAST(meta.meta_value AS SIGNED) DESC,post_date DESC
					LIMIT ".$instance['number']."
					";
				$questions = $wpdb->get_results($query);
				set_transient( 'hot_questions_query', $questions, apply_filters( 'qa_time_expired_transient', 24*60*60 ));
			} else {
				$questions = get_transient( 'hot_questions_query' );
			}

		} else {

			if(get_transient( 'latest_questions_query' ) === false){

				$query = "SELECT * FROM $wpdb->posts as post
						WHERE post_status = 'publish'
						AND post_type = 'question'
						ORDER BY post_date DESC
						LIMIT ".$instance['number']."
						";

			$questions = $wpdb->get_results($query);
			set_transient( 'latest_questions_query', $questions, apply_filters( 'qa_time_expired_transient', 24*60*60 ) );

			} else {
				$questions = get_transient( 'latest_questions_query' );
			}
		}
		// delete_transient( 'latest_questions_query' );
		// delete_transient( 'hot_questions_query' );
	?>
    <li class="widget widget-hot-questions">
        <h3><?php echo esc_attr($instance['title']) ?></h3>
        <ul>
			<?php
				foreach ($questions as $question) {
			?>
            <li>
                <a href="<?php echo get_permalink( $question->ID );?>">
                    <span class="topic-avatar">
                    	<?php echo et_get_avatar($question->post_author, 30) ?>
                    </span>
                    <span class="topic-title"><?php echo $question->post_title ?></span>
                </a>
            </li>
            <?php } ?>
        </ul>
    </li><!-- END widget-related-tags -->
	<?php
	}
}//End Class Hot Questions

class QA_Statistic_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget', 'description' => __( 'Drag this widget to sidebar to display the statistic of website.',ET_DOMAIN) );
		$control_ops = array('width' => 250, 'height' => 100);
		parent::__construct('qa_statistic_widget', __('QA Statistics',ET_DOMAIN) , $widget_ops ,$control_ops );
	}

	function update ( $new_instance, $old_instance ) {
		return $new_instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => __('STATISTICS WIDGET',ET_DOMAIN)) );
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', ET_DOMAIN) ?> </label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
	<?php
	}

	function widget( $args, $instance ) {
		$questions = wp_count_posts('question');
		$args = array(
			'orderby' => 'display_name',
        );
		$query       = new QA_User_Query($args);
	?>
    <li class="widget widget-statistic">
    	<ul>
    		<li class="questions-count">
    			<p><?php _e("Questions",ET_DOMAIN) ?><p>
    			<span><?php echo  $questions->publish; ?></span>
    		</li>
    		<li class="members-count">
    			<p><?php _e("Members",ET_DOMAIN) ?><p>
    			<span><?php echo $query->total_users ?></span>
    		</li>
    	</ul>
    </li><!-- END widget-statistic -->
	<?php
	}
}

class QA_Tags_Widget extends WP_Widget {
	function __construct() {
		$widget_ops = array('classname' => 'widget', 'description' => __( 'Drag this widget to sidebar to display the list of tags.',ET_DOMAIN) );
		$control_ops = array('width' => 250, 'height' => 100);
		parent::__construct('qa_tags_widget', __('QA Tags',ET_DOMAIN) , $widget_ops ,$control_ops );
	}

	function update ( $new_instance, $old_instance ) {
		return $new_instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => __('Tags Widget',ET_DOMAIN) , 'number' => '8') );
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', ET_DOMAIN) ?> </label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of tag to display:', ET_DOMAIN) ?> </label>
			<input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo esc_attr( $instance['number'] ); ?>" />
		</p>
	<?php
	}

	function widget( $args, $instance ) {
		$tags = get_terms( 'qa_tag', array(
			'hide_empty' => 0 ,
			'orderby' 	 => 'count',
			'order'		 => 'DESC',
			'number'	 => $instance['number']
			));
	?>
    <li class="widget widget-related-tags">
        <h3><?php echo esc_attr($instance['title']) ?></h3>
        <ul>
        	<?php
        		foreach ($tags as $tag) {
        	?>
            <li>
            	<a class="q-tag" href="<?php echo get_term_link( $tag, 'qa_tag' ); ?>"><?php echo $tag->name ?></a> x <?php echo $tag->count ?>
            </li>
            <?php } ?>
        </ul>
        <a href="<?php echo et_get_page_link('tags') ?>"><?php _e("See more tags", ET_DOMAIN) ?></a>
    </li><!-- END widget-related-tags -->
	<?php
	}
}

class QA_Top_Users_Widget extends WP_Widget{

	function __construct() {
		$widget_ops = array(
			'classname'   => 'widget',
			'description' => __( 'Drag this widget to sidebar to display the list of top users.',ET_DOMAIN )
		);
		$control_ops = array(
			'width'  => 250,
			'height' => 100
		);
		parent::__construct('top_users_widget', __('QA Top Users',ET_DOMAIN) , $widget_ops ,$control_ops );
	}

	function update ( $new_instance, $old_instance ) {
		if( $new_instance['number'] != $old_instance['number'] || $new_instance['orderby'] != $old_instance['orderby'] || $new_instance['latest_users'] != $old_instance['latest_users'] )
			delete_transient( 'top_users_query' );
		return $new_instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array(
			'title'        => __('TOP USERS',ET_DOMAIN) ,
			'number'       => '8',
			'orderby'      => 'point',
			'latest_users' => 0
		));
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', ET_DOMAIN) ?> </label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of users to display:', ET_DOMAIN) ?> </label>
			<input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo esc_attr( $instance['number'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('latest_users'); ?>">
				<?php _e('Latest users (sort by date)', ET_DOMAIN) ?>
			</label>
			<input class="widefat latest-checkbox" id="<?php echo $this->get_field_id('latest_users'); ?>" name="<?php echo $this->get_field_name('latest_users'); ?>" value="1" type="checkbox" <?php checked( $instance['latest_users'], 1 ); ?> value="<?php echo esc_attr( $instance['latest_users'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Order By:', ET_DOMAIN) ?> </label>
			<select class="widefat" <?php disabled( $instance['latest_users'], 1); ?> id="<?php echo $this->get_field_id('orderby'); ?>" name="<?php echo $this->get_field_name('orderby'); ?>">
				<option value="point" <?php selected( esc_attr( $instance['orderby'] ), "point" ); ?>>
					<?php _e( 'Points', ET_DOMAIN ); ?>
				</option>
				<option value="question" <?php selected( esc_attr( $instance['orderby'] ), "question" ); ?>>
					<?php _e( 'Questions', ET_DOMAIN ); ?>
				</option>
				<option value="answer" <?php selected( esc_attr( $instance['orderby'] ), "answer" ); ?>>
					<?php _e( 'Answers', ET_DOMAIN ); ?>
				</option>
			</select>
		</p>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$(document).on('change', 'input#<?php echo $this->get_field_id('latest_users'); ?>', function () {
					if (!this.checked) {
						$("select#<?php echo $this->get_field_id('orderby'); ?>").prop('disabled', false);
					} else {
						$("select#<?php echo $this->get_field_id('orderby'); ?>").prop('disabled', true);
					}
				});
			});
		</script>
	<?php
	}

	function widget( $args, $instance ) {
		global $wpdb;

		$admins_id = array();
		$admins    = get_users( 'role=administrator' );
		//normal site
		if(!empty($admins)){
			foreach ($admins as $admin) {
				$admins_id[] = $admin->ID;
			}
		}
		//multisites
		if(is_multisite()){
			$super_admins = get_super_admins();
			if(!empty($super_admins)){
				foreach ($super_admins as $admin) {
					$admin       = get_user_by( 'slug', $admin );
					$admins_id[] = $admin->ID;
				}
			}
		}

		$latest_users = isset($instance['latest_users']) && $instance['latest_users'] == 1 ? 1 : 0;
		$widget_id    = $args['widget_id'];

		//top users
		if( !$latest_users ){

			if(get_transient( 'top_users_query_'.$widget_id ) === false){

				$orderby = $instance['orderby'];
				$query = "SELECT  user.ID as uid,
							display_name,
							usermeta.meta_value as meta_sort
					FROM $wpdb->users as user ";
					$query .=" INNER JOIN $wpdb->usermeta as usermeta
						 ON user.ID = usermeta.user_id";

				if ($orderby == "question") {
					$query .=" AND usermeta.meta_key = 'et_question_count' ";

				} else if ($orderby == "answer") {
					$query .=" AND usermeta.meta_key = 'et_answer_count' ";
				} else {
					$query.= " AND usermeta.meta_key = 'qa_point' ";
				}
				$str_order =  " CAST(meta_sort AS SIGNED) DESC, display_name ASC";

				$query .=" WHERE user.ID NOT IN ( '" . implode($admins_id, "', '") . "' )
					GROUP BY user.ID
					ORDER BY ".$str_order."
					LIMIT ".$instance['number'];
					//echo $sql;

				$users = $wpdb->get_results($query);
				set_transient( 'top_users_query', $users, apply_filters( 'qa_time_expired_transient', 24*60*60 ) );
			} else {
				$users = get_transient( 'top_users_query_'.$widget_id );
			}

		} else {
			if(get_transient( 'latest_users_query_'.$widget_id ) === false){
				$users = get_users( array(
					'orderby' => 'registered',
					'number'  => $instance['number'],
					'order'   => 'DESC',
					'exclude' => $admins_id
					) );
			} else {
				$users = get_transient( 'latest_users_query_'.$widget_id );
			}
		}
		// delete_transient( 'top_users_query' );
		// delete_transient( 'latest_users_query' );
	?>
	<li class="widget user-widget">
		<h3 class="widgettitle"><?php echo esc_attr($instance['title']) ?></h3>
	    <div class="hot-user-question">
	    	<ul>
            <?php
            	$i = 1;
            	foreach ($users as $user) {
            		$uid = isset($user->uid) ? $user->uid : $user->ID;
            ?>
	        	<li>
                    <span class="number"><?php echo $i ?></span>
                    <span class="username <?php echo $latest_users ? 'latest' : ''; ?>">
                    	<a href="<?php echo get_author_posts_url($uid); ?>" title="<?php echo $user->meta_sort ?>">
                    		<?php echo $user->display_name ?>
                    	</a>
                    </span>
                    <?php
                    	if( !$latest_users ){
                    		if( $instance['orderby'] == "question" ){
                    ?>
                    <span class="questions-count" title="<?php printf( __('%d Question(s)'), $user->meta_sort > 0 ? $user->meta_sort : 0 ) ?>">
                    	<i class="fa fa-question-circle"></i>
                    	<span><?php echo $user->meta_sort > 0 ? custom_number_format($user->meta_sort) : 0 ?></span>
                    </span>
                    <?php
	               			} else if( $instance['orderby'] == "answer" ) {
	                ?>
                    <span class="answers-count" title="<?php printf( __('%d Answer(s)'), $user->meta_sort > 0 ? $user->meta_sort : 0 ) ?>">
                    	<i class="fa fa-comments"></i>
                    	<span><?php echo $user->meta_sort > 0 ? custom_number_format($user->meta_sort) : 0 ?></span>
                    </span>
                    <?php 	} else { ?>
                    <span class="points-count" title="<?php printf( __('%d Point(s)'), $user->meta_sort > 0 ? $user->meta_sort : 0 ) ?>">
                    	<i class="fa fa-star"></i>
                    	<span><?php echo $user->meta_sort > 0 ? custom_number_format($user->meta_sort) : 0 ?></span>
                    </span>
                    <?php
                			}
                		}//end if latest
                	?>
	            </li>
	        <?php $i++;} ?>
	        </ul>
	    </div>
	</li>
	<?php
	}
}

/**
 * QA_Recent_Activity widget class
 *
 * @since 1.0
 */
class QA_Recent_Activity extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget', 'description' => __( 'Drag this widget to sidebar to display the list of user\'s activities.',ET_DOMAIN) );
		$control_ops = array('width' => 250, 'height' => 100);
		parent::__construct('qa_recent_activity', __('QA Recent Activities',ET_DOMAIN) , $widget_ops ,$control_ops );
	}

	function update ( $new_instance, $old_instance ) {
		return $new_instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' , 'number' => '8') );

		?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', ET_DOMAIN) ?> </label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of activities to display:', ET_DOMAIN) ?> </label>
				<input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo esc_attr( $instance['number'] ); ?>" />
			</p>
		<?php

	}

	function widget( $args, $instance ) {
		global $user_ID;
		$param = array();

		if( !$user_ID ) return;

		if(isset($instance['number']) && $instance['number']) {
			$param['showposts']	=	$instance['number'];
		}

		?>
		<li class="widget widget-recent-activity">
			<?php if(esc_attr($instance['title']) != "" ){ ?>
				<h3><?php echo esc_attr($instance['title']) ?></h3>
			<?php }
			if(!get_transient( 'qa_changelog_'.$user_ID )) {
				ob_start();
				$content	=	qa_list_changelog($param);
				$content	=	ob_get_clean();
				set_transient( 'qa_changelog_'.$user_ID , $content, 300 );
			} else {
				$content	=	get_transient( 'qa_changelog_'.$user_ID );
			}
			echo $content;
		?>
		</li><!-- END widget-recent-activities -->

		<?php
	}
}

/**
 * Lastest Answers Widget
 * @since 2.0
 * @author tatthien
 */
class QA_Latest_Answers_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget', 'description' => __( 'Drag this widget to any sidebars to display a list of latest answers.',ET_DOMAIN) );
		$control_ops = array('width' => 250, 'height' => 100);
		parent::__construct('latest_answers_widget', __('QA Latest Answers',ET_DOMAIN) , $widget_ops ,$control_ops );
	}

	function update ( $new_instance, $old_instance ) {
		return $new_instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => __('LATEST ANSWERS',ET_DOMAIN) , 'number' => '8') );
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">
				<?php _e('Title:', ET_DOMAIN) ?>
			</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>">
				<?php _e('Number of questions to display:', ET_DOMAIN) ?>
			</label>
			<input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo esc_attr( $instance['number'] ); ?>" />
		</p>
	<?php
	}

	function widget( $args, $instance ) {
		global $wpdb;
		$query = "SELECT * FROM $wpdb->posts as post
					WHERE post_status = 'publish'
					AND post_type = 'answer'
					ORDER BY post_date DESC
					LIMIT ".$instance['number']."
					";

		$answers = $wpdb->get_results($query);
	?>
    <li class="widget widget-latest-answers widget-hot-questions">
        <h3><?php echo esc_attr($instance['title']) ?></h3>
        <ul>
			<?php
				foreach ($answers as $answer) {
			?>
            <li class="media">
				<?php
					// Get author name
					$display_name = get_the_author_meta('display_name', $answer->post_author);
					$author_url = get_author_posts_url($answer->post_author);
				?>

				<span class="topic-avatar media-left">
					<?php echo et_get_avatar($answer->post_author, 30) ?>
				</span>

				<div class="media-body">
					<strong><a href="<?php echo get_permalink($answer->post_parent); ?>"><?php echo wp_trim_words(strip_tags($answer->post_title), 20, '...'); ?></a></strong>
	               	<span class="latest-answer-date">
	               		<?php
	               			printf(__('By <a href="%s">%s</a> - %s'), $author_url, $display_name, et_the_time(strtotime($answer->post_date)));
	               		?>
	               	</span>

	               	<p class="topic-answer-content">
		            	<?php
		            		// Remove shortcode
		            		$answer_content = preg_replace('/(\[img]).*(\[\/img])/', '', $answer->post_content);
		            		$answer_content = preg_replace('/(\[code]).*(\[\/code])/', '', $answer_content);
		            		echo wp_trim_words(strip_tags($answer_content), 20, '...');
		            	?>
	            	</p>
				</div>
            </li>
            <?php } ?>
        </ul>
    </li><!-- END widget-related-tags -->
	<?php
	}
}//End Class Hot Questions


/**
 * Recent Comment Widget
 * @since 1.0
 * @author thanhtu
 */
class QA_Recent_Comments extends WP_Widget {

	public function __construct() {
		$widget_ops = array(
			'classname' => 'widget_recent_comments',
			'description' => __( 'Your site&#8217;s most recent comments.' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'recent-comments', __( 'Recent Comments' ), $widget_ops );
		$this->alt_option_name = 'widget_recent_comments';

		if ( is_active_widget( false, false, $this->id_base ) || is_customize_preview() ) {
			add_action( 'wp_head', array( $this, 'recent_comments_style' ) );
		}
	}

 	/**
	 * Outputs the default styles for the Recent Comments widget.
	 *
	 * @since 2.8.0
	 * @access public
	 */
	public function recent_comments_style() {
		/**
		 * Filter the Recent Comments default widget styles.
		 *
		 * @since 3.1.0
		 *
		 * @param bool   $active  Whether the widget is active. Default true.
		 * @param string $id_base The widget ID.
		 */
		if ( ! current_theme_supports( 'widgets' ) // Temp hack #14876
			|| ! apply_filters( 'show_recent_comments_widget_style', true, $this->id_base ) )
			return;
		?>
		<style type="text/css">.recentcomments{ word-wrap: break-word; } .recentcomments a{display:inline !important;padding:0 !important;margin:0 !important;}</style>
		<?php
	}

	/**
	 * Outputs the content for the current Recent Comments widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Recent Comments widget instance.
	 */
	public function widget( $args, $instance ) {
		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		$output = '';

		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Comments' );

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
		if ( ! $number )
			$number = 5;

		/**
		 * Filter the arguments for the Recent Comments widget.
		 *
		 * @since 3.4.0
		 *
		 * @see WP_Comment_Query::query() for information on accepted arguments.
		 *
		 * @param array $comment_args An array of arguments used to retrieve the recent comments.
		 */
		$comments = get_comments( apply_filters( 'widget_comments_args', array(
			'number'      => $number,
			'status'      => 'approve',
			'post_status' => 'publish',
			'type__not_in'		=> array('submit_poll_answer')
		) ) );

		$output .= $args['before_widget'];
		if ( $title ) {
			$output .= $args['before_title'] . $title . $args['after_title'];
		}

		$output .= '<ul id="recentcomments">';
		if ( is_array( $comments ) && $comments ) {
			// Prime cache for associated posts. (Prime post term cache if we need it for permalinks.)
			$post_ids = array_unique( wp_list_pluck( $comments, 'comment_post_ID' ) );
			_prime_post_caches( $post_ids, strpos( get_option( 'permalink_structure' ), '%category%' ), false );

			foreach ( (array) $comments as $comment ) {
				$type = '';
				if($comment->comment_type == '' || $comment->comment_type == 'answer' || $comment->comment_type == 'question' ){
					$type = 'comment';
				}elseif($comment->comment_type == 'submit_poll_question'){
					$type = 'vote';
				}elseif($comment->comment_type == 'vote_up'){
					$type = 'vote up';
				}elseif($comment->comment_type == 'vote_down'){
					$type = 'vote down';
				}
				$output .= '<li class="recentcomments">';
				/* translators: comments widget: 1: comment author, 2: post link */
				$output .= sprintf( _x( '%1$s %2$s on %3$s', 'widgets' ),
					'<span class="comment-author-link">' . get_comment_author_link( $comment ) . '</span>',
					$type,
					'<a href="' . esc_url( get_comment_link( $comment ) ) . '">' . get_the_title( $comment->comment_post_ID ) . '</a>'
				);
				$output .= '</li>';
			}
		}
		$output .= '</ul>';
		$output .= $args['after_widget'];

		echo $output;
	}

	/**
	 * Handles updating settings for the current Recent Comments widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Updated settings to save.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['number'] = absint( $new_instance['number'] );
		return $instance;
	}

	/**
	 * Outputs the settings form for the Recent Comments widget.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of comments to show:' ); ?></label>
		<input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" /></p>
		<?php
	}
}