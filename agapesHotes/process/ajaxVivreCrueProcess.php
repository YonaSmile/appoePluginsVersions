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