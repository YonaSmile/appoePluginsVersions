<?php
require_once('../main.php');
if (checkAjaxRequest()) {
    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        $VivreCrueProcess = new \App\Plugin\AgapesHotes\VivreCrue();

        // ADD COURSES
        if (!empty($_POST['ADDVIVRECRUE'])) {

            if (!empty($_POST['nom']) && !empty($_POST['etablissementId']) && !empty($_POST['date'])
                && !empty($_POST['prixHTunite']) && !empty($_POST['quantite'])
                && !empty($_POST['tauxTVA']) && !empty($_POST['total'])) {

                $VivreCrueProcess->feed($_POST);

                if ($VivreCrueProcess->notExist()) {

                    if ($VivreCrueProcess->save()) {
                        echo json_encode(true);
                    }
                } else {
                    echo 'Cette course existe déjà !';
                }
            } else {
                echo 'Tous les champs sont obligatoires !';
            }
        }

        // UPDATE COURSES
        if (!empty($_POST['UPDATECOURSES'])) {

            if (!empty($_POST['idCoursesUpdate']) && !empty($_POST['newName'])) {

                $VivreCrueProcess->setId($_POST['idCoursesUpdate']);
                if ($VivreCrueProcess->show()) {

                    $VivreCrueProcess->setNom($_POST['newName']);

                    if ($VivreCrueProcess->notExist(true)) {

                        if ($VivreCrueProcess->update()) {
                            echo json_encode(true);
                        } else {
                            echo 'Impossible de mettre à jour cet article !';
                        }
                    } else {
                        echo 'Cet article existe déjà !';
                    }
                } else {
                    echo 'Cet article n\'existe pas !';
                }
            }
        }

        // ARCHIVE COURSES
        if (!empty($_POST['ARCHIVECOURSES'])) {

            if (!empty($_POST['idCoursesArchive'])) {

                $VivreCrueProcess->setId($_POST['idCoursesArchive']);
                if ($VivreCrueProcess->show()) {

                    if ($VivreCrueProcess->delete()) {
                        echo json_encode(true);
                    } else {
                        echo 'Impossible d\'archiver cet article !';
                    }
                } else {
                    echo 'Cet article n\'existe pas !';
                }
            }
        }

    }
}