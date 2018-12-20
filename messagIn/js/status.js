"use strict";

//get messages
function liveMessages() {
    return jQuery.get('/app/plugin/messagIn/syncMessages.php');
}

function getLiveMessage() {
    liveMessages().done(function (data) {
        if ($.isNumeric(data) || data === 0) {
            badgeMessageCheck(data);
        }
    });
}

function badgeMessageCheck(data) {
    if (!$('a#navbarDropdownMessageMenu > span.badge').length) {
        $('a#navbarDropdownMessageMenu').append(' <span class="badge badge-pill badge-pill-messagIn badge-danger ml-1">' + data + '</span>');
    } else {
        $('a#navbarDropdownMessageMenu span.badge').html(data);
    }
}

jQuery(document).ready(function () {

    getLiveMessage();

    //Start Cron
    var msgCron = setInterval(function () {
        getLiveMessage();
    }, 15000);
});