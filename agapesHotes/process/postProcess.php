<?php
require_once('../main.php');
if (checkPostAndTokenRequest()) {

    $_POST = cleanRequest($_POST);
    \App\Flash::setLocation($_POST['redirectUrl']);

    // MAJORATION DES PRESTATION
    if (!empty($_POST['INCREASEPRESTATION'])) {

        if (!empty($_POST['etablissementId']) && !empty($_POST['increase']) && is_numeric($_POST['increase'])
            && $_POST['increase'] > 0 && !empty($_POST['dateDebutMois']) && !empty($_POST['dateDebutAnnee'])) {

            if (checkdate($_POST['dateDebutMois'], 1, $_POST['dateDebutAnnee'])) {

                $PrestationProcess = new \App\Plugin\AgapesHotes\Prestation();
                $PrestationPriceProcess = new \App\Plugin\AgapesHotes\PrixPrestation();

                $PrestationPriceProcess->setEtablissementId($_POST['etablissementId']);
                $allPrestationPrice = extractFromObjArr($PrestationPriceProcess->showAllByEtablissement(), 'id');

                $PrestationProcess->setEtablissementId($_POST['etablissementId']);
                $allPrestations = extractFromObjArr($PrestationProcess->showAll(), 'id');

                if (count($allPrestations) === count($allPrestationPrice)) {


                    $resultArray = array();
                    foreach ($allPrestationPrice as $prestationPrice) {

                        $PrestationPriceProcess->setId($prestationPrice->id);
                        $PrestationPriceProcess->setDateDebut($_POST['dateDebutAnnee'] . '-' . $_POST['dateDebutMois'] . '-01');
                        $PrestationPriceProcess->setPrestationId($prestationPrice->prestation_id);
                        $PrestationPriceProcess->setPrixHT($prestationPrice->prixHT * (1 + $_POST['increase']));

                        if ($PrestationPriceProcess->notExist()) {

                            if (!$PrestationPriceProcess->save()) {

                                $resultArray[] = 'Impossible d\'enregistrer la  majoration de ' . $allPrestations[$prestationPrice->prestation_id]->nom . ' !';
                            }

                        } else {

                            if ($PrestationPriceProcess->notExist(true)) {

                                if (!$PrestationPriceProcess->update()) {

                                    $resultArray[] = 'Impossible de mettre à jour la  majoration de ' . $allPrestations[$prestationPrice->prestation_id]->nom . ' !';
                                }

                            } else {
                                $resultArray[] = 'Un prix existe déjà pour ' . $allPrestations[$prestationPrice->prestation_id]->nom . ' à cette date !';
                            }
                        }

                    }
                    unset($_POST);

                    if (!isArrayEmpty($resultArray)) {
                        \App\Flash::redirect(implode('<br>', $resultArray), 'danger');
                    } else {
                        \App\Flash::redirect('La majoration a bien été enregistrée !', 'success');
                    }
                } else {
                    \App\Flash::redirect('Certaines prestations n\'ont pas de prix renseigné !');
                }

            } else {
                \App\Flash::redirect('Cette date n\'existe pas !');
            }
        }
    }
}