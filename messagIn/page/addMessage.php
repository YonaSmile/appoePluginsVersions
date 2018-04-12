<?php require('header.php'); ?>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="bigTitle"><?= trans('Nouveau message'); ?></h1>
                <hr class="my-4">
            </div>
        </div>
        <?php require_once('../process/postProcess.php'); ?>

        <?php if (isset($Response)): ?>
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-<?= $Response->display()->status ?>" role="alert">
                        <?= $Response->display()->error_msg; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <form action="" method="post" id="addMessageForm">
            <?= getTokenField(); ?>
            <div class="my-4"></div>
            <div class="row">

                <div class="col-12 col-lg-6">
                    <p class="p-3"><?= trans('De la part de'); ?> <?= $User->getNom() . ' ' . $User->getPrenom(); ?></p>
                </div>

                <div class="col-12 col-lg-6">
                    <?php $allUsers = $User->showAll(); ?>
                    <div class="form-group">
                        <label for="toUser"><?= trans('destiné à'); ?></label>
                        <select class="form-control custom-select" id="toUser" name="toUser" required>
                            <?php foreach ($allUsers as $user): ?>
                                <?php if ($User->getId() != $user->id): ?>
                                    <option value="<?= $user->id; ?>"><?= $user->nom . ' ' . $user->prenom; ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="my-4"></div>
            <div class="row">
                <div class="col-12">
                    <?= App\Form::textarea(trans('Texte'), 'text', '', 5, true); ?>
                </div>
            </div>

            <div class="my-4"></div>
            <div class="row">
                <div class="col-12">
                    <button type="submit" name="ADDMESSAGE"
                            class="btn btn-outline-primary btn-block btn-lg"><?= trans('Envoyer'); ?>
                    </button>
                </div>
            </div>
        </form>
        <div class="my-4"></div>
    </div>
<?php require('footer.php'); ?>