"use strict";

//get messages
function liveMessages() {
    return jQuery.get('/app/plugin/messagIn/syncMessages.php');
}

jQuery(document).ready(function () {

    liveMessages().done(function (data) {
        if ($.isNumeric(data) || data == 0) {
            if (!$('#menu-messages > a span.badge').length) {
                $('#menu-messages > a').append(' <span class="badge badge-pill badge-danger ml-1">' + data + '</span>');
            } else {
                $('#menu-messages > a span.badge').html(data);
            }
        }
    });

    //Start Cron
    var msgCron = setInterval(function () {

        liveMessages().done(function (data) {
            if ($.isNumeric(data) || data == 0) {
                if (!$('#menu-messages > a span.badge').length) {
                    $('#menu-messages > a').append(' <span class="badge badge-pill badge-danger ml-1">' + data + '</span>');
                } else {
                    $('#menu-messages > a span.badge').html(data);
                }
            } else {
                clearInterval(msgCron);
            }
        });

    }, 15000);
});