<?php
require('header.php');
if (!empty($_GET['id'])): ?>
    <?php
    require(ITEMGLUE_PATH . 'process/postProcess.php');
    $Article = new App\Plugin\ItemGlue\Article();
    $Article->setId($_GET['id']);
    if ($Article->show()) : ?>
        <?php
        $ArticlesBrowse = new App\Plugin\ItemGlue\Article();
        $ArticlesBrowse->setStatut(1);
        $allArticles = $ArticlesBrowse->showAll();

        $ArticleContent = new App\Plugin\ItemGlue\ArticleContent($Article->getId(), LANG);

        $Category = new App\Category();
        $Category->setType('ITEMGLUE');
        $listCatgories = extractFromObjToArrForList($Category->showByType(), 'id');

        $CategoryRelation = new App\CategoryRelations('ITEMGLUE', $Article->getId());
        $allCategoryRelations = extractFromObjToSimpleArr($CategoryRelation->getData(), 'categoryId', 'name');

        $ArticleMedia = new App\Plugin\ItemGlue\ArticleMedia($Article->getId());
        $allArticleMedias = $ArticleMedia->showFiles();
        ?>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="bigTitle"><?= $Article->getName(); ?></h1>
                    <hr class="mb-2">
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <?php if ($Menu->checkUserPermission($USER->getRole(), 'updateArticle')): ?>
                        <a id="updateArticleBtn"
                           href="<?= getPluginUrl('itemGlue/page/update/', $Article->getId()); ?>"
                           class="btn btn-warning btn-sm">
                            <span class="fas fa-cog"></span> <?= trans('Modifier l\'article'); ?>
                        </a>
                    <?php endif; ?>
                    <select class="custom-select otherArticlesSelect otherProjetSelect notPrint float-right"
                            title="<?= trans('Parcourir les articles'); ?>...">
                        <option selected="selected" disabled><?= trans('Parcourir les articles'); ?>...</option>
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
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered bg-white">
                            <tr class="table-info-light">
                                <th><?= trans('Nom'); ?></th>
                                <th><?= trans('Slug'); ?></th>
                                <th><?= trans('Statut de l\'article'); ?></th>
                            </tr>
                            <tr>
                                <td><?= $Article->getName(); ?></td>
                                <td><?= $Article->getSlug(); ?></td>
                                <td><?= ITEMGLUE_ARTICLES_STATUS[$Article->getStatut()] ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="my-2"></div>
            <?php if (isset($Response)): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-<?= $Response->display()->status ?>" role="alert">
                            <?= $Response->display()->error_msg; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="my-1"></div>
            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="row">
                        <div class="col-12">
                            <h2 class="subTitle"><?= trans('Contenu de l\'article'); ?></h2>
                        </div>
                    </div>
                    <form action="" id="contentArticleManage" class="row" method="post">
                        <?= getTokenField(); ?>
                        <input type="hidden" name="articleId" value="<?= $Article->getId(); ?>">
                        <div class="col-12">
                        <textarea name="articleContent" id="articleContent"
                                  class="ckeditor"><?= html_entity_decode($ArticleContent->getContent()); ?></textarea>
                        </div>
                        <div class="my-2"></div>
                        <div class="col-12">
                            <?= App\Form::checkbox(trans('Catégories'), 'categories', $listCatgories, $allCategoryRelations, 'checkCategories'); ?>
                        </div>
                        <div class="col-12">
                            <?= App\Form::submit(trans('Enregistrer'), 'SAVEARTICLECONTENT'); ?>
                        </div>
                    </form>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="row">
                        <div class="col-12">
                            <h2 class="subTitle"><?= trans('Médias de l\'article'); ?></h2>
                        </div>
                    </div>
                    <form class="row" id="galleryArticleForm" action="" method="post" enctype="multipart/form-data">
                        <?= getTokenField(); ?>
                        <input type="hidden" name="articleId" value="<?= $Article->getId(); ?>">
                        <div class="col-12">
                            <?= App\Form::text(trans('Sélection des médias'), 'inputFile[]', 'file', '', true, 800, 'multiple'); ?>
                        </div>
                        <div class="col-12">
                            <?= App\Form::submit(trans('Enregistrer'), 'ADDIMAGESTOARTICLE'); ?>
                        </div>
                    </form>
                    <?php
                    if ($allArticleMedias): ?>
                        <hr class="mt-2 mb-3 mx-5">
                        <div class="card-columns" style="column-count:2">
                            <?php foreach ($allArticleMedias as $file): ?>
                                <div class="card bg-none border-0 my-1">
                                    <?php if (isImage(FILE_DIR_PATH . $file->name)): ?>
                                        <img src="<?= FILE_DIR_URL . $file->name; ?>"
                                             alt="<?= $file->description; ?>"
                                             class="img-fluid img-thumbnail seeOnOverlay">
                                    <?php else: ?>
                                        <a href="<?= FILE_DIR_URL . $file->name; ?>" target="_blank">
                                            <img src="<?= getImgAccordingExtension(getFileExtension($file->name)); ?>">
                                        </a>
                                        <small class="fileLink" data-src="<?= FILE_DIR_URL . $file->name; ?>">
                                            <button class="btn btn-sm btn-outline-info btn-block copyLinkOnClick">
                                                <?= trans('Copier le lien du média'); ?>
                                            </button>
                                        </small>
                                    <?php endif; ?>
                                    <form method="post" data-imageid="<?= $file->id; ?>">
                                        <input type="text" name="description"
                                               class="form-control imageDescription"
                                               value="<?= $file->description; ?>"
                                               placeholder="<?= trans('Description'); ?>">
                                        <input type="url" name="link" class="form-control imagelink"
                                               value="<?= $file->link; ?>"
                                               placeholder="<?= trans('Lien'); ?>">
                                        <input type="tel" name="position" class="form-control imagePosition"
                                               value="<?= $file->position; ?>"
                                               placeholder="<?= trans('Position'); ?>">
                                        <input type="hidden" class="typeId" name="typeId"
                                               value="<?= $file->typeId; ?>">
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
                    <?php endif; ?>
                    <?php if ($USER->getRole() > 3): ?>
                        <div class="row">
                            <div class="col-12 my-3">
                                <h2 class="subTitle" style="position: relative">
                                    <?= trans('Détails de l\'article'); ?>
                                    <button id="addMetaArticleBtn" type="button" class="btn btn-outline-info btn-sm"
                                            data-toggle="modal" style="position: absolute;bottom: 0;right: 0;"
                                            data-target="#modalAddArticleMeta">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </h2>
                            </div>
                            <?php
                            $ArticleMeta = new App\Plugin\ItemGlue\ArticleMeta($Article->getId());
                            if (!is_null($ArticleMeta->getData())): ?>
                                <div class="col-12 d-flex flex-column" id="articleMetaContainer">
                                    <?php foreach ($ArticleMeta->getData() as $data): ?>
                                        <div class="d-flex align-items-stretch my-1 fileContent">
                                            <span class="bg-info p-2 text-white"><?= $data->metaKey; ?></span><span
                                                    class="bg-secondary p-2 text-white"><?= $data->metaValue; ?></span>
                                            <button type="button" class="deleteArticleMeta btn btn-danger btn-sm"
                                                    style="position: absolute; top: 0; right: 0;"
                                                    data-idmetaarticle="<?= $data->id; ?>">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="my-4"></div>
        <div class="modal fade" id="modalAddArticleMeta" tabindex="-1" role="dialog"
             aria-labelledby="modalAddArticleMetaTitle"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="" method="post" id="addArticleMetaForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalAddArticleMetaTitle">Ajouter un détail</h5>
                        </div>
                        <div class="modal-body" id="modalArticleMetaBody">
                            <input type="hidden" name="idArticle" value="<?= $Article->getId(); ?>">
                            <input type="hidden" name="ADDARTICLEMETA" value="OK">
                            <?= getTokenField(); ?>
                            <div class="row">
                                <div class="col-12 col-8 my-2">
                                    <?= App\Form::text(trans('Clef'), 'metaKey', 'text', !empty($_POST['metaKey']) ? $_POST['metaKey'] : '', true, 150); ?>
                                </div>
                                <div class="col-12 col-4 my-2">
                                    <?= App\Form::text(trans('Valeur'), 'metaValue', 'text', !empty($_POST['metaValue']) ? $_POST['metaValue'] : '', true, 300); ?>
                                </div>
                                <div class="col-12 col-4 my-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="addTradValue"
                                               id="customCheck1" checked>
                                        <label class="custom-control-label"
                                               for="customCheck1"><?= trans('Ajouter une traduction'); ?></label>
                                    </div>
                                </div>
                                <script>
                                    $(document).ready(function () {
                                        $('input#metaKey').keyup(function () {
                                            $(this).val(convertToSlug($(this).val()));
                                        });
                                    });
                                </script>
                            </div>
                            <div id="addArticleMetaError"></div>
                        </div>
                        <div class="modal-footer" id="modalArticleMetaFooter">
                            <button type="submit" name="ADDARTICLEMETA"
                                    class="btn btn-primary"><?= trans('Enregistrer'); ?></button>
                            <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal"><?= trans('Fermer'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $(document).ready(function () {

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

                $('input.imageDescription, input.imagelink, input.imagePosition').on('keyup', function () {
                    busyApp();
                    $('small.infosMedia').hide().html('');
                    var $input = $(this);
                    var $form = $input.parent('form');
                    var idImage = $form.data('imageid');
                    var description = $form.children('input.imageDescription').val();
                    var link = $form.children('input.imagelink').val();
                    var position = $form.children('input.imagePosition').val();
                    var typeId = $form.children('input.typeId').val();
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

                $('form#addArticleMetaForm').on('submit', function (event) {

                    event.preventDefault();
                    var $btn = $('form#addArticleMetaForm button[type="submit"]');
                    var metaKey = $('input#metaKey');
                    var metaValue = $('input#metaValue');
                    var idArticle = $('input[name="idArticle"]').val();
                    var $errorContainer = $('#addArticleMetaError');

                    if (metaKey.length && metaValue.length) {
                        busyApp();
                        $.post(
                            '<?= ITEMGLUE_URL; ?>process/ajaxProcess.php',
                            $('form#addArticleMetaForm').serialize(),
                            function (data) {
                                if ($.isNumeric(data)) {
                                    $btn.html('Ajouter').removeClass('disabled').attr('disabled', false);
                                    $('#articleMetaContainer').append(
                                        '<div class="d-flex align-items-stretch my-1 fileContent">'
                                        + '<button type="button" class="deleteBtn deleteArticleMeta" '
                                        + ' data-idmetaarticle="' + data + '">&times;</button>'
                                        + '<span class="bg-info p-2 text-white">'
                                        + metaKey.val()
                                        + '</span><span class="bg-secondary p-2 text-white">'
                                        + metaValue.val()
                                        + '</span></div>');
                                    availableApp();
                                } else {
                                    $errorContainer.html(data);
                                }
                            }
                        );
                    } else {
                        $errorContainer.html('<?= trans('Tous les champs sont obligatoires'); ?> !');
                    }
                });

                $('#articleMetaContainer').on('click', '.deleteArticleMeta', function (event) {
                    event.preventDefault();
                    event.stopPropagation();

                    var $btn = $(this);
                    var idMetaArticle = $btn.data('idmetaarticle');

                    if (confirm('<?= trans('Vous allez supprimer ce détail'); ?>')) {
                        busyApp();
                        $.post(
                            '<?= ITEMGLUE_URL; ?>process/ajaxProcess.php',
                            {
                                DELETEMETAARTICLE: 'OK',
                                idMetaArticle: idMetaArticle
                            }, function (data) {
                                if (data && (data == 'true' || data === true)) {
                                    $btn
                                        .parent('div')
                                        .fadeOut('fast')
                                        .delay(100)
                                        .removeClass('d-flex');
                                    availableApp();
                                }
                            }
                        )
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
