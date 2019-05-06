<?php
require_once('../main.php');
require_once('../includeApp/twitter_functions.php');

if (checkAjaxRequest()) {

    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        if (!empty($_POST['sendMessageToLists'])) {

            if (!empty($_POST['lists']) && !empty($_POST['url'])) {

                $url = WEB_DIR_URL . DEFAULT_ARTICLES_PAGE . DIRECTORY_SEPARATOR . $_POST['url'];
                $message = 'Bonjour cher membre, voici quelque chose qui peut t\'intéresser : ' . $url;

                if (twitter_send_message_to_lists($_POST['lists'], $message)) {

                    echo trans('L\'article a été partagé');
                } else {
                    echo 'false';
                }
            }
        }
    }
}