<?php
$SiteAccess = new \App\Plugin\AgapesHotes\SiteAccess();
$SiteAccess->setSiteUserId(getUserIdSession());
$Site = $SiteAccess->showSiteByUser();

$Etablissement = new \App\Plugin\AgapesHotes\Etablissement();
$Etablissement->setSiteId($Site->id);
$allEtablissements = $Etablissement->showAllBySite();

$dateMonthAgo = new \DateTime();
$dateMonthAgo->sub(new \DateInterval('P1M'));

$dateMonthLater = new DateTime();
$dateMonthLater->add(new \DateInterval('P1M'));
?>
<div class="row mb-3">
    <div class="d-flex col-12 col-lg-4">
        <div class="card border-0 w-100">
            <div class="card-header bg-white pb-0 border-0 boardBlock1Title">
                <h5 class="m-0 pl-4 colorPrimary"><?= $Site->nom; ?></h5>
                <hr class="mx-4">
            </div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-12">
                        <a href="<?= AGAPESHOTES_URL; ?>page/mainCourante/<?= $Site->secteurSlug; ?>/<?= $Site->slug; ?>/"
                           class="btn btn-block btn-info py-4">Main Courante</a>
                        <!--<a href="<?= AGAPESHOTES_URL; ?>page/planning/<?= $Site->secteurSlug; ?>/<?= $Site->slug; ?>/"
                           class="btn btn-block btn-info py-4">Planning</a>-->
                        <a href="<?= AGAPESHOTES_URL; ?>page/noteDeFrais/<?= $Site->secteurSlug; ?>/<?= $Site->slug; ?>/"
                           class="btn btn-block btn-info py-4">Note de frais</a>
                        <a href="<?= AGAPESHOTES_URL; ?>page/vivreCrue/<?= $Site->secteurSlug; ?>/<?= $Site->slug; ?>/"
                           class="btn btn-block btn-info py-4">Vivre cru</a>
                        <a href="<?= AGAPESHOTES_URL; ?>page/mainSupplementaire/<?= $Site->secteurSlug; ?>/<?= $Site->slug; ?>/"
                           class="btn btn-block btn-info py-4">Facturation HC</a>

                        <?php foreach ($allEtablissements as $etablissement): ?>
                            <small class="littleTitle colorPrimary mt-3"><?= $etablissement->nom; ?></small>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <a href="<?= AGAPESHOTES_URL; ?>page/allPrestations.php?secteur=<?= $Site->secteurSlug; ?>&site=<?= $Site->slug; ?>&etablissement=<?= $etablissement->slug; ?>"
                                       class="btn btn-outline-dark btn-block my-2">Liste prestations</a>
                                </div>
                                <div class="col-12 col-md-6">
                                    <a href="<?= AGAPESHOTES_URL; ?>page/allCourses.php?secteur=<?= $Site->secteurSlug; ?>&site=<?= $Site->slug; ?>&etablissement=<?= $etablissement->slug; ?>"
                                       class="btn btn-outline-dark btn-block my-2">Liste vivre cru</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex col-12 col-lg-8">
        <div class="card border-0 w-100">
            <div class="card-header bg-white pb-0 border-0 boardBlock2Title">
                <h5 class="m-0 pl-4 colorSecondary"><span class="currentMonth"><?= trans('Infos'); ?>
                        <?= ucfirst(strftime("%B", strtotime(date('Y-m-d')))); ?> <?= date('Y'); ?></span>
                    <div class="float-right">
                        <button type="button" class="btn btn-sm btn-info seeMonthBefore">Mois précédent</button>
                        <button type="button" class="btn btn-sm btn-info seeMonthNow">Mois courant</button>
                        <button type="button" class="btn btn-sm btn-info seeMonthAfter">Mois Prochain</button>
                    </div>
                </h5>
                <hr class="mx-4">
            </div>
            <div class="card-body pt-0" id="cuisinierDashboardData"></div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {

        var today = new Date();
        var month = today.getMonth() + 1;
        var year = today.getFullYear();

        if (month < 10) {
            month = '0' + month
        }

        function changeDashboard(year, month) {

            busyApp();
            $('#cuisinierDashboardData').html(loaderHtml()).load('<?= AGAPESHOTES_URL; ?>data/dashboardCuisinier.php', {
                year: year,
                month: month
            }, function (data) {
                availableApp();
            });
        }

        changeDashboard(year, month);

        $('.seeMonthNow').on('click', function () {

            month = today.getMonth() + 1;
            year = today.getFullYear();

            if (month < 10) {
                month = '0' + month
            }

            changeDashboard(year, month);
        });

        $('.seeMonthBefore').on('click', function () {
            if (month > 1) {
                month = parseFloat(month) - 1;
            } else {
                month = '12';
                year = parseFloat(year) - 1;
            }

            changeDashboard(year, month);
        });

        $('.seeMonthAfter').on('click', function () {
            if (month < 12) {
                month = parseFloat(month) + 1;
            } else {
                month = '01';
                year = parseFloat(year) + 1;
            }
            changeDashboard(year, month);
        });
    });
</script>