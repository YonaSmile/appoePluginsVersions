<?php
require_once('header.php');
require_once('mehoubarim_functions.php');

if (!empty($_POST['resetStats'])) {
    mehoubarim_cleanVisitor();
}

$visitors = mehoubarim_getVisitor();
$globalData = mehoubarim_getGlobal();
?>
<?php
if ($visitors && is_array($visitors['totalPagesViews']) && is_array($visitors['visitors'])):
    arsort($visitors['totalPagesViews']);
    ?>
    <strong>
        <i class="fas fa-clock"></i> <?= trans('Depuis'); ?> <?= !empty($globalData['dateBegin']) ? displayCompleteDate($globalData['dateBegin'], true) : ""; ?>
    </strong>
    <div class="my-4">
        <div class="my-2 ml-4" style="position: relative;">
            <span class="mr-2"><?= trans('Visiteurs'); ?></span>
            <span class="visitsStatsBadge"><?= count($visitors['visitors']); ?></span>
        </div>
        <div class="my-2 ml-4" style="position: relative;">
            <span class="mr-2"> <?= trans('Pages consultées'); ?></span>
            <span class="visitsStatsBadge"><?= array_sum($visitors['visitors']); ?></span>
        </div>
    </div>
    <strong><?= trans('Les pages les plus consultées'); ?></strong>
    <div class="my-4">
        <?php foreach (array_slice($visitors['totalPagesViews'], 0, 5, true) as $name => $nb): ?>
            <div class="my-2 ml-4" style="position: relative;">
                <span class="mr-2"><?= ucfirst(strtolower($name)); ?></span>
                <span class="visitsStatsBadge"><?= $nb; ?></span>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="text-right">
        <button class="btn btn-outline-info btn-sm border-radius-0" id="resetStats" type="button">
            <?= trans('Réinitialiser les statistiques'); ?>
        </button>
    </div>
    <div class="progress my-2" style="height: 1px;">
        <div class="progress-bar bg-info" role="progressbar" id="visitsLoader" style="width: 0;"
             aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
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