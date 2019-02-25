<?php
require_once('../main.php');
if (checkAjaxRequest()) {
    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        $NoteDeFraisProcess = new \App\Plugin\AgapesHotes\NoteDeFrais();

        // CREATE AND UPDATE NOTE DE FRAIS
        if (!empty($_POST['UPDATENOTEDEFRAIS']) && valideAjaxToken()) {

            if (!empty($_POST['siteId']) && !empty($_POST['date']) && !empty($_POST['type'])
                && !empty($_POST['nom']) && !empty($_POST['montantHt'])) {

                $NoteDeFraisProcess->feed($_POST);

                if (empty($_POST['id'])) {

                    if ($NoteDeFraisProcess->notExist()) {
                        if ($NoteDeFraisProcess->save()) {

                            echo json_encode(true);
                            exit();

                        } else {
                            echo 'Impossible d\'enregistrer cette note de frais';
                            exit();
                        }
                    } else {
                        echo 'Cette note existe déjà !';
                        exit();
                    }
                } else {

                    if ($NoteDeFraisProcess->notExist(true)) {
                        if ($NoteDeFraisProcess->update()) {

                            echo json_encode(true);
                            exit();

                        } else {
                            echo 'Impossible de mettre à jour cette note de frais';
                            exit();
                        }
                    } else {
                        echo 'Cette note existe déjà !';
                        exit();
                    }
                }
            } else {
                echo 'Tous les champs sont obligatoires !';
                exit();
            }
        }
    }
}