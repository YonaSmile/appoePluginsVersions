function mehoubarim_getUsersVisites(reset = false) {
    let $icon = $('#refreshVisites').find('i');
    $icon.addClass('fa-spin');
    jQuery('#visitorsStats').load('/app/plugin/mehoubarim/visites.php' + (true === reset ? '?resetStats=OK' : ''), function () {
        $icon.removeClass('fa-spin');
    });
}

function mehoubarim_getUserStatus() {
    jQuery('#usersStatsSubMenu').load('/app/plugin/mehoubarim/index.php');
}

jQuery(document).ready(function () {

    checkUserSessionExit().done(function (data) {
        if (data == 'true') {

            mehoubarim_getUserStatus();
            mehoubarim_getUsersVisites();

            setInterval(function () {
                mehoubarim_getUserStatus();
            }, 15000);
        }
    });

    var userId = 0;
    $(document).on('click', '#refreshVisites', function () {
        mehoubarim_getUsersVisites();
    });

    $(document).on('click', '#resetStats', function () {
        if (confirm('Vous allez réinitialiser les statistiques')) {
            $(this).attr('disabled', 'disabled').addClass('disabled')
                .html('<i class="fas fa-circle-notch fa-spin"></i> Chargement...');

            $('#visitorsStats').html('');
            mehoubarim_getUsersVisites(true);
        }
    });

    $(document).on('dblclick', 'span.activeUser', function () {
        let $user = $(this);
        userId = $user.data('userid');

        let date = new Date($user.data('last-connexion') * 1000);
        let html = '<p><strong>Localité: </strong>' + $user.data('page-consulting') + ' ' +
            '<a class="text-info" href="' + $user.data('page-consulting') + '"><i class="fas fa-external-link-alt"></i></a></p>';
        html += '<p><strong>Dernière connexion: </strong>' + date.getHours() + 'h:' + (date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes()) + '</p>';
        html += '<button type="button" class="btn btn-sm btn-danger btnLogoutUser m-3">' + $user.data('txt-btn-logout') + '</button>';
        html += '<button type="button" class="btn btn-sm btn-warning btnFreeUser m-3">' + $user.data('txt-btn-freeuser') + '</button>';

        $('#modalInfo #modalTitle').html($user.data('user-name'));
        $('#modalInfo #modalBody').html(html);
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
            mehoubarim_getUserStatus();
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
            mehoubarim_getUserStatus();
        });
    });
});