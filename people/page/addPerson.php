<?php require('header.php'); ?>
<?= getTitle($Page->getName(), $Page->getSlug()); ?>
    <div class="container">
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
                    <?= App\Form::select('Enregistrement de type', 'type', getAppTypes(), !empty($_POST['type']) ? $_POST['type'] : '', true); ?>
                </div>
                <div class="col-12 col-lg-4 my-2">
                    <?= App\Form::select('Nature de la personne', 'nature', PEOPLE_NATURE, !empty($_POST['nature']) ? $_POST['nature'] : '', true); ?>
                </div>
                <div class="col-12 col-lg-4 my-2">
                    <?= App\Form::text('Nom', 'name', 'text', !empty($_POST['name']) ? $_POST['name'] : '', true, 150); ?>
                </div>
                <div class="col-12 col-lg-4 my-2">
                    <?= App\Form::text('Prénom', 'firstName', 'text', !empty($_POST['firstName']) ? $_POST['firstName'] : '', false, 150); ?>
                </div>

                <div class="col-12 col-lg-4 my-2">
                    <?= App\Form::text('Date de naissance', 'birthDate', 'date', !empty($_POST['birthDate']) ? $_POST['birthDate'] : '', false, 10); ?>
                </div>
                <div class="col-12 col-lg-4 my-2">
                    <?= App\Form::text('Adresse Email', 'email', 'email', !empty($_POST['email']) ? $_POST['email'] : '', false, 255); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-4 my-2">
                    <?= App\Form::text('Téléphone', 'tel', 'tel', !empty($_POST['tel']) ? $_POST['tel'] : '', false, 10); ?>
                </div>

                <div class="col-12 col-lg-8 my-2">
                    <?= App\Form::text('Adresse postale', 'address', 'text', !empty($_POST['address']) ? $_POST['address'] : '', false, 255); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-2 my-2">
                    <?= App\Form::text('Code postal', 'zip', 'tel', !empty($_POST['zip']) ? $_POST['zip'] : '', false, 7); ?>
                </div>
                <div class="col-12 col-lg-5 my-2">
                    <?= App\Form::text('Ville', 'city', 'text', !empty($_POST['city']) ? $_POST['city'] : '', false, 100); ?>
                </div>
                <div class="col-12 col-lg-5 my-2">
                    <?= App\Form::select('Pays', 'country', listPays(), !empty($_POST['country']) ? $_POST['country'] : 'FR', false); ?>
                </div>
            </div>
            <div class="my-2"></div>
            <div class="row">
                <div class="col-12">
                    <?= App\Form::target('ADDPERSON'); ?>
                    <?= App\Form::submit('Enregistrer', 'ADDPERSONSUBMIT'); ?>
                </div>
            </div>
        </form>
        <div class="my-4"></div>
    </div>
<?php require('footer.php'); ?>