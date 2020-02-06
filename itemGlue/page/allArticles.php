<?php require('header.php');

use App\Plugin\ItemGlue\Article;

$Article = new Article();
$Article->setLang(APP_LANG);
$allArticles = $Article->showAll();

echo getTitle(getAppPageName(), getAppPageSlug()); ?>
    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <table id="pagesTable"
                       class="sortableTable table table-striped">
                    <thead>
                    <tr>
                        <th><?= trans('Nom'); ?></th>
                        <th><?= trans('Slug'); ?></th>
                        <th><?= trans('Catégories'); ?></th>
                        <th><?= trans('Modifié le'); ?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($allArticles): ?>
                        <?php foreach ($allArticles as $article): ?>
                            <tr data-idarticle="<?= $article->id ?>">
                                <td><?= $article->name ?></td>
                                <td><?= $article->slug ?></td>
                                <td><?= implode(', ', extractFromObjToSimpleArr(getCategoriesByArticle($article->id), 'name')); ?></td>
                                <td><?= displayTimeStamp($article->updated_at) ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm featuredArticle"
                                            title="<?= $article->statut == 2 ? trans('Article standard') : trans('Article vedette'); ?>"
                                            data-idarticle="<?= $article->id ?>" data-title-standard="<?= trans('Article standard'); ?>"
                                            data-title-vedette="<?= trans('Article vedette'); ?>"
                                            data-confirm-standard="<?= trans('Vous allez mettre cet article en vedette'); ?>"
                                            data-confirm-vedette="<?= trans('Cet article ne sera plus vedette'); ?>"
                                            data-statutarticle="<?= $article->statut; ?>">
                                            <span class="text-warning">
                                            <?= $article->statut == 2 ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>'; ?>
                                                </span>
                                    </button>
                                    <a href="<?= getPluginUrl('itemGlue/page/articleContent/', $article->id) ?>"
                                       class="btn btn-sm" title="<?= trans('Consulter'); ?>">
                                        <span class="btnUpdate"><i class="fas fa-cog"></i></span>
                                    </a>
                                    <button type="button" class="btn btn-sm archiveArticle"
                                            title="<?= trans('Archiver'); ?>"
                                            data-confirm-msg="<?= trans('Vous allez archiver cet article'); ?>"
                                            data-idarticle="<?= $article->id ?>">
                                        <span class="btnArchive"><i class="fas fa-archive"></i></span>
                                    </button>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php require('footer.php'); ?>