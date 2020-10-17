jQuery(document).ready(function($) {
	/**
	 * Dropdown menu
	 */
	$(".menu-item-has-children").prepend('<i class="fa fa-chevron-circle-down"></i>');

	/**
	 * Dropdown menu on tablet
	 */
	function tableMenuDropdown() {
		if($(window).width() <= 997) {
			$(".menu-item-has-children i").click(function(){
				var parent = $(this).parent('li');
				var subMenu = parent.find(".sub-menu");

				if(subMenu.is(":hidden")) {
					$(this).addClass("rotate");
					subMenu.slideDown();
				} else {
					$(this).removeClass("rotate");
					subMenu.slideUp();
				}
			});
		}
	}
	tableMenuDropdown();

	// Execute dropdown menu when resize browse
	$(window).resize(function(){
		tableMenuDropdown();
	});

	/* End dropdown menu on tablet */

	if( $("#answers_filter").length > 0 ){
		$('#answers_filter').waypoint('sticky', {
			stuckClass: 'stuck-sticky',
			wrapper: '<div class="sticky-wrapper" />'
		});
	}

	if( $("#question_filter").length > 0 ){
		$('#question_filter').waypoint(function(direction) {
			//console.log('aaa');
			if(direction == "down")
				$('#q_filter_waypoints').fadeIn();
			else
				$('#q_filter_waypoints').fadeOut('fast');

		}, { offset: 0 });
	}
	/*Picker*/
	$('#datetimepicker5').datetimepicker({
		format: 'DD/MM/YYYY',
		icons: {
			previous: 'fa fa-angle-left',
			next: 'fa fa-angle-right',
		}

	});
	/**/
	$('.picker').each(function(){
		$(this).css({background: $(this).data('color')});
	}).click(function(){
		$('.color-box').css({background: $(this).data('color')});
	});
	$('.input-answer').click(function(){
		if($(".box-color-picker").hasClass("hide")){
			$(".box-color-picker").removeClass("hide");
		}else{
			$(".box-color-picker").addClass("hide");
		}
	});

		function getContrastYIQ(hexcolor){
			var r = parseInt(hexcolor.substr(0,2),16);
			var g = parseInt(hexcolor.substr(2,2),16);
			var b = parseInt(hexcolor.substr(4,2),16);
			var yiq = ((r*299)+(g*587)+(b*114))/1000;
			return (yiq >= 128) ? 'black' : 'white';
		}
	$(".add-more-picker").click(function(){
		$("<div class='picker'><input type='submit' class='lettuce' name='topping' value=''></div>").insertBefore(".add-more-picker");
		$('input.lettuce').ColorPicker({
			onSubmit: function(hsb, hex, rgb, el) {	
			event.preventDefault();		
				$(el).val('#'+hex).ColorPickerHide();				
			},			
			onChange: function(hsb, hex, rgb, el){											
				var contrast = getContrastYIQ(hex);
        $(el).css({
          'color' : contrast,
          'background' : '#'+hex,
          'data-color' : '#'+hex,
        });        				
			}
		});	
	});


	$("input.submit-input").on({
		keyUp: function() {
			ae_has_change = true;
		},
		change: function() {
			ae_has_change = true;
		}
	});

	$('form').submit(function() {
		$(window).unbind("beforeunload");
	});

	$(window).bind('beforeunload', function() {
		if (typeof ae_has_change == "undefined") {
			ae_has_change = false;
		}
		if (ae_has_change) {
			return qa_front.texts.close_tab;
		}
	});

    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");

    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)){
		$(".adject").textrotator({
			animation: "dissolve",
			separator: "|",
			speed: 2000
		});
	} else {
		$(".adject").textrotator({
			animation: "flipUp",
			separator: "|",
			speed: 2000
		});
	}

	$(".to_register").click(function(event) {
		$(".group-btn-intro").find('.to_register').removeClass('active');
		$(this).addClass('active');
		var data_log = $(this).attr('data-log');
		console.log(data_log);
		$('.animate').removeClass('active');
		$('#'+data_log).addClass('active');
		return false;
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

	 // run test on initial page load
    checkSize();

    // run test on resize of the window
    $(window).resize(checkSize);
    //Function to the css rule
	function checkSize(){
		if ($(".sampleClass").css("float") == "none" ){
	        // your code here
	        $("#header_search input").focus(function(event) {
				$(this).css('width', '300px');
			});

			$("#header_search input").blur(function(event) {
				$(this).css('width', '150px');
			});
	    }
	    if ($(".sampleClass").css("float") == "left" ){
	        // your code here
	        $("#header_search input").focus(function(event) {
				$(this).css('width', '400px');
			});

			$("#header_search input").blur(function(event) {
				$(this).css('width', '350px');
			});
	    }
	    /*if ($(".sampleClass").css("float") == "right" ){
	        // your code here
	        /!*$("#menu_qa").addClass('col-md-6 col-xs-6');
	        $("#menu_qa").removeClass('col-md-8 col-xs-8');
	        $("#login_qa").addClass('col-md-4 col-xs-4');
	        $("#login_qa").removeClass('col-md-2 col-xs-2');*!/
	    }*/
	}

	//if($(".tabs-buy-package li:first-child").hasClass("active")){
	//	$(".progress-bars").css({"width":"0%", "display":"block"});
	//	$(".finish-progress-bar").css({"display":"none"});
	//}
	/*//buy packaged*
	if($(".tabs-buy-package li:first-child").hasClass("active")){
		$(".progress-bars").css({"width":"0%", "display":"block"});
		$(".finish-progress-bar").css({"display":"none"});
	}
	if($(".tabs-buy-package li:nth-child(3)").hasClass("active")){
		$(".progress-bars").css({"width":"32%", "display":"block"});
		$(".finish-progress-bar").css({"width":"18%", "display":"block"});
		$(".tabs-buy-package li a").css({"color":"#1abc9c"});
		$(".tabs-buy-package li.active a").css({"color":"#8c97b2"});
	}
	//Tabs click
	if($("#plan").click(function(){
		$(".select-plan-step").removeClass("hide");
		$(".authencation-step, .select-payment-step").addClass("hide");
		$(".tabs-buy-package li:first-child").addClass("active");
		$(".tabs-buy-package li:nth-child(2), .tabs-buy-package li:nth-child(3)").removeClass("active");
		$(".progress-bars").css({"width":"0%", "display":"block"});
		$(".finish-progress-bar").css({"display":"none"});
	}))
	if($("#authencation").click(function(){
		$(".authencation-step").removeClass("hide");
		$(".select-plan-step, .select-payment-step").addClass("hide");
		$(".tabs-buy-package li:nth-child(2)").addClass("active");
		$(".tabs-buy-package li:first-child, .tabs-buy-package li:nth-child(3)").removeClass("active");
			$(".progress-bars").css({"width":"19%", "display":"block"});
			$(".finish-progress-bar").css({"width":"7%", "display":"block"});
			$(".tabs-buy-package li:first-child a").css({"color":"#1abc9c"})
	}))
	if($("#payment").click(function(){
		$(".select-payment-step").removeClass("hide");
		$(".select-plan-step, .authencation-step").addClass("hide");
		$(".tabs-buy-package li:nth-child(3)").addClass("active");
		$(".tabs-buy-package li:first-child, .tabs-buy-package li:nth-child(2)").removeClass("active");
			if($(".tabs-buy-package.is_login").hasClass("is_login")){
				$(".tabs-buy-package li:nth-child(2)").addClass("active");
				$(".progress-bars").css({"width":"21%", "display":"block"});
				$(".finish-progress-bar").css({"width":"5%", "display":"block"});
				$(".tabs-buy-package li a").css({"color":"#1abc9c"});
				$(".tabs-buy-package li.active a").css({"color":"#8c97b2"});
			}else{
				$(".progress-bars").css({"width":"32%", "display":"block"});
				$(".finish-progress-bar").css({"width":"18%", "display":"block"});
				$(".tabs-buy-package li a").css({"color":"#1abc9c"});
				$(".tabs-buy-package li.active a").css({"color":"#8c97b2"});
			}
	}))
	*/
	//Step author login or register
	$(".btn-register").click(function(){
		$("#register-form-buy-package").removeClass("hide");
		$("#login-form-buy-package").addClass("hide");
	});
	$(".btn-login").click(function(){
		$("#login-form-buy-package").removeClass("hide");
		$("#register-form-buy-package").addClass("hide");
	});
	/*tab pump*/
	$("#tab-question").click(function(){
		$(".body-question").removeClass("hide");
		$(".body-poll").addClass("hide");
		$("#tab-question").addClass("active");
		$("#tab-poll").removeClass("active");
		if(typeof grecaptcha == 'object' && $(".body-question .container_captcha .gg-captcha").length > 0){
			var captHTML = $(".body-poll .container_captcha .gg-captcha").appendTo('.body-question .container_captcha');
			grecaptcha.reset();
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
	});
	$(".link_sign_up").click(function(event) {
		event.preventDefault();
		$('#signin_form').fadeOut("slow", function() {
			$(this).css({
				'z-index': 1
			});
			$('.modal-title-sign-in').empty().text(qa_front.texts.sign_up);
			$('#signup_form').fadeIn(500).css({
				'z-index': 2
			});
		});
	});

	$(".link_sign_in").click(function(event) {
		event.preventDefault();
		$('#signup_form').fadeOut("slow", function() {
			$(this).css({
				'z-index': 1
			});
			$('.modal-title-sign-in').empty().text(qa_front.texts.sign_in);
			$('#signin_form').fadeIn(500).css({
				'z-index': 2
			});
		});
	});

	$(".link_forgot_pass").click(function(event) {
		event.preventDefault();
		$('#signin_form').fadeOut("slow", function() {
			$(this).css({
				'z-index': 1
			});
			$('.modal-title-sign-in').empty().text(qa_front.texts.forgotpass);
			$('#forgotpass_form').fadeIn(500).css({
				'z-index': 2
			});
		});
	});

	$(".return_link_sign_in").click(function(event) {
		event.preventDefault();
		$('#forgotpass_form').fadeOut("slow", function() {
			$(this).css({
				'z-index': 1
			});
			$('.modal-title-sign-in').empty().text(qa_front.texts.sign_in);
			$('#signin_form').fadeIn(500).css({
				'z-index': 2
			});
		});
	});

	$(".link_change_password").click(function(event) {
		event.preventDefault();
		$(this).fadeOut("slow", function() {
			$('.link_change_profile').fadeIn(500);
		});
		$('.edit_profile_form').fadeOut("slow", function() {
			$(this).css({
				'z-index': 1
			});
			$('.edit_password_form').fadeIn(500).css({
				'z-index': 2
			});
		});
	});
	$(".link_change_profile").click(function(event) {
		event.preventDefault();
		$(this).fadeOut("slow", function() {
			$('.link_change_password').fadeIn(500);
		});
		$('.edit_password_form').fadeOut("slow", function() {
			$(this).css({
				'z-index': 1
			});
			$('.edit_profile_form').fadeIn(500).css({
				'z-index': 2
			});
		});
	});
	if( $('#wp-link-wrap').length > 0 ){
		$('#wp-link-wrap').addClass('modal fade in');
	}
	// if ($('#wp-link-wrap input#link-target-checkbox').length > 0)
	// 	$('#wp-link-wrap input#link-target-checkbox').prop('checked', true);

	// PUSH MENU
	var menuLeft 	  = document.getElementById('cbp-spmenu-s1'),
		menuRight     = document.getElementById('cbp-spmenu-s2'),
		showLeftPush  = document.getElementById('showLeftPush'),
		showRightPush = document.getElementById('showRightPush'),
		menuTop = document.getElementById( 'cbp-spmenu-s3' ),
		showTop = document.getElementById( 'showTop' ),
		body          = document.body;

	if( $('#showRightPush').length > 0 )
		showLeftPush.onclick = function() {
			$('#showRightPush').removeClass('active');
			$('.cbp-spmenu-right').removeClass('cbp-spmenu-open');
			$('#showTop').removeClass('active');
			$('.cbp-spmenu-top').removeClass('cbp-spmenu-open');
			$('body').removeClass('cbp-spmenu-push-toleft');
			classie.toggle(this, 'active');
			classie.toggle(body, 'cbp-spmenu-push-toright');
			classie.toggle(menuLeft, 'cbp-spmenu-open');
		};
	if( $('#showRightPush').length > 0 )
		showRightPush.onclick = function() {
			$('#showLeftPush').removeClass('active');
			$('.cbp-spmenu-left').removeClass('cbp-spmenu-open');
			$('#showTop').removeClass('active');
			$('.cbp-spmenu-top').removeClass('cbp-spmenu-open');
			$('body').removeClass('cbp-spmenu-push-toright');
			classie.toggle(this, 'active');
			classie.toggle(body, 'cbp-spmenu-push-toleft');
			classie.toggle(menuRight, 'cbp-spmenu-open');
		};
	if( $('#showTop').length > 0 )
		showTop.onclick = function() {
			$('#showLeftPush').removeClass('active');
			$('.cbp-spmenu-left').removeClass('cbp-spmenu-open');
			$('#showRightPush').removeClass('active');
			$('.cbp-spmenu-right').removeClass('cbp-spmenu-open');

			$('body').removeClass('cbp-spmenu-push-toleft');			
			$('body').removeClass('cbp-spmenu-push-toright');

			classie.toggle(this, 'active');
			classie.toggle( menuTop, 'cbp-spmenu-open' );
		};


			// 	var menuLeft = document.getElementById( 'cbp-spmenu-s1' ),
			// 	menuRight = document.getElementById( 'cbp-spmenu-s2' ),
			// 	menuTop = document.getElementById( 'cbp-spmenu-s3' ),

			// 	showLeftPush  = document.getElementById('showLeftPush'),
			// 	showRightPush = document.getElementById('showRightPush'),				
			// 	showTop = document.getElementById( 'showTop' ),
			// 	body = document.body;

			// if( $('#showLeftPush').length > 0 )
			// showLeftPush.onclick = function() {
			// 	classie.toggle( this, 'active' );
			// 	classie.toggle( body, 'cbp-spmenu-push-toright' );
			// 	classie.toggle( menuLeft, 'cbp-spmenu-open' );
			// 	disableOther( 'showLeftPush' );

			// 	$('#showRightPush').removeClass('active');

			// };
			// if( $('#showRightPush').length > 0 )
			// showRightPush.onclick = function() {
			// 	classie.toggle( this, 'active' );
			// 	classie.toggle( body, 'cbp-spmenu-push-toleft' );
			// 	classie.toggle( menuRight, 'cbp-spmenu-open' );
			// 	disableOther( 'showRightPush' );
			// };


			// if( $('#showTop').length > 0 )
			// showTop.onclick = function() {
			// 	classie.toggle( this, 'active' );
			// 	classie.toggle( menuTop, 'cbp-spmenu-open' );
			// 	disableOther( 'showTop' );
			// };
			
			// function disableOther( button ) {
				
			// 	if( button !== 'showTop' ) {
			// 		classie.toggle( showTop, 'disabled' );
			// 	}
				
			// 	if( button !== 'showLeftPush' ) {
			// 		classie.toggle( showLeftPush, 'disabled' );
			// 	}
			// 	if( button !== 'showRightPush' ) {
			// 		classie.toggle( showRightPush, 'disabled' );
			// 	}
				
			// }



	// INTRO PAGE
	var window_height = $(window).height();
	$('.intro-page-wrapper').height(window_height);

	// ================== HEART BEAT ================== //
	function send_popup( title, text, popup_class, delay ) {

		// Initialize parameters
		title = title !== '' ? '<span class="title">' + title + '</span>' : '';
		text = text !== '' ? text : '';
		popup_class = popup_class !== '' ? popup_class : 'update';
		delay = typeof delay === 'number' ? delay : 10000;

		var object = $('<div/>', {
		    class: 'notification ' + popup_class,
		    html: title + text + '<span class="close"><i class="fa fa-times"></i></span>'
		});

		$('#popup_container').prepend(object);

		$(object).hide().fadeIn(500);
		//$('html, body').animate({ scrollTop: 60000 }, 'slow');

		setTimeout(function() {

			$(object).fadeOut(500);

		}, delay);

	}

	$('<div/>', { id: 'popup_container' } ).appendTo('body');
	$('body').on('click', 'span.close', function () { $(this).parent().fadeOut(200); });

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
	var config = {
      '.chosen-select'           : {'disable_search': true},
    }
    var dir_rtl = $('html').attr('dir');
    if(dir_rtl == 'rtl') {
    	for (var selector in config) {
    		$(selector).addClass('chosen-rtl');
	     	$(selector).chosen(config[selector]);
	    }
    } else {
    	for (var selector in config) {
	    	$(selector).chosen(config[selector]);
	    }
    }
    
    // ================== CLEAN URL ================== //
    var url = String(document.location.href);
    if( url.indexOf("#") > -1 && !$('body').hasClass('page-template-page-intro') ){
    	var new_url = url.split('#');
    	document.location.href = new_url[0];
    }
    //responsive on iPad Mini
    $(window).resize(function(event) {
    	/* Act on the event */
    	$('body').removeClass('cbp-spmenu-push-toright').removeClass('cbp-spmenu-push-toleft');
    	$('nav.cbp-spmenu').removeClass('cbp-spmenu-open');
    });
});