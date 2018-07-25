<?php require('header.php'); ?>
<?= getTitle($Page->getName(), $Page->getSlug()); ?>
    <div class="container-fluid">
        <?php $People = new App\Plugin\People\People();
        $allPersons = $People->showAll();
        ?>
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table id="pagesTable"
                           class="sortableTable table table-striped">
                        <thead>
                        <tr>
                            <th><?= trans('Type'); ?></th>
                            <th><?= trans('Nature'); ?></th>
                            <th><?= trans('Nom'); ?></th>
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
                                    <td><?= !empty($person->nature) ? trans(PEOPLE_NATURE[$person->nature]) : ''; ?></td>
                                    <td><?= $person->entitled ?></td>
                                    <td>
                                        <strong><?= (!empty($person->birthDate) && $person->birthDate != '0000-00-00') ? age($person->birthDate) : '' ?></strong>
                                        <small><?= displayFrDate($person->birthDate); ?></small>
                                    </td>
                                    <td><?= $person->email ?></td>
                                    <td><?= $person->city ?></td>
                                    <td><?= getPaysName($person->country); ?></td>
                                    <td>
                                        <a href="<?= getPluginUrl('people/page/update/', $person->id) ?>"
                                           class="btn btn-sm" title="<?= trans('Modifier'); ?>">
                                            <span class="btnEdit"><i class="fas fa-wrench"></i></span>
                                        </a>
                                        <button type="button" class="btn btn-sm deletePerson"
                                                title="<?= trans('Archiver'); ?>"
                                                data-idperson="<?= $person->id ?>">
                                            <span class="btnArchive"><i class="fas fa-archive"></i></span>
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