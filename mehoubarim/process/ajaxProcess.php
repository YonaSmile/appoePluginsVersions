<?php
require_once('../header.php');
if (checkAjaxRequest()) {

    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        if (!empty($_POST['logoutUser'])) {
            mehoubarim_logoutUser($_POST['logoutUser']);
            echo 'true';
        }

        if (!empty($_POST['checkUserStatus'])) {
            $userStatus = mehoubarim_getConnectedStatut();
            if ($userStatus && $userStatus != 'Déconnecté') {
                echo 'true';
            }
        }

    }
}