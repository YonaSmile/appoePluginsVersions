<?php
require_once('../main.php');

//Get Secteur
$Secteur = new \App\Plugin\AgapesHotes\Secteur();
$Secteur->setSlug($_POST['secteur']);

//Get Site
$Site = new \App\Plugin\AgapesHotes\Site();
$Site->setSlug($_POST['site']);

if ($Secteur->showBySlug() && $Site->showBySlug() && $Site->getSecteurId() == $Secteur->getId()):

    //Get Courses
    $Course = new \App\Plugin\AgapesHotes\Courses();

    $startDate = !empty($_POST['startDate']) ? $_POST['startDate'] : date('Y-m-01');

    //Select period
    $start = new \DateTime($startDate);
    $end = new \DateTime($startDate);
    $end->add(new \DateInterval('P1M'));
    $interval = new \DateInterval('P1D');

    $period = new \DatePeriod($start, $interval, $end);

    //Get etablissements
    $Etablissement = new \App\Plugin\AgapesHotes\Etablissement();
    $Etablissement->setSiteId($Site->getId());
    $allEtablissements = $Etablissement->showAllBySite();
    ?>
    <div class="col-12 py-4">
        <h5 class="mb-0"><?= ucfirst(strftime("%B", strtotime($start->format('Y-m-d')))); ?> <?= $start->format('Y'); ?></h5>
        <small class="d-block">Commence le <?= $start->format('d/m/Y'); ?> et se termine
            le <?= $end->format('d/m/Y'); ?></small>
        <button type="button" class="btn btn-sm btn-info printVivreCrueBtn my-2">Imprimer la facture</button>
    </div>
    <div class="table-responsive col-12">
        <table class="table table-striped tableNonEffect fixed-header">
            <thead>
            <tr>
                <th><?= trans('Course'); ?></th>
                <th style="white-space: nowrap;"><?= trans('Prix/unité HT'); ?></th>
                <th style="white-space: nowrap;"><?= trans('Taux tva'); ?></th>
                <th><?= trans('Quantité'); ?></th>
                <th style="white-space: nowrap;"><?= trans('Total HT'); ?></th>
                <?php foreach ($period as $key => $date): ?>
                    <th class="text-center" style="<?= getDayColor($date, $Site->getAlsaceMoselle()); ?>">
                        <?= $date->format('d'); ?></th>
                <?php endforeach; ?>
            </tr>
            </thead>
            <?php foreach ($allEtablissements as $etablissement):

                //Get main courante
                $allVivresCrue = getAllVivreCrueByEtablissementInMonth($etablissement->id, $start->format('m'));

                $Course->setEtablissementId($etablissement->id);
                $allCourses = $Course->showAll();

                if ($allCourses): ?>
                    <tbody data-etablissement="<?= $etablissement->id; ?>">
                    <tr>
                        <th colspan="<?= ($end->format('t') + 5); ?>"><?= $etablissement->nom; ?></th>
                    </tr>
                    <?php foreach ($allCourses as $course):

                        $count = 1;
                        $vivreCrueQuantiteTotalDay = 0; ?>
                        <tr data-idcourse="<?= $course->id ?>" class="vivreCrueTr">
                            <th class="positionRelative courseName" style="vertical-align: middle;">
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
                            <td class="text-center" style="min-width: 130px;">
                                <input type="tel" name="prixUntitHT" value="<?= $vivreCruePrixHtUnit; ?>"
                                       class="text-center" data-idcourse="<?= $course->id ?>"
                                    <?= !empty($vivreCruePrixHtUnit) ? 'readonly' : ''; ?>
                                       style="width: 50px;margin-right: 5px;">€
                            </td>
                            <td class="text-center" style="min-width: 90px;">
                                <input type="tel" name="tauxTva" value="<?= $vivreCrueTauxTva; ?>"
                                       class="text-center" data-idcourse="<?= $course->id ?>"
                                    <?= !empty($vivreCrueTauxTva) ? 'readonly' : ''; ?>
                                       style="width: 50px;margin-right: 5px;">%
                            </td>
                            <td class="text-center tdQuantity" style="min-width: 91px;"
                                data-quantitecourseid="<?= $course->id ?>"></td>
                            <td class="text-center tdTotal" style="0.75em !important; min-width: 88px;"
                                data-totalcourseid="<?= $course->id ?>"></td>
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
                                <td style="padding: 4px !important; min-width: 45px;" data-tdposition="<?= $count; ?>">
                                    <input type="tel" data-idcourse="<?= $course->id; ?>"
                                           data-date="<?= $date->format('Y-m-d'); ?>"
                                           data-day="<?= $date->format('j'); ?>"
                                           data-totalprice="<?= $vivreCrueTotalPrice; ?>"
                                           class="text-center form-control vivreCrueInput sensibleField"
                                           name="<?= $vivreCrueId; ?>"
                                           value="<?= $vivreCrueQuantite; ?>"
                                           style="padding: 5px 0 !important; <?= getDayColor($date, $Site->getAlsaceMoselle()); ?>">
                                </td>

                                <?php $count++;
                            endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                <?php endif;
            endforeach; ?>
        </table>
        <table class="table table-striped tableNonEffect" id="totalHtTable" style="width: 250px;">
            <thead>
            <tr>
                <th>Tva</th>
                <th>Total HT</th>
            </tr>
            </thead>
            <tbody>
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
                    <small>Vivre Crue</small>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 my-2">
                            <div class="row my-2">
                                <div class="col-4 my-2">
                                    <?= \App\Form::text('Émetteur', 'emetteur', 'texte', 'Les Agapes Hôtes, ' . $Site->getNom(), true, 255, 'disabled="disabled" readonly', '', 'basePrint'); ?>
                                </div>
                                <div class="col-4 my-2">
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
                                    <div class="col-3"><strong>Produit</strong>
                                    </div>
                                    <div class="col-3"><strong>Quantité</strong></div>
                                    <div class="col-4"><strong>Prix/unité HT (TVA)</strong></div>
                                    <div class="col-2"><strong>Total HT</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 allFactureProducts"></div>
                    </div>
                    <div class="col-5 text-right float-right totalContainer py-3 px-5"></div>
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

            function prepareTotalFacture() {
                $('.allFactureProducts').html('');
                var totalVariable = 0;

                $('tr.vivreCrueTr').each(function () {
                    var $tr = $(this);
                    var prestationName = $tr.find('th.courseName').text();
                    var prestationPrice = parseFloat($tr.find('input[name="prixUntitHT"]').val());
                    var tva = parseFloat($tr.find('input[name="tauxTva"]').val());
                    var quantityTotal = parseFloat($tr.find('td.tdQuantity').text());
                    if ($.isNumeric(quantityTotal) && quantityTotal > 0) {
                        var total = financial(prestationPrice * quantityTotal);

                        var html = '<div class="row my-1 productFields positionRelative"><div class="col-3 mb-1">' +
                            prestationName + '</div><div class="col-3 mb-1">' +
                            quantityTotal + '</div><div class="col-4 mb-1">' +
                            prestationPrice + '€ (' + financial(tva) + '%)</div><div class="col-2 mb-1">' +
                            total + '€</div></div>';
                        $('.allFactureProducts').append(html);

                        totalVariable += parseFloat(total);
                    }
                });

                var $totalTable = $('#totalHtTable').clone();
                $totalTable.removeClass().addClass('float-right');
                $('tbody', $totalTable).append('<tr><hr><td></td><td>' + financial(totalVariable) + '€' + '</td></tr>');
                $('.totalContainer').html($totalTable);
            }


            $('.printVivreCrueBtn').on('click', function () {
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

            $('input[name="prixUntitHT"], input[name="tauxTva"]').on('blur', function () {
                $(this).val(financial($(this).val()));
            });

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

            function calculeAllTvaTypes() {

                var tvaTypes = {};
                $('input[name="tauxTva"]').each(function () {

                    var $input = $(this);
                    var idCourse = $input.data('idcourse');
                    var tauxTva = $input.val();
                    var $parent = $input.closest('tr');
                    var totalHT = parseFloat($parent.find('td.tdTotal').text());

                    if ($.isNumeric(tauxTva) && tauxTva > 0) {

                        if (!(tauxTva in tvaTypes)) {
                            tvaTypes[tauxTva] = totalHT;
                        } else {
                            tvaTypes[tauxTva] = tvaTypes[tauxTva] + totalHT;
                        }
                    }
                });

                if (tvaTypes.length !== 0) {
                    $('table#totalHtTable tbody').html('');
                    $.each(tvaTypes, function (tva, total) {
                        $('table#totalHtTable tbody').append('<tr><td>' + tva + '%</td><td>' + total + '€</td></tr>');
                    });
                }
            }

            function calculateTotalPriceByCourse(courseId) {

                var totalQuantity = parseFloat($('td[data-quantitecourseid="' + courseId + '"]').text());
                var unitPriceHT = $('input[name="prixUntitHT"][data-idcourse="' + courseId + '"]').val();
                $('td[data-totalcourseid="' + courseId + '"]').html(financial(totalQuantity * unitPriceHT) + '€');
            }

            $('input.vivreCrueInput').each(function () {
                calculateTotalQuantityByCourse($(this).data('idcourse'));
            });

            calculeAllTvaTypes();

            var delay = (function () {
                var timer = 0;
                return function (callback, ms) {
                    clearTimeout(timer);
                    timer = setTimeout(callback, ms);
                };
            })();

            $('input.vivreCrueInput').on('input', function (event) {
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
                                            calculeAllTvaTypes();
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
    <?= getContainerErrorMsg(trans('Ce site n\'est pas accessible')); ?>
<?php endif; ?>