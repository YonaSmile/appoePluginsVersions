<?php
require_once('../main.php');
if (checkAjaxRequest()) {
    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        $SecteurAccessProcess = new \App\Plugin\AgapesHotes\SecteurAccess();


        // ADD SECTEUR ACCESS
        if (!empty($_POST['ADDSECTEURACCESS'])) {

            if (!empty($_POST['secteurUserId']) && !empty($_POST['secteurId'])) {

                $SecteurAccessProcess->feed($_POST);
                if ($SecteurAccessProcess->notExist()) {

                    if ($SecteurAccessProcess->save()) {
                        echo json_encode(true);
                    } else {
                        echo 'Impossible d\'autoriser cet utilisateur à accéder à ce secteur!';
                    }
                } else {
                    echo 'Cet utilisateur a déjà accès à ce secteur !';
                }
            } else {
                echo 'Tous les champs sont obligatoires !';
            }
        }

        //DELETE SECTEUR ACCESS
        if (!empty($_POST['DELETESECTEURACCESS']) && !empty($_POST['idSecteur'])) {

            $SecteurAccessProcess->setId($_POST['idSecteur']);
            if ($SecteurAccessProcess->delete()) {
                echo json_encode(true);
            }
        }
    }
}