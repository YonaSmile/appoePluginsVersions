<?php if (empty($_GET['id'])): ?>
    <?php session_start(); header('location:products.php'); ?>
<?php else: ?>
    <?php
    require('header.php');
    $Product = new \App\Plugin\Shop\Product($_GET['id']);

    require_once(SHOP_PATH . 'process/updateProduct.php');

    $Category = new \App\Category();
    $Category->setType('SHOP');
    $listCatgories = extractFromObjToArrForList($Category->showByType(), 'id');

    $CategoryRelation = new \App\CategoryRelations('SHOP', $Product->getId());
    $allCategoryRelations = extractFromObjToSimpleArr($CategoryRelation->getData(), 'categoryId', 'name');
    ?>
    <?= getTitle($Page->getName(), $Page->getSlug()); ?>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <a href="<?= getPluginUrl('shop/page/updateProductData/', $Product->getId()); ?>"
                   class="btn btn-info btn-sm mb-4">
                    <?= trans('Détails du produit'); ?>
                </a>
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
                   for="updateSlugAuto"><?= trans('Mettre à jour le lien du produit automatiquement'); ?></label>
        </div>

        <form action="" method="post" id="updateProductForm">
            <?= getTokenField(); ?>
            <div class="row d-flex align-items-start">
                <div class="col-12 col-lg-8 mb-2 mb-lg-0">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <?= \App\Form::text('Nom du Produit', 'name', 'text', $Product->getName(), true); ?>
                        </div>
                        <div class="col-12 my-2">
                            <?= \App\Form::text('Lien du produit' . ' (Slug)', 'slug', 'text', $Product->getSlug(), true); ?>
                        </div>
                        <div class="col-12 my-2">
                            <?= \App\Form::select('Type de produit', 'type', TYPE_PRODUCT, $Product->getType(), true); ?>
                        </div>
                        <div class="col-12 col-lg-4 mt-2">
                            <?= \App\Form::text('Prix (€)', 'price', 'text', $Product->getPrice(), true, 9, '', '', '', 'Ex: 16.97'); ?>
                        </div>

                        <div class="col-12 col-lg-4 mt-2">
                            <?= \App\Form::text('Poids (en grammes)', 'poids', 'text', $Product->getPoids(), false, 9, '', '', '', 'Ex: 1500 pour 1.5 kg'); ?>
                        </div>

                        <div class="col-12 col-lg-4 mt-3">
                            <?= \App\Form::text('Épaisseur (en Millimètre)', 'dimension', 'text', $Product->getDimension(), false, 9, '', '', '', 'Ex: 1000 pour 1 m'); ?>
                        </div>
                        <div class="col-12 mt-2">
                            <?= \App\Form::checkbox('Catégories', 'categories', $listCatgories, $allCategoryRelations, 'checkCategories'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 bgColorPrimary">
                    <div class="row">
                        <div class="col-12 py-3 my-2 mb-auto">
                            <?= \App\Form::radio('Statut du produit', 'status', array_map('trans', PRODUCT_STATUS), $Product->getStatus(), true); ?>
                        </div>
                        <div class="col-12 my-2">
                            <?= \App\Form::target('UPDATEPRODUCT'); ?>
                            <?= \App\Form::submit('Enregistrer', 'UPDATEPRODUCTSUBMIT', 'btn-light'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
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
    <?php require('footer.php'); ?>
<?php endif; ?>
