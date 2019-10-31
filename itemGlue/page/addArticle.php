<?php require('header.php');
require(ITEMGLUE_PATH . 'process/postProcess.php');
echo getTitle(getAppPageName(), getAppPageSlug());
showPostResponse(getDataPostResponse());
?>
    <form action="" method="post" id="addArticleForm">
        <?= getTokenField(); ?>
        <div class="row my-2">
            <div class="col-12 col-lg-6 my-2">
                <?= \App\Form::text('Nom', 'name', 'text', !empty($_POST['name']) ? $_POST['name'] : '', true, 70); ?>
                <?= \App\Form::text('Nom du lien URL (slug)', 'slug', 'text', !empty($_POST['slug']) ? $_POST['slug'] : '', true, 70); ?>
            </div>
            <div class="col-12 col-lg-6 my-2">
                <?= \App\Form::textarea('Description', 'description', !empty($_POST['description']) ? $_POST['description'] : '', 3, true, 'maxlength="158"'); ?>
            </div>
            <div class="col-12 p-3">
                <?= \App\Form::radio('Statut de l\'article', 'statut', array_map('trans', ITEMGLUE_ARTICLES_STATUS), !empty($_POST['statut']) ? $_POST['statut'] : 1, true); ?>
            </div>
            <div class="col-12 mb-3">
                <?= \App\Form::target('ADDARTICLE'); ?>
                <?= \App\Form::submit('Enregistrer', 'ADDARTICLESUBMIT', 'btn-outline-info'); ?>
            </div>
        </div>
    </form>
    <script>
        $(document).ready(function () {
            setTimeout(function () {
                $('input#name').focus();
            }, 100);
            $('input#name').keyup(function () {
                $('input#slug').val(convertToSlug($(this).val()));
                $('textarea#description').val($(this).val());
            });
        });
    </script>
<?php require('footer.php'); ?>