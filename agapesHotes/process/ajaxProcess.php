<?php
require_once('../main.php');
if (checkAjaxRequest()) {

    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        //GET INPUT NOM DE FRAIS
        if (isset($_POST['getNomFraisPageByType']) && !empty($_POST['type'])) {

            $html = '';

            if (NOM_TYPES_NOTE_FRAIS) {

                foreach (NOM_TYPES_NOTE_FRAIS as $nomFrais):
                    if ($nomFrais['type'] == $_POST['type']):
                        $html .= '<option value="' . $nomFrais['nom'] . '" data-code="' . $nomFrais['code'] . '">' . $nomFrais['nom'] . '</option>';
                    endif;
                endforeach;
            }

            echo $html;
        }

        //GET INPUT PUISSANCE DU VEHICULE
        if (isset($_POST['getPuissanceVehiculePageByType']) && !empty($_POST['type'])) {

            $html = '';

            if (PUISSANCE_VEHICULE) {

                foreach (PUISSANCE_VEHICULE as $vehicule):
                    if ($vehicule['type'] == $_POST['type']):
                        $html .= '<option value="' . $vehicule['puissance'] . '" data-taux="' . $vehicule['taux'] . '">' . $vehicule['puissance'] . '</option>';
                    endif;
                endforeach;
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

        //ADD NOTE DE FRAIS
        if (isset($_POST['ADDINDEMNITEKM']) && valideAjaxToken()) {

            if (!empty($_POST['siteId']) && !empty($_POST['employeId']) && !empty($_POST['year'])
                && !empty($_POST['month']) && !empty($_POST['day']) && !empty($_POST['typeVehicule'])
                && !empty($_POST['puissance']) && !empty($_POST['taux'])
                && !empty($_POST['objetTrajet']) && !empty($_POST['trajet'])
                && !empty($_POST['km']) && !empty($_POST['affectation'])
                && !empty($_POST['montantHt'])) {

                $NoteIK = new \App\Plugin\AgapesHotes\NoteIk();
                $NoteIK->feed($_POST);

                if ($NoteIK->notExist()) {
                    if ($NoteIK->save()) {
                        echo json_encode(true);
                    } else {
                        echo 'Impossible d\'enregistrer la nouvelle indemnité kilométrique !';
                    }
                } else {
                    echo 'Cette indemnité kilométrique existe déjà !';
                }
            } else {
                echo 'Tous les champs accompagnés par un * sont obligatoires !!!';
            }
        }

        //DELETE NOTE DE FRAIS
        if (isset($_POST['DELETENOTEDEFRAIS']) && !isArrayEmpty($_POST['idsNotesDeFrais'])) {

            $NoteDeFrais = new \App\Plugin\AgapesHotes\NoteDeFrais();

            foreach ($_POST['idsNotesDeFrais'] as $key => $idNote) {
                $NoteDeFrais->setId($idNote);
                if ($NoteDeFrais->delete()) {
                    echo json_encode(true);
                }
            }
        }

        //DELETE INDEMNITE KILOMETRIQUE
        if (isset($_POST['DELETEINDEMNITEKM']) && !empty($_POST['idIndemniteKm'])) {

            $NoteIK = new \App\Plugin\AgapesHotes\NoteIk();

            $NoteIK->setId($_POST['idIndemniteKm']);
            if ($NoteIK->delete()) {
                echo json_encode(true);
            }

        }


    }
}