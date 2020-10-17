jQuery(document).ready(function($) {
	// SEARCH FORM
	$('.icon-search-top-bar, .close-form-search').click(function(event) {
		$('.icon-search-top-bar').stop().toggleClass('active');
		$('.form-search-wrapper').stop().fadeToggle(300);
		$('.form-input-search').focus();
	});	
	$('.clear-text-search').click(function(event) {
		$('.form-input-search').val('').focus();
	});
	$('.menu-btn').click(function() {
		//$('header').stop().toggleClass('position-fixed');
	    $('.menu-push').stop().slideToggle(300);
	    $('.post-question-form-wrapper').slideUp();
	});
	$('.post-question-btn, .cancel-post-question').click(function() {
		if(currentUser.id == 0){
			window.location.href = ae_globals.introURL;
		} else {

	    $('.post-question-form-wrapper').stop().slideToggle(300);
	    $("input#question_title").focus();
	    $('.menu-push').slideUp();

		}
	});
	$("a.your-remember").click(function(event) {
		event.preventDefault();
		if (!$(this).hasClass('clicked')) {
			$(this).addClass('clicked');
			$("input#remember").val(1);
		} else {
			$(this).removeClass('clicked');
			$("input#remember").val(0);
		}
	});
	var max_width_title = ae_globals.max_width_title,
		max_title = parseInt(max_width_title) + 1;	
	$("#question_title").keyup(function(){
	    el = $(this);
	    if(el.val().length >= max_title){
	        el.val( el.val().substr(0, max_width_title) );
	    } else {
	        $("#charNum").text(max_width_title-el.val().length);
	    }
	});
	$("#poll_question_title").keyup(function(){
	    el = $(this);
	    if(el.val().length >= max_title){
	        el.val( el.val().substr(0, max_width_title) );
	    } else {
	        $("#charNumPoll").text(max_width_title-el.val().length);
	    }
	});

	/*tab pump*/
	$("#tab-question").click(function(){
		$(".body-question").removeClass("hide");
		$(".body-poll").addClass("hide");
		$("#tab-question").addClass("active");
		$("#tab-poll").removeClass("active");
		if(typeof grecaptcha == 'object' && $(".body-poll .container_captcha .gg-captcha").length > 0){
			var captHTML = $(".body-poll .container_captcha .gg-captcha").appendTo('.body-question .container_captcha');
			grecaptcha.reset();
		}
		if($('#mobile_images_upload_container').length > 0 ){
			$('#submit_poll .upload_image').appendTo('#submit_question .container_upload');
		}
	});
	$("#tab-poll").click(function(){
		$(".body-poll").removeClass("hide");
		$(".body-question").addClass("hide");
		$("#tab-question").removeClass("active");
		$("#tab-poll").addClass("active");
		if(typeof grecaptcha == 'object' && $(".body-question .container_captcha .gg-captcha").length > 0){
			var captHTML = $(".body-question .container_captcha .gg-captcha").appendTo('.body-poll .container_captcha');
			grecaptcha.reset();
		}
		if($('#mobile_images_upload_container').length > 0 ){
			$('#submit_question .upload_image').appendTo('#submit_poll .container_upload');
		}
	});
	/*Picker*/
	$('#datetimepicker5').datetimepicker({
		format: 'DD/MM/YYYY',
		icons: {
			previous: 'fa fa-angle-left',
			next: 'fa fa-angle-right',
		}

	});
});

(function($){
$.fn.extend({ 
	hideMaxListItems: function(options) 
	{
		// DEFAULT VALUES
		var defaults = {
			max: 3,
			speed: 'normal',
			moreText:'READ MORE',
			lessText:'READ LESS',
			moreHTML:'<p class="maxlist-more"><a class="more-tag-link" href="javascript:void(0)"></a></p>', // requires class and child <a>
		};
		var options =  $.extend(defaults, options);
		
		// FOR EACH MATCHED ELEMENT
		return this.each(function() {
			var op = options;
			var totalListItems = $(this).children("li").length;
			var speedPerLI = op.speed;
			
			// Get animation speed per LI; Divide the total speed by num of LIs. 
			// Avoid dividing by 0 and make it at least 1 for small numbers.
			// if ( totalListItems > 0 && op.speed > 0  ) { 
			// 	speedPerLI = Math.round( op.speed / totalListItems );
			// 	if ( speedPerLI < 1 ) { speedPerLI = 1; }
			// } else { 
			// 	speedPerLI = 0; 
			// }
			
			// If list has more than the "max" option
			if ( (totalListItems > 0) && (totalListItems > op.max) )
			{
				// Initial Page Load: Hide each LI element over the max
				$(this).children("li").each(function(index) {
					if ( (index+1) > op.max ) {
						$(this).hide(0);
						$(this).addClass('maxlist-hidden');
					}
				});
				// Replace [COUNT] in "moreText" or "lessText" with number of items beyond max
				var howManyMore = totalListItems - op.max;
				var newMoreText = op.moreText;
				var newLessText = op.lessText;
				
				if (howManyMore > 0){
					newMoreText = newMoreText.replace("[COUNT]", howManyMore);
					newLessText = newLessText.replace("[COUNT]", howManyMore);
				}
				// Add "Read More" button
				$(this).after(op.moreHTML);
				// Add "Read More" text
				$(this).next(".maxlist-more").children("a").text(newMoreText);
				
				// Click events on "Read More" button: Slide up and down
				$(this).next(".maxlist-more").children("a").click(function(e)
				{
					// Get array of children past the maximum option 
					var listElements = $(this).parent().prev("ul, ol").children("li"); 
					listElements = listElements.slice(op.max);
					
					// Sequentially slideToggle the list items
					// For more info on this awesome function: http://goo.gl/dW0nM
					if ( $(this).text() == newMoreText ){
						$(this).text(newLessText);
						var i = 0; 
						(function() { $(listElements[i++] || []).slideToggle('fast',arguments.callee); })();
					} 
					else {			
						$(this).text(newMoreText);
						var i = listElements.length - 1; 
						(function() { $(listElements[i--] || []).slideToggle('fast',arguments.callee); })();
					}
					
					// Prevent Default Click Behavior (Scrolling)
					e.preventDefault();
				});
			}
		});
	}
	});
})(jQuery); // End jQuery Plugin
