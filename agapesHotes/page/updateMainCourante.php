<?php require('header.php');
if (!empty($_GET['secteur']) && !empty($_GET['site'])):

    //Get Secteur
    $Secteur = new \App\Plugin\AgapesHotes\Secteur();
    $Secteur->setSlug($_GET['secteur']);

    //Get Site
    $Site = new \App\Plugin\AgapesHotes\Site();
    $Site->setSlug($_GET['site']);

    //Check Secteur and Site
    if (
        $Secteur->showBySlug() && $Site->showBySlug() && $Site->getSecteurId() == $Secteur->getId()
    ):
        echo getTitle($Page->getName(), $Page->getSlug(), ' de <strong>' . $Site->getNom() . '</strong> du mois de <strong>' . strftime("%B", strtotime(date('Y-m-d'))) . '</strong>');

        //Get etablissements
        $Etablissement = new \App\Plugin\AgapesHotes\Etablissement();
        $Etablissement->setSiteId($Site->getId());
        $allEtablissements = $Etablissement->showAllBySite();

        //Get prestations
        $Prestation = new \App\Plugin\AgapesHotes\Prestation();

        //Select period
        $start = new \DateTime(date('Y-m-01'));
        $end = new \DateTime(date('Y-m-t'));
        $end = $end->modify('+1 day');
        $interval = new \DateInterval('P1D');

        $period = new \DatePeriod($start, $interval, $end);
        ?>
        <div class="container-fluid">
            <div class="table-responsive col-12">
                <table class="table table-striped tableNonEffect">
                    <thead>
                    <tr>
                        <th><?= trans('Prestation'); ?></th>
                        <?php foreach ($period as $key => $date): ?>
                            <th style="<?= $date->format('d') == date('d') ? 'background:#4fb99f;color:#fff;' : ''; ?> <?= $date->format('N') == 7 ? 'background:#f2b134;color:#4b5b68;' : ''; ?>"><?= $date->format('d'); ?></th>
                        <?php endforeach; ?>
                    </tr>
                    </thead>
                    <?php foreach ($allEtablissements as $etablissement):

                        //Get price by prestation
                        $allPrestationsPrix = getAllPrestationsPriceByEtablissement($etablissement->id);

                        //Get main courante
                        $allMainCourante = getAllMainCouranteByEtablissementInMonth($etablissement->id);

                        $Prestation->setEtablissementId($etablissement->id);
                        $allPrestations = $Prestation->showAll();

                        if ($allPrestations): ?>
                            <tbody data-etablissement="<?= $etablissement->id; ?>">
                            <tr>
                                <th colspan="<?= $end->format('t'); ?>"><?= $etablissement->nom; ?></th>
                            </tr>
                            <?php foreach ($allPrestations as $prestation): ?>
                                <tr data-idprestation="<?= $prestation->id ?>">
                                    <th class="positionRelative"
                                        style="vertical-align: middle;"><?= $prestation->nom; ?>
                                        <small class="prestationReelPrice"
                                               style="position: absolute;top: 0; right: 3px;font-size: 0.7em;"></small>
                                    </th>
                                    <?php foreach ($period as $key => $date):

                                        //Get prix by prestation
                                        $idPrixReel = 0;
                                        $prixReel = 0;

                                        if (array_key_exists($prestation->id, $allPrestationsPrix)) {
                                            $allPrestationsPrixDates = array_keys($allPrestationsPrix[$prestation->id]);
                                            usort($allPrestationsPrixDates, 'reelDatesSortDESC');

                                            foreach ($allPrestationsPrixDates as $prestationsPrixDate) {
                                                if ($prestationsPrixDate <= $date->format('Y-m-d')) {
                                                    $dateDebut = $allPrestationsPrix[$prestation->id][$prestationsPrixDate];

                                                    $idPrixReel = $dateDebut->id;
                                                    $prixReel = $dateDebut->prixHT;

                                                    break;
                                                }
                                            }
                                        }

                                        $mainCourantId = '';
                                        $mainCourantQuantite = '';
                                        if (array_key_exists($prestation->id, $allMainCourante) && array_key_exists($date->format('Y-m-d'), $allMainCourante[$prestation->id])) {
                                            $MainCourante = $allMainCourante[$prestation->id][$date->format('Y-m-d')];
                                            $mainCourantId = $MainCourante->id;
                                            $mainCourantQuantite = $MainCourante->quantite;
                                        }

                                        ?>

                                        <td style="padding: 4px !important;"
                                            data-prixprestation="<?= $prixReel; ?>"
                                            title="<?= $date->format('d') . ' / ' . $prestation->nom; ?>">
                                            <input type="tel" data-prestationid="<?= $prestation->id; ?>"
                                                   data-date="<?= $date->format('Y-m-d'); ?>"
                                                   data-prixid="<?= $idPrixReel; ?>"
                                                   data-day="<?= $date->format('d'); ?>"
                                                   class="text-center form-control mainCourantInput"
                                                   name="<?= $mainCourantId; ?>"
                                                   value="<?= $mainCourantQuantite; ?>"
                                                   style="padding: 5px 0 !important; <?= $date->format('d') == date('d') ? 'background:#4fb99f;color:#fff;' : ''; ?>">
                                            <small class="d-block text-center prestationPrice"
                                                   data-day="<?= $date->format('j'); ?>"
                                                   data-prixprestation="<?= $prixReel; ?>"
                                                   data-etablissement="<?= $etablissement->id; ?>"><?= financial($prixReel * $mainCourantQuantite); ?>
                                            </small>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; endif; ?>
                        <tr>
                            <th><?= trans('Total'); ?></th>
                            <?php foreach ($period as $key => $date): ?>
                                <td style="<?= $date->format('d') == date('d') ? 'background:#4fb99f;color:#fff !important;' : ''; ?> <?= $date->format('N') == 7 ? 'background:#f2b134;color:#4b5b68;' : ''; ?> font-size: 0.7em !important;"
                                    class="totalDayPrestationPrice" data-day="<?= $date->format('j'); ?>"
                                    data-etablissement="<?= $etablissement->id; ?>"></td>
                            <?php endforeach; ?>
                        </tr>
                        </tbody>
                    <?php endforeach; ?>
                </table>
                <table class="table table-striped tableNonEffect" style="width: 250px;">
                    <tbody>
                    <tr>
                        <th>CA Mois</th>
                        <td class="caPrestationMonth"></td>
                    </tr>
                    <tr>
                        <th>CA Semaine</th>
                        <td class="caPrestationWeek"></td>
                    </tr>
                    <tr>
                        <th>CA Jour</th>
                        <td class="caPrestationDay"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <script>
            $(document).ready(function () {

                function disabledAllFields(inputExlude) {
                    $('input.mainCourantInput').not(inputExlude).attr('disabled', 'disabled');
                }

                function activateAllFields() {
                    $('input.mainCourantInput').attr('disabled', false);
                }

                function calculateTotalPrestationPricePerDay() {
                    var totalPriceByDay = [];
                    $('.prestationPrice').each(function () {
                        var $info = $(this);
                        var day = $info.data('day');
                        var etablissement = $info.data('etablissement');
                        var totalDay = parseFloat($info.text());

                        if (typeof totalPriceByDay[etablissement] === 'undefined') {
                            totalPriceByDay[etablissement] = [];
                        }
                        if (typeof totalPriceByDay[etablissement][day] === 'undefined') {
                            totalPriceByDay[etablissement][day] = [];
                        }
                        totalPriceByDay[etablissement][day].push(totalDay);
                    });

                    $.each(totalPriceByDay, function (etablissement, day) {
                        $.each(day, function (key, value) {
                            console.log(value);
                            var sum = 0;
                            $.each(value, function (i, val) {
                                sum += val;
                            });
                            $('.totalDayPrestationPrice[data-day="' + key + '"][data-etablissement="' + etablissement + '"]').html(financial(sum));
                        });
                    });
                    calculateAveragePrestationPricePerMonth();
                }

                function calculateAveragePrestationPricePerMonth() {
                    var totalPriceByDay = [];
                    $('.totalDayPrestationPrice').each(function () {
                        var $totalDay = $(this);
                        var totalDay = parseFloat($totalDay.text());

                        totalPriceByDay.push(totalDay);
                    });
                    var sum = 0;
                    $.each(totalPriceByDay, function (key, value) {
                        sum += value;
                    });
                    $('.caPrestationMonth').html(financial(sum) + '€');

                    calculateAveragePrestationPricePerDay();
                }

                function calculateAveragePrestationPricePerDay() {

                    var totalMonth = parseFloat($('.caPrestationMonth').text());
                    var averageDay = (totalMonth / parseFloat(<?= date('t'); ?>));

                    $('.caPrestationDay').html(financial(averageDay) + '€');

                    calculateAveragePrestationPricePerWeek();
                }

                function calculateAveragePrestationPricePerWeek() {

                    var totalDays = parseFloat($('.caPrestationDay').text());
                    var averageWeek = (totalDays * 7);

                    $('.caPrestationWeek').html(financial(averageWeek) + '€');
                }


                $('tr[data-idprestation!=""]').each(function () {
                    var $tr = $(this);
                    var prestationPrice = $('td', $tr).eq(1).data('prixprestation');
                    $tr.find('.prestationReelPrice').html(prestationPrice + '€');
                });

                calculateTotalPrestationPricePerDay();

                var delay = (function () {
                    var timer = 0;
                    return function (callback, ms) {
                        clearTimeout(timer);
                        timer = setTimeout(callback, ms);
                    };
                })();

                $('input.mainCourantInput').on('input keyup', function (event) {
                    event.preventDefault();

                    var $Input = $(this);
                    var $InputInfo = $Input.next('small.prestationPrice');

                    $('input.mainCourantInput').removeClass('successInput');

                    if (!$Input.val().length > 0) {
                        $Input.val('');
                    }

                    var idMainCourante = $Input.attr('name');
                    var etablissementId = $Input.closest('tbody').data('etablissement');
                    var prestationId = $Input.data('prestationid');
                    var date = $Input.data('date');
                    var prixId = parseFloat($Input.data('prixid'));
                    var quantite = $Input.val();

                    if (new Date(date).getTime() <= new Date().getTime()) {
                        disabledAllFields($Input);
                        delay(function () {
                            busyApp();

                            $.post(
                                '<?= AGAPESHOTES_URL . 'process/ajaxMainCouranteProcess.php'; ?>',
                                {
                                    UPDATEMAINCOURANTE: 'OK',
                                    etablissementId: etablissementId,
                                    prestationId: prestationId,
                                    date: date,
                                    prixId: prixId,
                                    quantite: quantite,
                                    id: idMainCourante
                                },
                                function (data) {
                                    if (data && $.isNumeric(data)) {
                                        $Input.attr('name', data);
                                        $Input.addClass('successInput');
                                    } else {
                                        alert(data);
                                    }
                                    availableApp();
                                    var prestationPrice = parseFloat($InputInfo.data('prixprestation'));
                                    $InputInfo.html(financial(quantite * prestationPrice));
                                    activateAllFields();
                                    calculateTotalPrestationPricePerDay();
                                }
                            );
                        }, 300);
                    } else {
                        $Input.val('');
                        alert('Cette date est ultérieur à aujourdh\'hui !')
                    }

                })
            });
        </script>
    <?php else: ?>
        <?= getContainerErrorMsg(trans('Ce site n\'est pas accessible')); ?>
    <?php endif; ?>
<?php endif; ?>
<?php require('footer.php'); ?>