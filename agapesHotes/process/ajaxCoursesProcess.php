<?php
require_once('../main.php');
if (checkAjaxRequest()) {
    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        $CoursesProcess = new \App\Plugin\AgapesHotes\Courses();

        // ADD COURSES
        if (!empty($_POST['ADDCOURSES'])) {

            if (!empty($_POST['nom']) && !empty($_POST['etablissementId'])) {

                $CoursesProcess->setNom($_POST['nom']);
                $CoursesProcess->setEtablissementId($_POST['etablissementId']);

                if ($CoursesProcess->notExist()) {

                    if ($CoursesProcess->save()) {
                        echo json_encode(true);
                    }
                } else {
                    echo 'Cet article existe déjà !';
                }
            } else {
                echo 'Un nom est attendu !';
            }
        }

        // UPDATE COURSES
        if (!empty($_POST['UPDATECOURSES'])) {

            if (!empty($_POST['idCoursesUpdate']) && !empty($_POST['newName'])) {

                $CoursesProcess->setId($_POST['idCoursesUpdate']);
                if ($CoursesProcess->show()) {

                    $CoursesProcess->setNom($_POST['newName']);

                    if ($CoursesProcess->notExist(true)) {

                        if ($CoursesProcess->update()) {
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

                $CoursesProcess->setId($_POST['idCoursesArchive']);
                if ($CoursesProcess->show()) {

                    if ($CoursesProcess->delete()) {
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