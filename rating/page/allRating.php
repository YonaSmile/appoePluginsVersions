<?php
require('header.php');
$allRating = getAllRates();
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
                <?php if ($allRating): ?>
                    <?php $Cls = new App\Plugin\ItemGlue\Article(); ?>
                    <h2 class="subTitle"><?= trans('Article'); ?></h2>
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

                            <?php foreach ($allRating['ITEMGLUE'] as $typeId => $rating): ?>
                                <?php
                                $Cls->setId($typeId);
                                $Cls->show();
                                ?>
                                <tr>
                                    <td><?= $Cls->getName(); ?></td>
                                    <td>
                                        <span style="margin-right: 10px;">
                                            <strong><?= $rating['average'] ?></strong>/5
                                        </span> <?= showRatings('ITEMGLUE', $typeId, false, 'littleStars', true); ?>
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
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                <?php endif; ?>
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