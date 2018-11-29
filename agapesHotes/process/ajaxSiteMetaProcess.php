<?php
require_once('../main.php');
if (checkAjaxRequest()) {
    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        $SiteMetaProcess = new \App\Plugin\AgapesHotes\SiteMeta();

        // CREATE AND UPDATE SITE META
        if (!empty($_POST['UPDATESITEMETA'])) {

            if (!empty($_POST['siteId']) && !empty($_POST['year']) && !empty($_POST['month'])
                && !empty($_POST['dataName']) && isset($_POST['data'])) {


                $SiteMetaProcess->feed($_POST);

                if (empty($_POST['id'])) {

                    if ($SiteMetaProcess->notExist()) {
                        if ($SiteMetaProcess->save()) {
                            echo $SiteMetaProcess->getId();
                        } else {
                            echo 'Impossible d\'enregistrer cette donnée complémentaire';
                        }
                    } else {
                        echo 'Cette donnée complémentaire existe déjà !';
                    }
                } else {

                    if ($SiteMetaProcess->notExist(true)) {
                        if ($SiteMetaProcess->update()) {
                            echo $SiteMetaProcess->getId();
                        } else {
                            echo 'Impossible de mettre à jour cette donnée complémentaire';
                        }
                    } else {
                        echo 'Cette donnée complémentaire existe déjà !';
                    }
                }

            } else {
                echo 'Une donnée est attendu !';
            }


        }
    }
}