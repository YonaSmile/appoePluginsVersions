<?php
require('header.php');
$InteractiveMap = new App\Plugin\InteractiveMap\InteractiveMap();
$allCartes = extractFromObjArr($InteractiveMap->showAll(), 'id');
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="display-4 bigTitle"><?= trans('Cartes interactives'); ?></h1>
        </div>
    </div>
    <hr class="my-4">
    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <table id="pagesTable"
                       class="sortableTable table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?= trans('Titre'); ?></th>
                        <th><?= trans('Largeur'); ?></th>
                        <th><?= trans('Hauteur'); ?></th>
                        <th><?= trans('Modifié le'); ?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($allCartes): ?>
                        <?php foreach ($allCartes as $carte): ?>
                            <tr data-idcarte="<?= $carte->id ?>">
                                <td><?= $carte->title ?></td>
                                <td><?= $carte->width; ?></td>
                                <td><?= $carte->height ?></td>
                                <td><?= displayTimeStamp($carte->updated_at) ?></td>
                                <td>
                                    <a href="<?= getPluginUrl('interactiveMap/page/updateInterMapContent/', $carte->id) ?>"
                                       class="btn btn-info btn-sm"
                                       title="<?= trans('Consulter'); ?>">
                                        <span class="fa fa-eye"></span>
                                    </a>
                                    <a href="<?= getPluginUrl('interactiveMap/page/updateInterMap/', $carte->id) ?>"
                                       class="btn btn-warning btn-sm" title="<?= trans('Modifier'); ?>">
                                        <span class="fas fa-cog"></span>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm deleteMap"
                                            title="<?= trans('Archiver'); ?>" data-idcarte="<?= $carte->id ?>">
                                        <span class="fas fa-archive"></span>
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
<?php if ($User->getRole() > 3): ?>
    <script>
        $(document).ready(function () {
            $('.deleteMap').on('click', function () {

                var idCarte = $(this).data('idcarte');
                if (confirm('<?= trans('Vous allez archiver cette carte'); ?>')) {
                    $.post(
                        '<?= INTERACTIVE_MAP_URL . 'process/ajaxProcess.php'; ?>',
                        {
                            idMapDelete: idCarte
                        },
                        function (data) {
                            if (data === true || data == 'true') {
                                $('tr[data-idcarte="' + idCarte + '"]').slideUp();
                            }
                        }
                    );
                }
            });
        });
    </script>
<?php endif; ?>
<?php require('footer.php'); ?>
