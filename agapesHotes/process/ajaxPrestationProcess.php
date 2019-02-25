<?php
require_once('../main.php');
if (checkAjaxRequest()) {
    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        $PrestationProcess = new \App\Plugin\AgapesHotes\Prestation();

        // ADD PRESTATION
        if (!empty($_POST['ADDPRESTATION'])) {

            if (!empty($_POST['nom']) && !empty($_POST['etablissementId'])) {

                $PrestationProcess->setNom($_POST['nom']);
                $PrestationProcess->setEtablissementId($_POST['etablissementId']);

                if ($PrestationProcess->notExist()) {

                    if ($PrestationProcess->save()) {
                        echo json_encode(true);
                    }
                } else {

                    if ($PrestationProcess->getStatus() == 0) {
                        echo 'Cet prestation est archivée. Voulez vous le restaurer ?' .
                            '<button type="button" data-restaureprestationid="' . $PrestationProcess->getId() . '" class="btn btn-sm btn-link retaurePrestation">Oui</button>';
                    } else {
                        echo 'Cette prestation existe déjà !';
                    }
                }
            } else {
                echo 'Un nom est attendu !';
            }
            exit();
        }

        // UPDATE PRESTATION
        if (!empty($_POST['UPDATEPRESTATION'])) {

            if (!empty($_POST['idPrestationUpdate']) && !empty($_POST['newName'])) {

                $PrestationProcess->setId($_POST['idPrestationUpdate']);
                if ($PrestationProcess->show()) {

                    $PrestationProcess->setNom($_POST['newName']);

                    if ($PrestationProcess->notExist(true)) {

                        if ($PrestationProcess->update()) {
                            echo json_encode(true);
                        } else {
                            echo 'Impossible de mettre à jour cette prestation !';
                        }
                    } else {
                        echo 'Ce nom de prestation existe déjà !';
                    }
                } else {
                    echo 'Cette prestation n\'existe pas !';
                }
            }
            exit();
        }

        // ARCHIVE PRESTATION
        if (!empty($_POST['ARCHIVEPRESTATION'])) {

            if (!empty($_POST['idPrestationArchive'])) {

                $PrestationProcess->setId($_POST['idPrestationArchive']);
                if ($PrestationProcess->show()) {

                    if ($PrestationProcess->delete()) {
                        echo json_encode(true);
                    } else {
                        echo 'Impossible d\'archiver cette prestation !';
                    }
                } else {
                    echo 'Cette prestation n\'existe pas !';
                }
            }
            exit();
        }

        // RESTAURE PRESTATION
        if (!empty($_POST['RESTAUREPRESTATION'])) {

            if (!empty($_POST['idPrestationRestaure'])) {

                $PrestationProcess->setId($_POST['idPrestationRestaure']);
                if ($PrestationProcess->show()) {

                    $PrestationProcess->setStatus(1);

                    if ($PrestationProcess->update()) {
                        echo json_encode(true);
                    } else {
                        echo 'Impossible de restaurer cet prestation !';
                    }
                } else {
                    echo 'Cet prestation n\'existe pas !';
                }
            }
            exit();
        }

        // UPDATE | CREATE PRESTATION PRICE
        if (!empty($_POST['UPDATEPRESTATIONPRICE'])) {

            if (!empty($_POST['etablissementId']) && !empty($_POST['prestationId'])
                && !empty($_POST['prixHT']) && !empty($_POST['dateDebutMois']) && !empty($_POST['dateDebutAnnee'])) {

                if (checkdate($_POST['dateDebutMois'], 1, $_POST['dateDebutAnnee'])) {
                    $PrestationProcess->setId($_POST['prestationId']);
                    if ($PrestationProcess->show() && $PrestationProcess->getStatus()) {

                        $PrestationPriceProcess = new App\Plugin\AgapesHotes\PrixPrestation();
                        $PrestationPriceProcess->feed($_POST);
                        $PrestationPriceProcess->setDateDebut($_POST['dateDebutAnnee'] . '-' . $_POST['dateDebutMois'] . '-01');

                        if ($PrestationPriceProcess->notExist()) {

                            if ($PrestationPriceProcess->save()) {

                                echo json_encode(true);
                            } else {
                                echo 'Impossible d\'enregistrer le nouveau prix';
                            }

                        } else {

                            if ($PrestationPriceProcess->notExist(true)) {

                                if ($PrestationPriceProcess->update()) {

                                    echo json_encode(true);
                                } else {
                                    echo 'Impossible de mettre à jour le nouveau prix';
                                }

                            } else {
                                echo 'Le prix de la prestation existe déjà pour la même date !';
                            }
                        }
                    } else {
                        echo 'Cette prestation n\'existe pas !';
                    }
                } else {
                    echo 'Cette date n\'existe pas !';
                }
            }
            exit();
        }
    }
}