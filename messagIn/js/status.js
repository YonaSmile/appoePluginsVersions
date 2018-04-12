"use strict";

//get messages
function liveMessages() {

    jQuery.get(
        '/app/plugin/messagIn/syncMessages.php',
        function (data) {
            if ($.isNumeric(data)) {
                if (!$('#menu-messages > a span.badge').length) {
                    $('#menu-messages > a').append(' <span class="badge badge-pill badge-danger ml-1">' + data + '</span>');
                } else {
                    $('#menu-messages > a span.badge').html(data);
                }
            }
        }
    );
}

jQuery(document).ready(function () {
    liveMessages();

//Start Cron
    setInterval(function () {
        liveMessages();
    }, 15000);
});