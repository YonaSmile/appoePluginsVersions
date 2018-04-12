<?php if (!empty($_GET['id'])): ?>
    <?php require('header.php'); ?>
    <?php $Auteur = new App\Plugin\EventManagement\Auteur();
    $Auteur->setId($_GET['id']);
    if ($Auteur->show()) : ?>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="bigTitle"><i class="far fa-file-alt"></i> <?= trans('Fiche auteur'); ?></h1>
                    <hr class="my-4">
                </div>
            </div>
            <?php require_once(EVENTMANAGEMENT_PATH . 'process/updateAuteur.php'); ?>

            <?php if (isset($Response)): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-<?= $Response->display()->status ?>" role="alert">
                            <?= $Response->display()->error_msg; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form action="" method="post" id="updateAuteurForm">
                <?= getTokenField(); ?>
                <input type="hidden" name="id" value="<?= $Auteur->getId() ?>">
                <div class="row">
                    <div class="col-12">
                        <?= App\Form::text(trans('Nom'), 'nom', 'text', $Auteur->getNom(), true); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <?= App\Form::text(trans('Provenance'), 'provenance', 'text', $Auteur->getProvenance(), false); ?>
                    </div>
                </div>
                <div class="my-4"></div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit"
                                class="btn btn-primary btn-block btn-lg"><?= trans('Enregistrer'); ?></button>
                    </div>
                </div>
                <div class="my-4"></div>
                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-outline-warning"
                                id="displayForm"><?= trans('Modifier'); ?></button>
                    </div>
                </div>
                <div class="my-4"></div>
            </form>
        </div>
        <script>
            $(document).ready(function () {
                $('input, textarea, select, button[type="submit"], .custom-control-indicator').addClass('displayNoForm').attr('disabled', 'disabled');
                $('input[name="nature"]:not(:checked)').parent('label').css('display', 'none');
                $('#displayForm').click(function () {
                    $(this).remove();
                    $('input, textarea, select, button[type="submit"], .custom-control-indicator').removeClass('displayNoForm').attr('disabled', false);
                    $('input[name="nature"]:not(:checked)').parent('label').css('display', 'inline');
                });
            });
        </script>
        <?php require('footer.php'); ?>
    <?php else: ?>
        <?= getContainerErrorMsg(trans('Cet auteur n\'existe pas')); ?>
    <?php endif; ?>
<?php else: ?>
    <?= trans('Cet auteur n\'existe pas'); ?>
<?php endif; ?>
