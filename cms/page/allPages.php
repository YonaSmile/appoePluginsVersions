<?php require('header.php'); ?>
<?= getTitle($Page->getName(), $Page->getSlug()); ?>
    <div class="container-fluid">
        <?php $Cms = new \App\Plugin\Cms\Cms();
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
                                            <button type="button" class="btn btn-sm deleteCms"
                                                    title="<?= trans('Archiver'); ?>" data-idcms="<?= $cmsPage->id ?>">
                                                <span class="btnArchive"><i class="fas fa-archive"></i></span>
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
<?php if ($USER->getRole() > 3): ?>
    <script>
        $(document).ready(function () {
            $('.deleteCms').on('click', function () {
                var idCms = $(this).data('idcms');
                if (confirm('<?= trans('Vous allez archiver cette page'); ?>')) {
                    busyApp();
                    $.post(
                        '<?= CMS_URL . 'process/ajaxProcess.php'; ?>',
                        {
                            idCmsArchive: idCms
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
<?php endif; ?>
<?php require('footer.php'); ?>