<?php require('header.php'); ?>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="bigTitle"><?= trans('Nouvelle personne'); ?></h1>
                <hr class="my-4">
            </div>
        </div>
        <?php require_once(PEOPLE_PATH . 'process/postProcess.php'); ?>

        <?php if (isset($Response)): ?>
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-<?= $Response->display()->status ?>" role="alert">
                        <?= $Response->display()->error_msg; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <form action="" method="post" id="addPersonForm">
            <?= getTokenField(); ?>
            <div class="my-4"></div>
            <div class="row">
                <div class="col-12 col-lg-4 my-2">
                    <?= App\Form::select(trans('Enregistrement de type'), 'type', getAppTypes(), !empty($_POST['type']) ? $_POST['type'] : '', true); ?>
                </div>
                <div class="col-12 col-lg-4 my-2">
                    <?= App\Form::text(trans('Nom'), 'name', 'text', !empty($_POST['name']) ? $_POST['name'] : '', true, 150); ?>
                </div>
                <div class="col-12 col-lg-4 my-2">
                    <?= App\Form::text(trans('Prénom'), 'firstName', 'text', !empty($_POST['firstName']) ? $_POST['firstName'] : '', false, 150); ?>
                </div>

                <div class="col-12 col-lg-4 my-2">
                    <?= App\Form::text(trans('Date de naissance'), 'birthDate', 'date', !empty($_POST['birthDate']) ? $_POST['birthDate'] : '', false, 10); ?>
                </div>
                <div class="col-12 col-lg-4 my-2">
                    <?= App\Form::text(trans('Adresse Email'), 'email', 'email', !empty($_POST['email']) ? $_POST['email'] : '', false, 255); ?>
                </div>
                <div class="col-12 col-lg-4 my-2">
                    <?= App\Form::text(trans('Téléphone'), 'tel', 'tel', !empty($_POST['tel']) ? $_POST['tel'] : '', false, 10); ?>
                </div>

                <div class="col-12 my-2">
                    <?= App\Form::text(trans('Adresse postale'), 'address', 'text', !empty($_POST['address']) ? $_POST['address'] : '', false, 255); ?>
                </div>

                <div class="col-12 col-lg-2 my-2">
                    <?= App\Form::text(trans('Code postal'), 'zip', 'tel', !empty($_POST['zip']) ? $_POST['zip'] : '', false, 7); ?>
                </div>
                <div class="col-12 col-lg-5 my-2">
                    <?= App\Form::text(trans('Ville'), 'city', 'text', !empty($_POST['city']) ? $_POST['city'] : '', false, 100); ?>
                </div>
                <div class="col-12 col-lg-5 my-2">
                    <?= App\Form::text(trans('Pays'), 'country', 'text', !empty($_POST['country']) ? $_POST['country'] : 'France', false, 100); ?>
                </div>
            </div>
            <div class="my-2"></div>
            <div class="row">
                <div class="col-12">
                    <button type="submit" name="ADDPERSON" class="btn btn-outline-primary btn-block btn-lg">
                        <?= trans('Enregistrer'); ?>
                    </button>
                </div>
            </div>
        </form>
        <div class="my-4"></div>
    </div>
<?php require('footer.php'); ?>