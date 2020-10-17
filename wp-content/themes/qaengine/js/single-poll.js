(function (Models, Views, $, Backbone) {
    Views.Single_Question = Backbone.View.extend({
        el: "body.single-poll",
        initialize : function () {
            var question 	= new Models.Post(currentPoll);
            this.blockUi	=	new AE.Views.BlockUi();
            this.question 	= 	new Views.PostListItem({
                el: $("#question_content"),
                model: question
            });

            this.initBoostrapJS();
            //render zoom image
            $('.qa-zoom').magnificPopup({type:'image'});
            //render code
            SyntaxHighlighter.all();
        },

        initBoostrapJS : function() {
            $('html').click(function(e) {
                $('.vote-block a, .add-comment').popover('hide');
            })
            $('.vote-block li,a.action').tooltip();
            $('.vote-block a, .add-comment').popover();
            $('.share-social').popover({ html : true});
        },
    });
})( window.QAEngine.Models, window.QAEngine.Views, jQuery, Backbone );