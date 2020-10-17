<?php
global $current_user;
$role = get_user_role($current_user->ID);
$privi  =   qa_get_privileges();
?>
<!-- MODAL SUBMIT QUESTIONS -->
<div class="modal fade modal-submit-questions" id="modal_submit_questions" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header asks-question">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					<i class="fa fa-times"></i>
				</button>
				<ul class="tabs-title">
					<li id="title-tab-question"><h4 class="modal-title active" id="tab-question"><?php _e('Ask a Question',ET_DOMAIN) ?></h4></li>
					<?php if(ae_get_option('poll_maker') !=="0") { ?>
					<li id="title-tab-poll"><h4 class="modal-title" id="tab-poll"><?php _e('Create a Poll',ET_DOMAIN) ?></h4></li>
					<?php } ?>
				</ul>
			</div>
			<div class="modal-body">
				<div class="body-question">
					<form id="submit_question">
						<input type="hidden" id="qa_nonce" name="qa_nonce" value="<?php echo wp_create_nonce( 'insert_question' ); ?>">
						<input type="hidden" name="post_parent" value="0">
						<?php do_action( 'before_insert_question_form' ); ?>
						<div class="input-title">
							<input type="text" class="submit-input" id="question_title" name="post_title" placeholder="<?php _e('Your Question',ET_DOMAIN) ?>" />
							<span id="charNum"><?php echo ae_get_option('max_width_title', 150);?></span>
						</div>
						<?php qa_select_categories('slug', $args = array('orderby' => 'name')) ?>
						<div class="wp-editor-container">
							<textarea name="post_content" id="insert_question"></textarea>
						</div>

						<div id="question-tags-container">
							<input data-provide="typeahead" type="text" class="submit-input tags-input" id="question_tags" name="question_tags" placeholder="<?php _e('Tag (max 5 tags)',ET_DOMAIN) ?>" />
							<span class="tip-add-tag"><?php _e("Press enter to add new tag", ET_DOMAIN) ?></span>
							<ul class="tags-list" id="tag_list"></ul>
						</div>

						<input id="add_tag_text" type="hidden" value="<?php printf(__("You must have %d points to add tag. Current, you have to select existing tags.", ET_DOMAIN), $privi->create_tag  ); ?>" />

						<?php do_action( 'after_insert_question_form' ); ?>

						<?php if(ae_get_option('gg_question_captcha') && $role != 'administrator'){ ?>
							<div class="clearfix"></div>
							<div class="container_captcha">
								<div class="gg-captcha">
									<?php ae_gg_recaptcha(); ?>
								</div>
							</div>
						<?php } ?>

						<button id="btn_submit_question" class="btn-submit-question">
							<?php _e('SUBMIT QUESTION',ET_DOMAIN) ?>
						</button>
						<p class="term-texts">
							<?php qa_tos("question"); ?>
						</p>
					</form>
				</div>
				<div class="body-poll hide">
					<?php if(ae_get_option('poll_maker') !=="0") { ?>
					<form id="submit_poll" class="form_submit_poll">
						<input type="hidden" id="" name="" value="<?php echo wp_create_nonce( 'insert_poll' ); ?>">

						<?php do_action( 'before_insert_poll_form' ); ?>
						<div class="input-poll-title">
							<input type="text" class="submit-input" id="poll_question_title" name="post_title" placeholder="<?php _e('Your Question',ET_DOMAIN) ?>" />
							<span id="charNumPoll"><?php echo ae_get_option('max_width_title', 150);?></span>
						</div>
						<?php qa_select_categories('id', $args = array('orderby' => 'name')) ?>
						<div class="wp-editor-container">
							<textarea name="post_content" id="insert_poll"></textarea>
						</div>
						<div class="answer">
							<ul id="answer_list_poll_edit">
								<li class="item_poll_answer_edit">
									<input type="text" class="input-answer" placeholder="<?php _e('Your answer', ET_DOMAIN); ?>" name="poll_answers[]">
									<input type="hidden" class="answer-color-picker" value="#e6e6e6">
									<div class="function-right">
										<span class="color-box"></span>
									</div>
								</li>
							</ul>
						</div>
						<div class="btn-more-anwser">
							<span class="btn-add-more"><i class="fa fa-plus"></i><?php _e('More answers', ET_DOMAIN);?></span>
							<span><?php printf(__('You can create up to %s answer(s).', ET_DOMAIN), POLL_MAX_ANSWER); ?></span>
						</div>
						<div id="question-tags-container">
							<input data-provide="typeahead" type="text" class="submit-input tags-input" id="poll_question_tags" name="" placeholder="<?php _e('Tag (max 5 tags)',ET_DOMAIN) ?>" />
							<span class="tip-add-tag"><?php _e("Press enter to add new tag", ET_DOMAIN) ?></span>
							<ul class="tags-list" id="poll_tag_list"></ul>
						</div>


						<?php do_action( 'after_insert_poll_form' ); ?>

						<div class="choose-date">
							<div class='input-group date' id='datetimepicker5'>
								<input type='text' class="form-control form-group" name="poll_end_date" placeholder="<?php _e('End day',ET_DOMAIN) ?>" />
								<span class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</span>
							</div>
						</div>
						<div class="chose-multi">
							<!-- <div class="form-group">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="poll_multi_time" value="true"> <span><?php _e('Multi time', ET_DOMAIN)?></span>
									</label>
								</div>
							</div> -->
							<div class="form-group">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="poll_multi_choice" value="true"> <span><?php _e('Multichoice', ET_DOMAIN); ?></span>
									</label>
								</div>
							</div>
						</div>

						<?php if(ae_get_option('gg_question_captcha') && $role != 'administrator'){ ?>
							<div class="clearfix"></div>
							<div class="container_captcha">
								
							</div>
						<?php } ?>

						<button id="btn_submit_poll" class="btn-submit-question">
							<?php _e('SUBMIT A QUESTION',ET_DOMAIN) ?>
						</button>
						<p class="term-texts">
							<?php qa_tos("question"); ?>
						</p>
					</form>
					<?php } ?>
				</div>
				<input id="poll_add_tag_text" type="hidden" value="<?php printf(__("You must have %d points to add tag. Current, you have to select existing tags.", ET_DOMAIN), $privi->create_tag  ); ?>" />
			</div>
		</div>
	</div>
</div>
<!-- MODAL SUBMIT QUESTIONS -->
