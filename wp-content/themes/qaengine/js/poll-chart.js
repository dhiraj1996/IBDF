(function (Models, Views, $, Backbone) {
    /*************************************
     *     S U B M I T  A N S W E R      *
     *************************************/
    Views.SubmitAnswer = Backbone.View.extend({
        el: 'body.single-poll',
        events: {
            'submit form.form-option-answer' : 'doSubmitAnswer',
            'click .vote-link'  : 'voteLink',
            'click .vote-result'  : 'voteResult',
        },
        initialize: function() {
            if(typeof currentPoll !== 'undefined') {
                this.poll = new Models.Post(currentPoll);
            }

            this.blockUi = new AE.Views.BlockUi();
            var view = this;
            // Draw chart
            google.setOnLoadCallback(view.drawChart());
        },
        voteLink: function(event) {
            event.preventDefault();
            if(poll_settings.user_voted == '' && poll_settings.user_voted != 1) {
                this.showTabPollAnswer();
            }
        },

        voteResult: function(event) {
            event.preventDefault();
            if(poll_settings.user_voted != '' && poll_settings.user_voted == 1) {
                this.showTabChart();
            }
        },

        // Show tab poll answers
        showTabPollAnswer: function() {
            $('.vote-link span').addClass('active');
            $('.vote-result span').removeClass('active');
            $('.vote-answer').removeClass('hide');
            $('.result-answer').addClass('hide');
        },

        // Show tab chart
        showTabChart: function() {
            $('.vote-link span').removeClass('active');
            $('.vote-result span').addClass('active');
            $('.vote-answer').addClass('hide');
            $('.result-answer').removeClass('hide');

            var view = this;
            // Draw chart
            google.setOnLoadCallback(view.drawChart());

            $(window).on('throttledresize', function (event) {
                event.preventDefault();
                view.drawChart();
            });

        },

        //Submit answer
        doSubmitAnswer: function(event) {
            event.preventDefault();
            var $target = $(event.currentTarget),
                input = $target.find('.answer-selected'),
                button = $target.find('button'),
                view = this;

            input.each(function() {
                if($(this).is(':checked')) {
                    var value = $(this).val();
                    $(this).val(value);
                }
            });

            var data = $target.serializeObject();
            data = _.extend(data, {
                question_id : this.poll.get('ID'),
                user_id: currentUser.ID,
                method: 'submit-answer',
                action: 'qa-sync-answer'
            });

            $.ajax({
                type: 'POST',
                url: ae_globals.ajaxURL,
                data: data,
                beforeSend: function() {
                    view.blockUi.block(button);
                },
                success: function(res, status, xhr) {
                    view.blockUi.unblock();

                    // Show notifcation
                    if(res.success == true) {
                        poll_settings.user_voted = 1;

                        // Show chart
                        //view.showTabChart();

                        // Live update chart
                        // Update total votes
                        $('.total-vote').find('.number').text(res.data.total_votes);

                        // Update percents
                        var percents = res.data.percents;
                        var percentCount = percents.length;
                        for(var i = 0; i < percentCount; i++) {
                            $('#' + percents[i].answer_item).find('.percent-number').text(percents[i].value + '%');
                        }

                        AE.pubsub.trigger('ae:notification', {
                            notice_type: 'success',
                            msg: res.msg
                        });

                        window.location.reload();
                    } else {
                        AE.pubsub.trigger('ae:notification', {
                            notice_type: 'error',
                            msg: res.msg
                        });
                    }
                }
            });
        },
        // Draw chart
        drawChart: function(){
            var dataChart = [];
            var options = {
                legend: 'none',
                width: '100%',
                height: '100%',
                pieSliceText: 'none',
                tooltip: { trigger: 'none' },
                chartArea:{
                    left: "3%",
                    top: "10",
                    height: "90%",
                    width: "90%",
                },
                backgroundColor:'none',


            };

            switch(poll_settings.poll_chart_type) {
                // DONUT CHART
                case 'donut_chart':
                    options = _.extend(options, {
                        slices: chartSlices,
                        pieHole: 0.8,
                    });

                    dataChart.push(['', 'Hours per Day']);
                    for(var i = 0; i < chartSlicesValue.length; i++) {
                        dataChart.push(['', chartSlicesValue[i].vote]);
                    }

                    // Register bar chart
                    var chart = new google.visualization.PieChart(document.getElementById('piechart'));
                break;

                // COLUMN CHART
                case 'column_chart':
                    dataChart.push(['', 'Hours per Day', {role: 'style'}]);
                    for(var i = 0; i < chartSlicesValue.length; i++) {
                        dataChart.push(['', chartSlicesValue[i].vote, chartSlices[i].color]);
                    }

                    // Register bar chart
                    var chart = new google.visualization.ColumnChart(document.getElementById('piechart'));
                break;

                // PIE CHART
                case 'pie_chart':
                    options = _.extend(options, {slices: chartSlices});

                    dataChart.push(['', 'Hours per Day']);
                    for(var i = 0; i < chartSlicesValue.length; i++) {
                        dataChart.push(['', chartSlicesValue[i].vote]);
                    }

                    // Register bar chart
                    var chart = new google.visualization.PieChart(document.getElementById('piechart'));
                break;

                // BAR CHART
                case 'bar_chart':
                    dataChart.push(['', 'Hours per Day', {role: 'style'}]);
                    for(var i = 0; i < chartSlicesValue.length; i++) {
                        dataChart.push(['', chartSlicesValue[i].vote, chartSlices[i].color]);
                    }

                    // Register bar chart
                    var chart = new google.visualization.BarChart(document.getElementById('piechart'));
                break;
            }

            var data = google.visualization.arrayToDataTable(dataChart);
            chart.draw(data, options);
        }
    });
    var submitAnswer = new Views.SubmitAnswer();
})( window.QAEngine.Models, window.QAEngine.Views, jQuery, Backbone );