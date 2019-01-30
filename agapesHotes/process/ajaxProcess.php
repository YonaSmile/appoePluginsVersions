<?php
require_once('../main.php');
if (checkAjaxRequest()) {

    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        //GET INPUT NOM DE FRAIS
        if (isset($_POST['getNomFraisPageByType']) && !empty($_POST['type'])) {

            $html = '';

            if (NOM_TYPES_NOTE_FRAIS) {

                $html .= \App\Form::text('Nom du frais *', 'nom', '', '', true, 150, 'list="nomFraisList" autocomplete="off"');
                $html .= '<datalist id="nomFraisList">';

                foreach (NOM_TYPES_NOTE_FRAIS as $nomFrais):
                    if ($nomFrais['type'] == $_POST['type']):
                        $html .= '<option value="' . $nomFrais['nom'] . '" data-code="' . $nomFrais['code'] . '">' . $nomFrais['nom'] . '</option>';
                    endif;
                endforeach;

                $html .= '</datalist>';
            }

            echo $html;
        }

        //ADD NOTE DE FRAIS
        if (isset($_POST['ADDNOTEDEFRAIS']) && valideAjaxToken()) {

            if (!empty($_POST['siteId']) && !empty($_POST['employeId']) && !empty($_POST['year'])
                && !empty($_POST['month']) && !empty($_POST['day']) && !empty($_POST['type'])
                && !empty($_POST['nom']) && !empty($_POST['code'])
                && !empty($_POST['montantHt']) && !empty($_POST['tva'])
                && !empty($_POST['montantTtc']) && !empty($_POST['affectation'])) {

                $NoteDeFrais = new \App\Plugin\AgapesHotes\NoteDeFrais();
                $NoteDeFrais->feed($_POST);

                if ($NoteDeFrais->notExist()) {
                    if ($NoteDeFrais->save()) {
                        echo json_encode(true);
                    } else {
                        echo 'Impossible d\'enregistrer la nouvelle note de frais !';
                    }
                } else {
                    echo 'Cette note de frais existe déjà !';
                }
            } else {
                echo 'Tous les champs accompagnés par un * sont obligatoires !';
            }
        }


    }
}