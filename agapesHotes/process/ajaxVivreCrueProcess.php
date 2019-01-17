<?php
require_once('../main.php');
if (checkAjaxRequest()) {
    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        $VivreCrueProcess = new \App\Plugin\AgapesHotes\VivreCrue();

        define('PRODUCTPRICE_JSON', WEB_PLUGIN_PATH . 'agapesHotes/product_price.json');

        // UPDATE | CREATE VIVRE CRUE
        if (!empty($_POST['UPDATEVIVRECRUE'])) {

            if (!empty($_POST['etablissementId']) && !empty($_POST['date']) && !empty($_POST['idCourse'])
                && !empty($_POST['prixHTunite']) && !empty($_POST['tauxTva'])
                && isset($_POST['quantite']) && isset($_POST['id'])) {

                //Check date permissions
                if (!haveUserPermissionToUpdate($_POST['date'])) {
                    echo 'Vous ne pouvez plus modifier les données d\'avant !';
                    exit();
                }

                $VivreCrueProcess->feed($_POST);

                if (empty($_POST['id'])) {

                    if ($VivreCrueProcess->save()) {
                        echo $VivreCrueProcess->getId();
                    } else {
                        echo 'Impossible d\'enregistrer le vivre cru';
                    }

                } else {

                    if ($VivreCrueProcess->update()) {

                        echo $VivreCrueProcess->getId();
                    } else {
                        echo 'Impossible de mettre à jour le vivre cru';
                    }

                }
            } else {
                echo 'Tous les paramètres sont attendus !';
            }
        }

        //UPDATE PRODUCT PRICE
        if (!empty($_POST['UPDATEPRODUCTPRICE'])) {

            if (!empty($_POST['idCourse']) && !empty($_POST['date'])
                && !empty($_POST['prixHTunite']) && !empty($_POST['tauxTva'])) {

                //Create file if not exist
                if (!file_exists(PRODUCTPRICE_JSON)) {
                    if (false !== fopen(PRODUCTPRICE_JSON, 'w+')) {

                        //Edit
                        $parsed_json_product_price['products'] = array();

                        //Write
                        $json_file = fopen(PRODUCTPRICE_JSON, 'w');
                        fwrite($json_file, json_encode($parsed_json_product_price));
                        fclose($json_file);
                    } else {
                        echo 'Impossible de créer le fichier JSON';
                        exit();
                    }
                }

                $parsed_json = array();

                //Update product price
                if (file_exists(PRODUCTPRICE_JSON)) {

                    $json = file_get_contents(PRODUCTPRICE_JSON);
                    $parsed_json = json_decode($json, true);

                    //Edit
                    if (!empty($parsed_json['products'][$_POST['idCourse']])) {
                        if ($_POST['date'] < $parsed_json['products'][$_POST['idCourse']]['date']) {
                            echo 'La date est inférieur à la date enregistré dans le fichier';
                            exit();
                        }
                    }

                    $parsed_json['products'][$_POST['idCourse']]['date'] = $_POST['date'];
                    $parsed_json['products'][$_POST['idCourse']]['prixHTunite'] = $_POST['prixHTunite'];
                    $parsed_json['products'][$_POST['idCourse']]['tauxTva'] = $_POST['tauxTva'];

                    //Write
                    $json_file = fopen(PRODUCTPRICE_JSON, 'w');
                    fwrite($json_file, json_encode($parsed_json));
                    fclose($json_file);
                    exit();
                }

                echo 'Le fichier JSON n\'existe pas';
                exit();
            }
        }

        //GET PRODUCT PRICE
        if (!empty($_POST['GETPRODUCTPRICE'])) {
            echo file_get_contents(PRODUCTPRICE_JSON);
        }
    }
    unset($_POST);
}