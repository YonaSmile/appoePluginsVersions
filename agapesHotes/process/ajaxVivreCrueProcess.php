<?php
require_once('../main.php');
if (checkAjaxRequest()) {
    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        $VivreCrueProcess = new \App\Plugin\AgapesHotes\VivreCrue();

        // UPDATE | CREATE VIVRE CRUE
        if (!empty($_POST['UPDATEVIVRECRUE'])) {

            if (!empty($_POST['etablissementId']) && !empty($_POST['date']) && !empty($_POST['idCourse'])
                && !empty($_POST['prixHTunite']) && !empty($_POST['tauxTva'])
                && isset($_POST['quantite']) && isset($_POST['id'])) {

                //Check date permissions
                if (getUserRoleId(getUserIdSession()) == 1) {

                    $dateNow = new \DateTime();

                    $dateMonthAgo = new DateTime();
                    $dateMonthAgo->sub(new \DateInterval('P1M'));

                    $processDate = new \DateTime($_POST['date']);

                    if ($dateNow->format('j') >= 25) {
                        if ($processDate->format('n') < $dateNow->format('n')
                            && $processDate->format('Y') < $dateNow->format('Y')) {
                            echo 'Vous ne pouvez plus modifier les données d\'avant !';
                            exit();
                        }
                    } else {
                        if ($processDate->format('n') < $dateMonthAgo->format('n')
                            && $processDate->format('Y') <= $dateMonthAgo->format('Y')) {
                            echo 'Vous ne pouvez plus modifier les données d\'avant !';
                            exit();
                        }
                    }
                }

                $VivreCrueProcess->feed($_POST);

                if (empty($_POST['id'])) {

                    if ($VivreCrueProcess->save()) {
                        echo $VivreCrueProcess->getId();
                    } else {
                        echo 'Impossible d\'enregistrer le vivre crue';
                    }

                } else {

                    if ($VivreCrueProcess->update()) {

                        echo $VivreCrueProcess->getId();
                    } else {
                        echo 'Impossible de mettre à jour le vivre crue';
                    }

                }
            } else {
                echo 'Tous les paramètres sont attendus !';
            }
        }

    }
    unset($_POST);
}