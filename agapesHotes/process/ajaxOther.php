<?php
require_once('../main.php');
if (checkAjaxRequest()) {
    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        //DELETE OTHER ACHAT
        if (!empty($_POST['DELETEACHAT']) && !empty($_POST['idAchat'])) {

            $Achat = new \App\Plugin\AgapesHotes\Achat();
            $Achat->setId($_POST['idAchat']);
            if ($Achat->delete()) {
                echo json_encode(true);
            }
            exit();
        }

        if (!empty($_POST['ADDOTHERACHAT'])) {

            if (!empty($_POST['siteId']) && !empty($_POST['fournisseur'])
                && !empty($_POST['date']) && !empty($_POST['total'])) {

                $_POST['total'] = $_POST['total'] * 1.1;
                $Achat = new \App\Plugin\AgapesHotes\Achat();
                $Achat->feed($_POST);

                if (!$Achat->exist()) {

                    if ($Achat->save()) {
                        echo json_encode(true);
                    } else {
                        echo 'Impossible d\'enregistrer l\'achat !';
                    }
                } else {
                    echo 'Cet achat est déjà enregistré !';
                }
            } else {
                echo 'Tous les champs sont obligatoires !';
            }
            exit();
        }
    }
}
