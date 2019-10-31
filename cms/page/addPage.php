<?php
require('header.php');
require(CMS_PATH . 'process/postProcess.php');

$files = getFilesFromDir(WEB_PUBLIC_PATH . 'html/', ['onlyFiles' => true, 'onlyExtension' => 'php', 'noExtensionDisplaying' => true]);

echo getTitle(getAppPageName(), getAppPageSlug());
showPostResponse(getDataPostResponse()); ?>
    <form action="" method="post" id="addPageForm">
        <?= getTokenField(); ?>
        <div class="row my-2">
            <div class="col-12 col-lg-6 my-2">
                <?= \App\Form::text('Nom', 'name', 'text', !empty($_POST['name']) ? $_POST['name'] : '', true, 70); ?>
            </div>
            <div class="col-12 col-lg-6 mt-2">
                <?= \App\Form::text('Nom du lien URL (slug)', 'slug', 'text', !empty($_POST['slug']) ? $_POST['slug'] : '', true, 70); ?>
            </div>
            <div class="col-12 col-lg-6 my-2">
                <?= \App\Form::textarea('Description', 'description', !empty($_POST['description']) ? $_POST['description'] : '', 3, true, 'maxlength="158"'); ?>
            </div>
            <div class="col-12 col-lg-6 mt-2">
                <?= \App\Form::text('Nom du menu', 'menuName', 'text', !empty($_POST['menuName']) ? $_POST['menuName'] : '', true, 40); ?>
                <?= \App\Form::select('Fichier', 'filename', array_combine($files, $files), '', true); ?>
            </div>
            <div class="col-12 p-3">
                <?= \App\Form::radio('Statut de la page', 'statut', array_map('trans', CMS_PAGE_STATUS), !empty($_POST['statut']) ? $_POST['statut'] : 1, true); ?>
            </div>
            <div class="col-12 mb-3 mt-3">
                <?= \App\Form::target('ADDPAGE'); ?>
                <?= \App\Form::submit('Enregistrer', 'addPageSubmit', 'btn-outline-info'); ?>
            </div>
        </div>
    </form>
    <script>
        $(document).ready(function () {
            setTimeout(function () {
                $('input#name').focus();
            }, 100);
            $('input#name').on('input', function () {
                $('input#slug').val(convertToSlug($(this).val()));
                $('textarea#description').val($(this).val());
                $('input#menuName').val($(this).val());
            });
        });
    </script>
<?php require('footer.php'); ?>