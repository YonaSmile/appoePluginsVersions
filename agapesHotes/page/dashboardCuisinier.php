<?php
$SiteAccess = new \App\Plugin\AgapesHotes\SiteAccess();
$SiteAccess->setSiteUserId(getUserIdSession());
$Site = $SiteAccess->showSiteByUser();

$Etablissement = new \App\Plugin\AgapesHotes\Etablissement();
$Etablissement->setSiteId($Site->id);
$allEtablissements = $Etablissement->showAllBySite();

$dateMonthAgo = new \DateTime();
$dateMonthAgo->sub(new \DateInterval('P1M'));
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
                <h5 class="m-0 pl-4 colorSecondary"><?= trans('Infos'); ?> <span class="currentMonth">
                        <?= ucfirst(strftime("%B", strtotime(date('Y-m-d')))); ?> <?= date('Y'); ?></span>
                    <button type="button" class="btn btn-sm btn-info float-right changeDashboard"
                            data-month="<?= $dateMonthAgo->format('m'); ?>"
                            data-year="<?= $dateMonthAgo->format('Y'); ?>"
                            data-current="<?= ucfirst(strftime("%B", strtotime($dateMonthAgo->format('Y-m-d')))); ?> <?= $dateMonthAgo->format('Y'); ?>">
                        <i class="fas fa-arrow-left"></i>
                        Voir <?= ucfirst(strftime("%B", strtotime($dateMonthAgo->format('Y-m-d')))); ?> <?= $dateMonthAgo->format('Y'); ?>
                    </button>

                    <button type="button" class="btn btn-sm btn-info float-right changeDashboard" style="display: none;"
                            data-month="<?= date('m'); ?>"
                            data-year="<?= date('Y'); ?>"
                            data-current="<?= ucfirst(strftime("%B", strtotime(date('Y-m-d')))); ?> <?= date('Y'); ?>">
                        Voir <?= ucfirst(strftime("%B", strtotime(date('Y-m-d')))); ?> <?= date('Y'); ?>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </h5>
                <hr class="mx-4">
            </div>
            <div class="card-body pt-0" id="cuisinierDashboardData"></div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {

        var year = '';
        var month = '';

        function changeDashboard(year, month) {

            $('#cuisinierDashboardData').html(loaderHtml()).load('<?= AGAPESHOTES_URL; ?>data/dashboardCuisinier.php', {
                year: year,
                month: month
            });
        }

        changeDashboard(year, month);

        $('.changeDashboard').on('click', function () {
            year = $(this).data('year');
            month = $(this).data('month');

            changeDashboard(year, month);

            $('.currentMonth').html($(this).data('current'));
            $('.changeDashboard:hidden').delay(400).fadeIn();
            $(this).fadeOut(400);
        });
    });
</script>