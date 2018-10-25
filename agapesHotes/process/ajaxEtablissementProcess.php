<?php
require_once('../main.php');
if (checkAjaxRequest()) {
    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        $EtablissementProcess = new \App\Plugin\AgapesHotes\Etablissement();

        // ADD ETABLISSEMENT
        if (!empty($_POST['ADDETABLISSEMENT'])) {

            if (!empty($_POST['nom']) && !empty($_POST['site_id'])) {

                $EtablissementProcess->setNom($_POST['nom']);
                $EtablissementProcess->setSlug(slugify($_POST['nom']));
                $EtablissementProcess->setSiteId($_POST['site_id']);

                if ($EtablissementProcess->notExist()) {

                    if ($EtablissementProcess->save()) {
                        echo json_encode(true);
                    }
                } else {
                    echo 'Ce nom d\'établissement exist déjà !';
                }
            } else {
                echo 'Un nom est attendu !';
            }
        }

        // UPDATE ETABLISSEMENT
        if (!empty($_POST['UPDATEETABLISSEMENT'])) {

            if (!empty($_POST['idEtablissementUpdate']) && !empty($_POST['nom']) && !empty($_POST['site_id'])) {

                $EtablissementProcess->setId($_POST['idEtablissementUpdate']);
                if ($EtablissementProcess->show()) {

                    $EtablissementProcess->setNom($_POST['nom']);
                    $EtablissementProcess->setSlug(slugify($_POST['nom']));
                    $EtablissementProcess->setSiteId($_POST['site_id']);

                    if ($EtablissementProcess->notExist(true)) {

                        if ($EtablissementProcess->update()) {
                            echo json_encode(true);
                        }
                    } else {
                        echo 'Ce nom d\'établissement exist déjà !';
                    }
                } else {
                    echo 'Ce site n\'existe pas !';
                }
            }
        }

        // ARCHIVE ETABLISSEMENT
        if (!empty($_POST['ARCHIVEETABLISSEMENT'])) {

            if (!empty($_POST['idEtablissementArchive'])) {

                $EtablissementProcess->setId($_POST['idEtablissementArchive']);
                if ($EtablissementProcess->show()) {

                    if ($EtablissementProcess->delete()) {
                        echo json_encode(true);
                    } else {
                        echo 'Impossible d\'archiver cet établissement !';
                    }
                } else {
                    echo 'Ce site n\'existe pas !';
                }
            }
        }
    }
}