<?php
require('header.php');
require_once(INTERACTIVE_MAP_PATH . 'process/postProcess.php');
?>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="bigTitle"><?= trans('Nouvelle Carte'); ?></h1>
                <hr class="my-4">
            </div>
        </div>

        <?php if (isset($Response)): ?>
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-<?= $Response->display()->status ?>" role="alert">
                        <?= $Response->display()->error_msg; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <form action="" method="post" id="addInterMapForm">
            <?= getTokenField(); ?>
            <div class="row">
                <div class="col-12">
                    <?= App\Form::text(trans('Titre'), 'title', 'text', !empty($_POST['title']) ? $_POST['title'] : '', true); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6">
                    <?= App\Form::text(trans('Largeur'), 'width', 'text', !empty($_POST['width']) ? $_POST['width'] : '', true); ?>
                </div>
                <div class="col-12 col-md-6">
                    <?= App\Form::text(trans('Hauteur'), 'height', 'text', !empty($_POST['height']) ? $_POST['height'] : '', true); ?>
                </div>
            </div>
            <div class="my-4"></div>
            <div class="row">
                <div class="col-12">
                    <button type="submit" name="ADDINTERMAP" class="btn btn-primary btn-block btn-lg"><?= trans('Enregistrer'); ?></button>
                </div>
            </div>
        </form>

        <div class="my-4"></div>
    </div>
<?php require('footer.php'); ?>