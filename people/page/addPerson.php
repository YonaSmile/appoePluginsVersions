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
        <?= addPersonForm(isset($_POST) ? $_POST : array(), array('nameR' => true)); ?>
        <div class="my-4"></div>
    </div>
<?php require('footer.php'); ?>