<?php
/*
 * If the current post is protected by a password and the visitor has not yet
 * entered the password we will return early without loading the comments.
 */
if ( post_password_required() )
	return;
?>


<div id="comments" class="et-comments-area">
	<?php  if( comments_open() && ! is_page() && post_type_supports( get_post_type(), 'comments' ) ) { ?>
	<h2 class="et-comments-title">
		<?php
			// Get comment count for poll question
			if(is_singular('poll')) {
				global $post;
				$parent_comments    = get_comments( array(
				    'post_id'       => $post->ID,
				    'parent'        => 0,
				    'status'        => 'approve',
				    'post_status'   => 'publish',
				    'type'          => 'comment'
				));

				$poll_comment_count = count($parent_comments);

				if($poll_comment_count == 0) {
					_e('Be the first to post a comment.',ET_DOMAIN);
				} else if($poll_comment_count == 1) {
					_e('1 Comment',ET_DOMAIN);
				} else {
					printf(__('%s Comments'), $poll_comment_count);
				}
			} else {
				comments_number( __('Be the first to post a comment.',ET_DOMAIN), __('1 Comment on this article',ET_DOMAIN) , __('% Comments on this article',ET_DOMAIN) );
			}
		?>
	</h2>
	<?php } else { ?>
	<h2 class="et-comments-title">
		<?php _e("Comment closed!", ET_DOMAIN);?>
	</h2>
	<?php } ?>
	<ul class="et-comment-list">
		<?php
			wp_list_comments(array(
				'type' 			=> 'comment',
				'callback' 		=> 'qa_comment_post_template',
				'avatar_size' 	=> 40,
				'reply_text'	=> __('Reply ',ET_DOMAIN).'<i class="fa fa-edit"></i>',
			))
		?>
		<div class="comments-navigation">
		<?php
			paginate_comments_links();
		?>
		</div>
	</ul>
</div>


<div id="et_respond">
	<?php comment_form(array(
		'title_reply' 			=> __('Add a comment', ET_DOMAIN),
		'comment_notes_before' 	=> '',
		'comment_notes_after' 	=> ''
	)); ?>
</div>