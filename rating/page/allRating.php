<?php
require('header.php');
$unconfirmedRating = getUnconfirmedRates();
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
            <div class="col-12" id="allRatingTable"><i class="fas fa-circle-notch fa-spin"></i></div>
        </div>
        <?php if ($unconfirmedRating): ?>
            <hr>
            <h2 class="subTitle"><?= trans('Évaluations à confirmer'); ?></h2>
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table id="ratingTable"
                               class="sortableTable table table-striped table-hover table-bordered">
                            <thead>
                            <tr>
                                <th><?= trans('Type'); ?></th>
                                <th><?= trans('Titre'); ?></th>
                                <th><?= trans('Note'); ?></th>
                                <th><?= trans('Utilisateur'); ?></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($unconfirmedRating as $rating): ?>
                                <?php
                                $Obj = getObj($rating->type);
                                $Obj->setId($rating->typeId);
                                $Obj->show();
                                ?>
                                <tr>
                                    <td><?= trans(TYPES_NAMES[$rating->type]); ?></td>
                                    <td><?= $Obj->getName(); ?></td>
                                    <td><strong><?= $rating->score; ?></strong>/5</td>
                                    <td><?= $rating->user ?></td>
                                    <td>
                                        <button type="button" class="btn btn-success btn-sm confirmRating"
                                                title="<?= trans('Confirmer l\'évaluation'); ?>"
                                                data-idrating="<?= $rating->id; ?>">
                                            <span class="fas fa-check"></span>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {

            $('#allRatingTable').load('<?= RATING_URL; ?>page/getAllRating.php');

            $('#allRatingTable .initRating').on('click', function () {

                var $btn = $(this);
                var type = $btn.data('type');
                var typeId = $btn.data('typeid');

                if (confirm('<?= trans('Vous allez réinitialiser les évaluations'); ?>')) {

                    $btn.html('<i class="fas fa-circle-notch fa-spin"></i>').addClass('disabled').attr('disabled', 'disabled');
                    busyApp();

                    $.post(
                        '<?= RATING_URL; ?>process/ajaxProcess.php',
                        {
                            initRating: 1,
                            type: type,
                            typeId: typeId
                        }, function (data) {
                            if (data == 'true' || data === true) {
                                $btn.parent('td').parent('tr').fadeOut(function () {
                                    availableApp();
                                });
                            }
                        }
                    );
                }
            });

            $('.confirmRating').on('click', function () {

                busyApp();
                $('#allRatingTable').html('<i class="fas fa-circle-notch fa-spin"></i>');

                var $btn = $(this);
                var idRating = $btn.data('idrating');
                $btn.html('<i class="fas fa-circle-notch fa-spin"></i>').addClass('disabled').attr('disabled', 'disabled');

                $.post(
                    '<?= RATING_URL; ?>process/ajaxProcess.php',
                    {
                        confirmRating: 1,
                        idRating: idRating

                    }, function (data) {
                        if (data == 'true' || data === true) {
                            $btn.parent('td').parent('tr').fadeOut(function () {
                                $('#allRatingTable').load('<?= RATING_URL; ?>page/getAllRating.php', function () {
                                    availableApp();
                                });
                            });
                        }
                    }
                );
            });
        });
    </script>
<?php require('footer.php'); ?>