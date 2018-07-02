<?php require('header.php');
if (!empty($_GET['id'])): ?>
    <?php
    $People = new App\Plugin\People\People();
    $People->setId($_GET['id']);
    if ($People->show()) : ?>
        <?php require(PEOPLE_PATH . 'process/postProcess.php'); ?>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="bigTitle"><?= trans('Fiche de la personne'); ?></h1>
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
            <form action="" method="post" id="updatePersonForm">
                <?= getTokenField(); ?>
                <input type="hidden" name="id" value="<?= $People->getId(); ?>">
                <div class="row">
                    <div class="col-12 col-lg-4 my-2">
                        <?= App\Form::select(trans('Enregistrement de type'), 'type', getAppTypes(), $People->getType(), true); ?>
                    </div>
                    <div class="col-12 col-lg-4 my-2">
                        <?= App\Form::select(trans('Nature de la personne'), 'nature', PEOPLE_NATURE, $People->getNature(), true); ?>
                    </div>
                    <div class="col-12 col-lg-4 my-2">
                        <?= App\Form::text(trans('Nom'), 'name', 'text', $People->getName(), true, 150); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-lg-4 my-2">
                        <?= App\Form::text(trans('Prénom'), 'firstName', 'text', $People->getFirstName(), false, 150); ?>
                    </div>

                    <div class="col-12 col-lg-4 my-2">
                        <?= App\Form::text(trans('Date de naissance'), 'birthDate', 'date', $People->getBirthDate(), false, 10); ?>
                    </div>
                    <div class="col-12 col-lg-4 my-2">
                        <?= App\Form::text(trans('Adresse Email'), 'email', 'email', $People->getEmail(), false, 255); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-lg-4 my-2">
                        <?= App\Form::text(trans('Téléphone'), 'tel', 'tel', $People->getTel(), false, 10); ?>
                    </div>

                    <div class="col-12 col-lg-8 my-2">
                        <?= App\Form::text(trans('Adresse postale'), 'address', 'text', $People->getAddress(), false, 255); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-lg-2 my-2">
                        <?= App\Form::text(trans('Code postal'), 'zip', 'tel', $People->getZip(), false, 7); ?>
                    </div>
                    <div class="col-12 col-lg-5 my-2">
                        <?= App\Form::text(trans('Ville'), 'city', 'text', $People->getCity(), false, 100); ?>
                    </div>
                    <div class="col-12 col-lg-5 my-2">
                        <?= App\Form::text(trans('Pays'), 'country', 'text', $People->getCountry(), false, 100); ?>
                    </div>
                </div>
                <div class="my-2"></div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" name="UPDATEPERSON" class="btn btn-outline-primary btn-block btn-lg">
                            <?= trans('Enregistrer'); ?>
                        </button>
                    </div>
                </div>
            </form>
            <div class="my-4"></div>
        </div>
    <?php else: ?>
        <?= getContainerErrorMsg(trans('Cette page n\'existe pas')); ?>
    <?php endif; ?>
<?php else: ?>
    <?= trans('Cette page n\'existe pas'); ?>
<?php endif; ?>
<?php require('footer.php'); ?>
