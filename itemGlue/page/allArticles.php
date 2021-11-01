<?php require('header.php');
$allArticles = getRecentArticles(false, APP_LANG);
echo getTitle(getAppPageName(), getAppPageSlug()); ?>
    <div class="row">
        <div class="col-12 col-md-4 col-lg-3">
            <?php if ($allArticles): ?>
                <div class="input-group mb-1" id="admin-tab-search">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="search" class="form-control" id="admin-tab-search-input" placeholder="Rechercher...">
                </div>
                <div id="admin-tabs">
                    <?php foreach ($allArticles as $article):
                        $categories = extractFromObjToSimpleArr(getCategoriesByArticle($article->id), 'name'); ?>
                        <div class="admin-tab" data-idarticle="<?= $article->id ?>"
                             data-filter="<?= implode(' ', $categories) ?> <?= $article->name ?> <?= $article->slug ?>">
                            <div class="admin-tab-header">
                                <h5><?= $article->name ?></h5>
                                <small><?= $article->statut == 2 ? '<i class="fas fa-star"></i>' : ''; ?></small>
                            </div>
                            <div class="admin-tab-content">
                                <div class="d-flex align-items-center justify-content-center justify-content-lg-start p-3 text-center text-lg-start">
                                    <div class="d-none d-lg-block display-3 me-3"><i class="fas fa-thumbtack"></i></div>
                                    <div>
                                        <h2><?= $article->name ?></h2>
                                        <button type="button" class="btn btn-sm featuredArticle"
                                                title="<?= $article->statut == 2 ? trans('Article standard') : trans('Article vedette'); ?>"
                                                data-idarticle="<?= $article->id ?>"
                                                data-title-standard="<?= trans('Designer l\'article comme vedette'); ?>"
                                                data-title-vedette="<?= trans('Designer l\'article comme standard'); ?>"
                                                data-confirm-standard="<?= trans('Cet article ne sera plus en vedette'); ?>"
                                                data-confirm-vedette="<?= trans('Vous allez positionner cet article en vedette'); ?>"
                                                data-statutarticle="<?= $article->statut; ?>">
                                            <span class="text-warning">
                                            <?= $article->statut == 2 ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>'; ?>
                                                </span>
                                        </button>
                                        |
                                        <a href="<?= getPluginUrl('itemGlue/page/articleContent/', $article->id) ?>"
                                           class="btnLink"><?= trans('Consulter'); ?></a>
                                        |
                                        <button type="button" class="btnLink archiveArticle"
                                                data-confirm-msg="<?= trans('Vous allez archiver cet article'); ?>"
                                                data-idarticle="<?= $article->id ?>">
                                            <?= trans('Archiver'); ?></button>
                                    </div>
                                </div>
                                <div class="p-0 px-lg-3 pb-lg-3" data-idarticle="<?= $article->id ?>">
                                    <?= getArtFeaturedImg($article, ['tempPos' => 1, 'thumbSize' => 450, 'webp' => true]); ?>
                                    <p>
                                        <i class="fas fa-fingerprint"></i><strong><?= trans('ID'); ?></strong><?= $article->id ?>
                                    </p>
                                    <p><i class="far fa-clock"></i>
                                        <strong><?= trans('Crée le'); ?></strong><span data-page="date">
                                            <?= displayCompleteDate($article->created_at, false) ?></span>
                                    </p>
                                    <p>
                                        <i class="fas fa-project-diagram"></i><strong><?= trans('Catégories'); ?></strong><span
                                                data-page="categories"><?= implode(', ', $categories); ?></span></p>
                                    <p>
                                        <i class="far fa-file-alt"></i><strong><?= trans('Nom de l\'article'); ?></strong><span
                                                data-page="name"><?= $article->name ?></span></p>
                                    <p><i class="fas fa-link"></i><strong><?= trans('Slug'); ?></strong><span
                                                data-page="slug"><?= $article->slug ?></span></p>
                                    <p>
                                        <i class="fas fa-quote-right"></i><strong><?= trans('Description'); ?></strong><span
                                                data-page="description"><?= $article->description ?></span></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-12 col-md-8 col-lg-9">
            <div id="admin-tab-content" class="position-relative"></div>
        </div>
    </div>
<?php require('footer.php'); ?>