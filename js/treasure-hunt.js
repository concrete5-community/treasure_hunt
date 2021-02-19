$(document).ready(function() {
    // If cookie exists, hide all TH images
    if (document.cookie.indexOf("treasure-hunt") > 0) {
        $('.treasure-hunt').hide();
    }

    $('.treasure-hunt').click(function(e) {
        var me = $(this);

        $(this).prop("disabled", true);

        var popupMessage = $(this).data('treasure-hunt');
        popupMessage = (typeof popupMessage != "undefined") ? popupMessage : TREASURE_HUNT_RANDOM_POPUP_MESSAGE;

        $(this).fadeOut('300');

        $.ajax({
            url: CCM_APPLICATION_URL + '/index.php/treasure_hunt/gotcha',
            type: 'post',
            data: {
                'cID': CCM_CID
            },
            dataType: 'json',
            success: function (data) {
                if (data['remaining_items'] == 0) {
                    popupMessage = data['popup_message'];
                }

                if (popupMessage) {
                    var popup = new jPopup({
                        title: "<h2>" + data.popup_title + "</h2>",
                        content: "<p>" + popupMessage + "</p>",
                        keyClose: 27,
                        buttons: [{
                            text: TREASURE_HUNT_OK_BUTTON_CAPTION,
                            value: true,
                            buttonClass: "ok"
                        }],
                        draggable: true
                    });

                    if (data['redirect_url']) {
                        popup.open(function(r) {
                            switch (r) {
                                case true:
                                    redirectVisitor(data['redirect_url']);
                                break;
                            }
                        });
                    } else {
                        popup.open();
                    }
                } else {
                    if (data['redirect_url']) {
                        redirectVisitor(data['redirect_url']);
                    }
                }


            }
        }).fail(function() {
            alert('Something went wrong. Please try again.');
            $(me).fadeIn();
        }).always(function() {

        });
    });

    function redirectVisitor(redirect_url)
    {
        window.location.href = redirect_url;
    }
});