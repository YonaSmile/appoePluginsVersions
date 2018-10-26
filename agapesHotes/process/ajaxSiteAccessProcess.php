<?php
require_once('../main.php');
if (checkAjaxRequest()) {
    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        $SiteAccessProcess = new \App\Plugin\AgapesHotes\SiteAccess();


        // ADD SECTEUR ACCESS
        if (!empty($_POST['ADDSITEACCESS'])) {

            if (!empty($_POST['siteUserId']) && !empty($_POST['siteId'])) {

                $SiteAccessProcess->feed($_POST);
                if ($SiteAccessProcess->notExist()) {

                    if ($SiteAccessProcess->save()) {
                        echo json_encode(true);
                    } else {
                        echo 'Impossible d\'autoriser cet utilisateur à accéder à ce site!';
                    }
                } else {
                    echo 'Cet utilisateur a déjà accès à ce site !';
                }
            } else {
                echo 'Tous les champs sont obligatoires !';
            }
        }

        //DELETE SITE ACCESS
        if(!empty($_POST['DELETESITEACCESS']) && !empty($_POST['idSite'])){

            $SiteAccessProcess->setId($_POST['idSite']);
            if($SiteAccessProcess->delete()){
                echo json_encode(true);
            }
        }
    }
}