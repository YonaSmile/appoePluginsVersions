<?php
require_once('header.php');
require_once('mehoubarim_functions.php');

$mehoubarim_UserStat = new App\Users();
$UserManager = new App\Users(getUserIdSession());

//Connected User
mehoubarim_connectedUserStatus();


foreach (mehoubarim_connectedUsers() as $connectedUserId => $connectedUserData): ?>
    <?php
    $mehoubarim_UserStat->setId($connectedUserId);
    if ($mehoubarim_UserStat->show() && $mehoubarim_UserStat->getStatut() && $mehoubarim_UserStat->getRole() < 5): ?>
        <li>
            <a>
                <?php if ($UserManager->getRole() == '5' && $connectedUserData['status'] != 'Déconnecté'): ?>
                    <span class="logoutUser float-left linkBtn" data-userid="<?= $mehoubarim_UserStat->getId(); ?>">
                        <i class="fas fa-times"></i></span>
                <?php endif; ?>
                <small>
                    <?= $mehoubarim_UserStat->getNom() . ' ' . $mehoubarim_UserStat->getPrenom(); ?>
                </small>
                <span class="badge badge-<?= STATUS_CONNECTED_USER[$connectedUserData['status']]; ?>">
                    <?= trans($connectedUserData['status']); ?>
                </span>
            </a>
        </li>
    <?php endif; ?>
<?php endforeach; ?>
<script>

    jQuery(document).ajaxSend(function (e, xhr, options) {

        var userStatus = "<?= mehoubarim_getConnectedStatut(); ?>";

        if (!userStatus || userStatus == 'Déconnecté') {
            xhr.abort();
            return false;
        }
    });

    jQuery(document).ready(function () {

        jQuery('#sidebar span.logoutUser').on('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var $btn = jQuery(this);
            var userId = $btn.data('userid');
            jQuery.post(
                '/app/plugin/mehoubarim/process/ajaxProcess.php',
                {logoutUser: userId}
            );
            $btn.next('small').next('span').removeClass().addClass('badge badge-danger').text('Déconnecté');
            $btn.remove();
        });
    });
</script>
