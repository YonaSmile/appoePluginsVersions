<?php require('header.php');
if (!empty($_GET['id'])): ?>
    <?php
    $Cms = new App\Plugin\Cms\Cms();
    $Cms->setId($_GET['id']);
    if ($Cms->show()) : ?>
        <?php require(CMS_PATH . 'process/postProcess.php'); ?>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <a href="<?= getPluginUrl('cms/page/pageContent/', $Cms->getId()) ?>"
                       class="btn btn-info btn-sm float-right"
                       title="Consulter">
                        <span class="fa fa-eye"></span> <?= trans('Consulter la page'); ?>
                    </a>
                    <h1 class="bigTitle"><?= trans('Mise à jour de la page'); ?></h1>
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
                <input type="hidden" name="id" value="<?= $Cms->getId(); ?>">
                <div class="row">
                    <div class="col-12 my-2">
                        <?= App\Form::text(trans('Nom'), 'name', 'text', $Cms->getName(), true, 70); ?>
                    </div>
                    <div class="col-12 my-2">
                        <?= App\Form::text(trans('Description'), 'description', 'text', $Cms->getDescription(), true, 300); ?>
                    </div>
                    <div class="col-12 my-2">
                        <?= App\Form::text(trans('Nom du lien URL') . ' (slug)', 'slug', 'text', $Cms->getSlug(), true, 100); ?>
                    </div>
                    <div class="col-12 my-2">
                        <?= App\Form::radio(trans('Statut de la page'), 'statut', array_map('trans', CMS_PAGE_STATUS), $Cms->getStatut(), true); ?>
                    </div>
                </div>
                <div class="my-2"></div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" name="UPDATEPAGE" class="btn btn-outline-primary btn-block btn-lg">
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
