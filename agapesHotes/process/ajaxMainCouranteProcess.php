<?php
require_once('../main.php');
if (checkAjaxRequest()) {
    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        $MainCourantProcess = new \App\Plugin\AgapesHotes\MainCourante();

        // UPDATE | CREATE MAIN COURANTE
        if (!empty($_POST['UPDATEMAINCOURANTE'])) {

            if (!empty($_POST['etablissementId']) && !empty($_POST['prestationId'])
                && !empty($_POST['prixId']) && !empty($_POST['date']) && !empty($_POST['reelPrestationPrice'])
                && isset($_POST['quantite']) && isset($_POST['id']) && is_numeric($_POST['reelPrestationPrice'])) {

                //Check date permissions
                if (!haveUserPermissionToUpdate($_POST['date'])) {
                    echo 'Vous ne pouvez plus modifier les données d\'avant !';
                    exit();
                }

                if (!is_numeric($_POST['quantite'])) {
                    $_POST['quantite'] = 0;
                }

                $MainCourantProcess->feed($_POST);
                $MainCourantProcess->setTotal($_POST['reelPrestationPrice'] * $_POST['quantite']);
                if (empty($_POST['id'])) {

                    if ($MainCourantProcess->save()) {
                        echo $MainCourantProcess->getId();
                    } else {
                        echo 'Impossible d\'enregistrer la main courante';
                    }

                } else {
                    if ($MainCourantProcess->update()) {

                        echo $MainCourantProcess->getId();
                    } else {
                        echo 'Impossible de mettre à jour la main courante';
                    }
                }

            } else {
                echo 'Certains paramètres ne sont pas renseignés ou inexactes !';
            }
        }


    }
    unset($_POST);
}