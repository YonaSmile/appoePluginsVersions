<?php require('header.php'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1 class="display-4 bigTitle"><?= trans('Évènements'); ?></h1>
            </div>
        </div>
        <div class="my-4"></div>
        <?php
        $Event = new App\Plugin\EventManagement\Event();
        $evenements = $Event->showAll();
        $Auteur = new App\Plugin\EventManagement\Auteur();
        ?>
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table id="projetsTable"
                           class="sortableTable table table-striped table-hover table-bordered">
                        <thead>
                        <tr>
                            <th><?= trans('Auteur'); ?></th>
                            <th><?= trans('Titre'); ?></th>
                            <th><?= trans('Durée'); ?></th>
                            <th><?= trans('Type de spectacle'); ?></th>
                            <th><?= trans('IN / OFF'); ?></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($evenements as $evenement): ?>
                            <?php $Auteur->setId($evenement->auteurId);
                            $Auteur->show(); ?>
                            <tr>
                                <td><?= $Auteur->getName(); ?></td>
                                <td><?= $evenement->titre; ?></td>
                                <td><?= displayDuree($evenement->duree) ?></td>
                                <td><?= trans(SPECTACLES_TYPES[$evenement->spectacleType]); ?></td>
                                <td><?= trans(INDOOR_OFF[$evenement->indoor]); ?></td>
                                <td>
                                    <a href="<?= getPluginUrl('eventManagement/page/event/', $evenement->id) ?>"
                                       class="btn btn-info btn-sm"
                                       title="<?= trans('Consulter'); ?>">
                                        <span class="fa fa-eye"></span>
                                    </a>
                                    <a href="<?= getPluginUrl('eventManagement/page/event/update/', $evenement->id) ?>"
                                       class="btn btn-warning btn-sm"
                                       title="<?= trans('Modifier'); ?>">
                                        <span class="fa fa-cog"></span>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm deleteEvent"
                                            title="<?= trans('Archiver'); ?>"
                                            data-idevent="<?= $evenement->id ?>">
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

            $('.deleteEvent').click(function () {

                if (confirm('<?= trans('Vous allez archiver cet évènement'); ?>')) {
                    var $btn = $(this);
                    var idevent = $btn.data('idevent');

                    $.post(
                        '<?= EVENTMANAGEMENT_URL . 'ajax/event.php'; ?>',
                        {
                            idDeleteEvent: idevent
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