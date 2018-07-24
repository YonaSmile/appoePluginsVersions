<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/main.php');
require_once('../ini.php');
includePluginsFiles(true);
$MetaArticle = !empty($_GET['idArticle']) ? new App\Plugin\ItemGlue\ArticleMeta($_GET['idArticle']) : false;
if ($MetaArticle && !empty($MetaArticle->getData())): ?>
    <?php $allMetaArticle = extractFromObjArr($MetaArticle->getData(), 'id'); ?>
    <div class="accordion" id="accordionMetaProduct">
        <?php foreach ($allMetaArticle as $id => $meta): ?>
            <div class="card" data-idmetaproduct="<?= $id; ?>">
                <div class="card-header" id="headingMetaProduct<?= $id; ?>">
                    <h5 class="mb-0">
                        <button class="btn btn-link metaProductTitle-<?= $id; ?>" type="button" data-toggle="collapse"
                                data-target="#collapseMetaProduct<?= $id; ?>" aria-expanded="false"
                                aria-controls="collapseMetaProduct<?= $id; ?>">
                            <?= $meta->metaKey; ?>
                        </button>
                        <span class="float-right">
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
                     aria-labelledby="headingMetaProduct<?= $id; ?>" data-parent="#accordionMetaProduct">
                    <div class="card-body metaProductContent-<?= $id; ?>"><?= htmlSpeCharDecode($meta->metaValue); ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
