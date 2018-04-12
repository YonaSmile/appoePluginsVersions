<?php require('header.php'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1 class="display-4 bigTitle"><?= trans('Auteurs'); ?></h1>
            </div>
        </div>
        <div class="my-4"></div>
        <?php
        $Auteur = new App\Plugin\EventManagement\Auteur();
        $auteurs = $Auteur->showAll();
        ?>
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table id="clientTable"
                           class="sortableTable table table-striped table-hover table-bordered">
                        <thead>
                        <tr>
                            <th><?= trans('Nom'); ?></th>
                            <th><?= trans('Provenance'); ?></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($auteurs as $auteur): ?>
                            <tr>
                                <td><?= $auteur->nom ?></td>
                                <td><?= $auteur->provenance ?></td>
                                <td>
                                    <a href="<?= getPluginUrl('eventManagement/page/auteur/', $auteur->id); ?>"
                                       class="btn btn-warning btn-sm"
                                       title="<?= trans('Modifier'); ?>">
                                        <span class="fa fa-cog"></span>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm deleteAuteur"
                                            title="<?= trans('Archiver'); ?>"
                                            data-idauteur="<?= $auteur->id ?>">
                                        <span class="fa fa-archive" aria-hidden="true"></span>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="my-4"></div>
    </div>
    <script>
        $(document).ready(function () {
            $('.deleteAuteur').click(function () {
                if (confirm('<?= trans('Vous allez archiver cet auteur'); ?>')) {
                    var $btn = $(this);
                    var idAuteur = $btn.data('idauteur');
                    $.post(
                        '<?= EVENTMANAGEMENT_URL . 'ajax/auteurs.php'; ?>',
                        {
                            idDeleteAuteur: idAuteur
                        },
                        function (data) {
                            if (true === data || data == 'true') {
                                $btn.parent('td').parent('tr').slideUp();
                            }
                        }
                    );
                }
            });
        });
    </script>
<?php require('footer.php'); ?>