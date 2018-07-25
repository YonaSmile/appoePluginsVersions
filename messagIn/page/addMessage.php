<?php require('header.php'); ?>
<?= getTitle($Page->getName(), $Page->getSlug()); ?>
    <div class="container">
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
                    <p class="p-3"><?= trans('De la part de'); ?> <?= $USER->getNom() . ' ' . $USER->getPrenom(); ?></p>
                </div>

                <div class="col-12 col-lg-6">
                    <?php $allUsers = $USER->showAll(); ?>
                    <div class="form-group">
                        <label for="toUser"><?= trans('destiné à'); ?></label>
                        <select class="form-control custom-select" id="toUser" name="toUser" required>
                            <?php foreach ($allUsers as $user): ?>
                                <?php if ($USER->getId() != $user->id): ?>
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
                    <?= App\Form::textarea('Texte', 'text', '', 5, true, '', 'ckeditor'); ?>
                </div>
            </div>

            <div class="my-4"></div>
            <div class="row">
                <div class="col-12">
                    <?= App\Form::target('ADDMESSAGE'); ?>
                    <?= App\Form::submit('Envoyer', 'ADDMESSAGESUBMIT'); ?>
                </div>
            </div>
        </form>
        <div class="my-4"></div>
    </div>
<?php require('footer.php'); ?>