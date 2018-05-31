<?php
require_once('../main.php');
if (checkAjaxRequest()) {

    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        if (isset($_POST['deletePerson']) && !empty($_POST['idPersonDelete'])) {
            $People = new App\Plugin\People\People($_POST['idPersonDelete']);
            if ($People->delete()) {
                echo 'true';
            }
        }

        if(isset($_POST['unpackPerson']) && !empty($_POST['idUnpackPerson'])){
            $People = new App\Plugin\People\People($_POST['idUnpackPerson']);
            $People->setStatus(1);
            if ($People->update()) {
                echo 'true';
            }
        }
    }
}