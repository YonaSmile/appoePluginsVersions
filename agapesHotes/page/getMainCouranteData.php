<?php
require_once('../main.php');

//Get Secteur
$Secteur = new \App\Plugin\AgapesHotes\Secteur();
$Secteur->setSlug($_POST['secteur']);

//Get Site
$Site = new \App\Plugin\AgapesHotes\Site();
$Site->setSlug($_POST['site']);

//Check Secteur and Site
if (
    $Secteur->showBySlug() && $Site->showBySlug() && $Site->getSecteurId() == $Secteur->getId()
):

    //Get etablissements
    $Etablissement = new \App\Plugin\AgapesHotes\Etablissement();
    $Etablissement->setSiteId($Site->getId());
    $allEtablissements = $Etablissement->showAllBySite();

    //Get prestations
    $Prestation = new \App\Plugin\AgapesHotes\Prestation();

    $startDate = !empty($_POST['startDate']) ? $_POST['startDate'] : date('Y-m-01');

    //Select period
    $start = new \DateTime($startDate);
    $end = new \DateTime($startDate);
    $end->add(new \DateInterval('P1M'));
    $interval = new \DateInterval('P1D');

    $period = new \DatePeriod($start, $interval, $end);
    ?>
    <div class="col-12 py-4">
        <h5 class="mb-0"><?= ucfirst(strftime("%B", strtotime($start->format('Y-m-d')))); ?> <?= $start->format('Y'); ?></h5>
        <small class="d-block">Commence le <?= $start->format('d/m/Y'); ?> et se termine
            le <?= $end->format('d/m/Y'); ?></small>
        <button type="button" class="btn btn-sm btn-info printFirstHalfMonthMainCouranteBtn my-2"
                title="Du 1 au 15 <?= ucfirst(strftime("%B", strtotime($start->format('Y-m-d')))); ?> <?= $start->format('Y'); ?>">
            Édition du 1 - 15
        </button>
        <button type="button" class="btn btn-sm btn-info printLastHalfMonthMainCouranteBtn my-2"
                title="Du 16 au <?= $start->format('t'); ?> <?= ucfirst(strftime("%B", strtotime($start->format('Y-m-d')))); ?> <?= $start->format('Y'); ?>">
            Édition du 16 - <?= $start->format('t'); ?></button>
        <button type="button" class="btn btn-sm btn-info printMainCouranteBtn my-2">Imprimer la facture</button>
    </div>
    <div class="table-responsive col-12">
        <table id="mainCouranteTable" class="table table-striped tableNonEffect">
            <thead>
            <tr style="text-align: center;">
                <th><?= trans('Prestation'); ?></th>
                <?php foreach ($period as $key => $date): ?>
                    <th style="<?= getDayColor($date, $Site->getAlsaceMoselle()); ?>">
                        <?= $date->format('d'); ?></th>
                <?php endforeach; ?>
                <th><i class="fas fa-balance-scale"></i></th>
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
                        <th colspan="<?= $start->format('t') + 2; ?>"><?= $etablissement->nom; ?></th>
                    </tr>
                    <?php foreach ($allPrestations as $prestation):
                        $count = 1;
                        $mainCouranteQuantiteTotalDay = 0; ?>
                        <tr data-idprestation="<?= $prestation->id ?>" class="mainCourantTr">
                            <th class="positionRelative" data-name="prestationName"
                                style="vertical-align: middle;"><span><?= $prestation->nom; ?></span>
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
                                $mainCouranteQuantiteTotalDay += $mainCourantQuantite;
                                ?>

                                <td style="padding: 4px !important;" data-day="<?= $date->format('j'); ?>"
                                    data-tdposition="<?= $count; ?>"
                                    data-prixprestation="<?= $prixReel; ?>" class="mainCouranteTd"
                                    title="<?= $date->format('d') . ' / ' . $prestation->nom; ?>">
                                    <input type="tel" data-prestationid="<?= $prestation->id; ?>"
                                           data-date="<?= $date->format('Y-m-d'); ?>"
                                           data-prixid="<?= $idPrixReel; ?>"
                                           data-prixreelprestation="<?= $prixReel; ?>"
                                           data-day="<?= $date->format('d'); ?>"
                                           class="text-center form-control mainCourantInput sensibleField"
                                           name="<?= $mainCourantId; ?>"
                                           value="<?= $mainCourantQuantite; ?>"
                                           style="padding: 5px 0 !important; <?= getDayColor($date, $Site->getAlsaceMoselle()); ?>">
                                    <small class="d-block text-center prestationPrice"
                                           data-day="<?= $date->format('j'); ?>"
                                           data-prixprestation="<?= $prixReel; ?>"
                                           data-etablissement="<?= $etablissement->id; ?>"><?= financial($prixReel * $mainCourantQuantite); ?>
                                    </small>
                                    <small class="d-block text-center prestationPriceCumule"
                                           data-day="<?= $date->format('j'); ?>"
                                           data-prixprestation="<?= $prixReel; ?>"
                                           data-etablissement="<?= $etablissement->id; ?>"><?= financial($prixReel * $mainCouranteQuantiteTotalDay); ?>
                                    </small>
                                </td>
                                <?php $count++;
                            endforeach; ?>
                            <td class="quantityTotalDay text-center" data-idprestation="<?= $prestation->id ?>">
                                <span><?= $mainCouranteQuantiteTotalDay; ?></span>
                                <small class="d-block text-center prestationTotalPrice"
                                       data-prixprestation="<?= $prixReel; ?>"
                                       data-etablissement="<?= $etablissement->id; ?>">
                                    <?= financial($prixReel * $mainCouranteQuantiteTotalDay); ?>
                                </small>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <th><?= trans('Total'); ?></th>
                        <?php foreach ($period as $key => $date): ?>
                            <td style="font-size:0.65em !important; padding-left: 0.5px !important; padding-right: 0.5px !important; <?= getDayColor($date, $Site->getAlsaceMoselle()); ?>"
                                class="totalDayPrestationPrice text-center" data-day="<?= $date->format('j'); ?>"
                                data-etablissement="<?= $etablissement->id; ?>"></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endif; ?>
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
    <div class="modal fade" id="modalFactureMainCourante"
         tabindex="-1" role="dialog"
         aria-labelledby="modalFactureMainCouranteTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"
                        id="modalFactureMainCouranteTitle">
                        <?= trans('Demande de facturation'); ?></h5>
                    <small>MAIN COURANTE</small>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 my-2">
                            <div class="row my-2">
                                <div class="col-4 my-2">
                                    <?= \App\Form::text('Émetteur', 'emetteur', 'texte', 'Les Agapes Hôtes, ' . $Site->getNom(), true, 255, 'disabled="disabled" readonly', '', 'basePrint'); ?>
                                </div>
                                <div class="col-4 my-2 facturePeriod">
                                    <?= \App\Form::text('Période', 'period', 'text', '', true, 255, 'disabled="disabled" readonly', '', 'basePrint'); ?>
                                </div>
                                <div class="col-4 my-2 factureDate">
                                    <?= \App\Form::text('Date de la Facture', 'date', 'date', date('Y-m-d'), true, 255, 'min="" max=""', '', 'basePrint'); ?>
                                </div>
                                <div class="col-4 my-2">
                                    <?= App\Form::text('Destinataire', 'client_name', 'text', '', true, 150, 'list="etablissementList" autocomplete="off"', '', 'basePrint'); ?>
                                    <?php if ($allEtablissements): ?>
                                        <datalist id="etablissementList">
                                            <?php foreach ($allEtablissements as $etablissement): ?>
                                                <option value="<?= $etablissement->nom; ?>"><?= $etablissement->nom; ?></option>
                                            <?php endforeach; ?>
                                        </datalist>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <hr class="mx-5">
                            <div class="d-xs-none d-sm-block">
                                <div class="row my-1 infoTable">
                                    <div class="col-4"><strong>Prestation</strong>
                                    </div>
                                    <div class="col-3"><strong>Quantité</strong></div>
                                    <div class="col-3"><strong>Prix/unité
                                            HT</strong></div>
                                    <div class="col-2"><strong>Total HT</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 allFactureProducts"></div>
                    </div>
                </div>
                <?php $siteMeta = getSiteMeta($Site->getId(), $start->format('Y'), $start->format('m')); ?>
                <div class="text-right totalContainer py-3 px-5">
                    <div id="totalVariableContainer" class="my-1">
                        <strong>Total variable </strong><span class="totalVariable"></span>
                    </div>
                    <div id="totalFraisFixesContainer" class="my-1">
                        <strong>Frais fixes </strong><span
                                class="totalFraisFixes"><?= $siteMeta['fraisFixes']; ?>€</span>
                    </div>
                    <div id="totalCaHtContainer" class="my-1">
                        <strong>TOTAL CA € HT </strong><span class="totalCaHt"></span>
                    </div>
                </div>
                <div id="signatureFacture" class="m-3">
                    <div class="my-2">
                        <strong>Date de la signature :</strong>
                    </div>
                    <div class="my-2">
                        <strong>Signature :</strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button"
                            class="printFacture btn btn-warning"><?= trans('Imprimer'); ?></button>
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal"><?= trans('Fermer'); ?></button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="/app/js/printThis.js"></script>
    <script>
        $(document).ready(function () {

            $('.tableNonEffect tr td input').keydown(function (e) {

                var $input = $(this);
                var $td = $input.parent('td');
                var $tr = $td.closest('tr');
                var position = $td.data('tdposition');

                switch (e.which) {
                    case 37: // fleche gauche
                        if ($td.prev('td').find('td').data('tdposition') !== 'undefined') {
                            $td.prev('td').find('input').focus();
                        }
                        break;
                    case 38: // fleche haut

                        if ($tr.prev('tr').find('td').length) {
                            $tr.prev('tr').find('td[data-tdposition="' + position + '"]').find('input').focus();
                        }
                        break;
                    case 39: // fleche droite
                        if ($td.next('td').find('td').data('tdposition') !== 'undefined') {
                            $td.next('td').find('input').focus();
                        }
                        break;
                    case 40: // fleche bas

                        if ($tr.next('tr').find('td').length) {
                            $tr.next('tr').find('td[data-tdposition="' + position + '"]').find('input').focus();
                        }
                        break;
                }
            });

            function prepareFirstMonthHalfFacture() {
                $('.allFactureProducts').html('');
                var totalVariable = 0;
                var totalQuantite = 0;
                var totalPrestation = 0;
                var facture = {};

                $('tr.mainCourantTr td.mainCouranteTd').each(function () {
                    var $td = $(this);
                    var $tr = $td.closest('tr');
                    var prestationName = $tr.find('th[data-name="prestationName"] span').text();
                    var prestationPrice = parseFloat($tr.find('small.prestationReelPrice').text());

                    if (!(prestationName in facture)) {
                        facture[prestationName] = {price: prestationPrice, quantite: 0};
                        totalQuantite = 0;
                    }

                    if ($td.data('day') < 16) {
                        var $input = $td.find('input.mainCourantInput');
                        var quantite = parseFloat($input.val());

                        if ($.isNumeric(quantite) && quantite > 0) {

                            totalQuantite += parseFloat(quantite);
                            facture[prestationName].quantite = totalQuantite;
                        }
                    }
                });

                if (facture.length !== 0) {
                    $.each(facture, function (prestation, data) {

                        totalVariable += parseFloat(data.price * data.quantite);

                        var html = '<div class="row my-1 productFields positionRelative"><div class="col-4 mb-1">' +
                            prestation + '</div><div class="col-3 mb-1">' +
                            data.quantite + '</div><div class="col-3 mb-1">' +
                            data.price + '€</div><div class="col-2 mb-1">' +
                            financial(data.price * data.quantite) + '€</div></div>';
                        $('.allFactureProducts').append(html);
                    });
                }
                $('#totalVariableContainer').hide();
                $('#totalFraisFixesContainer').hide();
                $('.totalContainer .totalCaHt').html(financial(totalVariable) + '€');
            }

            function prepareLastMonthHalfFacture() {
                $('.allFactureProducts').html('');
                var totalVariable = 0;
                var totalQuantite = 0;
                var totalPrestation = 0;
                var facture = {};

                $('tr.mainCourantTr td.mainCouranteTd').each(function () {
                    var $td = $(this);
                    var $tr = $td.closest('tr');
                    var prestationName = $tr.find('th[data-name="prestationName"] span').text();
                    var prestationPrice = parseFloat($tr.find('small.prestationReelPrice').text());

                    if (!(prestationName in facture)) {
                        facture[prestationName] = {price: prestationPrice, quantite: 0};
                        totalQuantite = 0;
                    }

                    if ($td.data('day') > 15) {
                        var $input = $td.find('input.mainCourantInput');
                        var quantite = parseFloat($input.val());

                        if ($.isNumeric(quantite) && quantite > 0) {

                            totalQuantite += parseFloat(quantite);
                            facture[prestationName].quantite = totalQuantite;
                        }
                    }
                });

                if (facture.length !== 0) {
                    $.each(facture, function (prestation, data) {

                        totalVariable += parseFloat(data.price * data.quantite);

                        var html = '<div class="row my-1 productFields positionRelative"><div class="col-4 mb-1">' +
                            prestation + '</div><div class="col-3 mb-1">' +
                            data.quantite + '</div><div class="col-3 mb-1">' +
                            data.price + '€</div><div class="col-2 mb-1">' +
                            financial(data.price * data.quantite) + '€</div></div>';
                        $('.allFactureProducts').append(html);
                    });
                }
                $('#totalVariableContainer').hide();
                $('#totalFraisFixesContainer').hide();
                $('.totalContainer .totalCaHt').html(financial(totalVariable) + '€');
            }

            function prepareTotalFacture() {
                $('.allFactureProducts').html('');
                var totalVariable = 0;

                $('tr.mainCourantTr').each(function () {
                    var $tr = $(this);
                    var prestationName = $tr.find('th[data-name="prestationName"] span').text();
                    var prestationPrice = parseFloat($tr.find('small.prestationReelPrice').text());
                    var quantityTotalDay = parseFloat($tr.find('td.quantityTotalDay span').text());
                    var total = financial(prestationPrice * quantityTotalDay);

                    var html = '<div class="row my-1 productFields positionRelative"><div class="col-4 mb-1">' +
                        prestationName + '</div><div class="col-3 mb-1">' +
                        quantityTotalDay + '</div><div class="col-3 mb-1">' +
                        prestationPrice + '€</div><div class="col-2 mb-1">' +
                        total + '€</div></div>';
                    $('.allFactureProducts').append(html);

                    totalVariable += parseFloat(total);
                });
                $('#totalVariableContainer').show();
                $('#totalFraisFixesContainer').show();
                $('.totalContainer .totalVariable').html(financial(totalVariable) + '€');
                var totalFraisFixes = parseFloat($('.totalContainer .totalFraisFixes').text());
                $('.totalContainer .totalCaHt').html(financial(totalVariable + totalFraisFixes) + '€');
            }

            $('.printFirstHalfMonthMainCouranteBtn').on('click', function () {
                prepareFirstMonthHalfFacture();
                $('.factureDate').hide();
                $('.facturePeriod').show();
                $('#signatureFacture').show();
                $('#modalFactureMainCouranteTitle').html('Edition Main Courante');
                $('input#client_name').removeClass('is-invalid');
                $('input#period').val($(this).attr('title'));
                $('#modalFactureMainCourante').modal('show');
            });

            $('.printLastHalfMonthMainCouranteBtn').on('click', function () {
                prepareLastMonthHalfFacture();
                $('.factureDate').hide();
                $('.facturePeriod').show();
                $('#signatureFacture').show();
                $('#modalFactureMainCouranteTitle').html('Edition Main Courante');
                $('input#client_name').removeClass('is-invalid');
                $('input#period').val($(this).attr('title'));
                $('#modalFactureMainCourante').modal('show');
            });

            $('.printMainCouranteBtn').on('click', function () {
                $('.facturePeriod').hide();
                $('.factureDate').show();
                $('#signatureFacture').hide();
                $('#modalFactureMainCouranteTitle').html('Demande de Facturation');
                prepareTotalFacture();
                $('input#client_name').removeClass('is-invalid');
                $('#modalFactureMainCourante').modal('show');
            });


            $('button.printFacture').on('click', function () {

                if ($('input#client_name').val() == '') {
                    $('input#client_name').addClass('is-invalid');
                } else {
                    var $factureMainCourante = $('#modalFactureMainCourante').clone();

                    $('input', $factureMainCourante).each(function () {

                        var $input = $(this);
                        var inputVal = $input.val();
                        if ($input.attr('id') == 'date') {
                            var formattedDate = new Date(inputVal);
                            var day = formattedDate.getDate();
                            if (day < 10) {
                                day = '0' + day;
                            }
                            var month = formattedDate.getMonth();
                            month += 1;
                            var year = formattedDate.getFullYear();

                            inputVal = day + "/" + month + "/" + year
                        }
                        var $parent = $input.parent();
                        $parent.wrap('<strong></strong>');
                        $input.remove();

                        if ($input.hasClass('basePrint')) {
                            $parent.append('<br>' + inputVal);
                        }
                    });

                    $('button, .modal-footer', $factureMainCourante).remove();
                    $factureMainCourante.printThis({
                        loadCSS: "<?= AGAPESHOTES_URL; ?>css/print.css",
                    });
                }
            });

            function calculateTotalPrestationQuantityPerDay(idPrestation) {
                var totalQuantityByDay = [];
                $('.mainCourantInput[data-prestationid="' + idPrestation + '"]').each(function () {
                    var $input = $(this);
                    var $small = $input.next().next('.prestationPriceCumule');
                    var prixprestation = $small.data('prixprestation');
                    var quantity = parseFloat($input.val());

                    if ($.isNumeric(quantity) && quantity > 0) {
                        totalQuantityByDay.push(quantity);
                    }

                    var cumule = 0;
                    $.each(totalQuantityByDay, function (key, value) {
                        cumule += value;
                    });

                    $small.html(financial(cumule * prixprestation));
                });

                var sum = 0;
                $.each(totalQuantityByDay, function (key, value) {
                    sum += value;
                });
                var totalQuantityDayContainer = $('.quantityTotalDay[data-idprestation="' + idPrestation + '"]');
                totalQuantityDayContainer.find('span').html(sum);
                var prixreelprestation = totalQuantityDayContainer.find('small.prestationTotalPrice').data('prixprestation');
                totalQuantityDayContainer.find('small.prestationTotalPrice').html(financial(sum * prixreelprestation) + '€');
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

            $('input.mainCourantInput').on('input', function (event) {
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
                var reelPrestationPrice = $Input.data('prixreelprestation');
                var prixId = parseFloat($Input.data('prixid'));
                var quantite = $Input.val();

                var dateEnd = new Date();
                dateEnd.setDate(dateEnd.getDate() + 7);

                if (new Date(date).getTime() <= dateEnd.getTime()) {
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
                                reelPrestationPrice: reelPrestationPrice,
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
                                calculateTotalPrestationQuantityPerDay(prestationId);
                            }
                        );
                    }, 300);
                } else {
                    $Input.val('');
                    alert('Il est interdit de saisir une quantité pour une date supérieure à une semaine depuis aujourd\'hui !');
                }
            });
        });
    </script>
<?php else: ?>
    <?= getContainerErrorMsg(trans('Ce site n\'est pas accessible')); ?>
<?php endif; ?>