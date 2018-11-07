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

        //Get price by prestation
        $allPrestationsPrix = getAllPrestationsPriceBySite($Site->getId());

        //Get main courante
        $allMainCourante = getAllMainCouranteBySiteInMonth($Site->getId());

        //Get prestations
        $Prestation = new \App\Plugin\AgapesHotes\Prestation();
        $Prestation->setSiteId($Site->getId());
        $allPrestations = $Prestation->showAll();


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
                            <th style="<?= $date->format('d') == date('d') ? 'background:#4fb99f;color:#fff;' : ''; ?> <?= $date->format('N') == 7 ? 'background:#f2b134;color:#4b5b68;' : ''; ?>"><?= $date->format('d'); ?></th>
                        <?php endforeach; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($allPrestations as $prestation):
                        if (array_key_exists($prestation->id, $allMainCourante)): ?>
                            <tr data-idprestation="<?= $prestation->id ?>">
                                <th><?= $prestation->nom; ?></th>
                                <?php foreach ($period as $key => $date):

                                    $allPrestationsPrixDates = array_keys($allPrestationsPrix[$prestation->id]);
                                    usort($allPrestationsPrixDates, 'reelDatesSortDESC');

                                    //Get prix by prestation
                                    $idPrixReel = 0;
                                    $prixReel = 0;
                                    foreach ($allPrestationsPrixDates as $prestationsPrixDate) {
                                        if ($prestationsPrixDate <= $date->format('Y-m-d')) {
                                            $dateDebut = $allPrestationsPrix[$prestation->id][$prestationsPrixDate];

                                            $idPrixReel = $dateDebut->id;
                                            $prixReel = $dateDebut->prixHT;

                                            break;
                                        }
                                    }

                                    $mainCourantId = '';
                                    $mainCourantQuantite = '';
                                    if (array_key_exists($date->format('Y-m-d'), $allMainCourante[$prestation->id])) {
                                        $MainCourante = $allMainCourante[$prestation->id][$date->format('Y-m-d')];
                                        $mainCourantId = $MainCourante->id;
                                        $mainCourantQuantite = $MainCourante->quantite;
                                    }

                                    ?>
                                    <td style="padding: 4px !important;" data-toggle="tooltip" data-placement="right"
                                        title="<?= $date->format('d') . ' / ' . $prestation->nom; ?>">
                                        <input type="tel" data-prestationid="<?= $prestation->id; ?>"
                                               data-date="<?= $date->format('Y-m-d'); ?>"
                                               data-prixid="<?= $idPrixReel; ?>"
                                               class="text-center form-control mainCourantInput"
                                               name="<?= $mainCourantId; ?>"
                                               value="<?= $mainCourantQuantite; ?>"
                                               style="padding: 5px 0 !important; <?= $date->format('d') == date('d') ? 'background:#4fb99f;color:#fff;' : ''; ?>">
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

                    if (!$Input.val().length > 0) {
                        $Input.val('');
                    }

                    var idMainCourante = $Input.attr('name');
                    var siteId = '<?= $Site->getId(); ?>';
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