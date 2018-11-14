<?php require('header.php');
if (!empty($_GET['secteur']) && !empty($_GET['site'])):

    //Get Secteur
    $Secteur = new \App\Plugin\AgapesHotes\Secteur();
    $Secteur->setSlug($_GET['secteur']);

    //Get Site
    $Site = new \App\Plugin\AgapesHotes\Site();
    $Site->setSlug($_GET['site']);


    //Check Secteur, Site and Etablissement
    if ($Secteur->showBySlug() && $Site->showBySlug() && $Site->getSecteurId() == $Secteur->getId()):
        echo getTitle($Page->getName(), $Page->getSlug(), ' de <strong>' . $Site->getNom() . '</strong> du mois de <strong>' . strftime("%B", strtotime(date('Y-m-d'))) . '</strong>');

        //Get Courses
        $Course = new \App\Plugin\AgapesHotes\Courses();

        //Select period
        //Select period
        $start = new \DateTime(date('Y-m-01'));
        $end = new \DateTime(date('Y-m-t'));
        $end = $end->modify('+1 day');
        $interval = new \DateInterval('P1D');

        $period = new \DatePeriod($start, $interval, $end);

        //Get etablissements
        $Etablissement = new \App\Plugin\AgapesHotes\Etablissement();
        $Etablissement->setSiteId($Site->getId());
        $allEtablissements = $Etablissement->showAllBySite();
        ?>
        <div class="table-responsive col-12">
            <table class="table table-striped tableNonEffect">
                <thead>
                <tr>
                    <th><?= trans('Course'); ?></th>
                    <th style="white-space: nowrap;"><?= trans('Prix/unité HT'); ?></th>
                    <th style="white-space: nowrap;"><?= trans('Taux tva'); ?></th>
                    <th><?= trans('Quantité'); ?></th>
                    <th style="white-space: nowrap;"><?= trans('Total HT'); ?></th>
                    <?php foreach ($period as $key => $date): ?>
                        <th class="text-center"
                            style="<?= $date->format('d') == date('d') ? 'background:#4fb99f;color:#fff;' : ''; ?>
                            <?= $date->format('N') == 7 ? 'background:#f2b134;color:#4b5b68;' : ''; ?>
                            <?= isferie($date->format('Y-m-d'), true) ? 'background:#d8886f;color:#fff;' : ''; ?>"><?= $date->format('d'); ?></th>
                    <?php endforeach; ?>
                    <th><i class="fas fa-balance-scale"></i></th>
                </tr>
                </thead>
                <?php foreach ($allEtablissements as $etablissement):

                    //Get main courante
                    $allVivresCrue = getAllVivreCrueByEtablissementInMonth($etablissement->id);
