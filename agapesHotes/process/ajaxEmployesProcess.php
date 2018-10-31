<?php
require_once('../main.php');
if (checkAjaxRequest()) {
    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        $EmployeProcess = new \App\Plugin\AgapesHotes\Employe();

        // ADD EMPLOYE
        if (!empty($_POST['ADDEMPLOYE'])) {

            if (!empty($_POST['nature']) && !empty($_POST['name'])
                && !empty($_POST['firstName']) && valideAjaxToken()) {

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

        // UPDATE EMPLOYE
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

        // ARCHIVE EMPLOYE
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

        // ADD, UPDATE EMPLOYE CONTRAT
        if (!empty($_POST['UPDATEEMPLOYECONTRAT'])) {

            if (!empty($_POST['employeId']) && !empty($_POST['siteId'])
                && !empty($_POST['dateDebut']) && !empty($_POST['typeContrat'])
                && !empty($_POST['nbHeuresSemaines']) && valideAjaxToken()) {

                $EmployeContratProcess = new \App\Plugin\AgapesHotes\EmployeContrat();

                $EmployeContratProcess->feed($_POST);
                if ($EmployeContratProcess->notExist()) {

                    if ($EmployeContratProcess->save()) {
                        echo json_encode(true);
                    } else {
                        echo 'Impossible d\'enregistrer ce contrat !';
                    }
                } else {
                    echo 'Ce contrat existe déjà !';
                }
            } else {
                echo 'Tous les champs sont obligatoires !';
            }
        }

        // ARCHIVE EMPLOYE CONTRAT
        if (!empty($_POST['ARCHIVEEMPLOYECONTRAT']) && !empty($_POST['idContrat'])) {

            $EmployeContratProcess = new \App\Plugin\AgapesHotes\EmployeContrat();
            $EmployeContratProcess->setId($_POST['idContrat']);
            if ($EmployeContratProcess->show()) {

                if ($EmployeContratProcess->delete()) {
                    echo json_encode(true);
                } else {
                    echo 'Impossible d\'archiver ce contrat !';
                }
            } else {
                echo 'Ce contrat n\'existe pas !';
            }

        }

    }
}