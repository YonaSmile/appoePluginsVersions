<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/main.php');
require_once('../include/appointment_functions.php');

if (checkAjaxRequest()) {

    $_POST = cleanRequest($_POST);

    if (isset($_POST['getRdvTypeByAgenda']) && !empty($_POST['idAgenda']) && is_numeric($_POST['idAgenda'])) {
        echo appointment_rdvType_getBtns($_POST['idAgenda']);
        exit();
    }

    if (isset($_POST['getDateByRdvType']) && !empty($_POST['idAgenda']) && !empty($_POST['idRdvType'])
        && is_numeric($_POST['idAgenda']) && is_numeric($_POST['idRdvType'])) {
        echo appointment_dates_get($_POST['idAgenda'], $_POST['idRdvType']);
        exit();
    }

    if (isset($_POST['getAvailabilitiesByDate']) && !empty($_POST['idAgenda']) && !empty($_POST['dateChoice'])
        && !empty($_POST['rdvTypeDuration']) && is_numeric($_POST['idAgenda']) && is_numeric($_POST['rdvTypeDuration'])) {
        echo appointment_availabilities_get($_POST['idAgenda'], $_POST['dateChoice'], $_POST['rdvTypeDuration']);
        exit();
    }

    if (isset($_POST['getFormByRdvType']) && !empty($_POST['idRdvType']) && is_numeric($_POST['idRdvType'])) {
        echo appointment_rdvTypeForm_get($_POST['idRdvType']);
        exit();
    }
}
echo json_encode(false);