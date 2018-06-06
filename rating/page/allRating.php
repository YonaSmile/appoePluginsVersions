<?php
require('header.php');
$unconfirmedRating = getAllRates(0);
$allRating = getAllRates();
$Article = new App\Plugin\ItemGlue\Article();
?>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="bigTitle"><?= trans('Évaluations'); ?></h1>
                <hr class="my-4">
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table id="ratingTable"
                           class="sortableTable table table-striped table-hover table-bordered">
                        <thead>
                        <tr>
                            <th><?= trans('Titre'); ?></th>
                            <th><?= trans('Note'); ?></th>
                            <th><?= trans('Nombre d\'évaluations'); ?></th>
                            <th><?= trans('Score'); ?></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if ($allRating): foreach ($allRating as $key => $type): ?>
                            <!-- TODO -->
                            <h2 class="subTitle"><?= trans('Article'); ?></h2>
                            <?php foreach ($type as $typeId => $rating) :
                                $Article->setId($typeId);
                                $Article->show();
                                ?>
                                <tr>
                                    <td><?= $Article->getName(); ?></td>
                                    <td>
                                    <span style="margin-right: 10px;">
                                        <strong><?= $rating['average'] ?></strong>/5
                                    </span> <?= showRatings($key, $typeId, false, 'littleStars', true); ?>
                                    </td>
                                    <td><?= $rating['nbVotes'] ?></td>
                                    <td><?= $rating['score'] ?></td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm initRating"
                                                title="<?= trans('Réinitialiser l\'évaluation'); ?>"
                                                data-type="ITEMGLUE" data-typeid="<?= $typeId ?>">
                                            <span class="fas fa-times"></span>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.initRating').on('click', function () {
                var $btn = $(this);
                var type = $btn.data('type');
                var typeId = $btn.data('typeid');

                if (confirm('<?= trans('Vous allez réinitialiser les évaluations'); ?>')) {
                    $.post(
                        '<?= RATING_URL; ?>process/ajaxProcess.php',
                        {
                            initRating: 1,
                            type: type,
                            typeId: typeId
                        }, function (data) {
                            if (data == 'true' || data === true) {
                                $btn.parent('td').parent('tr').fadeOut();
                            }
                        }
                    );
                }
            });
        });
    </script>
<?php require('footer.php'); ?>