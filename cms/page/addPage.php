<?php
require('header.php');
require(CMS_PATH . 'process/postProcess.php');
?>
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="bigTitle"><?= trans('Nouvelle page'); ?></h1>
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
    <form action="" method="post" id="addPageForm">
        <?= getTokenField(); ?>

        <div class="row">
            <div class="col-12 my-2">
                <?= App\Form::text('Nom', 'name', 'text', !empty($_POST['name']) ? $_POST['name'] : '', true, 70); ?>
            </div>
            <div class="col-12 my-2">
                <?= App\Form::text('Description', 'description', 'text', !empty($_POST['description']) ? $_POST['description'] : '', true, 300); ?>
            </div>
            <div class="col-12 my-2">
                <?= App\Form::text('Nom du lien URL' . ' (slug)', 'slug', 'text', !empty($_POST['slug']) ? $_POST['slug'] : '', true, 100); ?>
            </div>
            <div class="col-12 my-2">
                <?= App\Form::radio('Statut de la page', 'statut', array_map('trans', CMS_PAGE_STATUS), !empty($_POST['statut']) ? $_POST['statut'] : 1, true); ?>
            </div>
        </div>
        <div class="my-2"></div>
        <div class="row">
            <div class="col-12">
                <?= App\Form::target('ADDPAGE'); ?>
                <?= App\Form::submit('Enregistrer', 'addPageSubmit'); ?>
            </div>
        </div>
    </form>
    <div class="my-4"></div>
</div>
<script>
    $(document).ready(function () {
        $('input#name').keyup(function () {
            $('input#slug').val(convertToSlug($(this).val()));
        });
    });
</script>
<?php require('footer.php'); ?>
