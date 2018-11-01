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
        $Planning->setSiteId($Site->getId());
        $allPlanning = $Planning->showAllBySite();

        //Get Employe
        $allEmployes = getAllIdsEmployeHasContratInSite($Site->getId());

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
        <div class="container-fluid">
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
                    <?php foreach ($allEmployes as $employe): ?>
                        <tr data-idemploye="<?= $employe->employe_id; ?>">
                            <th><?= $employe->employe_id; ?></th>
                            <?php foreach ($period as $key => $date): ?>
                                <td> <?= $date->format('d'); ?></td>
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
                    $('input.mainCourantInput').not(inputExlude).attr('disabled', 'disabled');
                }

                function activateAllFields() {
                    $('input.mainCourantInput').attr('disabled', false);
                }

                var delay = (function () {
                    var timer = 0;
                    return function (callback, ms) {
                        clearTimeout(timer);
                        timer = setTimeout(callback, ms);
                    };
                })();

                $('[data-toggle="tooltip"]').tooltip({trigger: 'hover'});

                $('input.mainCourantInput').on('input keyup', function (event) {
                    event.preventDefault();

                    var $Input = $(this);
                    $('input.mainCourantInput').removeClass('successInput');

                    if ($Input.val().length > 0) {

                        var idMainCourante = $Input.attr('name');
                        var siteId = '<?= $Site->getId(); ?>';
                        var prestationId = $Input.data('prestationid');
                        var date = $Input.data('date');
                        var prixId = parseFloat($Input.data('prixid'));
                        var quantite = $Input.val();

                        if (prixId > 0) {
                            disabledAllFields($Input);
                            delay(function () {
                                busyApp();

                                $.post(
                                    '<?= AGAPESHOTES_URL . 'process/ajaxMainCouranteProcess.php'; ?>',
                                    {
                                        UPDATEMAINCOURANTE: 'OK',
                                        siteId: siteId,
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
                                        activateAllFields();
                                    }
                                );
                            }, 300);
                        } else {
                            $Input.val(0);
                            alert('Aucun prix n\'a été défini pour cette prestation à cette période');
                        }
                    }
                })
            });
        </script>
    <?php else: ?>
        <?= getContainerErrorMsg(trans('Ce site n\'est pas accessible')); ?>
    <?php endif; ?>
<?php endif; ?>
<?php require('footer.php'); ?>