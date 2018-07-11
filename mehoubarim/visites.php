<?php
require_once('header.php');
require_once('mehoubarim_functions.php');

if (!empty($_POST['resetStats'])) {
    mehoubarim_cleanVisitor();
}

$visitors = mehoubarim_getVisitor();
$globalData = mehoubarim_getGlobal();
?>
<?php if ($visitors && is_array($visitors['totalPagesViews']) && is_array($visitors['visitors'])): ?>
    <?php
    arsort($visitors['totalPagesViews']);
    $totalPagesViews = array_slice($visitors['totalPagesViews'], 0, 5, true);
    ?>
    <div class="row mb-3">
        <div class="col-12 col-lg-4 mb-3">
            <ul class="list-group" id="listVisitorsStats">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>
                        <?= trans('Depuis'); ?>
                        <?= !empty($globalData['dateBegin']) ? displayCompleteDate($globalData['dateBegin'], true) : ""; ?>
                    </strong>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= trans('Visiteurs'); ?>
                    <span class="badge badge-primary badge-pill"><?= count($visitors['visitors']); ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= trans('Pages consultées'); ?>
                    <span class="badge badge-primary badge-pill"><?= array_sum($visitors['visitors']); ?></span>
                </li>
            </ul>
        </div>
        <div class="col-12 col-lg-4 mb-3">
            <ul class="list-group" id="listPagesStats">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong><?= trans('Les pages les plus consultées'); ?></strong>
                </li>
                <?php foreach ($totalPagesViews as $name => $nb): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= ucfirst(strtolower($name)); ?>
                        <span class="badge badge-info badge-pill"><?= $nb; ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="col-12 col-lg-4 mb-3">
            <div class="list-group">
                <button class="list-group-item list-group-item-action list-group-item-info" id="resetStats"
                        type="button">
                    <?= trans('Réinitialiser les statistiques'); ?>
                </button>
            </div>
            <div class="progress my-2" style="height: 1px;">
                <div class="progress-bar bg-info" role="progressbar" id="visitsLoader" style="width: 0;"
                     aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {

            $('#resetStats').on('click', function () {

                $(this).attr('disabled', 'disabled').addClass('disabled')
                    .html('<i class="fas fa-circle-notch fa-spin"></i> <?= trans('Chargement'); ?>...');

                $('#listVisitorsStats, #listPagesStats').hide();

                $.post(
                    '/app/plugin/mehoubarim/visites.php',
                    {resetStats: 'OK'}
                );
            });
        });
    </script>

<?php endif; ?>