<?php
require_once('../main.php');
if (checkAjaxRequest()) {
    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        $MainCourantProcess = new \App\Plugin\AgapesHotes\MainCourante();

        // UPDATE | CREATE MAIN COURANTE
        if (!empty($_POST['UPDATEMAINCOURANTE'])) {

            if (!empty($_POST['siteId']) && !empty($_POST['prestationId'])
                && !empty($_POST['prixId']) && !empty($_POST['date'])
                && isset($_POST['quantite']) && isset($_POST['id'])) {

                $MainCourantProcess->feed($_POST);

                if ($MainCourantProcess->notExist()) {

                    if ($MainCourantProcess->save()) {
                        echo $MainCourantProcess->getId();
                    } else {
                        echo 'Impossible d\'enregistrer la main courante';
                    }

                } else {

                    if ($MainCourantProcess->notExist(true)) {

                        if ($MainCourantProcess->update()) {

                            echo $MainCourantProcess->getId();
                        } else {
                            echo 'Impossible de mettre à jour la main courante';
                        }

                    } else {
                        echo 'La quantité de la main courante pour cette date existe déjà !';
                    }
                }
            } else {
                echo 'Un nom est attendu !';
            }
        }


    }
}