<?php require('header.php');
require_once(PEOPLE_PATH . 'process/postProcess.php');
echo getTitle(getAppPageName(), getAppPageSlug());
showPostResponse( getDataPostResponse() ); ?>
    <div class="container">
        <form action="" method="post" id="addPersonForm">
            <?= people_addPersonFormFields(array(), isset($_POST) ? $_POST : array(), array('nameR' => true)); ?>
        </form>
        <div class="my-4"></div>
    </div>
<?php require('footer.php'); ?>