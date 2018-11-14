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

        //Get all Planning
        $allPlanning = getAllPlanningBySite($Site->getId());

        //Get all Pti
        $allPti = getAllPtiBySite($Site->getId());

        //Get Employe
        $Employe = new \App\Plugin\AgapesHotes\Employe();
        $allEmployes = extractFromObjArr($Employe->showByType(), 'id');

        $allContratEmployes = getAllEmployeHasContratInSite($Site->getId());

        //Select period
        $start = new \DateTime(date('Y-m-01'));
        $end = new \DateTime(date('Y-m-t'));
        $end = $end->modify('+1 day');
        $interval = new \DateInterval('P1D');

        $period = new \DatePeriod($start, $interval, $end);
        ?>
        <div class="row">
            <div class="table-responsive col-12">
                <table id="pagesTable" class="table table-striped tableNonEffect">
                    <thead>
                    <tr>
                        <th><?= trans('Employé'); ?></th>
                        <?php foreach ($period as $key => $date): ?>
                            <th style="<?= $date->format('d') == date('d') ? 'background:#4fb99f;color:#fff;' : ''; ?> <?= $date->format('N') == 7 ? 'background:#f2b134;color:#4b5b68;' : ''; ?>"><?= $date->format('d'); ?></th>
                        <?php endforeach; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($allContratEmployes as $employeId => $contrat):
                        if (array_key_exists($employeId, $allPti)):
                            $allPtiDates = array_keys($allPti[$employeId]);
                            usort($allPtiDates, 'reelDatesSortDESC');
                            ?>
                            <tr data-idemploye="<?= $employeId; ?>">
                                <th class="positionRelative"><?= $allEmployes[$employeId]->name . ' ' . $allEmployes[$employeId]->firstName; ?>
                                    <small class="totalContainer"
                                           style="position: absolute;top: 0; right: 3px;font-size: 0.7em;"></small>
                                </th>
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
                                    <td style="padding: 4px 0 !important;">
                                        <input class="text-center form-control updatePlanning inputUpdatePlanning"
                                               name="<?= !empty($Planning->id) ? $Planning->id : ''; ?>" type="text"
                                               maxlength="10" list="absenceReasonList" autocomplete="off"
                                               style="padding: 5px 0 !important; <?= $date->format('d') == date('d') ? 'background:#4fb99f;color:#fff;' : ''; ?>
                                               <?= isferie($date->format('Y-m-d'), true) ? 'border: 1px solid #d8886f !important;' : ''; ?>"
                                               data-date="<?= $date->format('Y-m-d'); ?>"
                                               data-employeid="<?= $employeId; ?>"
                                               value="<?= $inputCase; ?>">
                                        <small class="text-center d-block"><?= $dayInCycle > 0 ? $allPtiDetails[$dayInCycle]->nbHeures : ''; ?></small>
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
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endif;
                    endforeach; ?>

                    </tbody>
                </table>
            </div>

        </div>


        <script>
            $(document).ready(function () {

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

                $('[data-toggle="tooltip"]').tooltip({trigger: 'hover'});

                $('input.updatePlanning').on('input keyup', function (event) {
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
            });
        </script>
    <?php else: ?>
        <?= getContainerErrorMsg(trans('Ce site n\'est pas accessible')); ?>
    <?php endif; ?>
<?php endif; ?>
<?php require('footer.php'); ?>