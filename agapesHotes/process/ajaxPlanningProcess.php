<?php
require_once('../main.php');
if (checkAjaxRequest()) {
    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        $PlanningProcess = new \App\Plugin\AgapesHotes\Planning();

        // UPDATE | CREATE MAIN COURANTE
        if (!empty($_POST['UPDATEPLANNING'])) {

            if (!empty($_POST['siteId']) && !empty($_POST['employeId'])
                && !empty($_POST['date'])
                && isset($_POST['absenceReason']) && isset($_POST['id'])) {

                $PlanningProcess->feed($_POST);

                if (is_numeric($PlanningProcess->getAbsenceReason())) {
                    $PlanningProcess->setReelHours(financial($PlanningProcess->getAbsenceReason(), true));
                    $PlanningProcess->setAbsenceReason(null);
                } else {
                    $PlanningProcess->setReelHours(0);
                }

                if (empty($_POST['id'])) {

                    if ($PlanningProcess->save()) {
                        echo $PlanningProcess->getId();
                    } else {
                        echo 'Impossible d\'enregistrer le planning';
                    }

                } else {

                    if ($PlanningProcess->update()) {

                        echo $PlanningProcess->getId();
                    } else {
                        echo 'Impossible de mettre à jour le planning';
                    }

                }
            } else {
                echo 'Un motif d\'absence ou un nombre d\'heures est attendu !';
            }
        }


    }
}