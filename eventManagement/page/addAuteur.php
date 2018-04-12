<?php require('header.php'); ?>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="bigTitle"><?= trans('Nouvel Auteur'); ?></h1>
                <hr class="my-4">
            </div>
        </div>
        <?php require_once(EVENTMANAGEMENT_PATH . 'process/addAuteur.php'); ?>

        <?php if (isset($Response)): ?>
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-<?= $Response->display()->status ?>" role="alert">
                        <?= $Response->display()->error_msg; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <form action="" method="post" id="addAuteurForm">
            <?= getTokenField(); ?>
            <div class="row">
                <div class="col-12">
                    <?= App\Form::text(trans('Nom'), 'nom', 'text', !empty($_POST['nom']) ? $_POST['nom'] : '', true); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <?= App\Form::text(trans('Provenance'), 'provenance', 'text', !empty($_POST['provenance']) ? $_POST['provenance'] : '', false); ?>
                </div>
            </div>
            <div class="my-4"></div>
            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-block btn-lg"><?= trans('Enregistrer'); ?></button>
                </div>
            </div>
        </form>

        <div class="my-4"></div>
    </div>
<?php require('footer.php'); ?>