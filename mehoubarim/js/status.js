function checkUserSessionExit() {
    return $.post('/app/ajax/plugin.php', {checkUserSession: 'OK'});
}

function getUserStatus() {
    jQuery('#usersStatsSubMenu').load('/app/plugin/mehoubarim/index.php');

    var start = new Date(jQuery.now());
    jQuery('#visitorsStats').load('/app/plugin/mehoubarim/visites.php', function () {

        jQuery('#visitorsStats #visitsLoader')
            .animate(
                {
                    width: '100%',
                    valuenow: 100
                },
                {
                    duration: 14000,
                    step: function (now) {
                        jQuery(this).attr('aria-valuenow', now)
                    }
                }
            );
    });
}

jQuery(document).ready(function () {

    checkUserSessionExit().done(function (data) {
        if (data == 'true') {
            getUserStatus();
            setInterval(function () {
                getUserStatus();
            }, 15000);
        }
    });
});