    $('.btn-add-popup-message').click(function(e) {
        e.preventDefault();
        var me = this;

        var message = $('#message').val();

        $.ajax({
            url: $(me).attr('href'),
            type: 'post',
            data: {
                'message': message
            },
            dataType: 'json',
            success: function (data) {
                reloadPopupMessages();
            }
        });
    });

    function reloadPopupMessages() {
        $('.popup-messages').load(CCM_DISPATCHER_FILENAME + '/dashboard/treasure_hunt/settings .popup-messages > *');
    }

    $('body').on('click', '.btn-delete-popup-message', function(e) {
        e.preventDefault();

        var message = $(this).closest('.row').find('.col-msg').html();
        var me = this;

        $.ajax({
            url: $(me).attr('href'),
            type: 'post',
            data: {
                'message': message
            },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    $(me).closest('.row').slideUp();
                }
            }
        });
    });

