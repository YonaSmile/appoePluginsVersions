<?php require('header.php');
require(ITEMGLUE_PATH . 'process/postProcess.php');
echo getTitle($Page->getName(), $Page->getSlug());
showPostResponse(getDataPostResponse());
?>
    <form action="" method="post" id="addArticleForm">
        <?= getTokenField(); ?>
        <div class="row d-flex align-items-end my-2">
            <div class="col-12 col-lg-8">
                <div class="row">
                    <div class="col-12 my-2">
                        <?= \App\Form::text('Nom', 'name', 'text', !empty($_POST['name']) ? $_POST['name'] : '', true); ?>
                    </div>
                    <div class="col-12 my-2">
                        <?= \App\Form::text('Description', 'description', 'text', !empty($_POST['description']) ? $_POST['description'] : '', true); ?>
                    </div>
                    <div class="col-12 mt-2">
                        <?= \App\Form::text('Nom du lien URL' . ' (slug)', 'slug', 'text', !empty($_POST['slug']) ? $_POST['slug'] : '', true); ?>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4 bgColorPrimary">
                <div class="row">
                    <div class="col-12 pt-2 pb-3">
                        <?= \App\Form::radio('Statut de l\'article', 'statut', array_map('trans', ITEMGLUE_ARTICLES_STATUS), !empty($_POST['statut']) ? $_POST['statut'] : 1, true); ?>
                    </div>
                    <div class="col-12 mb-3">
                        <?= \App\Form::target('ADDARTICLE'); ?>
                        <?= \App\Form::submit('Enregistrer', 'ADDARTICLESUBMIT', 'btn-light'); ?>
                    </div>
                </div>
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
                $('input#description').val($(this).val());
            });
        });
    </script>
<?php require('footer.php'); ?>