<?php
require_once('../main.php');
if (checkAjaxRequest()) {
    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        $PtiProcess = new \App\Plugin\AgapesHotes\Pti();

        // ADD, UPDATE PTI
        if (!empty($_POST['UPDATEPTI'])) {

            if (!empty($_POST['employeId']) && !empty($_POST['siteId'])
                && !empty($_POST['dateDebut']) && !empty($_POST['nbWeeksInCycle'])
                && valideAjaxToken()) {

                if (employeHasContrat($_POST['employeId'], $_POST['siteId'])) {

                    $dateChecker = new \DateTime($_POST['dateDebut']);
                    if ($dateChecker->format('N') == 1) {

                        if (!empty($_POST['ptiId'])) {

                            $PtiProcess->setId($_POST['ptiId']);
                            if ($PtiProcess->show()) {

                                $PtiProcess->feed($_POST);
                                if ($PtiProcess->notExist(true)) {

                                    if ($PtiProcess->update()) {
                                        echo json_encode(true);
                                    } else {
                                        echo 'Impossible de modifier ce PTI !';
                                    }
                                } else {
                                    echo 'Ce PTI existe déjà !';
                                }
                            } else {
                                echo 'Ce PTI n\'existe pas !';
                            }
                        } else {
                            $PtiProcess->feed($_POST);
                            if ($PtiProcess->notExist()) {

                                if ($PtiProcess->save()) {
                                    echo json_encode(true);
                                } else {
                                    echo 'Impossible d\'enregistrer ce PTI !';
                                }
                            } else {
                                echo 'Ce PTI existe déjà !';
                            }
                        }
                    } else {
                        echo 'La date début du PTI n\'est pas un Lundi !';
                    }
                } else {
                    echo 'L\'employé n\'a pas de contrat avec ce site!';
                }
            } else {
                echo 'Tous les champs sont obligatoires !';
            }
        }

    }
}