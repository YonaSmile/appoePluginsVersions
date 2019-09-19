function checkUserSessionExit() {
    return jQuery.post('/app/ajax/plugin.php', {checkUserSession: 'OK'});
}

function getUserStatus() {
    jQuery('#usersStatsSubMenu').load('/app/plugin/mehoubarim/index.php');

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

    var userId = 0;

    $(document).on('dblclick', 'span.activeUser', function () {
        var $user = $(this);
        userId = $user.data('userid');

        $('#modalInfo #modalTitle').html($user.data('user-name'));
        $('#modalInfo #modalBody').html('<p><strong>Location: </strong>' + $user.data('page-consulting') + '</p>')
            .append('<button type="button" class="btn btn-sm btn-danger btnLogoutUser m-3">' + $user.data('txt-btn-logout') + '</button>')
            .append('<button type="button" class="btn btn-sm btn-warning btnFreeUser m-3">' + $user.data('txt-btn-freeuser') + '</button>');
        $('#modalInfo').modal('show');
    });

    $(document).on('click', '.btnLogoutUser', function (event) {
        event.preventDefault();
        event.stopPropagation();

        var $btn = $(this);
        $btn.html(loaderHtml());

        jQuery.post(
            '/app/plugin/mehoubarim/process/ajaxProcess.php',
            {logoutUser: userId}
        ).done(function (data) {
            $('#modalInfo').modal('hide');
            getUserStatus();
        });
    });

    $(document).on('click', '.btnFreeUser', function (event) {
        event.preventDefault();
        event.stopPropagation();

        var $btn = $(this);
        $btn.html(loaderHtml());

        jQuery.post(
            '/app/plugin/mehoubarim/process/ajaxProcess.php',
            {freeUser: userId}
        ).done(function (data) {
            $('#modalInfo').modal('hide');
            getUserStatus();
        });
    });
});