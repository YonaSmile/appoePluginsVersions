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
                if (!haveUserPermissionToUpdate($_POST['date'])) {
                    echo 'Vous ne pouvez plus modifier les données d\'avant !';
                    exit();
                }

                $VivreCrueProcess->feed($_POST);

                if (empty($_POST['id'])) {

                    if ($VivreCrueProcess->save()) {
                        echo $VivreCrueProcess->getId();
                    } else {
                        echo 'Impossible d\'enregistrer le vivre cru';
                    }

                } else {

                    if ($VivreCrueProcess->update()) {

                        echo $VivreCrueProcess->getId();
                    } else {
                        echo 'Impossible de mettre à jour le vivre cru';
                    }

                }
            } else {
                echo 'Tous les paramètres sont attendus !';
            }
        }

    }
    unset($_POST);
}