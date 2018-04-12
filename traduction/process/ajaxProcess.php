<?php
require_once('../main.php');
if (checkAjaxRequest()) {

    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        if (isset($_POST['web_traduction']) && !empty($_POST['id']) && !empty($_POST['metaKey']) && isset($_POST['metaValue'])) {

            $Traduction = new App\Plugin\Traduction\Traduction();
            $Traduction->feed($_POST);
            $Traduction->setLang(LANG);

            if ($Traduction->update()) {
                echo 'true';
            }
        }

        if (isset($_POST['deleteTrad']) && !empty($_POST['keytrad'])) {
            $Traduction = new App\Plugin\Traduction\Traduction();
            $Traduction->setMetaKey($_POST['keytrad']);

            if ($Traduction->deleteByKey()) {
                echo 'true';
            }
        }
    }
}