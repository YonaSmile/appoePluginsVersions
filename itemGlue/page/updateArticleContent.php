<?php

use App\Category;
use App\CategoryRelations;
use App\Plugin\ItemGlue\Article;
use App\Plugin\ItemGlue\ArticleMedia;
use App\Plugin\ItemGlue\ArticleRelation;

require('header.php');
if (!empty($_GET['id'])):

    require(ITEMGLUE_PATH . 'process/postProcess.php');
    $Article = new Article();
    $Article->setId($_GET['id']);
    $Article->setLang(APP_LANG);

    if ($Article->show()):

        $Article->setStatut(1);
        $allArticles = $Article->showAll(false, false, 'fr');

        $Category = new Category();
        $Category->setType('ITEMGLUE');
        $listCatgories = extractFromObjToArrForList($Category->showByType(), 'id');

        $CategoryRelation = new CategoryRelations('ITEMGLUE', $Article->getId());
        $allCategoryRelations = extractFromObjToSimpleArr($CategoryRelation->getData(), 'categoryId', 'name');

        $ArticleMedia = new ArticleMedia($Article->getId());
        $ArticleMedia->setLang(APP_LANG);
        $allArticleMedias = $ArticleMedia->showFiles();

        $listUsers = extractFromObjToSimpleArr(getAllUsers(true), 'id', 'nom', 'prenom');
        $allRelations = '';

        $ArticleRelation = new ArticleRelation($Article->getId(), 'USERS');
        if ($ArticleRelation) {
            $allRelations = extractFromObjToSimpleArr($ArticleRelation->getData(), 'typeId', 'typeId');
        }

        echo getTitle($Article->getName(), getAppPageSlug());
        showPostResponse();
        ?>
        <select class="custom-select custom-select-sm otherArticlesSelect otherProjetSelect notPrint float-right"
                title="<?= trans('Parcourir les autres articles'); ?>...">
            <option selected="selected" disabled><?= trans('Parcourir les autres articles'); ?>
                ...
            </option>
            <?php if ($allArticles):
                foreach ($allArticles as $article):
                    if ($Article->getId() != $article->id): ?>
                        <option data-href="<?= getPluginUrl('itemGlue/page/articleContent/', $article->id); ?>"><?= $article->name; ?></option>
                    <?php endif;
                endforeach;
            endif; ?>
        </select>
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a class="nav-item nav-link sidebarLink colorPrimary <?= !empty($contentTabActive) ? 'active' : ''; ?>"
                   id="nav-allLibraries-tab" data-toggle="tab"
                   href="#nav-allLibraries"
                   role="tab" aria-controls="nav-allLibraries"
                   aria-selected="true"><?= trans('Contenu de l\'article'); ?></a>
                <a class="nav-item nav-link sidebarLink colorPrimary <?= !empty($mediaTabactive) ? 'active' : ''; ?>"
                   id="nav-newFiles-tab" data-toggle="tab" href="#nav-newFiles" role="tab"
                   aria-controls="nav-newFiles" aria-selected="false"><?= trans('Les médias'); ?></a>
                <a class="nav-item nav-link sidebarLink colorPrimary"
                   id="nav-extra-tab" data-toggle="tab" href="#nav-extra" role="tab"
                   aria-controls="nav-extra" aria-selected="false"><?= trans('Détails supplémentaires'); ?></a>
                <a class="nav-item nav-link sidebarLink colorPrimary <?= !empty($relationActive) ? 'active' : ''; ?>"
                   id="nav-relation-tab" data-toggle="tab" href="#nav-relation" role="tab"
                   aria-controls="nav-relation" aria-selected="false"><?= trans('Association'); ?></a>
            </div>
        </nav>
        <div class="tab-content border border-top-0 bg-white py-3 mb-2" id="nav-mediaTabContent">
            <div class="tab-pane fade <?= !empty($contentTabActive) ? 'active show' : ''; ?>"
                 id="nav-allLibraries" role="tabpanel"
                 aria-labelledby="nav-allLibraries-tab">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <a id="updateArticleBtn" data-toggle="modal" data-target="#updateArticleHeadersModal"
                               href="<?= getPluginUrl('itemGlue/page/update/', $Article->getId()); ?>"
                               class="btn btn-warning btn-sm">
                                <span class="fas fa-wrench"></span> <?= trans('Modifier les en-têtes'); ?>
                            </a>
                            <?php if (defined('DEFAULT_ARTICLES_PAGE')): ?>
                                <a href="<?= webUrl(DEFAULT_ARTICLES_PAGE . '/', $Article->getSlug()); ?>"
                                   class="btn btn-primary btn-sm" target="_blank">
                                    <span class="fas fa-external-link-alt"></span> <?= trans('Visualiser l\'article'); ?>
                                </a>
                                <?php if (pluginExist('twitter')): ?>
                                    <button type="button" class="btn btn-primary btn-sm notPrint float-right ml-1"
                                            id="articleTwitterShareButton"
                                            data-toggle="modal" data-target="#modalTwitterManager"
                                            data-share-link="<?= $Article->getSlug(); ?>">
                                        <i class="fab fa-twitter"></i>
                                    </button>
                                <?php endif;
                            endif; ?>
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
                                                  class="ckeditor"><?= html_entity_decode($Article->getContent()); ?></textarea>
                                </div>
                                <div class="my-4"></div>
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
            <div class="tab-pane fade <?= !empty($mediaTabactive) ? 'active show' : ''; ?>"
                 id="nav-newFiles" role="tabpanel" aria-labelledby="nav-newFiles-tab">
                <div class="container-fluid">
                    <form class="row" id="galleryArticleForm" action="" method="post" enctype="multipart/form-data">
                        <?= getTokenField(); ?>
                        <input type="hidden" name="articleId" value="<?= $Article->getId(); ?>">
                        <div class="col-12 col-lg-6 my-2">
                            <?= \App\Form::file('Importer depuis votre appareil', 'inputFile[]', false, 'multiple', '', 'Choisissez...', false); ?>
                        </div>
                        <div class="col-12 col-lg-6 my-2">
                                <textarea name="textareaSelectedFile" id="textareaSelectedFile"
                                          class="d-none"></textarea>
                            <?= \App\Form::text('Choisissez dans la bibliothèque', 'inputSelectFiles', 'text', '0 fichiers', false, 300, 'readonly data-toggle="modal" data-target="#allMediasModal"'); ?>
                        </div>
                        <div class="col-12">
                            <?= \App\Form::target('ADDIMAGESTOARTICLE'); ?>
                            <?= \App\Form::submit('Enregistrer', 'ADDIMAGESTOARTICLESUBMIT'); ?>
                        </div>
                    </form>
                    <?php if ($allArticleMedias): ?>
                        <hr class="my-4 mx-5">
                        <div class="card-columns">
                            <?php foreach ($allArticleMedias as $file): ?>
                                <div class="card bg-none border-0 my-1">
                                    <?php if (isImage(FILE_DIR_PATH . $file->name)): ?>
                                        <img src="<?= getThumb($file->name, 370); ?>"
                                             alt="<?= $file->title; ?>"
                                             data-originsrc="<?= WEB_DIR_INCLUDE . $file->name; ?>"
                                             data-filename="<?= $file->name; ?>"
                                             class="img-fluid img-thumbnail seeOnOverlay seeDataOnHover">
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
                                        <?= \App\Form::text('Titre', 'title', 'text', $file->title, false, 255, '', '', 'form-control-sm imageTitle upImgForm', 'Titre'); ?>
                                        <?= \App\Form::textarea('Description', 'description', $file->description, 1, false, '', 'form-control-sm imageDescription upImgForm', 'Description'); ?>
                                        <?= \App\Form::text('Lien', 'link', 'url', $file->link, false, 255, '', '', 'form-control-sm imagelink upImgForm', 'Lien'); ?>
                                        <?= \App\Form::text('Position', 'position', 'text', $file->position, false, 5, '', '', 'form-control-sm imagePosition upImgForm', 'Position'); ?>
                                        <select class="custom-select custom-select-sm templatePosition form-control-sm upImgForm"
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
            <div class="tab-pane fade" id="nav-extra" role="tabpanel" aria-labelledby="nav-extra-tab">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div id="metaArticleContenair"></div>
                        </div>
                        <div class="col-12 col-lg-6" style="box-shadow: -100px 0px 6px -100px #ccc;">
                            <div class="row">
                                <div class="col-12 my-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" checked="checked"
                                               name="addMetaData"
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
            </div>
            <div class="tab-pane fade <?= !empty($relationActive) ? 'active show' : ''; ?>" id="nav-relation"
                 role="tabpanel" aria-labelledby="nav-relation-tab">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                            <form method="post" id="relationUsersForm">
                                <?= getTokenField(); ?>
                                <input type="hidden" name="articleId" value="<?= $Article->getId(); ?>">
                                <?= \App\Form::checkbox('Utilisateurs', 'userRelation', $listUsers, $allRelations); ?>
                                <div class="my-4"></div>
                                <?= \App\Form::target('RELATIONUSERS'); ?>
                                <?= \App\Form::submit('Associer', 'RELATIONUSERSSUBMIT'); ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
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
        <div class="modal fade" id="updateArticleHeadersModal" tabindex="-1" role="dialog"
             aria-labelledby="updateArticleHeadersModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="" method="post" id="updateArticleHeadersForm">
                        <div class="modal-header">
                            <h5 class="modal-title"
                                id="updateArticleHeadersModalLabel"><?= trans('Modifier les en têtes'); ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="custom-control custom-checkbox my-3">
                                <input type="checkbox" class="custom-control-input" id="updateSlugAuto">
                                <label class="custom-control-label"
                                       for="updateSlugAuto"><?= trans('Mettre à jour le lien de l\'article automatiquement'); ?></label>
                            </div>

                            <?= getTokenField(); ?>
                            <input type="hidden" name="id" value="<?= $Article->getId(); ?>">
                            <div class="row my-2">
                                <div class="col-12 my-2">
                                    <?= \App\Form::text('Nom', 'name', 'text', $Article->getName(), true, 70); ?>
                                </div>
                                <div class="col-12 my-2">
                                    <?= \App\Form::textarea('Description', 'description', $Article->getDescription(), 2, true, 'maxlength="158"'); ?>
                                </div>
                                <div class="col-12 my-2">
                                    <?= \App\Form::text('Nom du lien URL' . ' (slug)', 'slug', 'text', $Article->getSlug(), true, 100); ?>
                                </div>
                                <hr class="hrStyle">
                                <div class="col-12 my-2">
                                    <?= \App\Form::text('Date de création', 'createdAt', 'date', $Article->getCreatedAt(), true, 10); ?>
                                </div>
                                <div class="col-12 my-2">
                                    <?= \App\Form::radio('Statut de l\'article', 'statut', array_map('trans', ITEMGLUE_ARTICLES_STATUS), $Article->getStatut(), true); ?>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <?= \App\Form::target('UPDATEARTICLEHEADERS'); ?>
                            <button type="submit" name="UPDATEARTICLEHEADERSSUBMIT"
                                    class="btn btn-outline-info"><?= trans('Enregistrer'); ?></button>
                        </div>
                    </form>
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

                $('.upImgForm').on('input', function () {

                    busyApp();

                    $('small.infosMedia').hide().html('');
                    var $input = $(this);
                    var $form = $input.closest('form');
                    var idImage = $form.data('imageid');
                    var title = $form.find('input.imageTitle').val();
                    var description = $form.find('textarea.imageDescription').val();
                    var link = $form.find('input.imagelink').val();
                    var position = $form.find('input.imagePosition').val();
                    var typeId = $form.find('input.typeId').val();
                    var templatePosition = $form.find('select.templatePosition').val();
                    var $info = $form.find('small.infosMedia');
                    $info.html('');

                    delay(function () {
                        $.post(
                            '/app/ajax/media.php',
                            {
                                updateDetailsImg: 'OK',
                                idImage: idImage,
                                title: title,
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
                        );
                    }, 300);
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

                $('#updateSlugAuto').on('change', function () {
                    $('form#updateArticleHeadersForm input#slug').val(convertToSlug($('form#updateArticleHeadersForm input#name').val()));
                });

                $('form#updateArticleHeadersForm input#name').on('input', function () {
                    if ($('form#updateArticleHeadersForm #updateSlugAuto').is(':checked')) {
                        $('form#updateArticleHeadersForm input#slug').val(convertToSlug($(this).val()));
                    }
                });
            });
        </script>
    <?php else:
        echo getContainerErrorMsg(trans('Cette page n\'existe pas'));
    endif;
else:
    echo trans('Cette page n\'existe pas');
endif;
require('footer.php'); ?>