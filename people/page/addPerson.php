<?php require('header.php');
echo getTitle(getAppPageName(), getAppPageSlug()); ?>
    <div class="container">
        <?php require_once(PEOPLE_PATH . 'process/postProcess.php');
        if (isset($Response)): ?>
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-<?= $Response->display()->status ?>" role="alert">
                        <?= $Response->display()->error_msg; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <form action="" method="post" id="addPersonForm">
            <?= people_addPersonFormFields(array(), isset($_POST) ? $_POST : array(), array('nameR' => true)); ?>
        </form>
        <div class="my-4"></div>
    </div>
<?php require('footer.php'); ?>