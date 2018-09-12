<?php
require('header.php');
if (!empty($_GET['id'])): ?>
    <?php
    require(ITEMGLUE_PATH . 'process/postProcess.php');
    $Article = new \App\Plugin\ItemGlue\Article();
    $Article->setId($_GET['id']);
    if ($Article->show()) : ?>
        <?php
        $ArticlesBrowse = new \App\Plugin\ItemGlue\Article();
        $ArticlesBrowse->setStatut(1);
        $allArticles = $ArticlesBrowse->showAll();

        $ArticleContent = new \App\Plugin\ItemGlue\ArticleContent($Article->getId(), LANG);

        $Category = new \App\Category();
        $Category->setType('ITEMGLUE');
        $listCatgories = extractFromObjToArrForList($Category->showByType(), 'id');

        $CategoryRelation = new \App\CategoryRelations('ITEMGLUE', $Article->getId());
        $allCategoryRelations = extractFromObjToSimpleArr($CategoryRelation->getData(), 'categoryId', 'name');

        $ArticleMedia = new \App\Plugin\ItemGlue\ArticleMedia($Article->getId());
        $allArticleMedias = $ArticleMedia->showFiles();
        ?>
        <?= getTitle($Article->getName(), $Page->getSlug()); ?>
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
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link <?= empty($Response->MediaTabactive) ? 'active' : ''; ?>" id="nav-allLibraries-tab" data-toggle="tab"
                       href="#nav-allLibraries"
                       role="tab" aria-controls="nav-allLibraries"
                       aria-selected="true"><?= trans('Contenu de l\'article'); ?></a>
                    <a class="nav-item nav-link <?= !empty($Response->MediaTabactive) ? 'active' : ''; ?>" id="nav-newFiles-tab" data-toggle="tab" href="#nav-newFiles" role="tab"
                       aria-controls="nav-newFiles" aria-selected="false"><?= trans('Les médias'); ?></a>
                </div>
            </nav>
            <div class="tab-content border-top-0 bg-white py-3" id="nav-mediaTabContent">
                <div class="tab-pane fade <?= empty($Response->MediaTabactive) ? 'active show' : ''; ?>" id="nav-allLibraries" role="tabpanel"
                     aria-labelledby="nav-home-tab">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <?php if ($Menu->checkUserPermission($USER->getRole(), 'updateArticle')): ?>
                                    <a id="updateArticleBtn"
                                       href="<?= getPluginUrl('itemGlue/page/update/', $Article->getId()); ?>"
                                       class="btn btn-warning btn-sm">
                                        <span class="fas fa-wrench"></span> <?= trans('Modifier les en-têtes'); ?>
                                    </a>
                                <?php endif; ?>
                                <button id="addMetaArticleBtn" type="button" class="btn btn-info btn-sm"
                                        data-toggle="modal"
                                        data-target="#modalAddArticleMeta">
                                    <i class="fas fa-list"></i> <?= trans('Données complémentaires'); ?>
                                </button>
                                <select class="custom-select otherArticlesSelect otherProjetSelect notPrint float-right"
                                        title="<?= trans('Parcourir les autres articles'); ?>...">
                                    <option selected="selected" disabled><?= trans('Parcourir les autres articles'); ?>
                                        ...
                                    </option>
                                    <?php if ($allArticles): ?>
                                        <?php foreach ($allArticles as $article): ?>
                                            <?php if ($Article->getId() != $article->id): ?>
                                                <option data-href="<?= getPluginUrl('itemGlue/page/articleContent/', $article->id); ?>"><?= $article->name; ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="my-2"></div>

                        <div class="row">
                            <div class="col-12">
                                <form action="" id="contentArticleManage" class="row" method="post">
                                    <?= getTokenField(); ?>
                                    <input type="hidden" name="articleId" value="<?= $Article->getId(); ?>">
                                    <div class="col-12">
                                        <textarea name="articleContent" id="articleContent"
                                                  class="ckeditor"><?= html_entity_decode($ArticleContent->getContent()); ?></textarea>
                                    </div>
                                    <div class="my-2"></div>
                                    <div class="col-12">
                                        <?= \App\Form::checkbox('Catégories', 'categories', $listCatgories, $allCategoryRelations, 'checkCategories'); ?>
                                    </div>
                                    <div class="col-12">
                                        <?= \App\Form::target('SAVEARTICLECONTENT'); ?>
                                        <?= \App\Form::submit('Enregistrer', 'SAVEARTICLECONTENTSUBMIT'); ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade <?= !empty($Response->MediaTabactive) ? 'active show' : ''; ?>" id="nav-newFiles" role="tabpanel" aria-labelledby="nav-profile-tab">
                    <div class="container-fluid">
                        <form class="row" id="galleryArticleForm" action="" method="post" enctype="multipart/form-data">
                            <?= getTokenField(); ?>
                            <input type="hidden" name="articleId" value="<?= $Article->getId(); ?>">
                            <div class="col-12 col-lg-6 my-2">
                                <?= \App\Form::text('Importer des médias', 'inputFile[]', 'file', '', false, 800, 'multiple'); ?>
                            </div>
                            <div class="col-12 col-lg-6 my-2">
                                <textarea name="textareaSelectedFile" id="textareaSelectedFile"
                                          class="d-none"></textarea>
                                <?= \App\Form::text('Choisissez des médias', 'inputSelectFiles', 'text', '0 fichiers', false, 300, 'readonly data-toggle="modal" data-target="#allMediasModal"'); ?>
                            </div>
                            <div class="col-12">
                                <?= \App\Form::target('ADDIMAGESTOARTICLE'); ?>
                                <?= \App\Form::submit('Enregistrer', 'ADDIMAGESTOARTICLESUBMIT'); ?>
                            </div>
                        </form>
                        <?php
                        if ($allArticleMedias): ?>
                            <hr class="my-4 mx-5">
                            <div class="card-columns" style="column-count:3">
                                <?php foreach ($allArticleMedias as $file): ?>
                                    <div class="card bg-none border-0 my-1">
                                        <?php if (isImage(FILE_DIR_PATH . $file->name)): ?>
                                            <img src="<?= WEB_DIR_INCLUDE . $file->name; ?>"
                                                 alt="<?= $file->description; ?>"
                                                 class="img-fluid img-thumbnail seeOnOverlay">
                                        <?php else: ?>
                                            <a href="<?= WEB_DIR_INCLUDE . $file->name; ?>" target="_blank">
                                                <img src="<?= getImgAccordingExtension(getFileExtension($file->name)); ?>">
                                            </a>
                                            <small class="fileLink" data-src="<?= WEB_DIR_INCLUDE . $file->name; ?>">
                                                <button class="btn btn-sm btn-outline-info btn-block copyLinkOnClick">
                                                    <?= trans('Copier le lien du média'); ?>
                                                </button>
                                            </small>
                                        <?php endif; ?>
                                        <form method="post" data-imageid="<?= $file->id; ?>">
                                            <input type="hidden" class="typeId" name="typeId"
                                                   value="<?= $file->typeId; ?>">
                                            <input type="text" name="description"
                                                   class="form-control form-control-sm imageDescription"
                                                   value="<?= $file->description; ?>"
                                                   placeholder="<?= trans('Description'); ?>">
                                            <input type="url" name="link" class="form-control form-control-sm imagelink"
                                                   value="<?= $file->link; ?>"
                                                   placeholder="<?= trans('Lien'); ?>">
                                            <input type="tel" name="position"
                                                   class="form-control form-control-sm imagePosition"
                                                   value="<?= $file->position; ?>"
                                                   placeholder="<?= trans('Position'); ?>">
                                            <select class="custom-select custom-select-sm templatePosition form-control-sm"
                                                    name="templatePosition">
                                                <?php if (!getSerializedOptions($file->options, 'templatePosition')): ?>
                                                    <option selected value=""><?= trans('Zone du thème'); ?></option>
                                                <?php endif;
                                                foreach (FILE_TEMPLATE_POSITIONS as $filePositionId => $name): ?>
                                                    <option value="<?= $filePositionId; ?>" <?= $filePositionId == getSerializedOptions($file->options, 'templatePosition') ? 'selected' : ''; ?>><?= $name; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <small class="infosMedia"></small>
                                        </form>
                                        <button type="button" class="deleteImage btn btn-danger btn-sm"
                                                data-imageid="<?= $file->id; ?>"
                                                style="position: absolute; top: 0; right: 0;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <hr class="mt-2 mt-3 mb-1 mx-5">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="my-4"></div>
        </div>
        <div class="modal fade" id="allMediasModal" tabindex="-1" role="dialog" aria-labelledby="allMediasModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="allMediasModalLabel"><?= trans('Tous les médias'); ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="allMediaModalContainer"></div>
                    <div class="modal-footer">
                        <button type="button" id="closeAllMediaModalBtn" class="btn btn-secondary" data-dismiss="modal">
                            <?= trans('Fermer et annuler la sélection'); ?></button>
                        <button type="button" id="saveMediaModalBtn" class="btn btn-info" data-dismiss="modal">
                            0 <?= trans('médias'); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalAddArticleMeta" tabindex="-1" role="dialog"
             aria-labelledby="modalAddArticleMetaTitle"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"
                            id="modalAddArticleMetaTitle"><?= trans('Détails de l\'article'); ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="modalArticleMetaBody">
                        <div class="row">
                            <div class="col-12 col-lg-6">
                                <div id="metaArticleContenair"></div>
                            </div>
                            <div class="col-12 col-lg-6">

                                <div class="row">
                                    <div class="col-12 my-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="addMetaData"
                                                   id="metaDataAvailable">
                                            <label class="custom-control-label"
                                                   for="metaDataAvailable"><?= trans('Activer métadonnée'); ?></label>
                                        </div>
                                    </div>
                                </div>

                                <form action="" method="post" id="addArticleMetaForm">
                                    <input type="hidden" name="idArticle" value="<?= $Article->getId(); ?>">
                                    <input type="hidden" name="UPDATEMETAARTICLE" value="">
                                    <?= \App\Form::target('ADDARTICLEMETA'); ?>
                                    <?= getTokenField(); ?>
                                    <div class="row">
                                        <div class="col-12 my-2">
                                            <?= \App\Form::text('Titre', 'metaKey', 'text', '', true, 150); ?>
                                        </div>
                                        <div class="col-12 my-2">
                                            <?= \App\Form::textarea('Contenu', 'metaValue', '', 5, true, '', 'ckeditor'); ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 my-2">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" name="addTradValue"
                                                       id="customCheck1">
                                                <label class="custom-control-label"
                                                       for="customCheck1"><?= trans('Ajouter une traduction'); ?></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-lg-3 my-2">
                                            <button type="reset" name="reset" id="resetmeta"
                                                    class="btn btn-outline-danger btn-block btn-lg">
                                                <?= trans('Annuler'); ?>
                                            </button>
                                        </div>
                                        <div class="col-12 col-lg-9 my-2">
                                            <?= \App\Form::submit('Enregistrer', 'ADDMETAPRODUCTSUBMIT'); ?>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" id="modalArticleMetaFooter">
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal"><?= trans('Fermer'); ?></button>
                    </div>

                </div>
            </div>
        </div>
        <script type="text/javascript">
            $(document).ready(function () {

                $('#allMediaModalContainer').load('/app/ajax/media.php?getAllMedia');

                $('form#galleryArticleForm').submit(function () {
                    $('#loader').fadeIn('fast');
                });

                $('input[name="categories[]"]').each(function () {
                    if ($(this).next('label').text().charAt(0) !== '-') {
                        $(this).parent('.checkCategories').wrap('<div class="mr-5 my-4 pb-2 border-bottom">');
                    } else {
                        $(this).parent('.checkCategories').prev('div').append($(this).parent('.checkCategories'));
                    }
                }).eq(0).parent('.checkCategories').parent('div').parent('div')
                    .addClass('d-flex flex-row justify-content-start flex-wrap my-3')
                    .children('strong.inputLabel').addClass('w-100');

                $('input.imageDescription, input.imagelink, input.imagePosition, select.templatePosition').on('keyup change', function () {
                    busyApp();
                    $('small.infosMedia').hide().html('');
                    var $input = $(this);
                    var $form = $input.parent('form');
                    var idImage = $form.data('imageid');
                    var description = $form.children('input.imageDescription').val();
                    var link = $form.children('input.imagelink').val();
                    var position = $form.children('input.imagePosition').val();
                    var typeId = $form.children('input.typeId').val();
                    var templatePosition = $form.children('select.templatePosition').val();
                    var $info = $form.children('small.infosMedia');
                    $info.html('');

                    $.post(
                        '<?= WEB_DIR; ?>app/ajax/media.php',
                        {
                            updateDetailsImg: 'OK',
                            idImage: idImage,
                            description: description,
                            link: link,
                            position: position,
                            templatePosition: templatePosition,
                            typeId: typeId
                        },
                        function (data) {
                            if (data && (data == 'true' || data === true)) {
                                $info.html('<?= trans('Enregistré'); ?>').show();
                                availableApp();
                            }
                        }
                    )
                });

                $('.deleteImage').on('click', function () {
                    if (confirm('<?= trans('Vous allez supprimer cette image'); ?>')) {
                        busyApp();
                        var $btn = $(this);
                        var idImage = $btn.data('imageid');

                        $.post(
                            '<?= ITEMGLUE_URL; ?>process/ajaxProcess.php',
                            {
                                deleteImage: 'OK',
                                idImage: idImage
                            },
                            function (data) {
                                if (data && (data == 'true' || data === true)) {
                                    $btn.parent('div').fadeOut('fast');
                                    availableApp();
                                }
                            }
                        )
                    }
                });

                CKEDITOR.config.height = 300;

                $('#metaArticleContenair').load('<?= ITEMGLUE_URL; ?>page/getMetaArticle.php?idArticle=<?= $Article->getId(); ?>');

                $('#metaDataAvailable').change(function () {
                    if ($('#metaDataAvailable').is(':checked')) {
                        $('form#addArticleMetaForm input#metaKey').val(convertToSlug($('form#addArticleMetaForm input#metaKey').val()));
                    }
                });

                $('form#addArticleMetaForm input#metaKey').keyup(function () {
                    if ($('#metaDataAvailable').is(':checked')) {
                        $('form#addArticleMetaForm input#metaKey').val(convertToSlug($('form#addArticleMetaForm input#metaKey').val()));
                    }
                });

                $('#resetmeta').on('click', function () {
                    $('input[name="UPDATEMETAARTICLE"]').val('');
                    CKEDITOR.instances.metaValue.setData('');
                    $('form#addArticleMetaForm').trigger('reset');
                });

                $('form#addArticleMetaForm').on('submit', function (event) {
                    event.preventDefault();
                    var $form = $(this);
                    busyApp();

                    var data = {
                        ADDARTICLEMETA: 'OK',
                        UPDATEMETAARTICLE: $('input[name="UPDATEMETAARTICLE"]').val(),
                        idArticle: $('input[name="idArticle"]').val(),
                        metaKey: $('input#metaKey').val(),
                        metaValue: $('#metaDataAvailable').is(':checked')
                            ? CKEDITOR.instances.metaValue.document.getBody().getText()
                            : CKEDITOR.instances.metaValue.getData()
                    };

                    addMetaArticle(data).done(function (results) {
                        if (results == 'true') {
                            //clear form
                            $('#resetmeta').trigger('click');

                            $('#metaArticleContenair')
                                .html(loaderHtml())
                                .load('<?= ITEMGLUE_URL; ?>page/getMetaArticle.php?idArticle=<?= $Article->getId(); ?>');
                        }

                        $('[type="submit"]', $form).attr('disabled', false).html('<?= trans('Enregistrer'); ?>').removeClass('disabled');
                        availableApp();
                    });

                });

                $('#metaArticleContenair').on('click', '.metaProductUpdateBtn', function () {
                    var $btn = $(this);
                    var idMetaArticle = $btn.data('idmetaproduct');

                    var $contenair = $('div.card[data-idmetaproduct="' + idMetaArticle + '"]');
                    var title = $contenair.find('h5 button.metaProductTitle-' + idMetaArticle).text();
                    var content = $contenair.find('div.metaProductContent-' + idMetaArticle).html();

                    $('input[name="UPDATEMETAARTICLE"]').val(idMetaArticle);
                    $('input#metaKey').val($.trim(title));
                    CKEDITOR.instances.metaValue.setData(content);
                });

                $('#metaArticleContenair').on('click', '.metaProductDeleteBtn', function () {
                    var $btn = $(this);
                    var idMetaArticle = $btn.data('idmetaproduct');

                    if (confirm('<?= trans('Êtes-vous sûr de vouloir supprimer cette métadonnée ?'); ?>')) {
                        busyApp();

                        deleteMetaArticle(idMetaArticle).done(function (data) {
                            if (data == 'true') {

                                $('#metaArticleContenair')
                                    .html(loaderHtml())
                                    .load('<?= ITEMGLUE_URL; ?>page/getMetaArticle.php?idArticle=<?= $Article->getId(); ?>');
                            }
                            availableApp();
                        });
                    }
                });

                $('.otherArticlesSelect').change(function () {
                    var otherEventslink = $('option:selected', this).data('href');
                    location.assign(otherEventslink);
                });

                var textDefaultCopyFile = '<?= trans('Copier le lien du média'); ?>';
                $('.copyLinkOnClick').on('click', function (e) {
                    e.preventDefault();
                    $('.copyLinkOnClick').text(textDefaultCopyFile);
                    copyToClipboard($(this).parent().data('src'));
                    $(this).text('<?= trans('copié'); ?>');
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
