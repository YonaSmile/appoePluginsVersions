<?php require('header.php'); ?>
<?= getTitle($Page->getName(), $Page->getSlug()); ?>
    <div class="container-fluid">
        <?php $Article = new \App\Plugin\ItemGlue\Article();
        $Article->setStatut(1);
        $allArticles = $Article->showAll(false);
        ?>
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
                                                title="<?= $article->statut == 2 ? trans('Article standard') : trans('Article en vedette'); ?>"
                                                data-idarticle="<?= $article->id ?>"
                                                data-statutarticle="<?= $article->statut; ?>">
                                            <span class="text-warning">
                                            <?= $article->statut == 2 ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>'; ?>
                                                </span>
                                        </button>
                                        <a href="<?= getPluginUrl('itemGlue/page/articleContent/', $article->id) ?>"
                                           class="btn btn-sm" title="<?= trans('Consulter'); ?>">
                                            <span class="btnUpdate"><i class="fas fa-cog"></i></span>
                                        </a>
                                        <a href="<?= getPluginUrl('itemGlue/page/update/', $article->id) ?>"
                                           class="btn btn-sm" title="<?= trans('Modifier'); ?>">
                                            <span class="btnEdit"><i class="fas fa-wrench"></i></span>
                                        </a>
                                        <button type="button" class="btn btn-sm archiveArticle"
                                                title="<?= trans('Archiver'); ?>"
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
    </div>
    <script>
        $(document).ready(function () {

            $('.archiveArticle').on('click', function () {

                var idArticle = $(this).data('idarticle');
                if (confirm('<?= trans('Vous allez archiver cet article'); ?>')) {
                    busyApp();
                    $.post(
                        '<?= ITEMGLUE_URL . 'process/ajaxProcess.php'; ?>',
                        {
                            archiveArticle: 'OK',
                            idArticleArchive: idArticle
                        },
                        function (data) {
                            if (data === true || data == 'true') {
                                $('tr[data-idarticle="' + idArticle + '"]').slideUp();
                                availableApp();
                            }
                        }
                    );
                }
            });

            $('.featuredArticle').on('click', function () {

                var $btn = $(this);

                var currentStatut = $btn.data('statutarticle');
                var nowStatut = currentStatut == 2 ? 1 : 2;

                var idArticle = $btn.data('idarticle');
                var $iconContainer = $btn.children('span');

                var iconFeatured = nowStatut == 2 ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';

                var textConfirmFeatured = nowStatut == 2 ? '<?= trans('Vous allez mettre en vedette cet article'); ?>' : '<?= trans('Cet article ne sera plus mis en vedette'); ?>';
                var textTitleFeatured = nowStatut == 2 ? '<?= trans('Article standard'); ?>' : '<?= trans('Article en vedette'); ?>';

                if (confirm(textConfirmFeatured)) {
                    busyApp();
                    $.post(
                        '<?= ITEMGLUE_URL . 'process/ajaxProcess.php'; ?>',
                        {
                            featuredArticle: 'OK',
                            idArticleFeatured: idArticle,
                            newStatut: nowStatut
                        },
                        function (data) {
                            if (data === true || data == 'true') {

                                $btn.data('statutarticle', nowStatut);
                                $btn.attr('title', textTitleFeatured);
                                $iconContainer.html(iconFeatured);
                                availableApp();
                            }
                        }
                    );
                }
            });
        });
    </script>
<?php require('footer.php'); ?>