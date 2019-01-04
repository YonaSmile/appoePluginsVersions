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
    ?>
        <div class="row">
            <div class="col-12">
                <button type="button" class="btn btn-sm btn-info seeMonthBefore">Mois précédent</button>
                <button type="button" class="btn btn-sm btn-info seeMonthAfter">Mois Prochain</button>
            </div>
        </div>

        <div id="planningContainer" class="row"></div>
        <script>
            $(document).ready(function () {


                var today = new Date();
                var day = '01'; //today.getDate();
                var month = today.getMonth() + 1;
                var year = today.getFullYear();

                if (month < 10) {
                    month = '0' + month
                }

                $('#planningContainer').load('<?= AGAPESHOTES_URL . 'page/getPlanningData.php'; ?>', {
                    secteur: '<?= $Secteur->getSlug(); ?>',
                    site: '<?= $Site->getSlug(); ?>',
                    startDate: year + '-' + month + '-' + day
                });

                $('.seeMonthBefore').on('click', function () {
                    if (month > 1) {
                        month = parseFloat(month) - 1;
                    } else {
                        month = '12';
                        year = parseFloat(year) - 1;
                    }
                    busyApp();
                    $('#planningContainer').load('<?= AGAPESHOTES_URL . 'page/getPlanningData.php'; ?>', {
                        secteur: '<?= $Secteur->getSlug(); ?>',
                        site: '<?= $Site->getSlug(); ?>',
                        startDate: year + '-' + month + '-' + day
                    }, function () {
                        availableApp();
                    });
                });

                $('.seeMonthAfter').on('click', function () {
                    if (month < 12) {
                        month = parseFloat(month) + 1;
                    } else {
                        month = '01';
                        year = parseFloat(year) + 1;
                    }
                    busyApp();
                    $('#planningContainer').load('<?= AGAPESHOTES_URL . 'page/getPlanningData.php'; ?>', {
                        secteur: '<?= $Secteur->getSlug(); ?>',
                        site: '<?= $Site->getSlug(); ?>',
                        startDate: year + '-' + month + '-' + day
                    }, function () {
                        availableApp();
                    });
                });
            });
        </script>
    <?php else: ?>
        <?= getContainerErrorMsg(trans('Ce site n\'est pas accessible')); ?>
    <?php endif; ?>
<?php endif; ?>
<?php require('footer.php'); ?>