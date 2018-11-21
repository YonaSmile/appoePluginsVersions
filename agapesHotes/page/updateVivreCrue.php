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
                                        <?= !empty($vivreCruePrixHtUnit) ? 'readonly' : ''; ?>
                                           style="width: 50px;margin-right: 5px;">€
                                </td>
                                <td class="text-center">
                                    <input type="tel" name="tauxTva" value="<?= $vivreCrueTauxTva; ?>"
                                           class="text-center" data-idcourse="<?= $course->id ?>"
                                        <?= !empty($vivreCrueTauxTva) ? 'readonly' : ''; ?>
                                           style="width: 50px;margin-right: 5px;">%
                                </td>
                                <td class="text-center" class="tdQuantity"
                                    data-quantitecourseid="<?= $course->id ?>"></td>
                                <td class="text-center" class="tdTotal" data-totalcourseid="<?= $course->id ?>"></td>
                                <?php foreach ($period as $key => $date):
                                    $vivreCrueId = '';
                                    $vivreCrueQuantite = '';
                                    $vivreCrueTotalPrice = 0;
                                    if (array_key_exists($course->id, $allVivresCrue) && array_key_exists($date->format('Y-m-d'), $allVivresCrue[$course->id])) {
                                        $VivreCrue = $allVivresCrue[$course->id][$date->format('Y-m-d')];
                                        $vivreCrueId = $VivreCrue->id;
                                        $vivreCrueQuantite = $VivreCrue->quantite == 0 ? '' : $VivreCrue->quantite;
                                        $vivreCrueTotalPrice = $VivreCrue->total;
                                    }
                                    $vivreCrueQuantiteTotalDay += $vivreCrueQuantite;
                                    ?>
                                    <td style="padding: 4px !important;">
                                        <input type="tel" data-idcourse="<?= $course->id; ?>"
                                               data-date="<?= $date->format('Y-m-d'); ?>"
                                               data-day="<?= $date->format('j'); ?>"
                                               data-totalprice="<?= $vivreCrueTotalPrice; ?>"
                                               class="text-center form-control vivreCrueInput sensibleField"
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
                        </tbody>
                    <?php endif;
                endforeach; ?>
            </table>
        </div>
        <script>
            $(document).ready(function () {

                function calculateTotalQuantityByCourse(courseId) {

                    var totalQuantityByDay = [];
                    $('input.vivreCrueInput[data-idcourse="' + courseId + '"]').each(function () {

                        var quantity = parseFloat($(this).val());
                        if ($.isNumeric(quantity) && quantity > 0) {
                            totalQuantityByDay.push(quantity);
                        }
                    });

                    var sum = 0;
                    $.each(totalQuantityByDay, function (key, value) {
                        sum += value;
                    });
                    $('td[data-quantitecourseid="' + courseId + '"]').html(sum);
                    calculateTotalPriceByCourse(courseId);
                }

                function calculateTotalPriceByCourse(courseId) {

                    var totalQuantity = parseFloat($('td[data-quantitecourseid="' + courseId + '"]').text());
                    var unitPriceHT = $('input[name="prixUntitHT"][data-idcourse="' + courseId + '"]').val();
                    $('td[data-totalcourseid="' + courseId + '"]').html(financial(totalQuantity * unitPriceHT) + '€');
                }

                $('input.vivreCrueInput').each(function () {
                    calculateTotalQuantityByCourse($(this).data('idcourse'));
                });

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

                    $('input.vivreCrueInput').removeClass('successInput');

                    if (!$Input.val().match(/^\d+$/)) {
                        $Input.val('');
                    }

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

                        if (tauxTva == 5.5 || tauxTva == 10 || tauxTva == 20) {

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
                                            total: (quantite * prixUniteHT),
                                            id: idVivreCrue
                                        },
                                        function (data) {
                                            if (data && $.isNumeric(data)) {
                                                $Input.attr('name', data);
                                                $Input.addClass('successInput');
                                                calculateTotalQuantityByCourse(idCourse);
                                                $('input[name="prixUntitHT"][data-idcourse="' + idCourse + '"]').attr('readonly', 'readonly');
                                                $('input[name="tauxTva"][data-idcourse="' + idCourse + '"]').attr('readonly', 'readonly');
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
                            alert('Le taux de tva ne peut être que : 5.50 / 10.00 / 20.00');
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