(function (Views, Models, $, Backbone) {
    Views.MobileSinglePoll  = Backbone.View.extend({
        el : 'body.single-poll',
        initialize: function(){
            var question = new Models.Post(currentPoll);
            this.blockUi    =   new AE.Views.BlockUi();
            this.question   =   new Views.PostListItem({
                el: $("#question_content"),
                model: question
            });

            $('.answer-item').each(function(index){
                var element = $(this);
                if ( answersData ) {
                    var model   = new Models.Post(answersData[index]);
                    var answer  =   new Views.PostListItem({
                        el: element,
                        model: model
                    });
                }
            });
            $('.share-social').popover({ html : true});
        },
    });
})(QAEngine.Views, QAEngine.Models, jQuery, Backbone);

jQuery(document).ready(function(){
    jQuery('.share-social').popover();
});