<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/main.php');
require_once('../ini.php');
includePluginsFiles();
$MetaProduct = !empty($_GET['idProduct']) ? new \App\Plugin\Shop\ProductMeta($_GET['idProduct']) : false;
if ($MetaProduct && !empty($MetaProduct->getData())): ?>
    <?php $allMetaProduct = extractFromObjArr($MetaProduct->getData(), 'id'); ?>
    <div class="accordion" id="accordionMetaProduct">
        <?php foreach ($allMetaProduct as $id => $meta): ?>
            <div class="card" data-idmetaproduct="<?= $id; ?>">
                <div class="card-header" id="headingMetaProduct<?= $id; ?>">
                    <h5 class="mb-0">
                        <button class="btn btn-link metaProductTitle-<?= $id; ?>" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseMetaProduct<?= $id; ?>" aria-expanded="false"
                                aria-controls="collapseMetaProduct<?= $id; ?>">
                            <?= $meta->meta_key; ?>
                        </button>
                        <span class="float-end">
                        <button type="button"
                                class="btn btn-sm btn-link text-warning metaProductUpdateBtn"
                                data-idmetaproduct="<?= $id; ?>"
                                title="<?= trans('Modifier'); ?>">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button"
                                class="btn btn-sm btn-link text-danger metaProductDeleteBtn"
                                data-idmetaproduct="<?= $id; ?>"
                                title="<?= trans('Supprimer'); ?>">
                            <i class="fas fa-times"></i>
                        </button>
                        </span>
                    </h5>
                </div>

                <div id="collapseMetaProduct<?= $id; ?>" class="collapse"
                     aria-labelledby="headingMetaProduct<?= $id; ?>" data-bs-parent="#accordionMetaProduct">
                    <div class="card-body metaProductContent-<?= $id; ?>"><?= htmlSpeCharDecode($meta->meta_value); ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
