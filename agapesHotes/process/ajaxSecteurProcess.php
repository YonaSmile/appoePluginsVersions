<?php
require_once('../main.php');
if (checkAjaxRequest()) {
    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        $SecteurProcess = new \App\Plugin\AgapesHotes\Secteur();

        // ADD SECTEUR
        if (!empty($_POST['ADDSECTEUR'])) {

            if (!empty($_POST['nom'])) {

                $SecteurProcess->setNom($_POST['nom']);
                $SecteurProcess->setSlug(slugify($_POST['nom']));

                if ($SecteurProcess->notExist()) {

                    if ($SecteurProcess->save()) {
                        echo json_encode(true);
                    }
                } else {
                    echo 'Ce nom de secteur exist déjà !';
                }
            } else {
                echo 'Un nom est attendu !';
            }
        }

        // UPDATE SECTEUR
        if (!empty($_POST['UPDATESECTEUR'])) {

            if (!empty($_POST['idSecteurUpdate']) && !empty($_POST['newName'])) {

                $SecteurProcess->setId($_POST['idSecteurUpdate']);
                if ($SecteurProcess->show()) {

                    $SecteurProcess->setNom($_POST['newName']);
                    $SecteurProcess->setSlug(slugify($_POST['newName']));

                    if ($SecteurProcess->notExist(true)) {

                        if ($SecteurProcess->update()) {
                            echo json_encode(true);
                        }
                    } else {
                        echo 'Ce nom de secteur exist déjà !';
                    }
                } else {
                    echo 'Ce secteur n\'existe pas !';
                }
            }
        }

        // ARCHIVE SECTEUR
        if (!empty($_POST['ARCHIVESECTEUR'])) {

            if (!empty($_POST['idSecteurArchive'])) {

                $SecteurProcess->setId($_POST['idSecteurArchive']);
                if ($SecteurProcess->show()) {

                    if ($SecteurProcess->delete()) {
                        echo json_encode(true);
                    } else {
                        echo 'Impossible d\'archiver ce secteur !';
                    }
                } else {
                    echo 'Ce secteur n\'existe pas !';
                }
            }
        }
    }
}