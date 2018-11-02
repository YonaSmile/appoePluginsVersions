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

        //Get Planning
        $Planning = new \App\Plugin\AgapesHotes\Planning();

        //Get Employe
        $Employe = new \App\Plugin\AgapesHotes\Employe();
        $allEmployes = extractFromObjArr($Employe->showByType(), 'id');

        $allContratEmployes = getAllIdsEmployeHasContratInSite($Site->getId());

        //Select period
        $start = new \DateTime(date('Y-m-01'));
        $end = new \DateTime(date('Y-m-t'));
        $end = $end->modify('+1 day');
        $interval = new \DateInterval('P1D');

        $period = new \DatePeriod($start, $interval, $end);

        // date N = Jour de la semaine (1 = lundi, 7 = dimanche)
        // date W = Numéro de la semaine (Semaine commence le lundi)

        $pti = array(
            'dateDebut' => '2018-10-01',
            'cycle' => 2
        );

        $dayInCycle = getDayInCycle(new \DateTime($pti['dateDebut']), $pti['cycle'], new \DateTime('2018-10-29'));
        echo 'Jour du cycle : ' . $dayInCycle;
        ?>
        <div class="row">
            <div class="table-responsive col-12">
                <table id="pagesTable" class="table table-striped">
                    <thead>
                    <tr>
                        <th><?= trans('Employé'); ?></th>
                        <?php foreach ($period as $key => $date): ?>
                            <th style="<?= $date->format('d') == date('d') ? 'background:#ffeeba;color:#4b5b68;' : ''; ?>"><?= $date->format('d'); ?></th>
                        <?php endforeach; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($allContratEmployes as $employe): ?>
                        <tr data-idemploye="<?= $employe->employe_id; ?>">
                            <th><?= $allEmployes[$employe->employe_id]->entitled; ?></th>
                            <?php foreach ($period as $key => $date):
                                $Planning->setId('');
                                $Planning->setReelHours('');
                                $Planning->setEmployeId($employe->employe_id);
                                $Planning->setSiteId($Site->getId());
                                $Planning->setDate($date->format('Y-m-d'));
                                $Planning->showByDate();
                                $inputCase = empty($Planning->getReelHours()) || $Planning->getReelHours() == '0.00' ? $Planning->getAbsenceReason() : $Planning->getReelHours();
                                ?>
                                <td style="padding: 4px !important;">
                                    <input class="text-center form-control updatePlanning"
                                           name="<?= $Planning->getId(); ?>" type="text"
                                           maxlength="10" list="absenceReasonList" autocomplete="off"
                                           style="padding: 5px 0 !important;"
                                           data-date="<?= $date->format('Y-m-d'); ?>"
                                           data-employeid="<?= $employe->employe_id; ?>"
                                           value="<?= $inputCase; ?>">
                                    <datalist id="absenceReasonList">
                                        <option value="CP" label="Congés Parental">CP</option>
                                        <option value="CM" label="Congés Maternité">CM</option>
                                    </datalist>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>

                    </tbody>
                </table>
            </div>

        </div>


        <script>
            $(document).ready(function () {

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

                    if ($Input.val().length > 0) {

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
                                }
                            );
                        }, 300);

                    }
                });
            });
        </script>
    <?php else: ?>
        <?= getContainerErrorMsg(trans('Ce site n\'est pas accessible')); ?>
    <?php endif; ?>
<?php endif; ?>
<?php require('footer.php'); ?>