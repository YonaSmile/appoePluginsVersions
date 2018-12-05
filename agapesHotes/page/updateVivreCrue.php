<?php require('header.php');
if (!empty($_GET['secteur']) && !empty($_GET['site'])):

    //Get Secteur
    $Secteur = new \App\Plugin\AgapesHotes\Secteur();
    $Secteur->setSlug($_GET['secteur']);

    //Get Site
    $Site = new \App\Plugin\AgapesHotes\Site();
    $Site->setSlug($_GET['site']);


    //Check Secteur, Site and Etablissement
    if ($Secteur->showBySlug() && $Site->showBySlug() && $Site->getSecteurId() == $Secteur->getId()):
        echo getTitle($Page->getName(), $Page->getSlug(), ' de <strong>' . $Site->getNom() . '</strong>');
        ?>
        <button type="button" class="btn btn-sm btn-info seeMonthBefore">Mois précédent</button>
        <button type="button" class="btn btn-sm btn-info seeMonthAfter">Mois Prochain</button>
        <div id="vivreCrueContainer"></div>
        <script>
            $(document).ready(function () {


                var today = new Date();
                var day = '01'; //today.getDate();
                var month = today.getMonth() + 1;
                var year = today.getFullYear();

                if (month < 10) {
                    month = '0' + month
                }

                $('#vivreCrueContainer').load('<?= AGAPESHOTES_URL . 'page/getVivreCrueData.php'; ?>', {
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
                    $('#vivreCrueContainer').load('<?= AGAPESHOTES_URL . 'page/getVivreCrueData.php'; ?>', {
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
                    $('#vivreCrueContainer').load('<?= AGAPESHOTES_URL . 'page/getVivreCrueData.php'; ?>', {
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
        <?= getContainerErrorMsg(trans('Cet établissement n\'est pas accessible')); ?>
    <?php endif; ?>
<?php endif; ?>
<?php require('footer.php'); ?>