<?php
require_once('../main.php');

//Get Site
$Site = new \App\Plugin\AgapesHotes\Site();
$Site->setSlug($_POST['site']);
$allSites = extractFromObjToSimpleArr($Site->showAll(), 'id', 'nom');

//Check Secteur and Site
if ($Site->showBySlug()):

    //Get Note de Frais
    $NoteDeFrais = new \App\Plugin\AgapesHotes\NoteDeFrais();
    $NoteDeFrais->setSiteId($Site->getId());
    $NoteDeFrais->setEmployeId($_POST['employeId']);
    $NoteDeFrais->setYear($_POST['annee']);
    $NoteDeFrais->setMonth($_POST['month']);
    $allNoteDeFrais = groupMultipleKeysObjectsArray($NoteDeFrais->showByDateAndEmploye(), 'type');

    //Get Indemnité kilométrique
    $NoteIk = new \App\Plugin\AgapesHotes\NoteIk();
    $NoteIk->setSiteId($Site->getId());
    $NoteIk->setEmployeId($_POST['employeId']);
    $NoteIk->setYear($_POST['annee']);
    $NoteIk->setMonth($_POST['month']);
    $allIndemniteKm = groupMultipleKeysObjectsArray($NoteIk->showByDateAndEmploye(), 'type_vehicule');
    ?>
    <div id="noteDeFraisTablesContainer">
        <?php if (!isArrayEmpty($allNoteDeFrais)):
            foreach ($allNoteDeFrais as $type => $notesDeFrais) : ?>
                <h6><?= TYPES_NOTE_FRAIS[$type]; ?></h6>
                <table class="table table-sm table-striped tableNonEffect tableNoteDeFrais mb-4">
                    <thead>
                    <tr>
                        <td data-name="checkbox" style="text-align: center; width: 33px;">
                            <input type="checkbox" name="checkAllNotes">
                        </td>
                        <td style="text-align: center">Jour</td>
                        <td style="text-align: center">Code</td>
                        <td style="text-align: center">Nom</td>
                        <td style="text-align: center">Montant TTC</td>
                        <td style="text-align: center">Montant HT</td>
                        <td style="text-align: center">TVA</td>
                        <td style="text-align: center">Motif</td>
                        <td style="text-align: center">Affectation</td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($notesDeFrais as $noteDeFrais): ?>
                        <tr class="noteDeFraisTR" data-commentaires="<?= $noteDeFrais->commentaire; ?>"
                            data-idnote="">
                            <th data-name="checkbox" style="text-align: center; width: 33px;">
                                <input type="checkbox" name="checkNoteDeFrais" value="<?= $noteDeFrais->id; ?>"></th>
                            <td style="text-align: center"><?= $noteDeFrais->day; ?></td>
                            <td style="text-align: center"><?= $noteDeFrais->code; ?></td>
                            <td style="text-align: center"><?= $noteDeFrais->nom; ?></td>
                            <td data-name="totalNoteDeFrais" style="text-align: center">
                                <?= $noteDeFrais->montantTtc; ?>€
                            </td>
                            <td style="text-align: center"><?= $noteDeFrais->montantHt; ?>€</td>
                            <td style="text-align: center"><?= $noteDeFrais->tva; ?>%</td>
                            <td style="text-align: center;font-size: 9px;"><?= $noteDeFrais->motif; ?></td>
                            <td style="text-align: center;font-size: 9px;"><?= array_key_exists($noteDeFrais->affectation, $allSites) ? $allSites[$noteDeFrais->affectation] : ''; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucune note de frais.</p>
        <?php endif; ?>

        <?php if (!isArrayEmpty($allIndemniteKm)): ?>
            <div id="indemniteKmContainer">
                <?php foreach ($allIndemniteKm as $typeVehicule => $indemnitesKm) : ?>
                    <h6>Indemnité kilométrique pour
                        <em><?= TYPES_VEHICULE[$typeVehicule]; ?></em>&nbsp;<span id="indemniteKmVehiculeInfos"></span></h6>
                    <table class="table table-sm table-striped tableNonEffect tableNoteDeFrais">
                        <thead>
                        <tr>
                            <td style="text-align: center">Jour</td>
                            <td style="text-align: center">Objet du trajet</td>
                            <td style="text-align: center">Trajet</td>
                            <td style="text-align: center">KM</td>
                            <td style="text-align: center">Affectation</td>
                            <td></td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($indemnitesKm as $indemniteKm) : ?>
                            <tr class="indemniteKmTR" data-totalindemnitekm="<?= $indemniteKm->montantHt; ?>"
                                data-commentaires="<?= $indemniteKm->commentaire; ?>"
                                data-typevehicule="<?= TYPES_VEHICULE[$typeVehicule]; ?>"
                                data-puissance="<?= $indemniteKm->puissance; ?>" data-taux="<?= $indemniteKm->taux; ?>">
                                <td style="text-align: center"><?= $indemniteKm->day; ?></td>
                                <td style="text-align: center"><?= $indemniteKm->objet_du_trajet; ?></td>
                                <td style="text-align: center"><?= $indemniteKm->trajet; ?></td>
                                <td style="text-align: center"><?= $indemniteKm->km; ?></td>
                                <td style="text-align: center;font-size: 9px;"><?= array_key_exists($indemniteKm->affectation, $allSites) ? $allSites[$indemniteKm->affectation] : ''; ?></td>
                                <td class="removeFromPrint" style="width: 33px;">
                                    <button type="button" class="btn btn-link text-danger deleteIndemniteKilometrique"
                                            data-idindemnitekm="<?= $indemniteKm->id; ?>"><i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <script>
        $(document).ready(function () {

            function countPrintData() {
                return $('body input[type="checkbox"][name="checkNoteDeFrais"]:checked').length;
            }

            function displayCountPrintData() {
                var countCheckedData = countPrintData();
                $('body .deleteNoteDeFrais').hide();

                if (countCheckedData === 0) {
                    countCheckedData = 'les notes de frais';
                } else {
                    countCheckedData = countCheckedData + ' note' + (countCheckedData > 1 ? 's' : '') + ' de frais';
                    $('body .deleteNoteDeFrais').show();
                }
                $('body .countPrintInfo').html(countCheckedData);
                calculeTotalSelectedNoteDeFrais();
            }

            function calculeTotalSelectedNoteDeFrais() {

                if (countPrintData() > 0) {

                    var sum = 0;
                    $.each($('body input[type="checkbox"][name="checkNoteDeFrais"]:checked'), function () {
                        sum += parseReelFloat($(this).closest('tr').find('td[data-name="totalNoteDeFrais"]').text());
                    });

                    $('body #totalNoteDeFraisCheckedInfo').html(sum + '€');
                    $('body #totalNoteDeFraisCheckedContainer').show();

                } else {
                    $('body #totalNoteDeFraisCheckedContainer').hide();
                }
            }

            function calculeTotalNoteDeFrais() {

                var sum = 0;
                $.each($('body td[data-name="totalNoteDeFrais"]'), function () {
                    sum += parseReelFloat($(this).text());
                });
                $('body #totalNoteDeFraisInfo').html(financial(sum) + '€');
            }

            function calculateTotalIndemniteKm() {

                var sumIndemniteKm = 0;
                $.each($('body tr.indemniteKmTR'), function () {
                    sumIndemniteKm += parseReelFloat($(this).data('totalindemnitekm'));
                });

                $('body #totalIndemniteCheckedInfo').html(financial(sumIndemniteKm) + '€');
                $('body #totalIndemniteCheckedContainer').show();

            }

            calculeTotalNoteDeFrais();
            displayCountPrintData();
            calculateTotalIndemniteKm();

            $('input[name="checkAllNotes"]').on('change', function () {

                var $allInput = $(this).closest('table').find('input[name="checkNoteDeFrais"]');

                if (this.checked) {
                    $allInput.prop("checked", true);
                } else {
                    $allInput.prop("checked", false);
                }
                displayCountPrintData();
            });

            $('input[name="checkNoteDeFrais"]').on('change', function () {
                var $generalInput = $(this).closest('table').find('input[name="checkAllNotes"]');
                if ($generalInput.prop("checked") === true) {
                    $generalInput.prop("checked", false);
                }
                displayCountPrintData();
            });
        });
    </script>
<?php else: ?>
    <?= getContainerErrorMsg(trans('Ce site n\'est pas accessible')); ?>
<?php endif; ?>
