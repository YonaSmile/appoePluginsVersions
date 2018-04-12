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
                    duration: 15000 - (new Date(jQuery.now()) - start),
                    step: function (now) {
                        jQuery(this).attr('aria-valuenow', now)
                    }
                }
            );
    });
}

jQuery(document).ready(function () {

    getUserStatus();
    setInterval(function () {
        getUserStatus();
    }, 15000);
});


