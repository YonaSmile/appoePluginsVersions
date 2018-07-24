<?php require('header.php'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1 class="display-4 bigTitle"><?= trans('Archives des pages'); ?></h1>
            </div>
        </div>
        <hr class="my-4">
        <?php $Cms = new App\Plugin\Cms\Cms();
        $Cms->setStatut(0);
        $allCmsPages = $Cms->showAllPages();
        $allPages = extractFromObjArr($allCmsPages, 'id');
        ?>
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table id="pagesTable"
                           class="sortableTable table table-striped">
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
                        <?php if ($allPages): ?>
                            <?php foreach ($allPages as $cmsPage): ?>
                                <tr data-idcms="<?= $cmsPage->id ?>">
                                    <td><?= $cmsPage->name ?></td>
                                    <td><?= mb_strimwidth($cmsPage->description, 0, 70, '...'); ?></td>
                                    <td><?= $cmsPage->slug ?></td>
                                    <td><?= displayTimeStamp($cmsPage->updated_at) ?></td>
                                    <td>
                                        <a href="<?= getPluginUrl('cms/page/pageContent/', $cmsPage->id) ?>"
                                           class="btn btn-sm" title="<?= trans('Consulter'); ?>">
                                            <span class="btnUpdate"><i class="fas fa-cog"></i></span>
                                        </a>
                                        <?php if ($USER->getRole() > 3): ?>
                                            <a href="<?= getPluginUrl('cms/page/update/', $cmsPage->id) ?>"
                                               class="btn btn-sm" title="<?= trans('Modifier'); ?>">
                                                <span class="btnEdit"><i class="fas fa-wrench"></i></span>
                                            </a>
                                            <button type="button" data-deletecmsid="<?= $cmsPage->id ?>"
                                                    class="btn btn-sm deleteCms"
                                                    title="<?= trans('Supprimer'); ?>">
                                                <span class="btnArchive"><i class="fas fa-times"></i></span>
                                            </button>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-sm unpackPage"
                                                title="<?= trans('désarchiver'); ?>"
                                                data-idpage="<?= $cmsPage->id ?>">
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

            $('.unpackPage').on('click', function () {
                var idPage = $(this).data('idpage');
                if (confirm('<?= trans('Vous allez désarchiver cette page'); ?>')) {
                    busyApp();
                    $.post(
                        '<?= CMS_URL . 'process/ajaxProcess.php'; ?>',
                        {
                            unpackPage: 'OK',
                            idUnpackPage: idPage
                        },
                        function (data) {
                            if (data === true || data == 'true') {
                                $('tr[data-idcms="' + idPage + '"]').slideUp();
                                availableApp();
                            }
                        }
                    );
                }
            });

            $('.deleteCms').on('click', function () {
                var idCms = $(this).data('deletecmsid');
                if (confirm('<?= trans('Vous allez supprimer définitivement cette page'); ?>')) {
                    busyApp();
                    $.post(
                        '<?= CMS_URL . 'process/ajaxProcess.php'; ?>',
                        {
                            idCmsDelete: idCms
                        },
                        function (data) {
                            if (data === true || data == 'true') {
                                $('tr[data-idcms="' + idCms + '"]').slideUp();
                                availableApp();
                            }
                        }
                    );
                }
            });
        });
    </script>
<?php require('footer.php'); ?>