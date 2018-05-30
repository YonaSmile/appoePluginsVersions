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
                        <?php if ($allPages): ?>
                            <?php foreach ($allPages as $cmsPage): ?>
                                <tr data-idcms="<?= $cmsPage->id ?>">
                                    <td><?= $cmsPage->name ?></td>
                                    <td><?= mb_strimwidth($cmsPage->description, 0, 70, '...'); ?></td>
                                    <td><?= $cmsPage->slug ?></td>
                                    <td><?= displayTimeStamp($cmsPage->updated_at) ?></td>
                                    <td>
                                        <a href="<?= getPluginUrl('cms/page/pageContent/', $cmsPage->id) ?>"
                                           class="btn btn-info btn-sm" title="<?= trans('Consulter'); ?>">
                                            <span class="fa fa-eye"></span>
                                        </a>
                                        <?php if ($USER->getRole() > 3): ?>
                                            <a href="<?= getPluginUrl('cms/page/update/', $cmsPage->id) ?>"
                                               class="btn btn-warning btn-sm" title="<?= trans('Modifier'); ?>">
                                                <span class="fas fa-cog"></span>
                                            </a>
                                            <button type="button" data-deletecmsid="<?= $cmsPage->id ?>"
                                               class="btn btn-danger btn-sm deleteCms" title="<?= trans('Supprimer'); ?>">
                                                <span class="fas fa-times"></span>
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