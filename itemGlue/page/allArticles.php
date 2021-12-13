<?php require('header.php');
$allArticles = getRecentArticles(false, APP_LANG);
echo getTitle(getAppPageName(), getAppPageSlug()); ?>
    <div id="admin-tab-search">
        <input type="search" class="form-control" id="admin-tab-search-input" placeholder="Rechercher...">
    </div>
<?php if ($allArticles): ?>
    <div id="admin-tabs-menu" class="row d-none d-sm-flex">
        <div class="d-none d-sm-block col-sm-6 col-md-5 col-lg-4">Nom</div>
        <div class="d-none d-sm-block col-sm-6 col-md-5 col-lg-4">Lien</div>
        <div class="d-none d-md-block col-md-2 col-lg-1">Statut</div>
        <div class="d-none d-lg-block col-lg-3">Crée le</div>
    </div>
    <div class="row">
    <div id="admin-tabs" class="col-12">
        <?php foreach ($allArticles as $article):
            $categories = extractFromObjToSimpleArr(getCategoriesByArticle($article->id), 'name'); ?>
            <div class="admin-tab" data-idarticle="<?= $article->id ?>"
                 data-filter="<?= implode(' ', $categories) ?> <?= $article->name ?> <?= $article->slug ?>">
                <div class="admin-tab-header">
                    <div class="col-12 col-sm-6 col-md-5 col-lg-4"><?= shortenText($article->name, 90); ?></div>
                    <div class="d-none d-sm-block col-sm-6 col-md-5 col-lg-4"><?= shortenText($article->slug, 90); ?></div>
                    <div class="d-none d-md-block col-md-2 col-lg-1" data-article="status">
                        <?= ITEMGLUE_STATUS[$article->statut]; ?></div>
                    <div class="d-none d-lg-block col-lg-3"><?= displayCompleteDate($article->created_at, false) ?></div>
                </div>
                <div class="admin-tab-content">
                    <div class="admin-tab-content-header">
                        <h2><?= $article->name ?></h2>
                        <button type="button"
                                class="btn btn-sm featuredArticle" <?= $article->statut < 2 ? ' disabled="disabled" ' : ''; ?>
                                title="<?= $article->statut == 3 ? trans('Article standard') : trans('Article vedette'); ?>"
                                data-idarticle="<?= $article->id ?>"
                                data-title-standard="<?= trans('Designer l\'article comme vedette'); ?>"
                                data-title-vedette="<?= trans('Designer l\'article comme standard'); ?>"
                                data-confirm-standard="<?= trans('Cet article ne sera plus en vedette'); ?>"
                                data-confirm-vedette="<?= trans('Vous allez positionner cet article en vedette'); ?>"
                                data-statutarticle="<?= $article->statut; ?>">
                                                <span class="text-<?= $article->statut < 2 ? 'secondary' : 'warning'; ?>">
                                                    <?= $article->statut == 3 ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>'; ?>
                                                </span>
                        </button>
                        |
                        <?php if ($article->statut == 1): ?>
                            <button type="button" data-idarticle="<?= $article->id ?>"
                                    class="btn btn-sm publishArticle btnLink">Publier l'article
                            </button>
                        <?php else: ?>
                            <button type="button" data-idarticle="<?= $article->id ?>"
                                    class="btn btn-sm draftArticle btnLink">Dépublier l'article
                            </button>
                        <?php endif; ?>
                        |
                        <a href="<?= getPluginUrl('itemGlue/page/articleContent/', $article->id) ?>"
                           class="btnLink"><?= trans('Modifier'); ?></a>
                        |
                        <button type="button" class="btnLink archiveArticle"
                                data-confirm-msg="<?= trans('Vous allez archiver cet article'); ?>"
                                data-idarticle="<?= $article->id ?>">
                            <?= trans('Archiver'); ?></button>
                    </div>
                    <div class="px-2" data-idarticle="<?= $article->id ?>">
                        <div id="admin-tab-img"><?= getArtFeaturedImg($article, ['tempPos' => 1, 'thumbSize' => 400, 'webp' => true, 'class' => 'seeOnOverlay']); ?></div>
                        <p>
                            <i class="fas fa-fingerprint"></i><strong><?= trans('ID'); ?></strong><?= $article->id ?>
                        </p>
                        <p><i class="far fa-clock"></i>
                            <strong><?= trans('Crée le'); ?></strong><span data-page="date">
                                            <?= displayCompleteDate($article->created_at, false) ?></span></p>
                        <p><i class="fas fa-project-diagram"></i><strong><?= trans('Catégories'); ?></strong>
                            <span data-page="categories"><span class="badge bgColorPrimary p-1 mx-1">
                                    <?= implode('</span><span class="badge bgColorPrimary p-1 mx-1">', $categories); ?>
                                            </span></span></p>
                        <p><i class="far fa-file-alt"></i><strong><?= trans('Nom de l\'article'); ?></strong>
                            <span data-page="name"><?= $article->name ?></span></p>
                        <p><i class="fas fa-link"></i><strong><?= trans('Slug'); ?></strong>
                            <span data-page="slug"><?= $article->slug ?></span></p>
                        <p><i class="fas fa-quote-right"></i><strong><?= trans('Description'); ?></strong>
                            <span data-page="description"><?= $article->description ?></span></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
    </div>
<?php require('footer.php'); ?>