(function($, Views, Models, Collections){
    $('#pump_setup_button').on('click', function(event) {
        event.preventDefault();
        var $setupButton = $(event.currentTarget),
            blockUi = new Views.BlockUi();

        $.ajax({
            type: 'POST',
            url: ae_globals.ajaxURL,
            data: {
                action: 'qa_pump_sync',
                method: 'setup'
            },
            beforeSend: function() {
                blockUi.block($setupButton);
            },
            success: function(res, status, xhr) {
                if(res.success == true) {
                    alert(res.msg);
                    console.log('fff');
                    $('#qa_pump_setup_notice').fadeOut();
                    $('.pump_setup_data').hide('slow');
                } else {
                    alert(res.msg);
                    jQuery('.pump_setup_data').hide('slow');
                }

                blockUi.unblock();
            }
        });
    });
})(jQuery, window.AE.Views, window.AE.Models, window.AE.Collections);