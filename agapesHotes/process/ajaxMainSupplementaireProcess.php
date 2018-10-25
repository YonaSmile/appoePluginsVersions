<?php
require_once('../main.php');
if (checkAjaxRequest()) {
    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        $MainSuppProcess = new \App\Plugin\AgapesHotes\MainSupplementaire();

        // ADD MAIN SUPPLEMENTAIRE
        if (!empty($_POST['ADDMAINSUPPLEMENTAIRE']) && valideAjaxToken()) {

            if (!empty($_POST['siteId']) && !empty($_POST['date']) && !empty($_POST['client_nature']) && !empty($_POST['client_name'])) {

                $MainSuppProcess->setSiteId($_POST['siteId']);
                $MainSuppProcess->setDate($_POST['date']);

                $successArray = array();

                $Client = new \App\Plugin\AgapesHotes\Client();
                $Client->setNature($_POST['client_nature']);
                $Client->setName($_POST['client_name']);
                $Client->setFirstName($_POST['client_firstName']);

                if ($Client->notExist()) {
                    $Client->save();
                } else {
                    $Client->showByTypeAndNatureAndName();
                }

                $MainSuppProcess->setClientId($Client->getId());

                //Prepare products
                $allProductFields = array();
                $acceptedFields = array('nom', 'quantite', 'prixHTunite', 'tauxTVA', 'total');
                foreach ($_POST as $key => $value) {

                    if (false !== strpos($key, '_')) {

                        list($fieldName, $fieldNb) = explode('_', $key);
                        if (in_array($fieldName, $acceptedFields)) {

                            $allProductFields[$fieldNb][$fieldName] = $_POST[$key];
                        }
                    }
                }

                foreach ($allProductFields as $nbProduct => $facture) {

                    $MainSuppProcess->feed($facture);
                    if ($MainSuppProcess->notExist()) {

                        if (!$MainSuppProcess->save()) {
                            $successArray[] = 'Impossible d\'enregistrer <strong>' . $MainSuppProcess->getNom() . '</strong> dans la facture !';
                        }
                    } else {
                        $successArray[] = '<strong>' . $MainSuppProcess->getNom() . '</strong> a déjà été saisi !';
                    }
                }


                if (isArrayEmpty($successArray)) {
                    echo json_encode(true);

                } else {
                    echo implode('<br>', $successArray);
                }

            } else {
                echo 'Tous les champs sont obligatoires !';
            }
        }

        // UPDATE MAIN SUPPLEMENTAIRE
        if (!empty($_POST['UPDATECOURSES'])) {

            if (!empty($_POST['idCoursesUpdate']) && !empty($_POST['newName'])) {

                $MainSuppProcess->setId($_POST['idCoursesUpdate']);
                if ($MainSuppProcess->show()) {

                    $MainSuppProcess->setNom($_POST['newName']);

                    if ($MainSuppProcess->notExist(true)) {

                        if ($MainSuppProcess->update()) {
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

        // ARCHIVE MAIN SUPPLEMENTAIRE
        if (!empty($_POST['ARCHIVECOURSES'])) {

            if (!empty($_POST['idCoursesArchive'])) {

                $MainSuppProcess->setId($_POST['idCoursesArchive']);
                if ($MainSuppProcess->show()) {

                    if ($MainSuppProcess->delete()) {
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