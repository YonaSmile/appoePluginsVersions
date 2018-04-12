<?php require('header.php');
if (!empty($_GET['id'])): ?>
    <?php
    $Article = new App\Plugin\ItemGlue\Article();
    $Article->setId($_GET['id']);
    if ($Article->show()) : ?>
        <?php require(ITEMGLUE_PATH. 'process/postProcess.php'); ?>
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
            <form action="" method="post" id="updatePageForm">
                <?= getTokenField(); ?>
                <input type="hidden" name="id" value="<?= $Article->getId(); ?>">
                <div class="row">
                    <div class="col-12 my-2">
                        <?= App\Form::text(trans('Nom'), 'name', 'text', $Article->getName(), true, 100); ?>
                    </div>
                    <div class="col-12 my-2">
                        <?= App\Form::text(trans('Description'), 'description', 'text', $Article->getDescription(), true, 100); ?>
                    </div>
                    <div class="col-12 my-2">
                        <?= App\Form::text(trans('Nom du lien URL') . ' (slug)', 'slug', 'text', $Article->getSlug(), true, 100); ?>
                    </div>
                    <div class="col-12 my-2">
                        <?= App\Form::radio(trans('Statut de l\'article'), 'statut', array_map('trans', ITEMGLUE_ARTICLES_STATUS), $Article->getStatut(), true); ?>
                    </div>
                </div>
                <div class="my-2"></div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" name="UPDATEARTICLE" class="btn btn-outline-primary btn-block btn-lg">
                            <?= trans('Enregistrer'); ?>
                        </button>
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
    <?php else: ?>
        <?= getContainerErrorMsg(trans('Cette page n\'existe pas')); ?>
    <?php endif; ?>
<?php else: ?>
    <?= trans('Cette page n\'existe pas'); ?>
<?php endif; ?>
<?php require('footer.php'); ?>
