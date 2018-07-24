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
                                    <td><?= $Traduction->trans($article->slug) ?></td>
                                    <td><?= implode(', ', extractFromObjToSimpleArr(getCategoriesByArticle($article->id), 'name')); ?></td>
                                    <td><?= displayTimeStamp($article->updated_at) ?></td>
                                    <td>
                                        <a href="<?= getPluginUrl('itemGlue/page/articleContent/', $article->id) ?>"
                                           class="btn btn-sm" title="<?= trans('Consulter'); ?>">
                                            <span class="btnUpdate"><i class="fas fa-cog"></i></span>
                                        </a>
                                        <?php if ($USER->getRole() > 3): ?>
                                            <a href="<?= getPluginUrl('itemGlue/page/update/', $article->id) ?>"
                                               class="btn btn-sm" title="<?= trans('Modifier'); ?>">
                                                <span class="btnEdit"><i class="fas fa-wrench"></i></span>
                                            </a>
                                            <button type="button" class="btn btn-sm deleteArticle"
                                                    title="<?= trans('Supprimer'); ?>"
                                                    data-idarticle="<?= $article->id ?>">
                                                <span class="btnArchive"><i class="fas fa-times"></i></span>
                                            </button>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-sm unpackArticle"
                                                title="<?= trans('désarchiver'); ?>"
                                                data-idarticle="<?= $article->id ?>">
                                            <span class="btnCheck"> <i class="fas fa-check"></i></span>
                                        </button>
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

            $('.unpackArticle').on('click', function () {
                var idArticle = $(this).data('idarticle');
                if (confirm('<?= trans('Vous allez désarchiver cet article'); ?>')) {
                    busyApp();
                    $.post(
                        '<?= ITEMGLUE_URL . 'process/ajaxProcess.php'; ?>',
                        {
                            unpackArticle: 'OK',
                            idUnpackArticle: idArticle
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

            <?php if ($USER->getRole() > 3): ?>
            $('.deleteArticle').on('click', function () {

                var idArticle = $(this).data('idarticle');
                if (confirm('<?= trans('Vous allez supprimer définitivement cet article'); ?>')) {
                    busyApp();
                    $.post(
                        '<?= ITEMGLUE_URL . 'process/ajaxProcess.php'; ?>',
                        {
                            deleteArticle: 'OK',
                            idArticleDelete: idArticle
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
            <?php endif; ?>
        });
    </script>
<?php require('footer.php'); ?>