<?php require('header.php');
if (!empty($_GET['id'])): ?>
    <?php
    $Article = new App\Plugin\ItemGlue\Article();
    $Article->setId($_GET['id']);
    if ($Article->show()) : ?>
        <?php require(ITEMGLUE_PATH . 'process/postProcess.php'); ?>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <a href="<?= getPluginUrl('itemGlue/page/articleContent/', $Article->getId()) ?>"
                       class="btn btn-info btn-sm float-right"
                       title="Consulter">
                        <span class="fa fa-eye"></span> <?= trans('Consulter l\'article'); ?>
                    </a>
                    <h1 class="bigTitle"><?= trans('Mise à jour de l\'article'); ?></h1>
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
            <div class="custom-control custom-checkbox my-3">
                <input type="checkbox" class="custom-control-input" id="updateSlugAuto">
                <label class="custom-control-label"
                       for="updateSlugAuto"><?= trans('Mettre à jour le lien de l\'article automatiquement'); ?></label>
            </div>
            <form action="" method="post" id="updatePageForm">
                <?= getTokenField(); ?>
                <input type="hidden" name="id" value="<?= $Article->getId(); ?>">
                <div class="row">
                    <div class="col-12 my-2">
                        <?= App\Form::text('Nom', 'name', 'text', $Article->getName(), true, 100); ?>
                    </div>
                    <div class="col-12 my-2">
                        <?= App\Form::text('Description', 'description', 'text', $Article->getDescription(), true, 100); ?>
                    </div>
                    <div class="col-12 my-2">
                        <?= App\Form::text('Nom du lien URL' . ' (slug)', 'slug', 'text', $Article->getSlug(), true, 100); ?>
                    </div>
                    <div class="col-12 my-2">
                        <?= App\Form::radio('Statut de l\'article', 'statut', array_map('trans', ITEMGLUE_ARTICLES_STATUS), $Article->getStatut(), true); ?>
                    </div>
                </div>
                <div class="my-2"></div>
                <div class="row">
                    <div class="col-12">
                        <?= App\Form::target('UPDATEARTICLE'); ?>
                        <?= App\Form::submit('Enregistrer', 'UPDATEARTICLESUBMIT'); ?>
                    </div>
                </div>
            </form>
            <div class="my-4"></div>
        </div>
        <script>
            $(document).ready(function () {

                $('#updateSlugAuto').on('change', function () {
                    $('input#slug').val(convertToSlug($('input#name').val()));
                });

                $('input#name').keyup(function () {
                    if ($('#updateSlugAuto').is(':checked')) {
                        $('input#slug').val(convertToSlug($(this).val()));
                    }
                });
            });
        </script>
    <?php else: ?>
        <?= getContainerErrorMsg(trans('Cette page n\'existe pas')); ?>
    <?php endif; ?>
<?php else: ?>
    <?= trans('Cette page n\'existe pas'); ?>
<?php endif; ?>
<?php require('footer.php'); ?>
