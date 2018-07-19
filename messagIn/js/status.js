"use strict";

//get messages
function liveMessages() {
    return jQuery.get('/app/plugin/messagIn/syncMessages.php');
}

function badgeMessageCheck(data) {
    if (!$('a#navbarDropdownMessageMenu > span.badge').length) {
        $('a#navbarDropdownMessageMenu').append(' <span class="badge badge-pill badge-pill-messagIn badge-danger ml-1">' + data + '</span>');
    } else {
        $('a#navbarDropdownMessageMenu span.badge').html(data);
    }
}

jQuery(document).ready(function () {

    liveMessages().done(function (data) {
        if ($.isNumeric(data) || data == 0) {
            badgeMessageCheck(data);
        }
    });

    //Start Cron
    var msgCron = setInterval(function () {

        liveMessages().done(function (data) {
            if ($.isNumeric(data) || data == 0) {
                badgeMessageCheck(data);
            } else {
                clearInterval(msgCron);
            }
        });

    }, 15000);
});