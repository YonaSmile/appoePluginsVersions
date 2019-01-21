<?php
require_once('../main.php');
if (checkAjaxRequest()) {
    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        $MainSuppProcess = new \App\Plugin\AgapesHotes\MainSupplementaire();

        // ADD MAIN SUPPLEMENTAIRE
        if (!empty($_POST['ADDMAINSUPPLEMENTAIRE']) && valideAjaxToken()) {

            if (!empty($_POST['siteId']) && !empty($_POST['date']) && !empty($_POST['client_name'])) {

                //Check date permissions
                if (!haveUserPermissionToUpdate($_POST['date'])) {
                    echo 'Vous ne pouvez plus modifier les données d\'avant !';
                    exit();
                }

                $MainSuppProcess->setSiteId($_POST['siteId']);
                $MainSuppProcess->setDate($_POST['date']);
                $MainSuppProcess->setClientName($_POST['client_name']);

                $successArray = array();

                //Prepare products
                $allProductFields = array();
                $acceptedFields = array('nom', 'quantite', 'prixHTunite', 'tauxTVA', 'total');
                $refusedFiledNb = array();

                foreach ($_POST as $key => $value) {

                    if (false !== strpos($key, '_')) {
                        list($fieldName, $fieldNb) = explode('_', $key);

                        if ($fieldName == 'nom' && empty($value)) {
                            $refusedFiledNb[] = $fieldNb;
                            continue;
                        }

                        if (in_array($fieldName, $acceptedFields) && !in_array($fieldNb, $refusedFiledNb)) {
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
                echo 'Tous les champs avec un * sont obligatoires !';
            }
        }

        // UPDATE MAIN SUPPLEMENTAIRE
        if (!empty($_POST['UPDATEMAINSUPPLEMENTAIRE']) && valideAjaxToken()) {

            if (!empty($_POST['siteId']) && !empty($_POST['date']) && !empty($_POST['client_name'])) {

                //Check date permissions
                if (!haveUserPermissionToUpdate($_POST['date'])) {
                    echo 'Vous ne pouvez plus modifier les données d\'avant !';
                    exit();
                }

                $MainSuppProcess->setSiteId($_POST['siteId']);
                $MainSuppProcess->setDate($_POST['date']);
                $MainSuppProcess->setClientName($_POST['client_name']);

                $successArray = array();

                //Prepare products
                $allProductFields = array();
                $acceptedFields = array('id', 'nom', 'quantite', 'prixHTunite', 'tauxTVA', 'total');
                $refusedFiledNb = array();

                foreach ($_POST as $key => $value) {

                    if (false !== strpos($key, '_')) {
                        list($fieldName, $fieldNb) = explode('_', $key);

                        if ($fieldName == 'nom' && empty($value)) {
                            $refusedFiledNb[] = $fieldNb;
                            continue;
                        }

                        if (in_array($fieldName, $acceptedFields) && !in_array($fieldNb, $refusedFiledNb)) {
                            $allProductFields[$fieldNb][$fieldName] = $_POST[$key];
                        }
                    }
                }

                foreach ($allProductFields as $nbProduct => $facture) {

                    $MainSuppProcess->setId('');
                    $MainSuppProcess->feed($facture);
                    if (!empty($MainSuppProcess->getId())) {
                        if ($MainSuppProcess->notExist(true)) {

                            if (!$MainSuppProcess->update()) {
                                $successArray[] = 'Impossible d\'enregistrer <strong>' . $MainSuppProcess->getNom() . '</strong> dans la facture !';
                            }
                        } else {
                            $successArray[] = '<strong>' . $MainSuppProcess->getNom() . '</strong> a déjà été saisi !';
                        }
                    } else {
                        if ($MainSuppProcess->notExist()) {

                            if (!$MainSuppProcess->save()) {
                                $successArray[] = 'Impossible d\'enregistrer <strong>' . $MainSuppProcess->getNom() . '</strong> dans la facture !';
                            }
                        } else {
                            $successArray[] = '<strong>' . $MainSuppProcess->getNom() . '</strong> a déjà été saisi !';
                        }
                    }
                }


                if (isArrayEmpty($successArray)) {
                    echo json_encode(true);

                } else {
                    echo implode('<br>', $successArray);
                }

            } else {
                echo 'Tous les champs avec un * sont obligatoires !';
            }
        }

        if (!empty($_POST['DELETEACHAT']) && !empty($_POST['idAchat'])) {

            $MainSuppProcess->setId($_POST['idAchat']);
            $MainSuppProcess->show();

            //Check date permissions
            if (!haveUserPermissionToUpdate($MainSuppProcess->getDate())) {
                echo 'Vous ne pouvez plus modifier les données d\'avant !';
                exit();
            }

            if ($MainSuppProcess->delete()) {
                echo json_encode(true);
            } else {
                echo 'Un problème est survenue lors de la suppression du produit !';
            }
        }

        if (!empty($_POST['SENDFACTUREBYEMAIL']) && !empty($_POST['data'])) {

            $data = array(
                'fromEmail' => 'facturation@lesagapeshotes.com',
                'fromName' => 'Les Agapes Hôtes',
                'toName' => 'Smilevitch Yona',
                'toEmail' => 'yonasmilevitch@gmail.com',
                'object' => 'Demande de facturation',
                'message' => $_POST['data']
            );

            if (sendMail($data)) {
                echo json_encode(true);
            } else {
                echo 'Un problème est survenue lors de l\'envoi de la facture !';
            }
        }

    }
}