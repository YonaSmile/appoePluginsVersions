"use strict";

//get messages
function liveMessages() {
    return jQuery.get('/app/plugin/messagIn/syncMessages.php');
}

function checkUserSessionExit() {
    return $.post('/app/ajax/plugin.php', {checkUserSession: 'OK'});
}

function getLiveMessage() {
    liveMessages().done(function (data) {
        if ($.isNumeric(data) || data === 0) {
            badgeMessageCheck(data);
        }
    });
}

function badgeMessageCheck(data) {
    if (!$('a#navbarDropdownMessageMenu > span.countMsg').length) {
        $('a#navbarDropdownMessageMenu').append(' <span class="countMsg">' + data + '</span>');
    } else {
        $('a#navbarDropdownMessageMenu span.countMsg').html(data);
    }
}

jQuery(document).ready(function () {
    checkUserSessionExit().done(function (data) {
        if (data == 'true') {
            getLiveMessage();

            //Start Cron
            var msgCron = setInterval(function () {
                getLiveMessage();
            }, 15000);
        }
    });
});