showDebugData($allVivresCrue);
                    $Course->setEtablissementId($etablissement->id);
                    $allCourses = $Course->showAll();

                    if ($allCourses): ?>
                        <tbody data-etablissement="<?= $etablissement->id; ?>">
                        <tr>
                            <th colspan="<?= ($end->format('t') + 5); ?>"><?= $etablissement->nom; ?></th>
                        </tr>
                        <?php foreach ($allCourses as $course):
                            $vivreCrueQuantiteTotalDay = 0; ?>
                            <tr data-idcourse="<?= $course->id ?>">
                                <th class="positionRelative" style="vertical-align: middle;">
                                    <?= $course->nom; ?>
                                </th>
                                <?php
                                $vivreCruePrixHtUnit = '';
                                $vivreCrueTauxTva = '';
                                if (array_key_exists($course->id, $allVivresCrue)) {
                                    $VivreCrue = current($allVivresCrue[$course->id]);
                                    $vivreCruePrixHtUnit = $VivreCrue->prixHTunite;
                                    $vivreCrueTauxTva = $VivreCrue->tauxTVA;
                                }
                                ?>
                                <td class="text-center">
                                    <input type="tel" name="prixUntitHT" value="<?= $vivreCruePrixHtUnit; ?>"
                                           class="text-center" data-idcourse="<?= $course->id ?>"
                                           style="width: 50px;margin-right: 5px;">€
                                </td>
                                <td class="text-center">
                                    <input type="tel" name="tauxTva" value="<?= $vivreCrueTauxTva; ?>"
                                           class="text-center" data-idcourse="<?= $course->id ?>"
                                           style="width: 50px;margin-right: 5px;">%
                                </td>
                                <td class="text-center" data-quantitecourseid="<?= $course->id ?>"></td>
                                <td class="text-center" data-totalcourseid="<?= $course->id ?>"></td>
                                <?php foreach ($period as $key => $date):
                                    $vivreCrueId = '';
                                    $vivreCrueQuantite = '';
                                    if (array_key_exists($course->id, $allVivresCrue) && array_key_exists($date->format('Y-m-d'), $allVivresCrue[$course->id])) {
                                        $VivreCrue = $allVivresCrue[$course->id][$date->format('Y-m-d')];
                                        $mainCourantId = $VivreCrue->id;
                                        $vivreCrueQuantite = $VivreCrue->quantite;
                                    }
                                    $vivreCrueQuantiteTotalDay += $vivreCrueQuantite;
                                    ?>

                                    <td style="padding: 4px !important;">
                                        <input type="tel" data-idcourse="<?= $course->id; ?>"
                                               data-date="<?= $date->format('Y-m-d'); ?>"
                                               data-day="<?= $date->format('d'); ?>"
                                               class="text-center form-control vivreCrueInput"
                                               name="<?= $vivreCrueId; ?>"
                                               value="<?= $vivreCrueQuantite; ?>"
                                               style="padding: 5px 0 !important; <?= $date->format('d') == date('d') ? 'background:#4fb99f;color:#fff;' : ''; ?>">
                                    </td>


                                <?php endforeach; ?>
                                <td class="quantityTotalDay" data-idcourse="<?= $course->id ?>">
                                    <?= $vivreCrueQuantiteTotalDay; ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>
                        <tr>
                            <th><?= trans('Total'); ?></th>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <?php foreach ($period as $key => $date): ?>
                                <td style="<?= $date->format('d') == date('d') ? 'background:#4fb99f;color:#fff !important;' : ''; ?> <?= $date->format('N') == 7 ? 'background:#f2b134;color:#4b5b68;' : ''; ?> font-size: 0.7em !important;"
                                    class="totalDayPrestationPrice text-center"
                                    data-day="<?= $date->format('j'); ?>"
                                    data-etablissement="<?= $etablissement->id; ?>"></td>
                            <?php endforeach; ?>
                        </tr>
                        </tbody>
                    <?php endif;
                endforeach; ?>
            </table>
        </div>
        <script>
            $(document).ready(function () {

                var delay = (function () {
                    var timer = 0;
                    return function (callback, ms) {
                        clearTimeout(timer);
                        timer = setTimeout(callback, ms);
                    };
                })();

                $('input.vivreCrueInput').on('input keyup', function (event) {
                    event.preventDefault();

                    var $Input = $(this);

                    $('input.mainCourantInput').removeClass('successInput');

                    if (!$Input.val().length > 0) {
                        $Input.val('');
                    }

                    var idVivreCrue = $Input.attr('name');
                    var idCourse = $Input.data('idcourse');
                    var etablissementId = $Input.closest('tbody').data('etablissement');
                    var date = $Input.data('date');
                    var quantite = $Input.val();

                    var prixUniteHT = $('input[name="prixUntitHT"][data-idcourse="' + idCourse + '"]').val();
                    var tauxTva = $('input[name="tauxTva"][data-idcourse="' + idCourse + '"]').val();

                    if (typeof prixUniteHT !== 'undefined' && typeof tauxTva !== 'undefined'
                        && prixUniteHT.length > 0 && tauxTva.length > 0) {


                        var dateEnd = new Date();
                        dateEnd.setDate(dateEnd.getDate() + 7);

                        if (new Date(date).getTime() <= dateEnd.getTime()) {
                            disabledAllFields($Input);
                            delay(function () {
                                busyApp();

                                $.post(
                                    '<?= AGAPESHOTES_URL . 'process/ajaxVivreCrueProcess.php'; ?>',
                                    {
                                        UPDATEVIVRECRUE: 'OK',
                                        etablissementId: etablissementId,
                                        idCourse: idCourse,
                                        date: date,
                                        quantite: quantite,
                                        prixHTunite: prixUniteHT,
                                        tauxTva: tauxTva,
                                        total: (quantite*prixUniteHT),
                                        id: idVivreCrue
                                    },
                                    function (data) {
                                        if (data && $.isNumeric(data)) {
                                            $Input.attr('name', data);
                                            $Input.addClass('successInput');
                                        } else {
                                            alert(data);
                                        }
                                        availableApp();
                                        activateAllFields();
                                    }
                                );
                            }, 300);
                        } else {
                            $Input.val('');
                            alert('Il est interdit de saisir une quantité pour une date supérieure à une semaine depuis aujourd\'hui !');
                        }
                    } else {
                        $Input.val('');
                        alert('Veuillez fournir le prix/unité HT et le taux de TVA');
                    }

                });
            });
        </script>

    <?php else: ?>
        <?= getContainerErrorMsg(trans('Cet établissement n\'est pas accessible')); ?>
    <?php endif; ?>
<?php endif; ?>
<?php require('footer.php'); ?>