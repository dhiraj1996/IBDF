(function(Views, Models, $, Backbone) {

	Views.UserProfile = Backbone.View.extend({
		el: 'body',

		events: {
			'click a.show-edit-form': 'openEditProfileForm',
			'click .inbox': 'openContactModal',
			'click .resend-confirm-link': 'sendConfirmEmail',
		},

		initialize: function() {
			this.user    = new Models.User(currentUser);
			this.blockUi = new AE.Views.BlockUi();
		},
		openEditProfileForm: function(event) {
			event.preventDefault();
			if( typeof this.modalEditProfile == "undefined"){
				this.modalEditProfile = new Views.EditProfileModal({
					el: $("#edit_profile")
				});
			}
			this.modalEditProfile.openModal();
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
		}
	});

})(QAEngine.Views, QAEngine.Models, jQuery, Backbone);