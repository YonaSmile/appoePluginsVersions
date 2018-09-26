<?php
require('header.php');
if (!empty($_GET['id'])):
    require(SHOP_PATH . 'process/postProcess.php');
    $Product = new \App\Plugin\Shop\Product();
    $Product->setId($_GET['id']);
    if ($Product->show()) :
        $ProductBrowse = new \App\Plugin\Shop\Product();
        $ProductBrowse->setStatus(1);
        $allProduct = $ProductBrowse->showAll();

        $ProductContent = new \App\Plugin\Shop\ProductContent($Product->getId(), LANG);

        $ProductMedia = new \App\Plugin\Shop\ShopMedia($Product->getId());
        $allProductMedias = $ProductMedia->showFiles();

        ?>
        <?= getTitle($Product->getName(), $Page->getSlug()); ?>
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
                    <a class="nav-item nav-link <?= empty($Response->MediaTabactive) ? 'active' : ''; ?>"
                       id="nav-allLibraries-tab" data-toggle="tab"
                       href="#nav-allLibraries"
                       role="tab" aria-controls="nav-allLibraries"
                       aria-selected="true"><?= trans('Contenu de l\'article'); ?></a>
                    <a class="nav-item nav-link <?= !empty($Response->MediaTabactive) ? 'active' : ''; ?>"
                       id="nav-newFiles-tab" data-toggle="tab" href="#nav-newFiles" role="tab"
                       aria-controls="nav-newFiles" aria-selected="false"><?= trans('Les médias'); ?></a>
                </div>
            </nav>
            <div class="tab-content border-top-0 bg-white py-3" id="nav-mediaTabContent">
                <div class="tab-pane fade <?= empty($Response->MediaTabactive) ? 'active show' : ''; ?>"
                     id="nav-allLibraries" role="tabpanel"
                     aria-labelledby="nav-home-tab">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <?php if ($Menu->checkUserPermission($USER->getRole(), 'updateProduct')): ?>
                                    <a id="updateArticleBtn"
                                       href="<?= getPluginUrl('shop/page/updateProduct/', $Product->getId()); ?>"
                                       class="btn btn-warning btn-sm">
                                        <span class="fas fa-cog"></span> <?= trans('Modifier le produit'); ?>
                                    </a>
                                <?php endif; ?>
                                <button type="button" data-toggle="modal" data-target="#modalInfoMetaProduct"
                                        class="btn btn-info btn-sm">
                                    <?= trans('Détails du produit'); ?>
                                </button>
                                <select class="custom-select otherProductsSelect otherProjetSelect notPrint float-right"
                                        title="<?= trans('Parcourir les produits'); ?>...">
                                    <option selected="selected" disabled><?= trans('Parcourir les produits'); ?>...
                                    </option>
                                    <?php if ($allProduct): ?>
                                        <?php foreach ($allProduct as $product): ?>
                                            <?php if ($Product->getId() != $product->id): ?>
                                                <option data-href="<?= getPluginUrl('shop/page/updateProductData/', $product->id); ?>"><?= $product->name; ?></option>
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
                                    <input type="hidden" name="productId" value="<?= $Product->getId(); ?>">
                                    <div class="col-12 mb-2">
                                        <?= \App\Form::text('Résumé', 'resume', 'text', html_entity_decode($ProductContent->getResume()), false, 255); ?>
                                    </div>
                                    <div class="col-12">
                                        <textarea name="productContent" id="productContent"
                                                  class="ckeditor"><?= html_entity_decode($ProductContent->getContent()); ?></textarea>
                                    </div>
                                    <div class="my-2"></div>
                                    <div class="col-12">
                                        <?= \App\Form::target('SAVEPRODUCTCONTENT'); ?>
                                        <?= \App\Form::submit('Enregistrer', 'SAVEPRODUCTCONTENTSUBMIT'); ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade <?= !empty($Response->MediaTabactive) ? 'active show' : ''; ?>"
                     id="nav-newFiles" role="tabpanel" aria-labelledby="nav-profile-tab">
                    <div class="container-fluid">
                        <form class="row" id="galleryArticleForm" action="" method="post" enctype="multipart/form-data">
                            <?= getTokenField(); ?>
                            <input type="hidden" name="productId" value="<?= $Product->getId(); ?>">
                            <div class="col-12 col-lg-6 my-2">
                                <?= \App\Form::text('Importer des médias', 'inputFile[]', 'file', '', false, 800, 'multiple'); ?>
                            </div>
                            <div class="col-12 col-lg-6 my-2">
                                <textarea name="textareaSelectedFile" id="textareaSelectedFile"
                                          class="d-none"></textarea>
                                <?= \App\Form::text('Choisissez des médias', 'inputSelectFiles', 'text', '0 fichiers', false, 300, 'readonly data-toggle="modal" data-target="#allMediasModal"'); ?>
                            </div>
                            <div class="col-12">
                                <?= \App\Form::target('ADDIMAGESTOPRODUCT'); ?>
                                <?= \App\Form::submit('Enregistrer', 'ADDIMAGESTOPRODUCTSUBMIT'); ?>
                            </div>
                        </form>
                        <?php
                        if ($allProductMedias): ?>
                            <hr class="my-4 mx-5">
                            <div class="card-columns" style="column-count:3">
                                <?php foreach ($allProductMedias as $file): ?>
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
        <div class="modal fade" id="modalInfoMetaProduct" tabindex="-1" role="dialog" aria-labelledby="modalTitle"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle"><?= trans('Détails du produit'); ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="modalBody">
                        <div class="row">
                            <div class="col-12 col-lg-6">
                                <div id="metaProductContenair"></div>
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
                                <form action="" method="post" id="addMetaProductForm">
                                    <input type="hidden" name="productId" value="<?= $Product->getId(); ?>">
                                    <?= \App\Form::target('ADDMETAPRODUCT'); ?>
                                    <input type="hidden" name="UPDATEMETAPRODUCT" value="">
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
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            $(document).ready(function () {
                $('#allMediaModalContainer').load('/app/ajax/media.php?getAllMedia');

                $('form#galleryProductForm').submit(function () {
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
                            '<?= SHOP_URL; ?>process/ajaxProcess.php',
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

                $('.otherProductsSelect').change(function () {
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

                CKEDITOR.config.height = 300;

                $('#metaProductContenair').load('/app/plugin/shop/page/getMetaProduct.php?idProduct=<?= $Product->getId(); ?>');

                $('#metaDataAvailable').change(function () {
                    if ($('#metaDataAvailable').is(':checked')) {
                        $('form#addMetaProductForm input#metaKey').val(convertToSlug($('form#addMetaProductForm input#metaKey').val()));
                    }
                });

                $('form#addMetaProductForm input#metaKey').keyup(function () {
                    if ($('#metaDataAvailable').is(':checked')) {
                        $('form#addMetaProductForm input#metaKey').val(convertToSlug($('form#addMetaProductForm input#metaKey').val()));
                    }
                });

                $('#resetmeta').on('click', function () {
                    $('input[name="UPDATEMETAPRODUCT"]').val('');
                    CKEDITOR.instances.metaValue.setData('');
                    $('form#addMetaProductForm').trigger('reset');
                });

                $('form#addMetaProductForm').on('submit', function (event) {
                    event.preventDefault();
                    var $form = $(this);
                    busyApp();

                    var data = {
                        ADDMETAPRODUCT: 'OK',
                        UPDATEMETAPRODUCT: $('input[name="UPDATEMETAPRODUCT"]').val(),
                        productId: $('input[name="productId"]').val(),
                        metaKey: $('input#metaKey').val(),
                        metaValue: $('#metaDataAvailable').is(':checked')
                            ? CKEDITOR.instances.metaValue.document.getBody().getText()
                            : CKEDITOR.instances.metaValue.getData()
                    };

                    addMetaProduct(data).done(function (results) {
                        if (results == 'true') {
                            //clear form
                            $('#resetmeta').trigger('click');

                            $('#metaProductContenair')
                                .html(loaderHtml())
                                .load('/app/plugin/shop/page/getMetaProduct.php?idProduct=<?= $Product->getId(); ?>');
                        }

                        $('[type="submit"]', $form).attr('disabled', false).html('<?= trans('Enregistrer'); ?>').removeClass('disabled');
                        availableApp();
                    });
                });

                $('#metaProductContenair').on('click', '.metaProductUpdateBtn', function () {
                    var $btn = $(this);
                    var idMetaProduct = $btn.data('idmetaproduct');

                    var $contenair = $('div.card[data-idmetaproduct="' + idMetaProduct + '"]');
                    var title = $contenair.find('h5 button.metaProductTitle-' + idMetaProduct).text();
                    var content = $contenair.find('div.metaProductContent-' + idMetaProduct).html();

                    $('input[name="UPDATEMETAPRODUCT"]').val(idMetaProduct);
                    $('input#metaKey').val($.trim(title));
                    CKEDITOR.instances.metaValue.setData(content);
                });

                $('#metaProductContenair').on('click', '.metaProductDeleteBtn', function () {
                    var $btn = $(this);
                    var idMetaProduct = $btn.data('idmetaproduct');

                    if (confirm('<?= trans('Êtes-vous sûr de vouloir supprimer cette métadonnée ?'); ?>')) {
                        busyApp();

                        deleteMetaProduct(idMetaProduct).done(function (data) {
                            if (data == 'true') {
                                $('#metaProductContenair')
                                    .html(loaderHtml())
                                    .load('/app/plugin/shop/page/getMetaProduct.php?idProduct=<?= $Product->getId(); ?>');
                            }
                            availableApp();
                        });
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