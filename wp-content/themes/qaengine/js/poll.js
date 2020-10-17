(function($, Views, Models, Collections) {
    $(document).ready(function() {
        // Hide color picker
        $(document).click(function (e) {
            if (!$(e.target).is(".color-box, .iris-picker, .iris-picker-inner, .iris-palette")) {
                if($('.iris-picker').length > 0) {
                    $('.answer-color-picker').iris('hide');
                }
            }
        });

        // Set defatul amount of answer is zero
        window.maxAnswer = 0;
        // Flag to check on edit modal or create modal
        window.isCreate = true;

        Models.PollModel = AE.Models.Post.extend({
            action: 'qa-sync-poll'
        });

        Views.EditPoll = AE.Views.Modal_Box.extend({
            events: {
                'submit form#submit_poll' : 'savePoll',
                'keypress #poll_question_tags' : 'onAddTag'
            },

            initialize: function() {
                var view = this;
                this.blockUi = new AE.Views.BlockUi();
                this.tag_list = this.$el.find('#poll_tag_list');
                view.tags = {};

                // Do not load tinyMCE on mobile
                if( typeof tinymce !== 'undefined') {
                    tinymce.EditorManager.execCommand("mceAddEditor", false, "insert_poll");
                }

                $("#insert_poll").on('change',function(event) {
                    $(this).valid();
                });

                $("#insert_poll").on('change',function(event) {
                    $(this).valid();
                });

                $('#poll_question_tags').typeahead({
                    minLength: 0,
                    items: 99,
                    source: function(query, process) {
                        if (view.tags.length > 0) return view.tags;

                        return $.getJSON(
                            ae_globals.ajaxURL, {
                                action: 'qa_get_tags'
                            },
                            function(data) {
                                //console.log(data);
                                view.tags = data;
                                return process(data);
                            });

                    },
                    updater: function(item) {
                        //console.log(item);
                        view.addTag(item);
                    }
                });
            },

            //Form validate
            validate: function() {
                this.submit_poll_validator = $('form#submit_poll').validate({
                    ignore: "",
                    rules: {
                        post_title: {
                            required :true,
                            maxlength: qa_front.texts.max_lengh,
                        },
                        question_category: "required",
                        post_content: "required",
                        poll_end_date: "required",
                    },
                    /*messages: {
                        post_title: {
                            required: qa_front.form_auth.error_msg,
                        },
                        question_category: 'qa_front.form_auth.error_msg',
                        post_content: qa_front.form_auth.error_msg,
                        poll_end_date: qa_front.form_auth.error_msg,
                    },*/
                    errorPlacement: function(label, element) {
                        // position error label after generated textarea
                        if (element.is("select") && ae_globals.ae_is_mobile!=="1") {
                            label.insertAfter(element.next());
                        } else {
                            $(element).closest('div').append(label);
                        }
                    }
                });
            },

            savePoll: function(event) {
                event.preventDefault();
                this.validate();
                if(this.submit_poll_validator.form()) {
                    var form = $(event.currentTarget),
                        $button = form.find('#btn_submit_poll'),
                        captcha  = $("#g-recaptcha-response").val(),
                        view = this;

                    form.find('.input-answer').each(function() {
                        var input = $(this);
                        if(input.val() == "") {
                            input.attr('name', '');
                        } else {
                            input.attr('name', 'poll_answers[]');
                        }
                    });

                    form.find('input[type="checkbox"]').each(function() {
                        var checkbox = $(this);
                        if(checkbox.is(':checked')) {
                            checkbox.val('true');
                        }
                    });
                    
                    form.find('textarea').each(function() {
                        view.model.set($(this).attr('name'), $(this).val());
                    });

                    var data = form.serializeObject();
                    this.model.attributes = _.extend(this.model.attributes, data);

                    if(typeof currentPoll !== 'undefined' && isCreate == false) {
                        this.model.set('id', currentPoll.ID);
                        this.model.set('ID', currentPoll.ID);
                    }

                    this.model.save('', '', {
                        beforeSend: function() {
                            view.blockUi.block($button);
                        },
                        success: function(result, res, xhr) {
                            view.blockUi.unblock();
                            if(res.success == true) {
                                //Sync poll answer
                                AE.pubsub.trigger('AE:onCreatePollSuccess', res.ID);

                                view.closeModal();
                                AE.pubsub.trigger('ae:notification', {
                                    notice_type: 'success',
                                    msg: res.msg
                                });

                                setTimeout(function() {
                                   window.location.href = res.redirect; 
                                }, 5000);
                                
                            } else {
                                AE.pubsub.trigger('ae:notification', {
                                    notice_type: 'error',
                                    msg: res.msg
                                })
                            }
                        }
                    });
                }
                return false;
            },

            /**
             * add tag to modal, render tagItem base on in put tag
             */
            addTag: function(tag) {
                var duplicates = this.tag_list.find('input[type=hidden][value="' + tag + '"]'),
                    count = this.tag_list.find('li');
                if (duplicates.length == 0 && tag != '' && count.length < 5) {
                    var data = {
                        'name': tag
                    };
                    var tagView = new QAEngine.Views.TagItem({
                        model: new Backbone.Model(data)
                    });
                    if($('#poll_tag_item').length > 0) {
                        tagView.template = _.template($('#poll_tag_item').html());
                    }
                    this.tag_list.append(tagView.render().$el);
                    $('input#poll_question_tags').val('').css('border', '1px solid #dadfea');;
                }
            },

            /**
             * catch event user enter in tax input, call function addTag to render tag item
             */
            onAddTag: function(event) {
                var val = $(event.currentTarget).val();
                if (event.which == 13) {
                    /**
                     * check current user cap can add_tag or not
                     */
                    var caps = currentUser.cap;
                    if (typeof caps['create_tag'] === 'undefined' && $.inArray(val, this.tags) == -1) {
                        if(ae_globals.ae_is_mobile==="0")
                            this.$('#poll_question_tags').popover({
                                content: this.$('#poll_add_tag_text').val(),
                                container: '#modal_submit_questions',
                                placement: 'top'
                            });
                        else
                            AE.pubsub.trigger('ae:notification', {
                                msg: this.$('#add_poll_tag_text').val(),
                                notice_type: 'error',
                            });
                        $('#poll_question_tags').popover('show');
                        return false;
                    }
                    else
                        $('#poll_question_tags').popover('hide');
                    /**
                     * add tag
                     */
                    this.addTag(val);
                }
                return event.which != 13;
            }
        });

        /**
         * Model for an answer item
         */
        Models.AnswerItem = Backbone.Model.extend({
            action: 'qa-sync-answer',
            defaults: {
                post_title: '',
                post_status: 'publish',
                poll_answer_color: '#e6e6e6',
                placeholder: poll_settings.answer_placeholder
            },
        });

        /**
         * View for answer item on edit modal
         */
        Views.AnswerItemOnEdit = Backbone.View.extend({
            tagName: 'li',
            className: 'item_poll_answer_edit',
            template: [],
            events: {
                'change .input-answer': 'changeModel',
                'click .color-box': 'changeColor',
                'click .remove-box': 'removeAnswer'
            },
            initialize: function() {
                if($('#edit_poll_answer_item').length > 0) {
                    this.template = _.template($('#edit_poll_answer_item').html());
                }
                this.model.bind('change', this.status, this);
            },
            status: function() {
                console.log('changed');
            },
            render: function() {
                this.$el.html(this.template(this.model.toJSON()));
                var view = this;
                this.$el.find('.answer-color-picker').iris({
                    palettes: ['#e6e6e6', '#00a388', '#84a36f', '#d93153', '#a30865', '#ffd717', '#083358'],
                    change: function(event, ui) {
                        // event = standard jQuery event, produced by whichever control was changed.
                        // ui = standard jQuery UI object, with a color member containing a Color.js object

                        // change the headline color
                        view.$el.find('.color-box').css( 'background', ui.color.toString());
                        view.model.set('poll_answer_color', ui.color.toString());
                    }
                });
                this.$el.find('.answer-color-picker').iris('option', 'border', '#dadfea')
                return this;
            },
            changeModel: function(event) {
                event.preventDefault();
                var target = $(event.currentTarget);
                this.model.set('post_title', target.val());
            },
            changeColor: function(event) {
                event.preventDefault();
                $('.answer-color-picker').iris('hide');
                this.$el.find('.answer-color-picker').iris('show');
            },
            removeAnswer: function(event) {
                event.preventDefault();
                this.$el.remove();
                this.model.destroy();
                window.maxAnswer--;
                if(window.maxAnswer >= poll_settings.max_answer) {
                    $('.btn-add-more').hide();
                } else {
                    $('.btn-add-more').show();
                }
            }
        });

        /**
         * Collection for answers
         */
        Collections.AnswerList = Backbone.Collection.extend({
            model: Models.AnswerItem,
            parse: function(result) {
                return result.posts;
            }
        });

        /**
         * Answer list for collection
         */
        Views.AnswerList = Backbone.View.extend({
            initialize: function() {
                this.listenTo(this.collection, 'add', this.addOne);
            },

            addOne: function(model) {
                console.log('add one');
                var answer = new Views.AnswerItemOnEdit({model: model});
                this.$el.append(answer.render().el);
            },

            render: function() {
                this.collection.each( this.addOne, this );
                return this;
            }
        });

        /**
         * View for add answer on edit modal
         */
        Views.AddAnswerList = Backbone.View.extend({
            el: '#submit_poll',
            events: {
                'click .btn-add-more': 'addMoreAnswer'
            },
            initialize: function() {
                _.bindAll(this, 'onEditPoll', 'onCreatePoll');
                /**
                 * Listen on the setting up poll trigger
                 */
                AE.pubsub.on('AE:setupPollFields', this.onEditPoll, this);
                AE.pubsub.on('AE:afterOpenSubmitModal', this.onCreatePoll, this);
            },
            addOne: function(model) {
                var answer = new Views.AnswerItemOnEdit({model: model});
                this.$el.find('#answer_list_poll_edit').append(answer.render().el);
            },

            render: function() {
                this.collection.each( this.addOne, this );
                return this;
            },
            addMoreAnswer: function(event) {
                // Hidden color picker
                $('.answer-color-picker').iris('hide');

                var answer = new Models.AnswerItem({
                    post_parent: this.model.get('ID'),
                    post_author: currentUser.ID
                });

                var target = $(event.currentTarget);

                // Increase answer count
                window.maxAnswer++;
                // Hide/show add more button
                if(window.maxAnswer >= poll_settings.max_answer) {
                    target.hide();
                } else {
                    target.show();
                }
                this.collection.add(answer);
            },
            onEditPoll: function(model) {
                if(ae_globals.ae_is_mobile==="0")
                    window.isCreate = false;
                // Init collection
                if($('#poll_answers_json').length > 0 && model.get('ID')) {
                    this.collection = new Collections.AnswerList(pollAnswers);
                } else {
                    var answerItem = new Models.AnswerItem();
                    this.collection = new Collections.AnswerList();
                    this.collection.add(answerItem);
                }
                this.listenTo(this.collection, 'add', this.addOne);

                window.maxAnswer = 0;
                // Increase amount of answers
                this.collection.each(function(answer, index, col) {
                    window.maxAnswer++;
                    // Hide/show add more button
                    if(window.maxAnswer >= poll_settings.max_answer) {
                        $('.btn-add-more').hide();
                    } else {
                        $('.btn-add-more').show();
                    }
                });

                // Init model
                this.model = model;

                // Emtly answer list on html
                this.$el.find('#answer_list_poll_edit').html('');

                // Render answer list
                this.render();
            },
            onCreatePoll: function(model) {
                window.isCreate = true;
                window.maxAnswer = 1;
                // Init collection
                var answerItem = new Models.AnswerItem();
                this.collection = new Collections.AnswerList();
                this.collection.add(answerItem);
                this.listenTo(this.collection, 'add', this.addOne);

                // Init model
                this.model = model;

                // Emtly answer list on html
                this.$el.find('#answer_list_poll_edit').html('');

                // Render answer list
                this.render();
            }
        });

        // Init
        if(ae_globals.ae_is_mobile != 1) {
            var editPoll = new Views.EditPoll({
                model: new Models.PollModel(),
                el: $('#modal_submit_questions')
            });

            var editView = new Views.AddAnswerList();
        } else {
            var editPoll = new Views.EditPoll({
                model: new Models.PollModel(),
                el: $('.body-poll')
            });

            var editView = new Views.AddAnswerList();
            var model = new Models.AnswerItem();
            editView.onCreatePoll(model);
            editView.onEditPoll(model);
        }

        /**
         * Listen on the creating poll success
         */
        AE.pubsub.on('AE:onCreatePollSuccess', function(id) {
            editView.collection.each(function(answer, index, col) {
                window.setTimeout (function () {
                    if(answer.get('post_title') != '') {
                        answer.set('post_parent', id);
                        answer.save();
                    }
                }, 500 * index);
            });
        });

        /**
         * Hide tag popover when change tab poll
         */
        $('#tab-question').click(function(){
            //$('#poll_question_tags').popover('hide');
        });
    });
})(jQuery, window.AE.Views, window.AE.Models, window.AE.Collections);