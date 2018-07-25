<?php
require('header.php');
require(CMS_PATH . 'process/postProcess.php');
?>
<?= getTitle($Page->getName(), $Page->getSlug()); ?>
<div class="container">
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
        <div class="row d-flex align-items-end">
            <div class="col-12 col-lg-8">
                <div class="row">
                    <div class="col-12 my-2">
                        <?= App\Form::text('Nom', 'name', 'text', !empty($_POST['name']) ? $_POST['name'] : '', true, 70); ?>
                    </div>
                    <div class="col-12 my-2">
                        <?= App\Form::text('Description', 'description', 'text', !empty($_POST['description']) ? $_POST['description'] : '', true, 300); ?>
                    </div>
                    <div class="col-12 mt-2">
                        <?= App\Form::text('Nom du lien URL' . ' (slug)', 'slug', 'text', !empty($_POST['slug']) ? $_POST['slug'] : '', true, 100); ?>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4 bgColorPrimary">
                <div class="row">
                    <div class="col-12 p-3">
                        <?= App\Form::radio('Statut de la page', 'statut', array_map('trans', CMS_PAGE_STATUS), !empty($_POST['statut']) ? $_POST['statut'] : 1, true); ?>
                    </div>
                    <div class="col-12 mb-3 mt-3">
                        <?= App\Form::target('ADDPAGE'); ?>
                        <?= App\Form::submit('Enregistrer', 'addPageSubmit', 'btn-light'); ?>
                    </div>
                </div>
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
