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

        //Get main courante
        $MainCourante = new \App\Plugin\AgapesHotes\MainCourante();
        $MainCourante->setSiteId($Site->getId());

        //Get prestations
        $Prestation = new \App\Plugin\AgapesHotes\Prestation();
        $Prestation->setSiteId($Site->getId());
        $allPrestations = $Prestation->showAll();

        //Get price by prestation
        $PrestationPrix = new \App\Plugin\AgapesHotes\PrixPrestation();
        $PrestationPrix->setSiteId($Site->getId());

        //Select period
        $start = new \DateTime(date('Y-m-01'));
        $end = new \DateTime(date('Y-m-t'));
        $end = $end->modify('+1 day');
        $interval = new \DateInterval('P1D');

        $period = new \DatePeriod($start, $interval, $end);
        ?>
        <div class="container-fluid">
            <div class="table-responsive col-12">
                <table id="pagesTable" class="table table-striped">
                    <thead>
                    <tr>
                        <th><?= trans('Prestation'); ?></th>
                        <?php foreach ($period as $key => $date): ?>
                            <th style="<?= $date->format('d') == date('d') ? 'background:#ffeeba;color:#4b5b68;' : ''; ?>"><?= $date->format('d'); ?></th>
                        <?php endforeach; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($allPrestations as $prestation): ?>
                        <tr data-idprestation="<?= $prestation->id ?>">
                            <th><?= $prestation->nom; ?></th>
                            <?php foreach ($period as $key => $date):

                                //Get main courante
                                $quantite = 0;
                                $MainCourante->setId('');
                                $MainCourante->setPrestationId($prestation->id);
                                $MainCourante->setDate($date->format('Y-m-d'));
                                if ($MainCourante->showByDate()) {
                                    $quantite = $MainCourante->getQuantite();
                                }

                                //Get prix by prestation
                                $idPrixReel = 0;
                                $prixReel = 0;
                                $PrestationPrix->setDateDebut($date->format('Y-m-d'));
                                $PrestationPrix->setPrestationId($prestation->id);
                                if ($PrestationPrix->showReelPrice()) {
                                    $idPrixReel = $PrestationPrix->getId();
                                    $prixReel = $PrestationPrix->getPrixHT();
                                }
                                ?>
                                <td style="padding: 4px !important;" data-toggle="tooltip" data-placement="right"
                                    title="<?= $date->format('d') . ' / ' . $prestation->nom; ?>">
                                    <input type="tel" data-prestationid="<?= $prestation->id; ?>"
                                           data-date="<?= $date->format('Y-m-d'); ?>"
                                           data-prixid="<?= $idPrixReel; ?>"
                                           class="text-center form-control mainCourantInput"
                                           name="<?= $MainCourante->getId(); ?>"
                                           value="<?= $quantite; ?>"
                                           style="padding: 5px 0 !important;">
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