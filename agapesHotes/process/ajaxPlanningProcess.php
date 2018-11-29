<?php
require_once('../main.php');
if (checkAjaxRequest()) {
    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        // UPDATE | CREATE PLANNING
        if (!empty($_POST['UPDATEPLANNING'])) {

            if (!empty($_POST['siteId']) && !empty($_POST['employeId'])
                && !empty($_POST['date'])
                && isset($_POST['absenceReason']) && isset($_POST['id'])) {

                $PlanningProcess = new \App\Plugin\AgapesHotes\Planning();
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

        // UPDATE | CREATE PLANNING PLUS
        if (!empty($_POST['UPDATEPLANNINGPLUS'])) {

            if (!empty($_POST['siteId']) && !empty($_POST['employeId'])
                && !empty($_POST['year']) && !empty($_POST['month'])
                && isset($_POST['nbRepas']) && isset($_POST['primeObjectif'])
                && isset($_POST['primeExept']) && isset($_POST['accompte'])
                && isset($_POST['nbHeurePlus']) && isset($_POST['nbJoursFeries'])
                && isset($_POST['commentaires']) && isset($_POST['id'])) {

                $PlanningPlusProcess = new \App\Plugin\AgapesHotes\PlanningPlus();

                if (!empty($_POST['id'])) {

                    $PlanningPlusProcess->setId($_POST['id']);
                    if ($PlanningPlusProcess->show()) {

                        $PlanningPlusProcess->feed($_POST);
                        if ($PlanningPlusProcess->update()) {

                            echo $PlanningPlusProcess->getId();
                        } else {
                            echo 'Impossible de mettre à jour le planning';
                        }
                    }
                } else {
                    $PlanningPlusProcess->feed($_POST);

                    if ($PlanningPlusProcess->save()) {
                        echo $PlanningPlusProcess->getId();
                    } else {
                        echo 'Impossible d\'enregistrer le planning';
                    }
                }
            } else {
                echo 'Certains éléments manquent !';
            }
        }


    }
}