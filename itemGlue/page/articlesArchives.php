<?php require('header.php'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1 class="display-4 bigTitle"><?= trans('Archives des articles'); ?></h1>
            </div>
        </div>
        <hr class="my-4">
        <?php $Article = new App\Plugin\ItemGlue\Article();
        $Article->setStatut(0);
        $allArticles = $Article->showAll(false);
        ?>
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table id="pagesTable"
                           class="sortableTable table table-striped table-hover table-bordered">
                        <thead>
                        <tr>
                            <th><?= trans('Nom'); ?></th>
                            <th><?= trans('Description'); ?></th>
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
                                    <td><?= mb_strimwidth($article->description, 0, 70, '...'); ?></td>
                                    <td><?= $Traduction->trans($article->slug) ?></td>
                                    <td><?= implode(', ', extractFromObjToSimpleArr(getCategoriesByArticle($article->id), 'name')); ?></td>
                                    <td><?= displayTimeStamp($article->updated_at) ?></td>
                                    <td>
                                        <a href="<?= getPluginUrl('itemGlue/page/articleContent/', $article->id) ?>"
                                           class="btn btn-info btn-sm" title="<?= trans('Consulter'); ?>">
                                            <span class="fa fa-eye"></span>
                                        </a>
                                        <?php if ($User->getRole() > 3): ?>
                                            <a href="<?= getPluginUrl('itemGlue/page/update/', $article->id) ?>"
                                               class="btn btn-warning btn-sm" title="<?= trans('Modifier'); ?>">
                                                <span class="fas fa-cog"></span>
                                            </a>

                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php require('footer.php'); ?>