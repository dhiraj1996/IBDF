/**
 * Created by tatthien on 11/26/2015.
 */
(function($, Views, Models, AE){
    $(document).ready(function() {
        $('.btn-pump').on('click', function() {

        });
        var pumpingView = new Views.Pumping();
        var buyPackageView = new Views.BuyPackage({el: '.buy-package'});
    });

    Views.Pumping = Backbone.View.extend({
        el: 'body',
        events: {
            'click .btn-buy' : 'actionBuyPump',
            'click .btn-pump': 'actionPump',
            'click #run_pump_setup': 'setupPump',
            'submit #pump_login_form' : 'doLogin'
        },
        initialize: function() {
            this.blockUi = new Views.BlockUi();
            this.user = new QAEngine.Models.User();
            this.buyPackageView = new Views.BuyPackage();
            var view = this;
            $('.author .question-item').each(function(event) {
                var li = $(this);
                var pumpButton = li.find('.pump-deactive .btn-pump');
                var timeButton = li.find('.pump-deactive .btn-time');
                var countDownFormat = timeButton.data('countdown-format');
                var countDownTime = timeButton.data('countdown-time');
                view.countDownPump(countDownFormat, countDownTime, timeButton, pumpButton);
            });

            // Countdown for single question
            var li = $('.single-question-pump');
            var pumpButton = li.find('.pump-deactive .btn-pump');
            var timeButton = li.find('.pump-deactive .btn-time');
            var countDownFormat = timeButton.data('countdown-format');
            var countDownTime = timeButton.data('countdown-time');
            view.countDownPump(countDownFormat, countDownTime, timeButton, pumpButton);
        },
        actionBuyPump: function(event) {
            if(currentUser.register_status == "unconfirm") {
                AE.pubsub.trigger('ae:notification', {
                    msg: qa_front.texts.buy_pump,
                    notice_type: 'error',
                });
                return false;
            } else {
                window.location.href = ae_globals.buy_pump_link;
            }
        },
        /**
         * Action pump
         */
        actionPump: function(event) {
            event.preventDefault();
            var $pumpButton = $(event.currentTarget),
                $timeButton = $pumpButton.next('.btn-time');
                questionItem = $pumpButton.parents('li.question-item');

            //Check user loggin
            if(currentUser.ID != 0) {
                var id = $pumpButton.data('id');
                var view = this;
                $.ajax({
                    type: 'POST',
                    url: ae_globals.ajaxURL,
                    data: {
                        id: id,
                        action: 'qa_pump_sync',
                        method: 'pumping'
                    },
                    beforeSend: function() {
                        view.blockUi.block($pumpButton);
                    },
                    success: function(res) {
                        if(res.success == true) {
                            //Change pump button to countdown
                            $pumpButton.addClass('hide');
                            $timeButton.removeClass('hide');

                            //Update user pump number
                            $('.number-pump .number').text(res.data.pump_number);

                            //Init countdown
                            view.countDownPump(res.data.countdown_format, res.data.countdown_time, $timeButton, $pumpButton);

                            AE.pubsub.trigger('ae:notification', {
                                notice_type: 'success',
                                msg: res.msg
                            })
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                notice_type: 'error',
                                msg: res.msg
                            })
                        }

                        view.blockUi.unblock();
                    }
                });
            }
        },

        setupPump: function(event) {
            event.preventDefault();
            var $setupButton = $(event.currentTarget),
                view = this;

            $.ajax({
                type: 'POST',
                url: ae_globals.ajaxURL,
                data: {
                    action: 'qa_pump_sync',
                    method: 'setup'
                },
                beforeSend: function() {
                    view.blockUi.block($setupButton);
                },
                success: function(res) {
                    if(res.success == true) {
                        AE.pubsub.trigger('ae:notification', {
                            notice_type: 'success',
                            msg: res.msg
                        })
                    } else {
                        AE.pubsub.trigger('ae:notification', {
                            notice_type: 'error',
                            msg: res.msg
                        })
                    }

                    view.blockUi.unblock();
                }
            });
        },
        //Step authentication: Do login
        doLogin: function(event) {
            event.preventDefault();
            event.stopPropagation();
            this.login_validator = $("form#pump_login_form").validate({
                rules: {
                    username: "required",
                    password: "required",
                },
            });

            var form     = $(event.currentTarget),
                username = form.find('input#username').val(),
                password = form.find('input#password').val(),
                button   = form.find('input.btn-submit'),
                view     = this;

            if (this.login_validator.form()) {
                this.user.login(username, password,'',{
                    beforeSend: function() {
                        view.blockUi.block(button);
                    },
                    success: function(model, res, xhr) {
                        view.blockUi.unblock();
                        if (res.success == true) {
                            // Catch login event
                            var userID = res.data.user.ID;
                            AE.App.user.set('id', userID);

                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'success',
                            });

                            buyPackageView.triggerMethod('after:authSuccess', model, res);
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error',
                            });
                        }
                    }
                });
            }
        },
        countDownPump: function(countDownFormat, countDownTime, countDownButton, pumpButton) {
            if(countDownFormat == 'i:s') {
                time_format = 'MS';
            } else if(countDownFormat == 'H:i:s') {
                time_format = 'HMS';
            } else {
                time_format = 'DHMS';
            }

            var date = new Date();
            date.setSeconds(date.getSeconds() + countDownTime);

            countDownButton.find('.btn-time-content').countdown({
                until: date,
                compact: true,
                format: time_format,
                description: '',
                onExpiry: function() {
                    countDownButton.addClass('hide');
                    pumpButton.removeClass('hide');
                    countDownButton.find('.btn-time-content').countdown('destroy');
                }
            });
        }
    });

    // Buy Package View
    Views.BuyPackage = Views.SubmitPost.extend({
        onAfterInit: function() {
            if(currentUser.ID != 0) {
                this.user_login = true;
            }

            this.packageID = "";
            this.stepFinished = "";
            this.stepTabPlan = $('.step-heading[data-id="plan"]');
            this.stepTabAuth = $('.step-heading[data-id="authentication"]');
            this.stepTabPayment = $('.step-heading[data-id="payment"]');
        },
        onAfterSelectPlan: function($step, $li) {
            this.stepTabPlan.addClass('finish');
            this.stepTabPlan.next().addClass('active');

            if(this.stepFinished == 'auth') {
                this.processBarThreeTabs();
            } else {
                if(ae_globals.ae_is_mobile == "0") {
                    $processGap = 50;
                } else {
                    $processGap = 25;
                }
                $processBarWidth = this.stepTabPlan.width() + this.stepTabPlan.next().width() + $processGap;
                $('.progress-bars').width($processBarWidth);
            }

            this.packageID = $li.data('sku');
        },
        onAfterAuthSuccess: function(model, res) {
            this.stepFinished = "auth";
            this.stepTabAuth.addClass('finish');
            this.processBarThreeTabs();
            $('.warpper-buy-package #authentication').remove();

            //Notification
            AE.pubsub.trigger('ae:notification', {
                notice_type: 'success',
                msg: res.msg
            });
        },
        onAfterAuthFail: function(model, res) {
            AE.pubsub.trigger('ae:notification', {
                notice_type: 'error',
                msg: res.msg
            });
        },
        processBarThreeTabs: function() {
            if(ae_globals.ae_is_mobile == "0") {
                $processGap = 100;
                $finishGap = 50;
            } else {
                $processGap = 50;
                $finishGap = 25;
            }

            $processBarWidth = this.stepTabPlan.width() + this.stepTabAuth.width() + this.stepTabPayment.width() + $processGap;
            $finishProcessBarWidth = this.stepTabPlan.width() + this.stepTabAuth.width() + $finishGap;
            $('.progress-bars').width($processBarWidth);
            $('.finish-progress-bar').width($finishProcessBarWidth);
        },
        showNextStep: function() {
            this.triggerMethod("before:showNextStep", this);
            var next = 'auth',
                view = this;
            view.$('.step-wrapper').removeClass('current');

            view.$('.step-wrapper').addClass('hide');

            if (view.currentStep === 'plan') {
                if (view.user_login) { // user login skip step auth
                    next = 'payment';
                }
            }
            // current step is auth
            if (view.currentStep == 'auth') {
                // update user_login
                view.user_login = true;
                next = 'payment';
            }
            // show next step
            view.$('.step-' + next).removeClass('hide');

            /**
             * refresh map
             */
            if (typeof this.map !== 'undefined') {
                this.map.refresh();
            }
            // trigger onAfterShowNextStep
            view.triggerMethod("after:showNextStep", next, view.currentStep);
        },
        submitAuth: function(event) {
            event.preventDefault();
            var $target = $(event.currentTarget),
                view = this,
                data = $target.serializeObject(),
                captcha  = $("#g-recaptcha-response").val();
            // trigger method before submit Auth
            view.triggerMethod('before:submitAuth', view.user, view);
            //send captcha result
            if(captcha) {
                view.user.set('captcha', captcha);
            }

            //Re-define data
            var register_info = {
                username: data.user_login,
                email: data.user_email,
                password: data.user_pass,
                re_password: data.repeat_password
            };
            view.user.register(register_info, {
                beforeSend: function() {
                    view.blockUi.block($target);
                },
                success: function(model, res, jqXHR) {
                    view.blockUi.unblock($target);
                    if (res.success) {
                        view.currentStep = 'auth';
                        // add step auth to finish step
                        view.addFinishStep('step-auth');
                        // set user login is true
                        view.user_login = true;
                        // show nex step
                        view.showNextStep();
                        /*trigger event user authentication sucess*/
                        AE.pubsub.trigger('ae:user:auth', model, res, jqXHR);
                        // trigger method onSubmitAuthSuccess with params are model user and res
                        view.triggerMethod('after:authSuccess', model, res);
                    } else {
                        view.user_login = false;
                        // trigger method onSubmitAuthFail with params are model user and res
                        view.triggerMethod('after:authFail', model, res);
                    }
                }
            });
        },
        userLogin: function(model) {
            var view = this;
            view.user_login = true;
            view.currentStep = 'auth';
            view.addFinishStep('step-auth');
            view.showNextStep();
            view.triggerMethod("after:showNextStep", 'post', view.currentStep);
        },
        /**
         * @override selectStep
         */
        selectStep: function(event) {
            event.preventDefault();
            var $select = $(event.currentTarget),
                id = $select.find('a').attr('href'),
                view = this;

            if(id == '#plan' || (id == '#payment' && this.stepFinished == 'auth')) {
                $('.step-wrapper').addClass('hide');
                $(id).removeClass('hide');
            }
        },
        /**
         * @override selectPayment
         */
        selectPayment: function(event) {
            event.preventDefault();
            var $target = $(event.currentTarget),
                paymentType = $target.attr('data-type'),
                $button = $target.find('button'),
                view = this;
            $.ajax({
                url: ae_globals.ajaxURL,
                type: 'post',
                contentType: 'application/x-www-form-urlencoded;charset=UTF-8',
                // build data and send
                data: {
                    action: 'et-setup-payment',
                    // author
                    author: AE.App.user.get('id'),
                    // package sku id
                    packageID: this.packageID,
                    // payment gateway
                    paymentType: paymentType,
                    // send coupon code if exist
                    coupon_code: view.$('#coupon_code').val()
                },
                beforeSend: function() {
                    view.blockUi.block($target);
                },
                success: function(response) {
                    view.blockUi.unblock();
                    if (response.data.ACK) {
                        // call method onSubmitPaymenSuccess
                        view.triggerMethod('submit:paymentSuccess', response);
                        // update form check out and submit
                        $('#checkout_form').attr('action', response.data.url);
                        if( $('#checkout_form .packageType').length >0 ){
                            $('#checkout_form .packageType').val(view.model.get('et_package_type'));
                        }
                        if (typeof response.data.extend !== "undefined") {
                            $('#checkout_form .payment_info').html('').append(response.data.extend.extend_fields);
                        }
                        // trigger click on submit button
                        $('#payment_submit').click();
                    } else {
                        // call method onSubmitPaymentFail
                        view.triggerMethod('submit:paymentFail', response);
                    }
                }
            });
        },
    });

})(jQuery, window.AE.Views, window.AE.Models, window.AE);
