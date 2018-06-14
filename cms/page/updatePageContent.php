<?php require('header.php');
if (!empty($_GET['id'])): ?>
    <?php
    $Cms = new App\Plugin\Cms\Cms();
    $Cms->setId($_GET['id']);
    if ($Cms->show()) : ?>
        <?php
        $allCmsPages = $Cms->showAllPages();
        $allPages = extractFromObjArr($allCmsPages, 'id');
        $CmsContent = new App\Plugin\Cms\CmsContent($Cms->getId(), LANG);
        ?>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="bigTitle"><?= $Cms->getName(); ?></h1>
                    <hr class="mb-2">
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <?php if ($Menu->checkUserPermission($USER->getRole(), 'updatePage')): ?>
                        <a id="updatePageBtn"
                           href="<?= getPluginUrl('cms/page/update/', $Cms->getId()); ?>"
                           class="btn btn-warning btn-sm">
                            <span class="fas fa-cog"></span> <?= trans('Modifier la page'); ?>
                        </a>
                    <?php endif; ?>
                    <select class="custom-select otherPagesSelect otherProjetSelect notPrint float-right"
                            title="<?= trans('Parcourir les pages'); ?>...">
                        <option selected="selected" disabled><?= trans('Parcourir les pages'); ?>...</option>
                        <?php foreach ($allCmsPages as $pageSelect): ?>
                            <?php if ($Cms->getId() != $pageSelect->id): ?>
                                <option data-href="<?= getPluginUrl('cms/page/pageContent/', $pageSelect->id); ?>"><?= $pageSelect->name; ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="my-2"></div>
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered bg-white">
                            <tr class="table-info-light">
                                <th><?= trans('Nom'); ?></th>
                                <th><?= trans('Slug'); ?></th>
                                <th><?= trans('Statut de la page'); ?></th>
                            </tr>
                            <tr>
                                <td><?= $Cms->getName(); ?></td>
                                <td><?= $Cms->getSlug(); ?></td>
                                <td><?= CMS_PAGE_STATUS[$Cms->getStatut()] ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="my-2"></div>
            <div class="row">
                <div class="col-12">
                    <h2 class="subTitle text-uppercase"><?= trans('Contenu de la page'); ?></h2>
                </div>
            </div>
            <?php if (file_exists(TEMPLATES_PATH . $Cms->getSlug() . '.php')): ?>
                <form action="" method="post" id="pageContentManageForm">
                    <div class="row">
                        <?= showTemplateZones(TEMPLATES_PATH . $Cms->getSlug() . '.php', $CmsContent->getData()); ?>
                    </div>
                    <div class="my-2"></div>
                    <div class="row">
                        <div class="col-12">
                            <button type="button" class="btn btn-outline-primary btn-block btn-lg">
                                <?= trans('Enregistrer'); ?>
                            </button>
                        </div>
                    </div>
                </form>
            <?php else: ?>
                <p><?= trans('Model manquant'); ?></p>
            <?php endif; ?>
        </div>
        <div class="my-4"></div>

        <div class="modal fade bd-example-modal-lg" id="libraryModal" tabindex="-1" role="dialog"
             aria-labelledby="mediaLibraryModalTitle" aria-hidden="true" data-inputid="">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"
                            id="mediaLibraryModalTitle"><?= trans('Choisissez le fichier média'); ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="libraryModalContent">
                        <div class="spinner">
                            <div class="rect1"></div>
                            <div class="rect2"></div>
                            <div class="rect3"></div>
                            <div class="rect4"></div>
                            <div class="rect5"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">

            function updateCmsContent($input, metaValue) {

                busyApp();

                var idCms = '<?= $Cms->getId(); ?>';
                var idCmsContent = $input.data('idcmscontent');
                var metaKey = $input.attr('id');

                $.post(
                    '<?= CMS_URL; ?>process/ajaxProcess.php',
                    {
                        id: idCmsContent,
                        idCms: idCms,
                        metaKey: metaKey,
                        metaValue: metaValue
                    },
                    function (data) {
                        if (data) {
                            if ($.isNumeric(data)) {
                                $input.data('idcmscontent', data);
                            }

                            $('small.categoryIdFloatContenaire').stop().fadeOut(function () {
                                $('small.' + metaKey).html('<?= trans('Enregistré'); ?>').stop().fadeIn();
                            });
                            availableApp();
                        }
                    }
                )
            }


            $(document).ready(function () {

                var idCmsInput;

                $(document).on('click', '.libraryButton', function () {

                    window.idCmsInput = $(this).data('idcmsinput');

                    $('#libraryModal').modal('show');
                    $('#libraryModalContent').load('<?= CMS_URL; ?>page/getMediaLibrary.php');
                });

                $('form#pageContentManageForm').submit(function (event) {
                    event.preventDefault();
                });

                $.each($('input, textarea'), function () {
                    var id = $(this).attr('name');
                    $('<small class="' + id + ' categoryIdFloatContenaire">').insertAfter($(this));
                });

                for (var i in CKEDITOR.instances) {

                    CKEDITOR.instances[i].on('blur', function () {
                        var id = this.element.$.id;
                        var $input = $('#' + id);
                        var metaValue = this.getData();

                        updateCmsContent($input, metaValue);
                    });

                }

                $('form#pageContentManageForm').on('blur', 'input, textarea', function (event) {
                    event.preventDefault();
                    var $input = $(this);
                    var metaValue = $input.val();

                    updateCmsContent($input, metaValue);
                });

                $('.otherPagesSelect').change(function () {
                    var otherEventslink = $('option:selected', this).data('href');
                    location.assign(otherEventslink);
                });

                $('#libraryModal').on('click', '.copyLinkOnClick', function (e) {
                    e.preventDefault();

                    var src = $(this).parent().data('src');
                    $('input[data-idcmscontent="' + window.idCmsInput + '"]').val(src).trigger('focus');
                    $('#libraryModal').modal('hide');
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
