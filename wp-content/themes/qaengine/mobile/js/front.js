(function (Views, Models, $, Backbone) {
	Views.Front = Backbone.View.extend({
		el: 'body',
	});

	// Create AE.App
	AE.App = new Views.Front();
	if($('#user_id').length > 0) {
		var user = JSON.parse($('#user_id').html());
		AE.App.user = new Models.User(JSON.parse($('#user_id').html()));
	} else {
		AE.App.user = new Models.User();
	}
	Views.ContactModal = AE.Views.Modal_Box.extend({
		events: {
			'submit form#contact_form'  : 'submitContact'
		},
		// initialize modal Contact
		initialize: function() {
			this.blockUi = new AE.Views.BlockUi();
		},
		validate: function(){
			this.submit_validator = $("form#contact_form").validate({
				rules: {
					txt_msg: "required",
				},
				messages: {
					txt_msg: qa_front.form_auth.error_msg
				}
			});
		},
		submitContact: function(event){
			event.preventDefault();
			this.validate();
			var view    = this,
				form    = $(event.currentTarget),
				message = form.find('textarea#txt_msg').val(),
				$button = form.find("input.btn"),
				user_id = form.find("#user_id").val();
			if( this.submit_validator.form() ){
				$.ajax({
					url: ae_globals.ajaxURL,
					type: 'POST',
					data: {
						action: 'et_user_sync',
						method: 'inbox',
						content: {
							user_id: user_id,
							message: message
						}
					},
					beforeSend: function() {
						if( message == '' ){
							AE.pubsub.trigger('ae:notification', {
								msg: qa_front.form_auth.error_msg,
								notice_type: 'error',
							});
							return false;
						}
						view.blockUi.block($button);
					},
					success: function(status) {
						view.blockUi.unblock();
						if(status.success)	{
							view.closeModal();
							AE.pubsub.trigger('ae:notification', {
								msg: status.msg,
								notice_type: 'success',
							});
						} else {
							AE.pubsub.trigger('ae:notification', {
								msg: status.msg,
								notice_type: 'error',
							});
						}
						$("form#contact_form")[0].reset();
					}
				});
			}
		}
	});

	Views.ChangeProfileModal = Backbone.View.extend({
		el : 'body',
		currentUser : [],
		events: {
			'submit form#change_profile' : 'changeProfile',
			'submit form#change_password': 'changePassword',
			// 'click .inbox': 'openContactModal',
			'click .login-url': 'redirectToIntroPage',
			'click .resend-confirm-link': 'sendConfirmEmail'
		},
		initialize: function() {
			$('.inbox').on('click:inbox', this.openContactModal );

			this.blockUi = new AE.Views.BlockUi();
			this.user    = new Models.User(currentUser);
			var $container = $("#user_avatar_container"),
				view = this,
				upload = '';
			if (typeof this.avatar_uploader === "undefined") {
				var id = this.user.get('ID');

				this.avatar_uploader = new AE.Views.File_Uploader({
					el: $container,
					uploaderID: 'user_avatar',
					thumbsize: 'thumbnail',
					multipart_params: {
						_ajax_nonce: $container.find('.et_ajaxnonce').attr('id'),
						action: 'et_user_sync',
						method: 'change_logo',
						author: id
					},
					cbUploaded: function(up, file, res) {
						if (res.success) {
							$('#' + this.container).parents('.desc').find('.error').remove();
						} else {
							$('#' + this.container).parents('.desc').append('<div class="error">' + res.msg + '</div>');
						}
					},
					beforeSend: function(ele) {
						button = $(ele).find('img');
						view.blockUi.block(button);
					},
					success: function() {
						view.blockUi.unblock();
					}
				});
			}
			$('.inbox').click(function() {
				$(this).trigger('click:inbox');
			});
		},
		openContactModal: function(event) {
			event.preventDefault();
			if( typeof this.modalContact == "undefined"){
				this.modalContact = new Views.ContactModal({
					el: $("#contactFormModal")
				});
			}
			this.modalContact.openModal();
		},
		redirectToIntroPage: function(event) {
			event.preventDefault();
			window.location.href = ae_globals.introURL;
		},
		// Send email confirm to user
		sendConfirmEmail: function(event) {
			event.preventDefault();
			var view = this;
			var data = {
				ID: view.user.get("ID"),
				method: "resend_confirm_email",
				action: "et_user_sync"
			};

			$.ajax({
				type: "Post",
				dataType: "json",
				data: data,
				url: ae_globals.ajaxURL,
				beforeSend: function() {
					view.blockUi.block(view.$el);
				},
				success:function(res) {
					if(res.success == true) {
						AE.pubsub.trigger("ae:notification", {
							notice_type: "success",
							msg: res.msg
						});
					} else {
						AE.pubsub.trigger("ae:notification", {
							notice_type: "error",
							msg: res.msg
						})
					}
				},
				complete: function() {
					view.blockUi.unblock();
				}
			})
		},
		changeProfile :function(event){
			event.preventDefault();
			this.submit_validator = $("form#change_profile").validate({
				rules: {
					display_name: "required",
					// user_location: "required",
					user_email: {
						required: true,
						email: true
					},
					user_facebook: {
						url: true
					},
					user_twitter: {
						url: true
					},
					user_gplus: {
						url: true
					}
				},
				messages: {
					display_name: qa_front.form_auth.error_msg,
					// user_location: qa_front.form_auth.error_msg,
					user_email: {
						required: qa_front.form_auth.error_msg,
						email: qa_front.form_auth.error_email,
					}
				}
			});

			var form = $(event.currentTarget),
				$button = form.find("input.btn-submit"),
				data = form.serializeObject(),
				view = this;

			if (this.submit_validator.form()) {

				this.user.set('content', data);
				this.user.save('do_action', 'saveProfile', {
					beforeSend: function() {
						view.blockUi.block($button);
						//console.log('chay');
					},
					success: function(result, status, jqXHR) {
						if (status.success) {
							AE.pubsub.trigger('ae:notification', {
								msg: status.msg,
								notice_type: 'success',
							});
							setTimeout(function(){
								window.location.href = status.redirect;
							},2000);
						} else {
							AE.pubsub.trigger('ae:notification', {
								msg: status.msg,
								notice_type: 'error',
							});
						}
						view.blockUi.unblock();
					}
				});
			}
		},
		changePassword: function(event) {
			event.preventDefault();

			this.change_pass_validator = this.$("form#change_password").validate({
				rules: {
					old_password: "required",
					new_password: "required",
					re_password: {
						required: true,
						equalTo: "#new_password"
					},
				},
				messages: {
					old_password: qa_front.form_auth.error_msg,
					new_password: qa_front.form_auth.error_msg,
					re_password: {
						required: qa_front.form_auth.error_msg,
						equalTo: qa_front.form_auth.error_repass,
					}
				}
			});

			var form = $(event.currentTarget),
				$button = form.find("input.btn-submit"),
				data = form.serializeObject(),
				view = this;

			if (this.change_pass_validator.form()) {

				this.user.set('content', data);
				this.user.save('do_action', 'changePassword', {
					beforeSend: function() {
						view.blockUi.block($button);
						//console.log('chay');
					},
					success: function(result, status, jqXHR) {
						if(status.success){
							AE.pubsub.trigger('ae:notification', {
								msg: status.msg,
								notice_type: 'success',
							});
							setTimeout(function(){
								window.location.href = status.redirect;
								//window.location.href = status.redirect;
								timeOutRedirect(status.redirect, 1000);
							},2000);

						} else {
							AE.pubsub.trigger('ae:notification', {
								msg: status.msg,
								notice_type: 'error',
							});
						}
						view.blockUi.unblock();
					}
				});
			}
		}
	});

	// Report
	if ( typeof( AE.Views.ReportModal ) == 'undefined' ){
		Views.ReportModal = AE.Views.Modal_Box.extend({
			events: {
				'submit form#report_form'  : 'submitReport'
			},
			// initialize modal question

			initialize: function() {
				this.blockUi = new AE.Views.BlockUi();

			},
			submitReport: function(event){
				event.preventDefault();

				var view    = this,
					form    = $(event.currentTarget),
					message = form.find('textarea#txt_report').val(),
					data    = form.serializeObject(),
					$button = form.find("input.btn");

				this.model.set('do_action', 'report');
				this.model.save('data',data, {
					beforeSend: function() {
						if( message == '' ){
							AE.pubsub.trigger('ae:notification', {
								msg: qa_front.form_auth.error_msg,
								notice_type: 'error',
							});
							return false;
						}
						view.blockUi.block($button);
					},
					success: function(result, status, jqXHR) {
						view.blockUi.unblock();
						if(status.success)	{
							view.closeModal();
							AE.pubsub.trigger('ae:afterReport', {
								msg: status.msg,
								notice_type: 'success'
							});
							view.stopListening(AE.pubsub, 'ae:afterReport');
						}
						else{
							AE.pubsub.trigger('ae:notification', {
								msg: status.msg,
								notice_type: 'error',
							});
						}
						$("form#report_form")[0].reset();
					},
				});
			},
			setModel : function (model) {
				this.model = model;
			},
		});
	}
	// Report
	Views.PostListItem = Backbone.View.extend({
		tagName: 'section',
		className: 'list-answers-wrapper answer-item',
		model : [],
		events: {
			'click a.action'  	 		 		 : 'doAction',
			'click a.mb-show-comments'	 		 : 'showCommentsList',
			'click a.add-cmt-in-cmt'	 		 : 'showCommentForm',
			'click a.close-form-post-answers'	 : 'hideCommentForm',
			'submit form.create-comment'	 	 : 'insertComment',
		},
		initialize: function(){
			if($('#mobile_answer_item').length > 0){
				this.template =  _.template($('#mobile_answer_item').html());
			}
			this.blockUi	=	new AE.Views.BlockUi();

			// if( currentUser ) {
			// 	this.currentUser = QAEngine.MobileApp.currentUser ;
			// }
		},
		render: function(model){
			return this.$el.html(this.template(model.toJSON()));
		},
		showCommentForm: function(event){
			event.preventDefault();
			var target = $(event.currentTarget);
			this.$("form.create-comment").slideDown('slow').find("textarea").focus();
			this.$("a.add-cmt-in-cmt").hide();
		},
		hideCommentForm: function(event){
			event.preventDefault();
			var target = $(event.currentTarget);
			this.$("form.create-comment").slideUp();
			this.$("a.add-cmt-in-cmt").show();
		},
		doAction: function(event){
			event.preventDefault();
			var target 		= $(event.currentTarget),
				action 		= target.attr('data-name'),
				userCaps	= currentUser.cap;
				view   		= this;

			if(currentUser.ID == 0){
				AE.pubsub.trigger('ae:notification', {
					msg: qa_front.texts.require_login,
					notice_type: 'error',
				});
				//window.location.href = ae_globals.introURL;
				timeOutRedirect(ae_globals.introURL, 1000);
				return false;
			}

			if( typeof userCaps[action] === 'undefined' &&
				// check action not in privileges
				!( action == 'accept-answer' || action == 'un-accept-answer' || action == 'approve') ) {
				AE.pubsub.trigger('ae:notification', {
					msg: qa_front.texts.enought_points,
					notice_type: 'error',
				});
				return false;
			}

			if(target.hasClass('loading'))
				return false;

			/* ========== ON VOTEs ========== */
			if(action == "vote_up" || action == "vote_down"){

				if (currentUser.ID == this.model.get("post_author") || target.hasClass('disabled'))
					return;

				if(target.hasClass('active')) {
					target.removeClass('active');
					view.$el.find('div.vote-wrapper a.vote').removeClass('disabled');
				}else{
					view.$el.find('div.vote-wrapper a.vote').removeClass('active').addClass('disabled');
					target.addClass('active').removeClass('disabled');
				}
			  /* ========== ON MARK ACCEPT ========== */
			} else if(action == "accept-answer" || action == "un-accept-answer"){

				if(target.hasClass('best-answers')) {
					target.removeClass('best-answers')
						.addClass('pending-answers')
						.text(qa_front.texts.accept_txt)
						.attr('data-name', 'accept-answer');
				}else{
					$('a.answer-active-label').not('.has-best-answer').removeClass('best-answers')
											.addClass('pending-answers')
											.text(qa_front.texts.accept_txt)
											.attr('data-name', 'accept-answer');
					target.addClass('best-answers')
						.removeClass('pending-answers')
						.html('<i class="fa fa-check"></i>'+qa_front.texts.best_ans_txt)
						.attr('data-name', 'un-accept-answer');
				}
			  /* ========== ON EDIT POST ========== */
			} else if( action == "approve" ){
				this.model.set('do_action', action);
				this.model.save('', '', {
					beforeSend: function() {
						target.addClass('loading');
						view.blockUi.block(view.$el);
					},
					success: function(result, status, jqXHR) {
						view.blockUi.unblock();
						target.removeClass('loading');
						if (status.success) {
							target.remove();
							view.$el.find('.pending-ans').remove();
						} else {
							AE.pubsub.trigger('ae:notification', {
								msg: status.msg,
								notice_type: 'error',
							});
						}
					}
				});
				return false;
			}

			this.model.set('do_action', action);
			this.model.save('', '', {
				beforeSend:function(){
					target.addClass('loading');
				},
				success : function (result, status, jqXHR) {
					target.removeClass('loading');
					if(status.success){
						if(action == "vote_up" || action == "vote_down")
							view.$el.find('span.number-vote').text(result.get('et_vote_count'));
					} else {
						AE.pubsub.trigger('ae:notification', {
							msg: status.msg,
							notice_type: 'error',
						});
					}
				}
			});
		},
		showPostControls: function(event){
			this.$el.find('ul.post-controls').fadeIn('slow');
		},
		hidePostControls: function(event){
			this.$el.find('ul.post-controls').fadeOut('slow');
		},
		showCommentsList: function(event){
			event.preventDefault();
			var target 	 = $(event.currentTarget),
				countCmt = this.$(".cmt-in-cmt-wrapper .mobile-comments-list li").length,
				post_id = this.$(".mb-show-comments").attr('data-post-id'),
				btn_add_comment = '#btn-add-comment-' + this.$(".mb-show-comments").attr('data-post-id'),
				list_comment = '#list-comment-' + this.$(".mb-show-comments").attr('data-post-id');
			this.$(".cmt-in-cmt-wrapper .add-cmt-in-cmt").hide();
			$("a.mb-show-comments").removeClass('active-comment');
			target.addClass('active-comment');
			$(list_comment).stop().slideToggle(300);

			if(countCmt == 0){
				this.$("form.create-comment").slideDown('slow').find("textarea").focus();
			} else{
				$(btn_add_comment).css('display','block');
			}
			return false;
		},
		insertComment: function(event){
			event.preventDefault();
			var form = $(event.currentTarget),
				$button = form.find("input.btn-submit"),
				textarea = form.find('textarea'),
				data = form.serializeObject(),
				view = this;

			if(currentUser.ID == 0){
				AE.pubsub.trigger('ae:notification', {
					msg: qa_front.texts.require_login,
					notice_type: 'error',
				});
				//window.location.href = ae_globals.introURL;
				timeOutRedirect(ae_globals.introURL, 1000);
				return false;
			}

			if(textarea.val() == ''){
				textarea.focus();
				return;
			}

			comment = new Models.Post();
			comment.set('content',data);
			comment.save('','',{
				beforeSend:function(){
					view.blockUi.block($button);
				},
				success : function (result, status, jqXHR) {
					view.blockUi.unblock();
					if(status.success){
						viewPost = new Views.CommentItem({
							id: result.get('comment_ID'),
							model: result
						});
						textarea.val('').focusout();
						view.$el.find('ul.mobile-comments-list').append(viewPost.render(result));
					} else {
						AE.pubsub.trigger('ae:notification', {
							msg: status.msg,
							notice_type: 'error',
						});
					}
				}
			});
		},
	});

	Views.CommentItem = Views.PostListItem.extend({
		tagName: 'li',
		className: 'comment-item',
		events: {
			// 'click a.action'  	 		: 'doAction',
			// 'click a.edit-comment'		: 'editComment',
			// 'click a.cancel-comment'	: 'cancelComment',
			// 'submit form.edit-comment'  : 'updateComment',
		},
		initialize: function(){
			Views.PostListItem.prototype.initialize.call();
			this.model.set('id',this.model.get('comment_ID'));
			if($('#mobile_comment_item').length > 0){
				this.template = _.template($('#mobile_comment_item').html());
			}
			this.blockUi = new AE.Views.BlockUi();
		},
		editComment: function(event){
			event.preventDefault();
			console.log('edit comment');
			var view 	= this,
				txtID 	= view.$el.find('div.cm-content-edit textarea').attr('id'),
				content = this.model.get('comment_content');


			view.$el.find('div.cm-content-wrap').fadeOut('fast', function() {
				tinymce.EditorManager.execCommand("mceAddEditor", false, txtID);
				tinymce.activeEditor.execCommand('mcesetContent', false, content);
				view.$el.find('div.cm-content-edit').fadeIn('fast', function() {
					tinymce.activeEditor.execCommand('mceAutoResize');
				});
			});
		},
		cancelComment: function(event){
			event.preventDefault();
			//console.log('cancel comment');
			var view 	= this,
				txtID 	= view.$el.find('div.cm-content-edit textarea').attr('id');

			view.$el.find('div.cm-content-edit').fadeOut('fast', function() {
				view.$el.find('div.cm-content-wrap').fadeIn();
				tinymce.EditorManager.get(txtID).remove();
			});
		},

		/**
		 * update comment model.save
		*/
		updateComment: function(event){
			event.preventDefault();
			var $target		= $(event.currentTarget);
				view 		= this,
				txtID 		= view.$el.find('div.cm-content-edit textarea').attr('id'),
				new_content = tinymce.EditorManager.get(txtID).getContent();

			this.model.set('comment_content', new_content);
			this.model.set('do_action', 'saveComment');

			this.model.save('', '', {
				beforeSend:function(){
					view.blockUi.block($target);
				},
				success : function (result, status, jqXHR) {
					view.blockUi.unblock();
					if(status.success){
						view.$el.find('div.cm-content-edit').fadeOut('fast', function() {
							view.$el.find('div.cm-content-wrap .cm-wrap').html(result.get('content'));
							view.$el.find('div.cm-content-wrap').fadeIn();
							tinymce.EditorManager.get(txtID).remove();
						});
					} else {
						AE.pubsub.trigger('ae:notification', {
							msg: status.msg,
							notice_type: 'error',
						});
					}
				}
			});
		}
	});

	Views.TagItem = Backbone.View.extend({

		'tagName'	: 'li',
		'className' : 'tag-item',
		events 		: {
			'click a.delete' : 'deleteItem'
		},
		//template 	: _.template( $('#tag_item').html() ),
		initialize: function(){
			if( $('#tag_item').length > 0 )
				this.template = _.template( $('#tag_item').html() );
		},
		render : function(){
			this.$el.html( this.template( this.model.toJSON() ) );
			return this;
		},
		deleteItem: function(event){
			event.preventDefault();
			this.$el.fadeOut('normal', function(){
				$(this).remove();
			});
		}
	});

	Views.MobileFront = Backbone.View.extend({
		el : 'body',
		currentUser : [],
		events : {
			'change select#filter-numbers'		: 'sortPostNumber',
			'change select#move_to_category' 	: 'moveToCategory',
			'submit form#sign_in' 		 		: 'doLogin',
			'submit form#sign_up' 		 		: 'doRegister',
			'submit form#submit_question' 		: 'saveQuestion',
			'submit form#form_forgot_password_mobile' : 'sendMailResetPassword',
			'submit form#resetpass_form' : 'resetPassword',
			'keypress input#question_tags'		: 'onAddTag',
			'click .toggle-list-categories' : 'toggleListCategories',
			//Follow or unfollow on mobile
			'click .mobile-follow-question' : 'doFollowQuestion',
			'click .mobile-unfollow-question' : 'doUnFollowQuestion'
		},
		initialize: function(){
			var view = this;

			/**
			 * tags list container
			*/
			this.tag_list = this.$('ul.post-question-tags');
			this.model    = new Models.Post();
			this.blockUi  =	new AE.Views.BlockUi();

			$('ul.mobile-tags-list').hideMaxListItems({
				'moreText':'Touch here to show more tags',
				'lessText':'Touch here to show less tags',
				'max':4
			});

			/**
			 * type ahead to get suggestion
			*/
			view.tags	= {};

			$('#question_tags').typeahead({
				minLength: 0,
				items : 99,
				source: function (query, process)
						{
							if(view.tags.length > 0 ) return view.tags;

							return $.getJSON(
									ae_globals.ajaxURL,
									{ action : 'qa_get_tags'},
									function (data) {
										console.log(data);
										view.tags	=	data;
										return process(data);
								});

						},
				updater : function (item) {
					//console.log(item);
					view.addTag(item);
				}
			});
			// notification template
			this.noti_templates = new _.template(
				'<div class="pubsub-notification autohide {{= type }}-bg">' +
				'<div class="main-center">' +
				'{{= msg }}' +
				'</div>' +
				'</div>'
			);
			// catch event nofifications
			AE.pubsub.on('ae:notification', this.showNotice, this);

			// Upload image for question
			this.uploadImage(
				'#mobile_images_upload_browse_button', 	// Upload button
				'#mobile_images_upload_container', 		// Upload container
				'mobile_images_upload', 				// Upload ID
				'#submit_question textarea' 			// Textarea to receive shortcode
			);

			// Upload image for answer
			this.uploadImage(
				'#answer_images_upload_browse_button',			// Upload button
				'#answer_images_upload_container',		// Upload container
				'answer_images_upload',			// Upload ID
				'#insert_answer textarea'		// Textarea to receive shortcode
			);
		},
		// Uploader for mobile
		uploadImage: function(upload_button, upload_container, upload_id, upload_textarea) {
			var $images_upload = $(upload_container);
			var $upload_image_button = $(upload_button);
			var view = this;
			this.uploader = new AE.Views.File_Uploader({
				el: $images_upload,
				uploaderID: upload_id,
				multi_selection: false,
				unique_names: false,
				upload_later: false,
				filters: [{
					title: "Image Files",
					extensions: 'gif,jpg,jpeg,png'
				}, ],
				multipart_params: {
					_ajax_nonce: $images_upload.find('.et_ajaxnonce').attr('id'),
					action: 'et_upload_images'
				},
				cbAdded: function(up, files) {
					if (up.files.length > 1) {
						while (up.files.length > 1) {
							up.removeFile(up.files[0]);
						}
					}
				},
				cbUploaded: function(up, file, res) {
					if (res.success == true) {
						var textarea = $(upload_textarea);
						var post_content = textarea.val();
						$('.body-poll, .body-question').each(function(){

							if(!$(this).hasClass('hide')){
								console.log($(this).data('name'));
								if($(this).data('name') == 'poll'){
									tinymce.get('insert_poll').execCommand('mceInsertContent', false, "[img]" + res.data + "[/img]");
								}else{
									tinymce.get('post_content').execCommand('mceInsertContent', false, "[img]" + res.data + "[/img]");
								}
							}
						})
						post_content = post_content + '[img]'+ res.data +'[/img]';
						textarea.val(post_content);
					} else {
						AE.pubsub.trigger('ae:notification', {
							'notice_type' : 'error',
							'msg' : res.msg
						})

						view.blockUi.unblock();
					}
				},
				beforeSend: function() {
					view.blockUi.block($upload_image_button);
				},
				success: function() {
					view.blockUi.unblock();
				}
			});
		},
		/*
		 * Show notification
		 */
		showNotice: function(params) {
			var view = this;
			// remove existing notification
			$('div.notification').remove();

			var notification = $(view.noti_templates({
				msg: params.msg,
				type: params.notice_type
			}));

			if ($('#wpadminbar').length !== 0) {
				notification.addClass('having-adminbar');
			}

			notification.hide().prependTo('body')
				.fadeIn('fast')
				.delay(1000)
				.fadeOut(5000, function() {
					$(this).remove();
				});
		},
		saveQuestion: function(event){
			event.preventDefault();

			/**
			 * set validate form condition
			*/
			this.submit_validator	= $("form#submit_question").validate({
				rules	: {
					post_title			: "required",
					question_category	: "required",
					post_content		: "required",
				},
				messages: {
					post_title			: qa_front.form_auth.error_msg,
					question_category	: qa_front.form_auth.error_msg,
					post_content		: qa_front.form_auth.error_msg,
				}
			});

			if(ae_globals.user_confirm && currentUser.register_status == "unconfirm"){
				AE.pubsub.trigger('ae:notification', {
					msg: qa_front.texts.confirm_account,
					notice_type: 'error',
				});
				return false;
			}

			if(currentUser.ID == 0){
				AE.pubsub.trigger('ae:notification', {
					msg: qa_front.texts.require_login,
					notice_type: 'error',
				});
				//window.location.href = ae_globals.introURL;
				timeOutRedirect(ae_globals.introURL, 1000);
				return false;
			}

			var form = $(event.currentTarget),
			$button  = form.find("button.submit-post-question"),
			textarea = form.find('textarea'),
			data     = form.serializeObject(),
			captcha  = $("#g-recaptcha-response").val(),
			view     = this;

			// if( this.tag_list.find('li').length == 0 ) { // user should enter at least on tag
			// 	$("input#question_tags").attr('placeholder', 'Please insert at least one tag.').css('border', '1px solid red');
			// }

			if(	this.submit_validator.form()
				&& textarea.val() != ""
				/*&& this.tag_list.find('li').length > 0*/ ){

				if(form.find("#g-recaptcha-response").length) data.captcha = captcha;
				this.model.set('content',data);
				this.model.save('do_action','saveQuestion',{
					beforeSend:function(){
						view.blockUi.block($button);
					},
					success : function (result, status, jqXHR) {
						view.blockUi.unblock();
						if(status.success){
							AE.pubsub.trigger('ae:notification', {
								msg: status.msg,
								notice_type: 'success',
							});
							//window.location.href = status.redirect;
							timeOutRedirect(status.redirect, 1000);
						} else {
							//alert(status.msg);
							if(captcha) grecaptcha.reset();
							AE.pubsub.trigger('ae:notification', {
								msg: status.msg,
								notice_type: 'error',
							});
						}
					}
				});
			}
		},
		sendMailResetPassword: function(event) {
			event.preventDefault();

			//Validate form
			this.submit_validator = $("form#form_forgot_password_mobile").validate({
				rules: {
					user_email: {
						required: true,
						email: true
					}
				},
				messages: {
					user_email: {
						require: qa_front.form_auth.error_msg,
						email: qa_front.form_auth.error_email
					}
				}
			});

			if(this.submit_validator.form()) {
				$user_email_input = $("#form_forgot_password_mobile input[name='user_email']");
				$user_email = $user_email_input.val();
				$submit_button = $("#form_forgot_password_mobile input[type='submit']");
				var view = this;
				$.ajax({
					type: 'post',
					url: ae_globals.ajaxURL,
					data: {
						action: 'et_user_sync',
						content: {
							action: 'forgot',
							user_login: $user_email
						},
						method: 'read'
					},
					beforeSend: function() {
						view.blockUi.block($submit_button);
					},
					success: function(res) {
						view.blockUi.unblock();
						$user_email_input.val('');
						if(res.success == true) {
							AE.pubsub.trigger('ae:notification', {
								msg: res.msg,
								notice_type: 'success',
							});
						} else {
							AE.pubsub.trigger('ae:notification', {
								msg: res.msg,
								notice_type: 'error',
							});
						}
					}
				})
			}

			return false;
		},
		resetPassword: function(event) {
			event.preventDefault();
			this.user = new Models.User();
			var view = this;

			// Validate form
			view.submit_validator = $('form#resetpass_form').validate({
				rules: {
					re_new_password: "required",
					new_password: "required",
					re_new_password: {
						required: true,
						equalTo: "#new_password"
					},
				},
				messages: {
					re_new_password: qa_front.form_auth.error_msg,
					new_password: qa_front.form_auth.error_msg,
					re_new_password: {
						required: qa_front.form_auth.error_msg,
						equalTo: qa_front.form_auth.error_repass,
					}
				}
			});

			// Get data
			var form = $(event.currentTarget),
				$button = form.find('input.btn-submit'),
				user_login = form.find('input#user_login').val(),
				user_key = form.find('input#user_key').val(),
				new_pass = form.find('input#new_password').val();

			if(view.submit_validator.form()) {
				view.user.resetpass(user_login, new_pass, user_key,{
					beforeSend: function() {
						view.blockUi.block($button);
					},
					success: function(result, res, xhr) {
						view.blockUi.unblock();
						if(res.success == true) {
							AE.pubsub.trigger('ae:notification', {
								msg: res.msg,
								notice_type: 'success',
							});

							window.location.href = res.redirect;
						} else {
							AE.pubsub.trigger('ae:notification', {
								msg: res.msg,
								notice_type: 'error',
							});
						}
					}
				})
			}
		},
		/**
		 * add tag to modal, render tagItem base on in put tag
		*/
		addTag: function(tag){

			$('.body-question,.body-poll ').each(function(){
				if(!$(this).hasClass('hide')){
					var tag_list = $(this).find('.post-question-tags');
				
					var duplicates 	= tag_list.find('input[type=hidden][value="' + tag + '"]'),
						count 		= tag_list.find('li');
					if( count.length > 5 || duplicates.length > 0 ){
						$('input#question_tags').val('');
						return false;
					}

					if ( duplicates.length == 0 && tag != '' && count.length < 5 ){
						var data = { 'name' : tag };
						var tagView = new Views.TagItem( { model : new Backbone.Model(data) } );
						tag_list.append( tagView.render().$el );
						$('input#question_tags').val('').css('border', 'none');
					}
				}
			})
		},
		/**
		 * catch event user enter in tax input, call function addTag to render tag item
		*/
		onAddTag: function(event){

			var val = $(event.currentTarget).val(),
				code = event.keyCode || event.which;

			if ( code == 13 ){
				/**
				 * check current user cap can add_tag or not
				*/
				var caps 	=	currentUser.cap;
				if( typeof caps['create_tag'] === 'undefined' &&  $.inArray( val, this.tags ) == -1) {
					AE.pubsub.trigger('ae:notification', {
						msg: this.$('#add_tag_text').val(),
						notice_type: 'error',
					});
					return false;
				}

				if( val.trim() == "" )
					return false;

				/**
				 * add tag
				*/
				this.addTag(val.trim());
			}
			return code != 13;
		},
		toggleListCategories: function(event) {
			event.preventDefault();
			var menuCate = $('#menu-categories');
			var toggle = $('.toggle-list-categories');
			if(menuCate.is(':hidden')) {
				menuCate.slideDown();
				toggle.find('i').addClass('rotate');
			} else {
				menuCate.slideUp();
				toggle.find('i').removeClass('rotate');
			}
			return false;
		},
		doLogin: function(event){
			event.preventDefault();
			this.user = new Models.User(currentUser);
			this.login_validator = 	 $("form#sign_in").validate({
				rules	: {
					username		: "required",
					password		: "required",
				},
				messages: {
					username	: qa_front.form_auth.error_msg,
					password 	: qa_front.form_auth.error_msg,
				}
			});

			var form 	 = $(event.currentTarget),
				username = form.find('input#username').val(),
				password = form.find('input#password').val(),
				remember = form.find('input#remember').val(),
				redirect = form.find('input#redirect').val(),
				button   = form.find('input.btn-submit'),
				view 	 = this;
				console.log(redirect);
			if(this.login_validator.form()){
				this.user.login(username, password, remember, {
					beforeSend:function(){
						view.blockUi.block(button);
					},
					success : function (user, status, jqXHR) {
						view.blockUi.unblock();
						if(status.success){
							AE.pubsub.trigger('ae:notification', {
								msg: status.msg,
								notice_type: 'success',
							});
							//window.location.href = status.redirect;
							if( redirect == "" ) {
								timeOutRedirect(status.redirect, 1000);
							} else {
								timeOutRedirect(redirect, 1000);
							}
						} else {
							AE.pubsub.trigger('ae:notification', {
								msg: status.msg,
								notice_type: 'error',
							});
						}
					}
				});
			}
		},
		doRegister: function(event){
			event.preventDefault();
			this.user = new Models.User();
			this.register_validator = 	 $("form#sign_up").validate({
				rules	: {
					username		: "required",
					password		: "required",
					email			: {
						required: true,
						email: true
					},
					re_password 	: {
						required: true,
						equalTo: "#password1"
					}
				},
				messages: {
					username : qa_front.form_auth.error_msg,
					password : qa_front.form_auth.error_msg,
					email 	 : {
						required : qa_front.form_auth.error_msg,
						email : qa_front.form_auth.error_email,
					},
					re_password: {
						required: qa_front.form_auth.error_msg,
						equalTo: qa_front.form_auth.error_repass,
					}
				}
			});

			var form = $(event.currentTarget),
				username = form.find('input#username').val(),
				email    = form.find('input#email').val(),
				button   = form.find('input.btn-submit'),
				password = form.find('input#password1').val(),
				data     = form.serializeObject(),
				captcha  = $("#g-recaptcha-response").val(),
				view     = this;

			if(this.register_validator.form()){
				//send captcha result
				if(captcha)
					this.user.set('captcha', captcha);
				//send all data to server
				this.user.register(data, {
					beforeSend:function(){
						view.blockUi.block(button);
					},
					success : function (user, status, jqXHR) {
						view.blockUi.unblock();
						if(status.success){
							AE.pubsub.trigger('ae:notification', {
								msg: status.msg,
								notice_type: 'success',
							});
							//window.location.href = status.redirect;
							timeOutRedirect(status.redirect, 1000);
						} else {
							AE.pubsub.trigger('ae:notification', {
								msg: status.msg,
								notice_type: 'error',
							});
						}
					}
				});
			}

		},
		moveToCategory: function(event) {
			event.preventDefault();
			var target = $(event.currentTarget);

			var sort = this.urlParam('sort');
			if (target.val() != "" && sort != "") {
				window.location.href = target.val() + "?sort=" + sort;
			} else {
				window.location.href = target.val();
			}

		},
		urlParam: function(name) {
			var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
			if(results == null) {
				return "";
			} else {
				return results[1] || 0;
			}
		},
		sortPostNumber: function(event){
			event.preventDefault();
			var target = $(event.currentTarget);
			if(target.val())
				window.location.href = target.val();
		},
		// Follow a question
		doFollowQuestion: function(event) {
			this.followQuestion(event, 'follow');
		},
		// Unfollow a question
		doUnFollowQuestion: function(event) {
			this.followQuestion(event, 'unfollow');
		},
		followQuestion: function(event, action) {
			event.preventDefault();
			var $target = $(event.currentTarget),
				view = this,
				modelData = '';

			if(typeof currentQuestion !== 'undefined') {
				modelData = currentQuestion;
			} else if(typeof currentPoll !== 'undefined') {
				modelData = currentPoll;
			}

			var model = new Models.Post(modelData);
			model.set('do_action', action);
			model.save('', '', {
				beforeSend: function() {
					view.blockUi.block($target);
				},
				success: function(res, status, xhr) {
					if(status.success == true) {
						// Change button
						if(action == 'follow') {
							$('.mobile-follow-question').hide();
							$('.mobile-unfollow-question').show();
						} else {
							$('.mobile-unfollow-question').hide();
							$('.mobile-follow-question').show();
						}

						AE.pubsub.trigger('ae:notification', {
							msg: status.msg,
							notice_type: 'success',
						});
					} else {
						AE.pubsub.trigger('ae:notification', {
							msg: status.msg,
							notice_type: 'error',
						});
					}
				},
				complete: function() {
					view.blockUi.unblock();
				}
			});
		},
	});
	function timeOutRedirect(url, time, reload){
		time   = typeof time !== 'undefined' ? time : 1;
		reload = typeof reload !== 'undefined' ? reload : false;
		setTimeout(function(){
			if(reload)
				window.location.reload();
			else
				window.location.href = url;
		},time);
	}

	// Infinite Scroll
	$(document).ready(function(){
		InfinitiScroll = Backbone.View.extend({
			el: 'body',
			initialize : function(){
				var view = this;

				$(window).scroll(function(){
					if($(window).scrollTop() == $(document).height() - $(window).height()  && $("#post_loading").attr('data-fetch') == 1 ){
						view.ajaxData(query_default);
					}
				});
				var loading 		= $('body').find('#post_loading'),
					fetch 			= $(loading).data('fetch'),
					type 			= $(loading).data('type'),
					term 			= $(loading).data('category'),
					posts_per_page 	= $(loading).data('current-page'),
					sort 			= $(loading).data('sort'),
					keyword 			= $(loading).data('keyword'),
					query_default = {
								action : 'et_post_sync',
								method : 'scroll',
								data : {
									posts_per_page : posts_per_page,
									type : type,
									term : term,
									sort : sort,
									page : 1,
									keyword : keyword
								}
							};
				setInterval(function(){
					if($('ul.list-question li').length < 6  && $("#post_loading").attr('data-fetch') == 1){
						view.ajaxData(query_default);
					}							
				}, 3000);	
			},
			ajaxData : function(query_default){
				var loading = $('body').find('#post_loading');
				query_default['data']['page'] += 1;

				$.ajax({
					url : ae_globals.ajaxURL,
					type : 'post',
					data : query_default,
					beforeSend : function(){
						$(loading).removeClass('hide');
						$(loading).attr('data-fetch',0);
					},
					error : function(){
						$(loading).addClass('hide');
						$(loading).attr('data-fetch',1);
					},
					success : function (response){
						setTimeout(function(){
							if(response.success){
								var container = $('body').find('ul.list-question'),
									questions = response.data.questions;
								for (key in questions){
									$(container).append(questions[key]);
								}
								$(loading).addClass('hide');
								$(loading).attr('data-fetch',1);
							}else{
								$(loading).addClass('hide');
							}
							
						},1500);
					}
				});	
			}
		});
		if(ae_globals.is_infinite){
			new InfinitiScroll();
		}
		// ================== HEART BEAT ================== //
		function send_popup( title, text, popup_class, delay ) {
			// Initialize parameters
			title = title !== '' ? '<span class="title">' + title + '</span>' : '';
			text = text !== '' ? text : '';
			popup_class = popup_class !== '' ? popup_class : 'update';
			delay = typeof delay === 'number' ? delay : 10000;

			var object = $('<div/>', {
			    class: 'notification mobile-notification ' + popup_class,
			    html: title + text + '<span class="close-notifi"></span>'
			});

			$('#popup_container').prepend(object);

			$(object).hide().fadeIn(500);
			//$('html, body').animate({ scrollTop: 60000 }, 'slow');

			setTimeout(function() {

				$(object).fadeOut(500);

			}, delay);
			$('span.close-notifi').click(function(event) {
				//alert("Close noti");
				$('.mobile-notification').parent().fadeOut(200);
			});
			//$('body').on('click', 'span.close-notifi', function () { $(this).parent().fadeOut(200); });

		}

		$('<div/>', { id: 'popup_container' } ).appendTo('body');

		var check;

	    $(document).on( 'heartbeat-tick', function( e, data ) {
			//console.log(data);
	        if ( !data['message'] )
	        	return;

			$.each( data['message'], function( index, notification ) {
				if ( index != check ){
					send_popup( notification['title'], notification['content'], notification['type'] );
				}
				check = index;
			});

	    });
		// ================== HEART BEAT ================== //
	});
})(QAEngine.Views, QAEngine.Models, jQuery, Backbone);