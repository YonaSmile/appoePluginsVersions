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

    //Get all Planning
    $allPlanning = getAllPlanningBySite($Site->getId());

    //Get all Pti
    $allPti = getAllPtiBySite($Site->getId());

    //Get Employe
    $Employe = new \App\Plugin\AgapesHotes\Employe();
    $allEmployes = extractFromObjArr($Employe->showByType(), 'id');

    $allContratEmployes = getAllEmployeHasContratInSite($Site->getId());

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
            le <?= $start->format('t/m/Y'); ?></small>
        <button type="button" class="btn btn-link btn-sm printPlanning">Imprimer le Planning</button>
    </div>
    <div class="col-12">
        <table id="planningTable" class="table table-striped tableNonEffect">
            <thead>
            <tr>
                <th colspan="2"><?= trans('Employé'); ?></th>
                <?php foreach ($period as $key => $date): ?>
                    <th class="text-center"
                        style="<?= getDayColor($date, $Site->getAlsaceMoselle()); ?>"><?= $date->format('d'); ?></th>
                <?php endforeach; ?>
                <th><?= trans('Employé'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($allContratEmployes as $employeId => $contrat):

                $count = 1;
                if (array_key_exists($employeId, $allPti)):
                    $allPtiDates = array_keys($allPti[$employeId]);
                    usort($allPtiDates, 'reelDatesSortDESC');
                    ?>
                    <tr data-idemploye="<?= $employeId; ?>">
                        <th class="positionRelative"><?= $allEmployes[$employeId]->name . ' ' . $allEmployes[$employeId]->firstName; ?>
                            <small class="totalContainer"
                                   style="position: absolute;top: 0; right: 3px;font-size: 0.7em;"></small>
                        </th>
                        <td>
                            <small class="heightThirdTitle">Réels</small>
                            <small class="heightThirdTitle">Pti</small>
                            <small class="heightThirdTitle">Horaires</small>
                        </td>
                        <?php foreach ($period as $key => $date):

                            $inputCase = '';
                            $Planning = '';
                            if (array_key_exists($employeId, $allPlanning) && array_key_exists($date->format('Y-m-d'), $allPlanning[$employeId])) {
                                $Planning = $allPlanning[$employeId][$date->format('Y-m-d')];
                                $inputCase = empty($Planning->reelHours) || $Planning->reelHours == '0.00' ? $Planning->absenceReason : $Planning->reelHours;
                            }

                            $allPtiDetails = array();
                            $dayInCycle = 0;

                            foreach ($allPtiDates as $ptiDate) {
                                if ($ptiDate <= $date->format('Y-m-d')) {
                                    $dateDebut = $allPti[$employeId][$ptiDate];

                                    if (property_exists($dateDebut, 'details')) {

                                        $allPtiDetails = extractFromObjArr($dateDebut->details, 'numeroJour');
                                        $dayInCycle = getDayInCycle(new \DateTime($ptiDate), $dateDebut->nbWeeksInCycle, $date);
                                    }
                                    break;
                                }
                            }
                            ?>
                            <td style="padding: 4px 0 !important; vertical-align: top !important;"
                                data-tdposition="<?= $count; ?>">
                                <input class="text-center form-control updatePlanning inputUpdatePlanning heightThird"
                                       name="<?= !empty($Planning->id) ? $Planning->id : ''; ?>" type="text"
                                       maxlength="10" list="absenceReasonList" autocomplete="off"
                                       style="padding: 5px 0 !important; min-width: 45px; <?= getDayColor($date, $Site->getAlsaceMoselle()); ?>"
                                       data-date="<?= $date->format('Y-m-d'); ?>"
                                       data-employeid="<?= $employeId; ?>"
                                       value="<?= $inputCase; ?>">
                                <small class="text-center heightThird">
                                    <?= $dayInCycle > 0 ? $allPtiDetails[$dayInCycle]->nbHeures : ''; ?></small>
                                <small class="text-center horairesPlanning heightThird p-1"
                                       style="min-width: 75px;"><?= $dayInCycle > 0 ? $allPtiDetails[$dayInCycle]->horaires : ''; ?></small>
                                <datalist id="absenceReasonList">
                                    <option value="M" label="Maladie">M</option>
                                    <option value="AT" label="Accident du Travail">AT</option>
                                    <option value="MP" label="Maladie Professionnelle">MP</option>
                                    <option value="Ca" label="Carence maladie">Ca</option>
                                    <option value="CP" label="Congé Payé">CP</option>
                                    <option value="ANP" label="Absence Non Payée ">ANP</option>
                                    <option value="CSS" label="Congés Sans Solde">CSS</option>
                                    <option value="JFC" label="Jour Férié Chomé payé normalement">JFC
                                    </option>
                                    <option value="MàP" label="Mise à Pied">MàP</option>
                                    <option value="CIF" label="Congé format°">CIF</option>
                                    <option value="AAP" label="Absence Autorisée Payée">AAP</option>
                                    <option value="MAT" label="Maternité">MAT</option>
                                    <option value="PAT" label="Paternité">PAT</option>
                                    <option value="PAR" label="Parental">PAR</option>
                                    <option value="EF"
                                            label="Evènement Familial (mariage, décès…cf. conv. Coll.)">
                                        EF
                                    </option>
                                    <option value="JS" label="Journée Solidarité">JS</option>
                                    <option value="CFA" label="jour école apprenti(e)s">CFA</option>
                                    <option value="Peff" label="Préavis effectué">Peff</option>
                                    <option value="Dél" label="délégation">Dél</option>
                                    <option value="R°DP" label="réunion DP au siège">R°DP</option>
                                </datalist>
                                <span class="otherData text-center" style="display: block;"><span>
                            </td>
                            <?php $count++;
                        endforeach; ?>
                        <th><?= $allEmployes[$employeId]->name . ' ' . $allEmployes[$employeId]->firstName; ?></th>
                    </tr>
                <?php endif;
            endforeach; ?>
            </tbody>
            <i type="button" id="tableRightCursor" class="fas fa-angle-right"></i>
            <i type="button" id="tableLeftCursor" class="fas fa-angle-left"></i>
        </table>
    </div>
    <!--<hr class="w-100 d-block mx-5 my-4">
    <div class="table-responsive col-12" id="noteFraisTable">
        <table class="table table-striped tableNonEffect">
            <thead>
            <tr>
                <th><?= trans('Employé'); ?></th>
                <th style="width: 130px;">Nb de repas</th>
                <th>Prime d'objectifs d'heures</th>
                <th>Prime exceptionnelle</th>
                <th style="width: 100px;">Acompte</th>
                <th>Heures supp. à reporter</th>
                <th>Jours fériés à reporter</th>
                <th>Commentaires</th>
            </tr>
            </thead>
            <tbody>
            <?php
    $PlanningPlus = new \App\Plugin\AgapesHotes\PlanningPlus();
    $PlanningPlus->setSiteId($Site->getId());
    $PlanningPlus->setYear($start->format('Y'));
    $PlanningPlus->setMonth($start->format('m'));
    $allPlanningPlus = extractFromObjArr($PlanningPlus->showAllByDate(), 'employe_id');

    foreach ($allContratEmployes as $employeId => $contrat):

        $idPlanningPlus = !empty($allPlanningPlus[$employeId]->id) ? $allPlanningPlus[$employeId]->id : '';
        $nbRepas = !empty($allPlanningPlus[$employeId]->nbRepas) ? $allPlanningPlus[$employeId]->nbRepas : '';
        $primeObjectif = !empty($allPlanningPlus[$employeId]->primeObjectif) ? $allPlanningPlus[$employeId]->primeObjectif : '';
        $primeExept = !empty($allPlanningPlus[$employeId]->primeExept) ? $allPlanningPlus[$employeId]->primeExept : '';
        $accompte = !empty($allPlanningPlus[$employeId]->accompte) ? $allPlanningPlus[$employeId]->accompte : '';
        $nbHeurePlus = !empty($allPlanningPlus[$employeId]->nbHeurePlus) ? $allPlanningPlus[$employeId]->nbHeurePlus : '';
        $nbJoursFeries = !empty($allPlanningPlus[$employeId]->nbJoursFeries) ? $allPlanningPlus[$employeId]->nbJoursFeries : '';
        $commentaires = !empty($allPlanningPlus[$employeId]->commentaires) ? $allPlanningPlus[$employeId]->commentaires : '';
        ?>
                <tr data-year="<?= $start->format('Y'); ?>"
                    data-month="<?= $start->format('m'); ?>"
                    data-idplanningplus="<?= $idPlanningPlus; ?>"
                    data-employeid="<?= $employeId; ?>"
                    data-siteid="<?= $Site->getId(); ?>">
                    <td><?= $allEmployes[$employeId]->name . ' ' . $allEmployes[$employeId]->firstName; ?></td>
                    <td>
                        <input class="text-center form-control inputPlanningPlus"
                               name="nbRepas" type="tel" autocomplete="off"
                               style="padding: 5px 0 !important;"
                               value="<?= $nbRepas; ?>">
                    </td>
                    <td>
                        <input class="text-center form-control inputPlanningPlus"
                               name="primeObjectif" type="text" autocomplete="off"
                               style="padding: 5px 0 !important;"
                               value="<?= $primeObjectif; ?>">
                    </td>
                    <td>
                        <input class="text-center form-control inputPlanningPlus"
                               name="primeExept" type="text" autocomplete="off"
                               style="padding: 5px 0 !important;"
                               value="<?= $primeExept; ?>">
                    </td>
                    <td>
                        <input class="text-center form-control inputPlanningPlus"
                               name="accompte" type="text" autocomplete="off"
                               style="padding: 5px 0 !important;"
                               value="<?= $accompte; ?>">
                    </td>
                    <td>
                        <input class="text-center form-control inputPlanningPlus"
                               name="nbHeurePlus" type="text" autocomplete="off"
                               style="padding: 5px 0 !important;"
                               value="<?= $nbHeurePlus; ?>">
                    </td>
                    <td>
                        <input class="text-center form-control inputPlanningPlus"
                               name="nbJoursFeries" type="text" autocomplete="off"
                               style="padding: 5px 0 !important;"
                               value="<?= $nbJoursFeries; ?>">
                    </td>
                    <td>
                        <input class="text-center form-control inputPlanningPlus"
                               name="commentaires" type="text" autocomplete="off"
                               style="padding: 5px 0 !important;"
                               value="<?= $commentaires; ?>">
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>-->
    <script type="text/javascript" src="<?= AGAPESHOTES_URL; ?>js/footer.js"></script>
    <script type="text/javascript" src="/app/js/printThis.js"></script>
    <script>
        $(document).ready(function () {

            $('button.printPlanning').on('click', function () {
                $('#mainContent').printThis({
                    loadCSS: "<?= AGAPESHOTES_URL; ?>css/print.css",
                });
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

            $('#tableRightCursor').on('click', function () {
                $('#tableRightCursor').fadeOut();
                $('html, body').animate({scrollLeft: 1400}, 800);
                var sideBarPosition = $('#sidebar').css('margin-left') == 0 ? ($('#sidebar').offset().left + $('#sidebar').width()) : ($('#sidebar').offset().left + 250);
                $('#tableLeftCursor').css({
                    'left': sideBarPosition
                }).fadeIn();
            });


            $('#tableLeftCursor').on('click', function () {
                $('#tableLeftCursor').fadeOut();
                $('html, body').animate({scrollLeft: -1400}, 800);
                $('#tableRightCursor').fadeIn();
            });

            $('.sidebarCollapse').on('click', function () {
                if ($('#sidebar').hasClass('active')) {
                    $('#tableLeftCursor').css({
                        'left': 0
                    });
                } else {
                    $('#tableLeftCursor').css({
                        'left': $('#sidebar').width()
                    });
                }
            });

            $('body').on('click', '.copyTableData', function () {
                $(this).html('Compléter le tableau').removeClass('copyTableData').addClass('completTable');
                $('.inputUpdatePlanning').each(function () {
                    var $input = $(this);
                    var $parent = $input.parent('td');
                    var data = $input.val() == '' ? 0 : $input.val().replace(/\./g, ',');
                    $parent.find('small').hide();
                    $input.hide();
                    $parent.find('span.otherData').html(data).show();
                });

                $('.inputPlanningPlus').each(function () {
                    var $input = $(this);
                    var $parent = $input.parent('td');
                    var data = $input.val() == '' ? 0 : $input.val().replace(/\./g, ',');
                    $input.hide();
                    $parent.append('<span style="display:block;text-align:center;">' + data + '</span>');
                });
            });

            $('body').on('click', '.completTable', function () {
                $(this).html('Copier le tableau').removeClass('completTable').addClass('copyTableData');
                $('.inputUpdatePlanning').each(function () {
                    var $input = $(this);
                    var $parent = $input.parent('td');
                    $parent.find('span.otherData').html('').hide();
                    $input.show();
                    $parent.find('small').not('.horairesPlanning').show();

                });

                $('.inputPlanningPlus').each(function () {
                    var $input = $(this);
                    var $parent = $input.parent('td');
                    $parent.find('span').remove();
                    $input.show();
                });
            });

            $('[data-toggle="tooltip"]').tooltip({
                trigger: 'focus',
                placement: 'right'
            });

            function calculateTotalReelHours() {
                var totalArr = [];
                $('input.updatePlanning').each(function () {
                    var input = $(this);
                    var hours = parseFloat(input.val());
                    if ($.isNumeric(hours)) {
                        var idEmploye = input.data('employeid');
                        if (typeof totalArr[idEmploye] === 'undefined') {
                            totalArr[idEmploye] = [];
                        }

                        totalArr[idEmploye].push(hours)
                    }
                });

                $.each(totalArr, function (key, value) {
                    var sum = 0;
                    $.each(value, function (i, val) {
                        sum += val;
                    });
                    $('tr[data-idemploye="' + key + '"] th .totalContainer').html(sum);
                });
            }

            calculateTotalReelHours();

            function disabledAllFields(inputExlude) {
                $('input.updatePlanning').not(inputExlude).attr('disabled', 'disabled');
            }

            function activateAllFields() {
                $('input.updatePlanning').attr('disabled', false);
            }

            var delay = (function () {
                var timer = 0;
                return function (callback, ms) {
                    clearTimeout(timer);
                    timer = setTimeout(callback, ms);
                };
            })();

            $('input.updatePlanning').on('input', function (event) {
                event.preventDefault();

                var $Input = $(this);
                $('input.updatePlanning').removeClass('successInput');

                if (!$Input.val().length > 0) {
                    $Input.val('');
                }

                var idPlanning = $Input.attr('name');
                var siteId = '<?= $Site->getId(); ?>';
                var employeId = $Input.data('employeid');
                var date = $Input.data('date');
                var absenceReason = $Input.val();

                disabledAllFields($Input);
                delay(function () {
                    busyApp();

                    $.post(
                        '<?= AGAPESHOTES_URL . 'process/ajaxPlanningProcess.php'; ?>',
                        {
                            UPDATEPLANNING: 'OK',
                            siteId: siteId,
                            employeId: employeId,
                            date: date,
                            absenceReason: absenceReason,
                            id: idPlanning
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
                            calculateTotalReelHours();
                        }
                    );
                }, 300);
            });

            $('.inputPlanningPlus').on('input', function (event) {
                event.preventDefault();

                var $Input = $(this);
                var $Parent = $Input.closest('tr');

                $('input.inputPlanningPlus').removeClass('successInput');

                var idPlanningPlus = $Parent.data('idplanningplus');
                var siteId = $Parent.data('siteid');
                var employeId = $Parent.data('employeid');
                var year = $Parent.data('year');
                var month = $Parent.data('month');
                var nbRepas = $Parent.find('input[name="nbRepas"]').val();
                var primeObjectif = $Parent.find('input[name="primeObjectif"]').val();
                var primeExept = $Parent.find('input[name="primeExept"]').val();
                var accompte = $Parent.find('input[name="accompte"]').val();
                var nbHeurePlus = $Parent.find('input[name="nbHeurePlus"]').val();
                var nbJoursFeries = $Parent.find('input[name="nbJoursFeries"]').val();
                var commentaires = $Parent.find('input[name="commentaires"]').val();

                disabledAllFields($Input);
                delay(function () {
                    busyApp();

                    $.post(
                        '<?= AGAPESHOTES_URL . 'process/ajaxPlanningProcess.php'; ?>',
                        {
                            UPDATEPLANNINGPLUS: 'OK',
                            siteId: siteId,
                            employeId: employeId,
                            year: year,
                            month: month,
                            nbRepas: nbRepas,
                            primeObjectif: primeObjectif,
                            primeExept: primeExept,
                            accompte: accompte,
                            nbHeurePlus: nbHeurePlus,
                            nbJoursFeries: nbJoursFeries,
                            commentaires: commentaires,
                            id: idPlanningPlus
                        },
                        function (data) {
                            if (data && $.isNumeric(data)) {
                                $Parent.attr('idplanningplus', data);
                                $Input.addClass('successInput');
                            } else {
                                alert(data);
                            }
                            availableApp();
                            activateAllFields();
                        }
                    );
                }, 300);
            });
        });
    </script>
<?php else: ?>
    <?= getContainerErrorMsg(trans('Ce site n\'est pas accessible')); ?>
<?php endif; ?>
