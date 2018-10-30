<?php require('header.php'); ?>
<?= getTitle($Page->getName(), $Page->getSlug()); ?>
    <div class="container-fluid">
        <?php
        if (class_exists('App\Plugin\People\People')) :

        //Get Employe
        $Employe = new \App\Plugin\AgapesHotes\Employe();
        $allEmployes = $Employe->showByType();

        //Get Site
        $Site = new \App\Plugin\AgapesHotes\Site();
        $allDbSites = $Site->showAll();
        $allSites = extractFromObjToSimpleArr($allDbSites, 'id', 'nom');

        //Get Employe Contrat
        $EmployeContrat = new \App\Plugin\AgapesHotes\EmployeContrat();
        ?>
        <button id="addEmplye" type="button" class="btn btn-info btn-sm mb-4" data-toggle="modal"
                data-target="#modalAddEmploye">
            <?= trans('Ajouter un employé'); ?>
        </button>
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table id="pagesTable"
                           class="sortableTable table table-striped">
                        <thead>
                        <tr>
                            <th><?= trans('Nature'); ?></th>
                            <th><?= trans('Nom'); ?></th>
                            <th><?= trans('Prénom'); ?></th>
                            <th><?= trans('Contrats'); ?></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if ($allEmployes): ?>
                            <?php foreach ($allEmployes as $employe): ?>
                                <tr data-idemploye="<?= $employe->id ?>">
                                    <td data-nature="<?= $employe->nature; ?>"><?= !empty($employe->nature) ? trans(PEOPLE_NATURE[$employe->nature]) : ''; ?></td>
                                    <td data-name="<?= $employe->name ?>"><?= $employe->name ?></td>
                                    <td data-firstname="<?= $employe->firstName ?>"><?= $employe->firstName ?></td>
                                    <td>
                                        <?php $allContrats = array();
                                        foreach ($allDbSites as $site) {
                                            $EmployeContrat->setSiteId($site->id);
                                            $EmployeContrat->setEmployeId($employe->id);
                                            $EmployeContrat->setDateDebut(date('Y-m-d'));
                                            if ($EmployeContrat->showReelContrat()) {
                                                $allContrats[$site->id][] = $EmployeContrat->getTypeContrat() . ' - ' . $EmployeContrat->getNbHeuresSemaines() . 'h/s';
                                            }
                                        }
                                        foreach ($allContrats as $siteId => $contrat): ?>
                                            <span data-toggle="popover" data-placement="top" class="mr-2"
                                                  data-content=" <?= implode(', ', $contrat); ?>">
                                                <?= $allSites[$siteId]; ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm updateContrat"
                                                data-toggle="modal" data-target="#modalUpdateContratEmploye"
                                                title="<?= trans('Contrat'); ?>"
                                                data-idemploye="<?= $employe->id ?>"
                                                data-name="<?= trans(PEOPLE_NATURE[$employe->nature]) . ' ' . $employe->name ?>">
                                            <span class="btnPrice"><i class="fas fa-file-alt"></i></span>
                                        </button>
                                        <button type="button" class="btn btn-sm updateEmploye"
                                                data-toggle="modal" data-target="#modalupdateEmploye"
                                                title="<?= trans('Modifier'); ?>"
                                                data-idemploye="<?= $employe->id ?>"
                                                data-nature="<?= $employe->nature; ?>"
                                                data-name="<?= $employe->name ?>"
                                                data-firstname="<?= $employe->firstName ?>">
                                            <span class="btnEdit"><i class="fas fa-wrench"></i></span>
                                        </button>
                                        <button type="button" class="btn btn-sm archiveEmploye"
                                                title="<?= trans('Archiver'); ?>"
                                                data-idemploye="<?= $employe->id ?>">
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
    <div class="modal fade" id="modalAddEmploye" tabindex="-1" role="dialog"
         aria-labelledby="modalAddEmployeTitle"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="" method="post" id="addEmployeForm">
                    <div class="modal-header">
                        <h5 class="modal-title"
                            id="modalAddEmployeTitle"><?= trans('Ajouter un employé'); ?></h5>
                    </div>
                    <div class="modal-body" id="modalAddEmployeBody">
                        <div class="row">
                            <div class="col-12 my-2">
                                <?php
                                $excludesFileds = array('birthDateF' => false, 'emailF' => false, 'telF' => false, 'addressF' => false, 'zipF' => false, 'cityF' => false, 'countryF' => false);
                                $requiredFileds = array('natureR' => true, 'nameR' => true, 'firstNameR' => true);
                                echo people_addPersonFormFields($excludesFileds, array(), $requiredFileds, 'ADDEMPLOYE', false, false);
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 my-2" id="FormAddEmployeInfos"></div>
                        </div>
                    </div>
                    <div class="modal-footer" id="modalAddEmployeFooter">
                        <button type="submit" id="saveEmployeBtn"
                                class="btn btn-primary"><?= trans('Enregistrer'); ?></button>
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal"><?= trans('Fermer'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalupdateEmploye" tabindex="-1" role="dialog"
         aria-labelledby="modalUpdateEmployeTitle"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="" method="post" id="updateEmployeForm">
                    <div class="modal-header">
                        <h5 class="modal-title"
                            id="modalUpdateEmployeTitle"><?= trans('Mettre à jour un employé'); ?></h5>
                    </div>
                    <div class="modal-body" id="modalUpdateEmployeBody">
                        <div class="row">
                            <div class="col-12 my-2">
                                <input type="hidden" name="id" value="">
                                <?php
                                $excludesFileds = array('birthDateF' => false, 'emailF' => false, 'telF' => false, 'addressF' => false, 'zipF' => false, 'cityF' => false, 'countryF' => false);
                                $requiredFileds = array('natureR' => true, 'nameR' => true, 'firstNameR' => true);
                                echo people_addPersonFormFields($excludesFileds, array(), $requiredFileds, 'UPDATEEMPLOYE', false, false);
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 my-2" id="FormUpdateEmployeInfos"></div>
                        </div>
                    </div>
                    <div class="modal-footer" id="modalUpdateEmployeFooter">
                        <button type="submit" id="updateEmployeBtn"
                                class="btn btn-primary"><?= trans('Enregistrer'); ?></button>
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal"><?= trans('Fermer'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalUpdateContratEmploye" tabindex="-1" role="dialog"
         aria-labelledby="modalUpdateContratEmployeTitle"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="" method="post" id="updateContratEmployeForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalUpdateContratEmployeTitle"></h5>
                    </div>
                    <div class="modal-body" id="modalUpdateContratEmployeBody">
                        <div class="row">
                            <?= getTokenField(); ?>
                            <?= \App\Form::target('UPDATEEMPLOYECONTRAT'); ?>
                            <input type="hidden" name="employeId" value="">
                            <div class="col-12 col-lg-4 my-2">
                                <?= \App\Form::select('Site', 'siteId', $allSites, '', true); ?>
                            </div>
                            <div class="col-12 col-lg-8 my-2">
                                <?= \App\Form::text('Date d\'effet du nouveau contrat', 'dateDebut', 'date', '', true, 10); ?>
                            </div>
                            <div class="col-12 col-lg-6 my-2">
                                <?= \App\Form::text('Type de contrat', 'typeContrat', 'text', '', true, 50, 'list="typeContratsList" autocomplete="off"'); ?>
                                <datalist id="typeContratsList">
                                    <option value="CDI">CDI</option>
                                    <option value="CDD">CDD</option>
                                </datalist>
                            </div>
                            <div class="col-12 col-lg-6 my-2">
                                <output for="nbHeuresSemaines" class="outputNbHeuresSemaines float-right">35</output>
                                <?= \App\Form::text('Heures par semaines', 'nbHeuresSemaines', 'range', '35', true, 50, 'min="0.25" max="50" step="0.25"', '', 'custom-range'); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 my-2" id="FormUpdateContratEmployeInfos"></div>
                        </div>
                    </div>
                    <div class="modal-footer" id="modalUpdateContratEmployeFooter">
                        <button type="submit" id="saveContratEmployeBtn"
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
                $('#modalUpdateContratEmployeTitle').html('Gérez le contrat de <em>' + $btn.data('name') + '</em>');
                $('input[name="employeId"]').val($btn.data('idemploye'))
            });

            $('#saveContratEmployeBtn').on('click', function (event) {
                event.preventDefault();

                $('#FormUpdateContratEmployeInfos').hide().html('');

                busyApp();
                $.post(
                    '<?= AGAPESHOTES_URL . 'process/ajaxEmployesProcess.php'; ?>',
                    $('#updateContratEmployeForm').serialize(),
                    function (data) {
                        if (data === true || data == 'true') {
                            $('#loader').fadeIn(400);
                            location.reload();
                        } else {
                            $('#FormUpdateContratEmployeInfos')
                                .html('<p class="bg-danger text-white">' + data + '</p>').show();
                        }

                        availableApp();
                    }
                );
            });

            $('[data-toggle="popover"]').popover({
                trigger: 'hover'
            });

            $('input#nbHeuresSemaines').on("input", function () {
                $(this).val(this.value);
                $('.outputNbHeuresSemaines').val(this.value);
            }).trigger("change");

            $('.updateEmploye').on('click', function (event) {
                event.preventDefault();
                var idEmploye = $(this).data('idemploye');
                var nature = $(this).data('nature');
                var name = $(this).data('name');
                var firstName = $(this).data('firstname');

                var $form = $('#updateEmployeForm');
                $form.find('[name="id"]').val(idEmploye);
                $form.find('[name="nature"]').val(nature);
                $form.find('[name="name"]').val(name);
                $form.find('[name="firstName"]').val(firstName);
            });

            $('#updateEmployeBtn').on('click', function (event) {
                event.preventDefault();

                $('#FormUpdateEmployeInfos').hide().html('');
                busyApp();
                $.post(
                    '<?= AGAPESHOTES_URL . 'process/ajaxEmployesProcess.php'; ?>',
                    $('#updateEmployeForm').serialize(),
                    function (data) {
                        if (data === true || data == 'true') {
                            $('#loader').fadeIn(400);
                            location.reload();
                        } else {
                            $('#FormUpdateEmployeInfos')
                                .html('<p class="bg-danger text-white">' + data + '</p>').show();
                        }

                        availableApp();
                    }
                );

            });

            $('#saveEmployeBtn').on('click', function (event) {
                event.preventDefault();

                $('#FormAddEmployeInfos').hide().html('');
                busyApp();

                $.post(
                    '<?= AGAPESHOTES_URL . 'process/ajaxEmployesProcess.php'; ?>',
                    $('#addEmployeForm').serialize(),
                    function (data) {
                        if (data === true || data == 'true') {
                            $('#loader').fadeIn(400);
                            location.reload();
                        } else {
                            $('#FormAddEmployeInfos')
                                .html('<p class="bg-danger text-white">' + data + '</p>').show();
                        }
                        availableApp();
                    }
                )
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