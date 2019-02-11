<?php
require_once('header.php');

//Connected User
mehoubarim_connectedUserStatus();
$mehoubarim = mehoubarim_connectedUsers();
if ($mehoubarim && is_array($mehoubarim)): ?>
    <li class="pt-3 pl-2 pb-0 pr-2" style="font-size: 0.8em;"><strong><?= trans('Utilisateurs actifs'); ?></strong></li>
    <?php foreach ($mehoubarim as $connectedUserId => $connectedUserData): ?>
        <?php if (getUserIdSession() != $connectedUserId && getUserRoleId() > getUserRoleId($connectedUserId)): ?>
            <li class="list-inline-item p-0 pr-2 mr-0" style="font-size: 0.7em;">
                <?php if (isTechnicien(getUserRoleId()) && $connectedUserData['status'] != 'Déconnecté'): ?>
                    <span class="logoutUser float-left linkBtn" data-userid="<?= $connectedUserId; ?>">
                        <i class="fas fa-times"></i></span>
                <?php endif; ?>
                <?php if ($connectedUserData['status'] != 'Déconnecté'): ?>
                    <span class="text-<?= STATUS_CONNECTED_USER[$connectedUserData['status']]; ?>"
                    <?= isTechnicien(getUserRoleId()) ? 'title="Location: ' . $connectedUserData['pageConsulting'] . '"' : ''; ?>>
                    <i class="fas fa-user"></i></span>
                    <?= getUserFirstName($connectedUserId) . ucfirst(substr(getUserName($connectedUserId), 0, 1)); ?>
                <?php endif; ?>
            </li>
        <?php endif; ?>
    <?php endforeach;
endif; ?>
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
