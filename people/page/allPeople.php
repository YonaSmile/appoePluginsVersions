<?php require('header.php'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1 class="display-4 bigTitle"><?= trans('Les personnes'); ?></h1>
            </div>
        </div>
        <hr class="my-4">
        <?php $People = new App\Plugin\People\People();
        $allPersons = $People->showAll();
        ?>
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table id="pagesTable"
                           class="sortableTable table table-striped table-hover table-bordered">
                        <thead>
                        <tr>
                            <th><?= trans('Type'); ?></th>
                            <th><?= trans('Nom'); ?></th>
                            <th><?= trans('Prénom'); ?></th>
                            <th><?= trans('Age'); ?></th>
                            <th><?= trans('Email'); ?></th>
                            <th><?= trans('Ville'); ?></th>
                            <th><?= trans('Pays'); ?></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if ($allPersons): ?>
                            <?php foreach ($allPersons as $person): ?>
                                <tr data-idperson="<?= $person->id ?>">
                                    <td><?= $person->type ?></td>
                                    <td><?= $person->name ?></td>
                                    <td><?= $person->firstName; ?></td>
                                    <td>
                                        <strong><?= (!empty($person->birthDate) && $person->birthDate != '0000-00-00') ? age($person->birthDate) : '' ?></strong>
                                        <small><?= displayFrDate($person->birthDate); ?></small>
                                    </td>
                                    <td><?= $person->email ?></td>
                                    <td><?= $person->city ?></td>
                                    <td><?= $person->country ?></td>
                                    <td>
                                        <a href="<?= getPluginUrl('people/page/update/', $person->id) ?>"
                                           class="btn btn-warning btn-sm" title="<?= trans('Modifier'); ?>">
                                            <span class="fas fa-cog"></span>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm deletePerson"
                                                title="<?= trans('Archiver'); ?>"
                                                data-idperson="<?= $person->id ?>">
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
    <script>
        $(document).ready(function () {
            $('.deletePerson').on('click', function () {
                var idPerson = $(this).data('idperson');
                if (confirm('<?= trans('Vous allez supprimer cette personne'); ?>')) {
                    $.post(
                        '<?= PEOPLE_URL . 'process/ajaxProcess.php'; ?>',
                        {
                            deletePerson: 'OK',
                            idPersonDelete: idPerson
                        },
                        function (data) {
                            if (data === true || data == 'true') {
                                $('tr[data-idperson="' + idPerson + '"]').slideUp();
                            }
                        }
                    );
                }
            });
        });
    </script>
<?php require('footer.php'); ?>