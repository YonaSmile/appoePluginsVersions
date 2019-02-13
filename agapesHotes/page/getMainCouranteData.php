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
    <div class="col-12">
        <div class="row py-2">
            <div class="col-12 col-lg-11 my-2">
                <h5 class="mb-0"><?= ucfirst(strftime("%B", strtotime($start->format('Y-m-d')))); ?> <?= $start->format('Y'); ?></h5>
                <small class="d-block">Commence le <?= $start->format('d/m/Y'); ?> et se termine
                    le <?= $start->format('t/m/Y'); ?></small>
            </div>
            <div class="col-12 col-lg-1 my-2">
                <button type="button" class="printMainCourante btn bgColorPrimary">
                    <i class="fas fa-print"></i> Imprimer
                </button>
            </div>
        </div>
    </div>
    <div class="col-12 table-responsive">
        <table id="mainCouranteTable" class="table table-striped tableNonEffect fixed-header">
            <thead style="z-index: 2">
            <tr style="text-align: center;">
                <th><?= trans('Prestation'); ?></th>
                <?php foreach ($period as $key => $date): ?>
                    <th style="<?= getDayColor($date, $Site->getAlsaceMoselle()); ?>">
                        <?= $date->format('d'); ?></th>
                <?php endforeach; ?>
                <th><i class="fas fa-balance-scale"></i></th>
                <th><?= trans('Prestation'); ?></th>
            </tr>
            </thead>
            <?php foreach ($allEtablissements as $etablissement):

                //Get price by prestation
                $allPrestationsPrix = getAllPrestationsPriceByEtablissement($etablissement->id);

                //Get main courante
                $allMainCourante = getAllMainCouranteByEtablissementInMonth($etablissement->id, $start->format('m'));

                $Prestation->setEtablissementId($etablissement->id);
                $allPrestations = $Prestation->showAll();

                if ($allPrestations): ?>
                    <tbody data-etablissement="<?= $etablissement->id; ?>">
                    <tr>
                        <th colspan="<?= $start->format('t') + 3; ?>">
                            <span style="font-size: 2em;font-weight: 100 !important;">
                                <?= $etablissement->nom; ?>
                            </span>
                            <span class="float-right">
                            <button type="button"
                                    class="btn btn-sm btn-outline-light printFirstHalfMonthMainCouranteBtn my-2"
                                    data-etablissementid="<?= $etablissement->id; ?>"
                                    data-etablissementname="<?= $etablissement->nom; ?>"
                                    title="Du 1 au 15 <?= ucfirst(strftime("%B", strtotime($start->format('Y-m-d')))); ?> <?= $start->format('Y'); ?>">
                                Édition du 1 - 15
                            </button>
                            <button type="button"
                                    class="btn btn-sm btn-outline-light printLastHalfMonthMainCouranteBtn my-2"
                                    data-etablissementid="<?= $etablissement->id; ?>"
                                    data-etablissementname="<?= $etablissement->nom; ?>"
                                    title="Du 16 au <?= $start->format('t'); ?> <?= ucfirst(strftime("%B", strtotime($start->format('Y-m-d')))); ?> <?= $start->format('Y'); ?>">
                                Édition du 16 - <?= $start->format('t'); ?></button>
                            <button type="button" class="btn btn-sm btn-outline-light printMainCouranteBtn my-2"
                                    data-etablissementid="<?= $etablissement->id; ?>"
                                    data-etablissementname="<?= $etablissement->nom; ?>">
                                Imprimer la demande de facturation
                            </button>
                        </span></th>
                    </tr>
                    <?php foreach ($allPrestations as $prestation):
                        $count = 1;
                        $mainCouranteQuantiteTotalDay = 0; ?>
                        <tr data-idprestation="<?= $prestation->id ?>" class="mainCourantTr"
                            data-etablissementid="<?= $etablissement->id; ?>"
                            data-principal="<?= $etablissement->principal ? 'principal' : ''; ?>">
                            <th class="positionRelative" data-name="prestationName"
                                style="vertical-align: middle; min-width: 150px; z-index: 1">
                                <span><?= $prestation->nom; ?></span>
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
                                $mainCourantQuantite = 0;
                                if (array_key_exists($prestation->id, $allMainCourante) && array_key_exists($date->format('Y-m-d'), $allMainCourante[$prestation->id])) {
                                    $MainCourante = $allMainCourante[$prestation->id][$date->format('Y-m-d')];
                                    $mainCourantId = $MainCourante->id;
                                    $mainCourantQuantite = $MainCourante->quantite;
                                }
                                if ($prixReel > 0) {
                                    $mainCouranteQuantiteTotalDay += $mainCourantQuantite;
                                }
                                ?>

                                <td style="padding: 4px !important; min-width: 45px;"
                                    data-day="<?= $date->format('j'); ?>"
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
                                           value="<?= is_numeric($mainCourantQuantite) && $mainCourantQuantite > 0 ? $mainCourantQuantite : ''; ?>"
                                           style="padding: 5px 0 !important; <?= getDayColor($date, $Site->getAlsaceMoselle()); ?>">
                                    <small class="d-block text-center prestationPrice"
                                           data-day="<?= $date->format('j'); ?>"
                                           data-prixprestation="<?= $prixReel; ?>"
                                           data-etablissement="<?= $etablissement->id; ?>"><?= financial($prixReel * $mainCourantQuantite); ?>
                                    </small>
                                    <small class="d-block text-center prestationPriceCumule"
                                           style="font-size: 0.65em;"
                                           data-day="<?= $date->format('j'); ?>"
                                           data-prixprestation="<?= $prixReel; ?>"
                                           data-etablissement="<?= $etablissement->id; ?>"><?= financial($prixReel * $mainCouranteQuantiteTotalDay); ?>
                                    </small>
                                </td>
                                <?php $count++;
                            endforeach; ?>
                            <td class="quantityTotalDay text-center" data-idprestation="<?= $prestation->id ?>"
                                style="padding: 4px !important; min-width: 45px;">
                                <span><?= $mainCouranteQuantiteTotalDay; ?></span>
                                <small class="d-block text-center prestationTotalPrice" style="font-size: 0.7em;"
                                       data-prixprestation="<?= $prixReel; ?>"
                                       data-etablissement="<?= $etablissement->id; ?>">
                                    <?= financial($prixReel * $mainCouranteQuantiteTotalDay); ?>
                                </small>
                            </td>
                            <th><?= $prestation->nom; ?></th>
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
            <!--<tr>
                <th>CA Semaine</th>
                <td class="caPrestationWeek"></td>
            </tr>
            <tr>
                <th>CA Jour</th>
                <td class="caPrestationDay"></td>
            </tr>-->
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
    <script type="text/javascript" src="<?= AGAPESHOTES_URL; ?>js/footer.js"></script>
    <script type="text/javascript" src="/app/js/printThis.js"></script>
    <script>
        $(document).ready(function () {

            adaptResponsiveTable();
            $(window).resize(function () {
                adaptResponsiveTable();
            });
            $('.sidebarCollapse').on('click', function () {
                setTimeout(function () {
                    adaptResponsiveTable();
                }, 300);
            });

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

            function prepareFirstMonthHalfFacture(etablissementid) {
                $('.allFactureProducts').html('');
                var totalVariable = 0;
                var totalQuantite = 0;
                var totalPrestation = 0;
                var facture = {};

                $('tr.mainCourantTr[data-etablissementid="' + etablissementid + '"] td.mainCouranteTd').each(function () {
                    var $td = $(this);
                    var $tr = $td.closest('tr');
                    var prestationName = $tr.find('th[data-name="prestationName"] span').text();
                    var prestationPrice = parseReelFloat($tr.find('small.prestationReelPrice').text());

                    if (!(prestationName in facture)) {
                        facture[prestationName] = {price: prestationPrice, quantite: 0};
                        totalQuantite = 0;
                    }

                    if ($td.data('day') < 16) {
                        var $input = $td.find('input.mainCourantInput');
                        var quantite = parseReelFloat($input.val());

                        if ($.isNumeric(quantite) && quantite > 0) {

                            totalQuantite += parseReelFloat(quantite);
                            facture[prestationName].quantite = totalQuantite;
                        }
                    }
                });

                if (facture.length !== 0) {
                    $.each(facture, function (prestation, data) {

                        if (data.quantite > 0) {
                            totalVariable += parseReelFloat(data.price * data.quantite);

                            var html = '<div class="row my-1 productFields positionRelative"><div class="col-4 mb-1">' +
                                prestation + '</div><div class="col-3 mb-1">' +
                                data.quantite + '</div><div class="col-3 mb-1">' +
                                data.price + '€</div><div class="col-2 mb-1">' +
                                financial(data.price * data.quantite) + '€</div></div>';
                            $('.allFactureProducts').append(html);
                        }
                    });
                }
                $('#totalVariableContainer').hide();
                $('#totalFraisFixesContainer').hide();
                $('.totalContainer .totalCaHt').html(financial(totalVariable) + '€');
            }

            function prepareLastMonthHalfFacture(etablissementid) {
                $('.allFactureProducts').html('');
                var totalVariable = 0;
                var totalQuantite = 0;
                var totalPrestation = 0;
                var facture = {};

                $('tr.mainCourantTr[data-etablissementid="' + etablissementid + '"] td.mainCouranteTd').each(function () {
                    var $td = $(this);
                    var $tr = $td.closest('tr');
                    var prestationName = $tr.find('th[data-name="prestationName"] span').text();
                    var prestationPrice = parseReelFloat($tr.find('small.prestationReelPrice').text());

                    if (!(prestationName in facture)) {
                        facture[prestationName] = {price: prestationPrice, quantite: 0};
                        totalQuantite = 0;
                    }

                    if ($td.data('day') > 15) {
                        var $input = $td.find('input.mainCourantInput');
                        var quantite = parseReelFloat($input.val());

                        if ($.isNumeric(quantite) && quantite > 0) {

                            totalQuantite += parseReelFloat(quantite);
                            facture[prestationName].quantite = totalQuantite;
                        }
                    }
                });

                if (facture.length !== 0) {
                    $.each(facture, function (prestation, data) {

                        if (data.quantite > 0) {
                            totalVariable += parseReelFloat(data.price * data.quantite);

                            var html = '<div class="row my-1 productFields positionRelative"><div class="col-4 mb-1">' +
                                prestation + '</div><div class="col-3 mb-1">' +
                                data.quantite + '</div><div class="col-3 mb-1">' +
                                data.price + '€</div><div class="col-2 mb-1">' +
                                financial(data.price * data.quantite) + '€</div></div>';
                            $('.allFactureProducts').append(html);
                        }
                    });
                }
                $('#totalVariableContainer').hide();
                $('#totalFraisFixesContainer').hide();
                $('.totalContainer .totalCaHt').html(financial(totalVariable) + '€');
            }

            function prepareTotalFacture(etablissementid) {
                $('.allFactureProducts').html('');
                var totalVariable = 0;
                var principal = '';

                $('tr.mainCourantTr[data-etablissementid="' + etablissementid + '"]').each(function () {
                    var $tr = $(this);
                    var prestationName = $tr.find('th[data-name="prestationName"] span').text();
                    var prestationPrice = parseReelFloat($tr.find('small.prestationReelPrice').text());
                    var quantityTotalDay = parseReelFloat($tr.find('td.quantityTotalDay span').text());
                    if (quantityTotalDay > 0) {
                        var total = financial(prestationPrice * quantityTotalDay);
                        principal = $tr.data('principal');

                        var html = '<div class="row my-1 productFields positionRelative"><div class="col-4 mb-1">' +
                            prestationName + '</div><div class="col-3 mb-1">' +
                            quantityTotalDay + '</div><div class="col-3 mb-1">' +
                            prestationPrice + '€</div><div class="col-2 mb-1">' +
                            total + '€</div></div>';
                        $('.allFactureProducts').append(html);

                        totalVariable += parseReelFloat(total);
                    }
                });
                $('#totalVariableContainer').show();

                var totalFraisFixes = 0;
                if (principal === 'principal') {
                    $('#totalFraisFixesContainer').show();
                    totalFraisFixes = parseReelFloat($('.totalContainer .totalFraisFixes').text());
                } else {
                    $('#totalFraisFixesContainer').hide();
                }

                $('.totalContainer .totalVariable').html(financial(totalVariable) + '€');

                $('.totalContainer .totalCaHt').html(financial(totalVariable + totalFraisFixes) + '€');
            }
            function printMainCourante() {

                var $html = $('#mainCouranteTable');
                var $table = $html.clone();

                $('button', $table).remove();

                $('input.mainCourantInput', $table).each(function (i) {
                    var val = $.isNumeric($(this).val()) ? $(this).val() : 0;

                    $(this).parent('td').prepend(val+'<br>');
                    $(this).remove();
                });

                $('.prestationReelPrice', $table).removeAttr('style').prepend('<br>');
                $('.prestationPrice', $table).append('<br>');

                var data = {
                    titre: 'Main Courante',
                    table_lines: escapeHtml($table.prop('outerHTML')),
                    pdfTemplateOrientation: 'L',
                    pdfTemplateFilename: 'table',
                    pdfOutputName: 'MainCourante'
                };

                pdfSend(data);
            }

            $('.printMainCourante').on('click', function () {
                printMainCourante();
            });

            $('.printFirstHalfMonthMainCouranteBtn').on('click', function () {
                var etablissementid = $(this).data('etablissementid');
                var etablissementname = $(this).data('etablissementname');
                prepareFirstMonthHalfFacture(etablissementid);
                $('.factureDate').hide();
                $('.facturePeriod').show();
                $('#signatureFacture').show();
                $('#modalFactureMainCouranteTitle').html('Edition Main Courante');
                $('input#client_name').removeClass('is-invalid').val(etablissementname);
                $('input#period').val($(this).attr('title'));
                $('#modalFactureMainCourante').modal('show');
            });

            $('.printLastHalfMonthMainCouranteBtn').on('click', function () {
                var etablissementid = $(this).data('etablissementid');
                var etablissementname = $(this).data('etablissementname');
                prepareLastMonthHalfFacture(etablissementid);
                $('.factureDate').hide();
                $('.facturePeriod').show();
                $('#signatureFacture').show();
                $('#modalFactureMainCouranteTitle').html('Edition Main Courante');
                $('input#client_name').removeClass('is-invalid').val(etablissementname);
                $('input#period').val($(this).attr('title'));
                $('#modalFactureMainCourante').modal('show');
            });

            $('.printMainCouranteBtn').on('click', function () {
                var etablissementid = $(this).data('etablissementid');
                var etablissementname = $(this).data('etablissementname');
                $('.facturePeriod').hide();
                $('.factureDate').show();
                $('#signatureFacture').hide();
                $('#modalFactureMainCouranteTitle').html('Demande de Facturation');
                prepareTotalFacture(etablissementid);
                $('input#client_name').removeClass('is-invalid').val(etablissementname);
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
                    var prixprestation = $input.data('prixreelprestation');
                    var quantity = parseReelFloat($input.val());

                    if ($.isNumeric(quantity) && quantity > 0
                        && $.isNumeric(prixprestation) && prixprestation > 0) {
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
                    var totalDay = parseReelFloat($info.text());

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
                    var totalDay = parseReelFloat($totalDay.text());

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

                var totalMonth = parseReelFloat($('.caPrestationMonth').text());
                var averageDay = (totalMonth / parseFloat(<?= date('t'); ?>));

                $('.caPrestationDay').html(financial(averageDay) + '€');

                calculateAveragePrestationPricePerWeek();
            }

            function calculateAveragePrestationPricePerWeek() {

                var totalDays = parseReelFloat($('.caPrestationDay').text());
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

            var inputValue = '';
            $('input.mainCourantInput').on('focusin', function (event) {
                inputValue = $(this).val();
            });

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
                var prixId = parseReelFloat($Input.data('prixid'));
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

                                    var prestationPrice = parseReelFloat($InputInfo.data('prixprestation'));
                                    $InputInfo.html(financial(quantite * prestationPrice));
                                    calculateTotalPrestationPricePerDay();
                                    calculateTotalPrestationQuantityPerDay(prestationId);
                                } else {
                                    $Input.val(inputValue);
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
            });
        });
    </script>
<?php else: ?>
    <?= getContainerErrorMsg(trans('Ce site n\'est pas accessible')); ?>
<?php endif; ?>