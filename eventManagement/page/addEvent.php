<?php require('header.php'); ?>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="bigTitle"><?= trans('Nouvel Évènement'); ?></h1>
                <hr class="my-4">
            </div>
        </div>
        <?php require_once(EVENTMANAGEMENT_PATH . 'process/addEvent.php'); ?>

        <?php if (isset($Response)): ?>
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-<?= $Response->display()->status ?>" role="alert">
                        <?= $Response->display()->error_msg; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <form action="" method="post" id="addEventForm" enctype="multipart/form-data">
            <?= getTokenField(); ?>
            <div class="row">
                <div class="col-12 col-lg-4">
                    <?php
                    $Auteur = new App\Plugin\EventManagement\Auteur();
                    $auteurs = $Auteur->showAll();
                    $auteursArray = array();
                    foreach ($auteurs as $auteur) {
                        $auteursArray[$auteur->id] = $auteur->nom;
                    }

                    echo App\Form::select('Auteur', 'auteurId', $auteursArray, !empty($_POST['auteurId']) ? $_POST['auteurId'] : '', true);
                    ?>
                </div>
                <div class="col-12 col-lg-4">
                    <?= App\Form::text('Titre', 'titre', 'text', !empty($_POST['titre']) ? $_POST['titre'] : '', true, 250); ?>
                </div>
                <div class="col-12 col-lg-4">
                    <?= App\Form::selectDuree('Durée', 'duree', true, 0, 2, 55, 5); ?>
                    <small id="passwordHelpBlock" class="form-text text-muted">
                        <?= trans('La durée de l\'évènement n\'est pas modifiable'); ?>.
                    </small>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <?= App\Form::text('Pitch', 'pitch', 'text', !empty($_POST['pitch']) ? $_POST['pitch'] : '', false, 255); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <?= App\Form::textarea('Participation', 'participation', !empty($_POST['participation']) ? $_POST['participation'] : '', 5, false); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <?= App\Form::textarea('Description', 'description', !empty($_POST['description']) ? $_POST['description'] : '', 5, true, '', 'ckeditor'); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-8">
                    <?= App\Form::radio('Type de Spectacle', 'spectacleType', array_map('trans', SPECTACLES_TYPES)); ?>
                </div>
                <div class="col-12 col-lg-4">
                    <?= App\Form::radio('IN / OFF', 'indoor', array_map('trans', INDOOR_OFF)); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <?= App\Form::text('Image', 'image', 'file', '', false); ?>
                </div>
            </div>
            <div class="my-4"></div>
            <div class="row">
                <div class="col-12">
                    <?= App\Form::target('ADDEVENT'); ?>
                    <?= App\Form::submit('Enregistrer', 'ADDEVENTSUBMIT'); ?>
                </div>
            </div>
        </form>
        <div class="my-4"></div>
    </div>
    <script>
        $(document).ready(function () {
            $('form').submit(function () {
                $('#loader').fadeIn('fast');
            });
        });
    </script>
<?php require('footer.php'); ?>