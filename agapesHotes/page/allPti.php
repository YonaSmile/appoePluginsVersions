<?php require('header.php'); ?>
<?= getTitle($Page->getName(), $Page->getSlug()); ?>
    <div class="container-fluid">
        <?php
        if (class_exists('App\Plugin\People\People')) :

        //Get Pti
        $Pti = new \App\Plugin\AgapesHotes\Pti();

        //Get Employe
        $Employe = new \App\Plugin\AgapesHotes\Employe();
        $allEmployes = extractFromObjToSimpleArr($Employe->showByType(), 'id', 'name', 'firstName');

        //Get Site
        $Site = new \App\Plugin\AgapesHotes\Site();
        $allDbSites = $Site->showAll();
        $allSites = extractFromObjToSimpleArr($allDbSites, 'id', 'nom');
        ?>
        <button id="updatePtiBtn" type="button" class="btn btn-info btn-sm mb-4" data-toggle="modal"
                data-target="#modalUpdatePti">
            <?= trans('Ajouter un plan de travail'); ?>
        </button>
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table id="pagesTable"
                           class="sortableTable table table-striped">
                        <thead>
                        <tr>
                            <th><?= trans('Site'); ?></th>
                            <th><?= trans('Employé'); ?></th>
                            <th><?= trans('Cycle'); ?></th>
                            <th><?= trans('Date d\'effet'); ?></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if ($allEmployes): ?>
                            <?php foreach ($allEmployes as $employeId => $employeName):

                                $allEmployeContrats = getEmployeContrats($employeId);
                                if ($allEmployeContrats):

                                    foreach ($allEmployeContrats as $employeContrat):
                                        $Pti->setDateDebut(date('Y-m-d'));
                                        $Pti->setSiteId($employeContrat['siteId']);
                                        $Pti->setEmployeId($employeId);
                                        if ($Pti->showReelPti()): ?>
                                            <tr data-idpti="<?= $Pti->getId(); ?>">
                                                <td><?= $allSites[$Pti->getSiteId()]; ?></td>
                                                <td><?= $allEmployes[$Pti->getEmployeId()]; ?></td>
                                                <td><?php printf($Pti->getNbWeeksInCycle() . ' semaine%s', $Pti->getNbWeeksInCycle() > 1 ? 's' : ''); ?> </td>
                                                <td><?= displayCompleteDate($Pti->getDateDebut()); ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm updatePti"
                                                            data-toggle="modal" data-target="#modalUpdatePti"
                                                            title="<?= trans('Modifier'); ?>"
                                                            data-idpti="<?= $Pti->getId(); ?>"
                                                            data-idsite="<?= $Pti->getSiteId(); ?>"
                                                            data-idemploye="<?= $Pti->getEmployeId(); ?>"
                                                            data-weekscycle="<?= $Pti->getNbWeeksInCycle(); ?>"
                                                            data-datedebut="<?= $Pti->getDateDebut(); ?>">
                                                        <span class="btnEdit"><i class="fas fa-wrench"></i></span>
                                                    </button>
                                                    <button type="button" class="btn btn-sm archivePti"
                                                            title="<?= trans('Archiver'); ?>"
                                                            data-idpti="<?= $Pti->getId(); ?>">
                                                        <span class="btnArchive"><i class="fas fa-archive"></i></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endif;
                                    endforeach;
                                endif;
                            endforeach;
                        endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalUpdatePti" tabindex="-1" role="dialog"
         aria-labelledby="modalUpdatePtiTitle"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="" method="post" id="updatePtiForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalUpdatePtiTitle">Plan de travail individuel</h5>
                    </div>
                    <div class="modal-body" id="modalUpdatePtiBody">
                        <div class="row">
                            <?= getTokenField(); ?>
                            <?= \App\Form::target('UPDATEPTI'); ?>
                            <input type="hidden" name="ptiId" value="">
                            <div class="col-12 col-lg-4 my-2">
                                <?= \App\Form::select('Site', 'siteId', $allSites, '', true); ?>
                            </div>
                            <div class="col-12 col-lg-8 my-2">
                                <?= \App\Form::text('Date d\'effet du nouveau PTI', 'dateDebut', 'date', '', true, 10); ?>
                            </div>
                            <div class="col-12 col-lg-6 my-2">
                                <?= \App\Form::select('Employé', 'employeId', $allEmployes, '', true); ?>
                            </div>
                            <div class="col-12 col-lg-6 my-2">
                                <output for="nbWeeksInCycle" class="outputNbWeeksInCycle float-right">2</output>
                                <?= \App\Form::text('Semaines par cycle', 'nbWeeksInCycle', 'range', '2', true, 1, 'min="1" max="8" step="1"', '', 'custom-range'); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 my-2" id="FormUpdatePtiInfos"></div>
                        </div>
                    </div>
                    <div class="modal-footer" id="modalUpdatePtiFooter">
                        <button type="submit" id="savePtiBtn"
                                class="btn btn-primary"><?= trans('Enregistrer'); ?></button>
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal"><?= trans('Fermer'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {

            $('.updateContrat').on('click', function () {
                var $btn = $(this);
                $('#modalUpdatePtiTitle').html('Gérez le contrat de <em>' + $btn.data('name') + '</em>');
                $('input[name="employeId"]').val($btn.data('idemploye'))
            });

            $('#savePtiBtn').on('click', function (event) {
                event.preventDefault();

                $('#FormUpdatePtiInfos').hide().html('');

                busyApp();
                $.post(
                    '<?= AGAPESHOTES_URL . 'process/ajaxPtiProcess.php'; ?>',
                    $('#updatePtiForm').serialize(),
                    function (data) {
                        if (data === true || data == 'true') {
                            $('#loader').fadeIn(400);
                            location.reload();
                        } else {
                            $('#FormUpdatePtiInfos')
                                .html('<p class="bg-danger text-white">' + data + '</p>').show();
                        }

                        availableApp();
                    }
                );
            });

            $('[data-toggle="popover"]').popover({
                trigger: 'hover'
            });

            $('input#nbWeeksInCycle').on("input", function () {
                $(this).val(this.value);
                $('.outputNbWeeksInCycle').val(this.value);
            }).trigger("change");

            $('.updatePti').on('click', function (event) {
                event.preventDefault();
                var idPti = $(this).data('idpti');
                var idSite = $(this).data('idsite');
                var idEmploye = $(this).data('idemploye');
                var weeksCycle = $(this).data('weekscycle');
                var dateDebut = $(this).data('datedebut');

                var $form = $('#updatePtiForm');
                $form.find('[name="ptiId"]').val(idPti);
                $form.find('[name="siteId"]').val(idSite);
                $form.find('[name="dateDebut"]').val(dateDebut);
                $form.find('[name="employeId"]').val(idEmploye);
                $form.find('[name="nbWeeksInCycle"]').val(weeksCycle);
                $form.find('.outputNbWeeksInCycle').html(weeksCycle);

            });

            $('.archiveEmploye').on('click', function () {
                var idEmploye = $(this).data('idemploye');
                if (confirm('<?= trans('Vous allez supprimer cet employé'); ?>')) {
                    busyApp();
                    $.post(
                        '<?= AGAPESHOTES_URL . 'process/ajaxEmployesProcess.php'; ?>',
                        {
                            ARCHIVEEMPLOYE: 'OK',
                            idEmploye: idEmploye
                        },
                        function (data) {
                            if (data === true || data == 'true') {
                                $('tr[data-idemploye="' + idEmploye + '"]').slideUp();
                            }
                            availableApp();
                        }
                    );
                }
            });
        });
    </script>
<?php endif;
require('footer.php'); ?>