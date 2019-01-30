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
    ?>
    <div id="noteDeFraisTablesContainer">
        <?php if (!isArrayEmpty($allNoteDeFrais)):
            foreach ($allNoteDeFrais as $type => $notesDeFrais) : ?>
                <h6><?= TYPES_NOTE_FRAIS[$type]; ?></h6>
                <table class="table table-sm table-striped tableNonEffect tableNoteDeFrais">
                    <thead>
                    <tr>
                        <td data-name="checkbox" style="text-align: center; width: 33px;"><input type="checkbox" name="checkAll"></td>
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
                        <tr>
                            <th data-name="checkbox" style="text-align: center; width: 33px;">
                                <input type="checkbox" name="checkNoteDeFrais" value="<?= $noteDeFrais->id; ?>"></th>
                            <td style="text-align: center"><?= $noteDeFrais->day; ?></td>
                            <td style="text-align: center"><?= $noteDeFrais->code; ?></td>
                            <td style="text-align: center"><?= $noteDeFrais->nom; ?></td>
                            <td data-name="totalNoteDeFrais" style="text-align: center"><?= $noteDeFrais->montantTtc; ?>
                                €
                            </td>
                            <td style="text-align: center"><?= $noteDeFrais->montantHt; ?>€</td>
                            <td style="text-align: center"><?= $noteDeFrais->tva; ?>%</td>
                            <td style="text-align: center"><?= $noteDeFrais->motif; ?></td>
                            <td style="text-align: center"><?= array_key_exists($noteDeFrais->affectation, $allSites) ? $allSites[$noteDeFrais->affectation] : ''; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucune note de frais.</p>
        <?php endif; ?>
    </div>
    <script>
        $(document).ready(function () {

            function countPrintData() {
                return $('body input[type="checkbox"][name="checkNoteDeFrais"]:checked').length;
            }

            function displatCountPrintData() {
                var countCheckedData = countPrintData();
                if (countCheckedData === 0) {
                    countCheckedData = 'tous';
                } else {
                    countCheckedData = countCheckedData + ' note' + (countCheckedData > 1 ? 's' : '') + ' de frais';
                }
                $('#countPrintInfo').html(countCheckedData);
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
                    $('body #totalNoteDeFraisInfo').html('');
                    $('body #totalNoteDeFraisCheckedContainer').hide();
                }
            }

            var sum = 0;
            $.each($('body td[data-name="totalNoteDeFrais"]'), function () {
                sum += parseReelFloat($(this).text());
            });
            calculeTotalSelectedNoteDeFrais();

            $('body #totalNoteDeFraisInfo').html(sum + '€');

            $('input[name="checkAll"]').on('change', function () {

                var $allInput = $(this).closest('table').find('input[name="checkNoteDeFrais"]');

                if (this.checked) {
                    $allInput.prop("checked", true);
                } else {
                    $allInput.prop("checked", false);
                }
                displatCountPrintData();
            });

            $('input[name="checkNoteDeFrais"]').on('change', function () {
                var $generalInput = $(this).closest('table').find('input[name="checkAll"]');
                if ($generalInput.prop("checked") === true) {
                    $generalInput.prop("checked", false);
                }
                displatCountPrintData();
            });
        });
    </script>
<?php else: ?>
    <?= getContainerErrorMsg(trans('Ce site n\'est pas accessible')); ?>
<?php endif; ?>
