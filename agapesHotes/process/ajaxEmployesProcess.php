<?php
require_once('../main.php');
if (checkAjaxRequest()) {
    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        $EmployeProcess = new \App\Plugin\AgapesHotes\Employe();

        // ADD COURSES
        if (!empty($_POST['ADDEMPLOYE'])) {

            if (!empty($_POST['nature']) && !empty($_POST['name'])
                && !empty($_POST['firstName']) && valideToken()) {

                $EmployeProcess->setNature($_POST['nature']);
                $EmployeProcess->setName($_POST['name']);
                $EmployeProcess->setFirstName($_POST['firstName']);

                if ($EmployeProcess->notExist()) {

                    if ($EmployeProcess->save()) {
                        echo json_encode(true);
                    }
                } else {
                    echo 'Cet employé existe déjà !';
                }
            } else {
                echo 'Tous les champs sont obligatoires !';
            }
        }

        // UPDATE COURSES
        if (!empty($_POST['UPDATEEMPLOYE'])) {

            if (!empty($_POST['id']) && !empty($_POST['nature'])
                && !empty($_POST['name']) && !empty($_POST['firstName'])) {

                $EmployeProcess->setId($_POST['id']);
                if ($EmployeProcess->show()) {

                    $EmployeProcess->setNature($_POST['nature']);
                    $EmployeProcess->setName($_POST['name']);
                    $EmployeProcess->setFirstName($_POST['firstName']);

                    if ($EmployeProcess->notExist(true)) {

                        if ($EmployeProcess->update()) {
                            echo json_encode(true);
                        } else {
                            echo 'Impossible de mettre à jour cet employé !';
                        }
                    } else {
                        echo 'Cet employé existe déjà !';
                    }
                } else {
                    echo 'Cet employé n\'existe pas !';
                }
            }
        }

        // ARCHIVE COURSES
        if (!empty($_POST['ARCHIVEEMPLOYE'])) {

            if (!empty($_POST['idEmploye'])) {

                $EmployeProcess->setId($_POST['idEmploye']);
                if ($EmployeProcess->show()) {

                    if ($EmployeProcess->delete()) {
                        echo json_encode(true);
                    } else {
                        echo 'Impossible d\'archiver cet employé !';
                    }
                } else {
                    echo 'Cet employé n\'existe pas !';
                }
            }
        }

    }
}