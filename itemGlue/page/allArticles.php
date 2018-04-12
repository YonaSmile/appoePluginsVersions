<?php require('header.php'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1 class="display-4 bigTitle"><?= trans('Articles'); ?></h1>
            </div>
        </div>
        <hr class="my-4">
        <?php $Article = new App\Plugin\ItemGlue\Article();
        $Article->setStatut(1);
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
                                    <td><?= $article->slug ?></td>
                                    <td><?= displayTimeStamp($article->updated_at) ?></td>
                                    <td>
                                        <button type="button" class="btn btn-light btn-sm featuredArticle"
                                                title="<?= $article->statut == 2 ? trans('Article standard') : trans('Article en vedette'); ?>"
                                                data-idarticle="<?= $article->id ?>"
                                                data-statutarticle="<?= $article->statut; ?>">
                                            <span class="text-warning">
                                            <?= $article->statut == 2 ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>'; ?>
                                                </span>
                                        </button>
                                        <a href="<?= getPluginUrl('itemGlue/page/articleContent/', $article->id) ?>"
                                           class="btn btn-info btn-sm" title="<?= trans('Consulter'); ?>">
                                            <span class="fa fa-eye"></span>
                                        </a>
                                        <?php if ($User->getRole() > 3): ?>
                                            <a href="<?= getPluginUrl('itemGlue/page/update/', $article->id) ?>"
                                               class="btn btn-warning btn-sm" title="<?= trans('Modifier'); ?>">
                                                <span class="fas fa-cog"></span>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm deleteArticle"
                                                    title="<?= trans('Archiver'); ?>"
                                                    data-idarticle="<?= $article->id ?>">
                                                <span class="fas fa-archive"></span>
                                            </button>
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
    <script>
        $(document).ready(function () {

            <?php if ($User->getRole() > 3): ?>
            $('.deleteArticle').on('click', function () {

                var idArticle = $(this).data('idarticle');
                if (confirm('<?= trans('Vous allez supprimer cet article'); ?>')) {
                    $.post(
                        '<?= ITEMGLUE_URL . 'process/ajaxProcess.php'; ?>',
                        {
                            deleteArticle: 'OK',
                            idArticleDelete: idArticle
                        },
                        function (data) {
                            if (data === true || data == 'true') {
                                $('tr[data-idarticle="' + idArticle + '"]').slideUp();
                            }
                        }
                    );
                }
            });
            <?php endif; ?>

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
                            }
                        }
                    );


                }
            });
        });
    </script>
<?php require('footer.php'); ?>