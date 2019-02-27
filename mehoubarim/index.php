<?php
require_once('header.php');

//Connected User
mehoubarim_connectedUserStatus();
$mehoubarim = mehoubarim_connectedUsers();
if ($mehoubarim && is_array($mehoubarim)): ?>
    <li class="pt-3 pl-2 pb-0 pr-2" style="font-size: 0.8em;"><strong><?= trans('Utilisateurs actifs'); ?></strong></li>
    <?php foreach ($mehoubarim as $connectedUserId => $connectedUserData):
        $connectedUserId = \App\ShinouiKatan::Decrypter($connectedUserId); ?>
        <?php if (getUserIdSession() != $connectedUserId
        && getUserRoleId() > getUserRoleId($connectedUserId)
        && $connectedUserData['status'] < 4
        && isUserExist($connectedUserId)): ?>
        <li class="list-inline-item p-0 pr-2 mr-0" style="font-size: 0.7em;">
            <span class="activeUser pb-1 border-bottom border-<?= STATUS_CONNECTED_USER[$connectedUserData['status']]; ?>"
                  style="position: relative;"
                    <?= isTechnicien(getUserRoleId()) ? 'title="Location: ' . $connectedUserData['pageConsulting'] . '"' : ''; ?>>
                <?php if (isTechnicien(getUserRoleId())): ?>
                    <span class="logoutUser linkBtn" data-userid="<?= $connectedUserId; ?>"
                          style="display: none;position: absolute; right: -2px;top:-7px;">
                        <i class="fas fa-times"></i></span>
                <?php endif; ?>
                <?= getUserFirstName($connectedUserId) . ucfirst(substr(getUserName($connectedUserId), 0, 1)); ?>
            </span>
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

    jQuery(document).ready(function ($) {

        $('span.activeUser').hover(function () {
            $(this).find('span.logoutUser').stop().fadeIn(200);
        }, function () {
            $(this).find('span.logoutUser').stop().fadeOut(200);
        });

        $('#sidebar span.logoutUser').on('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var $btn = jQuery(this);
            var userId = $btn.data('userid');
            jQuery.post(
                '/app/plugin/mehoubarim/process/ajaxProcess.php',
                {logoutUser: userId}
            );
            $btn.closest('li.list-inline-item').fadeOut(500).delay(100).remove();
        });
    });
</script>