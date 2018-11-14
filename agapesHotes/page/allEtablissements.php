<?php require('header.php'); ?>
<?= getTitle($Page->getName(), $Page->getSlug());
$Etablissement = new \App\Plugin\AgapesHotes\Etablissement();
$Etablissement->setStatus(1);
$allEtablissement = $Etablissement->showAll();
$Site = new \App\Plugin\AgapesHotes\Site();
$Secteur = new \App\Plugin\AgapesHotes\Secteur();
?>
    <div class="container-fluid">
        <button id="addEtablissement" type="button" class="btn btn-info btn-sm mb-4" data-toggle="modal"
                data-target="#modalAddEtablissement">
            <?= trans('Ajouter un établissement'); ?>
        </button>
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table id="pagesTable"
                           class="sortableTable table table-striped">
                        <thead>
                        <tr>
                            <th><?= trans('Nom'); ?></th>
                            <th><?= trans('Site'); ?></th>
                            <th><?= trans('Modifié par'); ?></th>
                            <th><?= trans('Modifié le'); ?></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if ($allEtablissement):
                            foreach ($allEtablissement as $etablissement):
                                $Site->setId($etablissement->site_id);
                                if ($Site->show() && $Site->getStatus()):
                                    $Secteur->setId($Site->getSecteurId());
                                    if ($Secteur->show() && $Secteur->getStatus()):
                                        ?>
                                        <tr data-idetablissement="<?= $etablissement->id ?>"
                                            class="displayHiddenListOnHover">
                                            <td>
                                                <span data-name="nom"><?= $etablissement->nom ?></span>
                                                <small class="hiddenList">
                                                    <a href="<?= AGAPESHOTES_URL; ?>page/allCourses/<?= $Secteur->getSlug(); ?>/<?= $Site->getSlug(); ?>/<?= $etablissement->slug; ?>/">
                                                        Liste de courses</a>&nbsp;
                                                    <a href="<?= AGAPESHOTES_URL; ?>page/vivreCrue/<?= $Secteur->getSlug(); ?>/<?= $Site->getSlug(); ?>/">
                                                        Vivre crue</a>&nbsp;
                                                    <a href="<?= AGAPESHOTES_URL; ?>page/allPrestations/<?= $Secteur->getSlug(); ?>/<?= $Site->getSlug(); ?>/<?= $etablissement->slug; ?>/">Liste
                                                        des prestations</a>&nbsp;
                                                    <a href="<?= AGAPESHOTES_URL; ?>page/mainCourante/<?= $Secteur->getSlug(); ?>/<?= $Site->getSlug(); ?>/">
                                                        Main courante
                                                    </a>
                                                </small>
                                            </td>
                                            <td data-name="siteNom"
                                                data-siteid="<?= $Site->getId() ?>"><?= $Site->getNom() ?></td>
                                            <td><?= getUserEntitled($etablissement->userId); ?></td>
                                            <td><?= displayTimeStamp($etablissement->updated_at) ?></td>
                                            <td>
                                                <button data-idetablissement="<?= $etablissement->id ?>"
                                                        data-toggle="modal"
                                                        data-target="#modalUpdateEtablissement"
                                                        class="btn btn-sm updateEtablissement"
                                                        title="<?= trans('Modifier'); ?>">
                                                    <span class="btnEdit"><i class="fas fa-wrench"></i></span>
                                                </button>
                                                <button type="button" class="btn btn-sm archiveEtablissement"
                                                        title="<?= trans('Archiver'); ?>"
                                                        data-idetablissement="<?= $etablissement->id ?>">
                                                    <span class="btnArchive"><i class="fas fa-archive"></i></span>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endif;
                                endif;
                            endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalAddEtablissement" tabindex="-1" role="dialog"
         aria-labelledby="modalAddEtablissementTitle"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="" method="post" id="addEtablissementForm">
                    <div class="modal-header">
                        <h5 class="modal-title"
                            id="modalAddEtablissementTitle"><?= trans('Ajouter un établissement'); ?></h5>
                    </div>
                    <div class="modal-body" id="modalAddEtablissementBody">
                        <div class="row">
                            <div class="col-12 my-2">
                                <?= \App\Form::text('Nom', 'nom', 'text', '', true, 255); ?>
                            </div>
                            <div class="col-12 my-2">
                                <?php
                                $Site->setStatus(1);
                                $allSites = extractFromObjToSimpleArr($Site->showAll(), 'id', 'nom');
                                echo \App\Form::select('Site', 'site_id', $allSites, '', true); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 my-2" id="FormAddEtablissementInfos"></div>
                        </div>
                    </div>
                    <div class="modal-footer" id="modalAddEtablissementFooter">
                        <?= \App\Form::target('ADDETABLISSEMENT'); ?>
                        <button type="submit" id="saveAddEtablissementBtn"
                                class="btn btn-primary"><?= trans('Enregistrer'); ?></button>
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal"><?= trans('Fermer'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalUpdateEtablissement" tabindex="-1" role="dialog"
         aria-labelledby="modalUpdateEtablissementTitle"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="" method="post" id="updateEtablissementForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalUpdateEtablissementTitle"></h5>
                    </div>
                    <div class="modal-body" id="modalUpdateEtablissementBody">
                        <div class="row">
                            <div class="col-12 my-2">
                                <input type="hidden" id="updateEtablissementInputId" name="idEtablissementUpdate"
                                       value="">
                                <?= \App\Form::text('Nom', 'nom', 'text', '', true, 255); ?>
                            </div>
                            <div class="col-12 my-2">
                                <?php
                                $Site->setStatus(1);
                                $allSites = extractFromObjToSimpleArr($Site->showAll(), 'id', 'nom');
                                echo \App\Form::select('Site', 'site_id', $allSites, '', true); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 my-2" id="FormUpdateEtablissementInfos"></div>
                        </div>
                    </div>
                    <div class="modal-footer" id="modalUpdateEtablissementFooter">
                        <?= \App\Form::target('UPDATEETABLISSEMENT'); ?>
                        <button type="submit" id="saveUpdateEtablissementBtn"
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

            $('#saveAddEtablissementBtn').on('click', function (event) {
                event.preventDefault();

                $('#FormAddEtablissementInfos').hide().html('');
                busyApp();

                $.post(
                    '<?= AGAPESHOTES_URL . 'process/ajaxEtablissementProcess.php'; ?>',
                    $('#addEtablissementForm').serialize(),
                    function (data) {
                        if (data === true || data == 'true') {
                            $('#loader').fadeIn(400);
                            location.reload();
                        } else {
                            $('#FormAddEtablissementInfos')
                                .html('<p class="bg-danger text-white">' + data + '</p>').show();
                        }
                        availableApp();
                    }
                )
            });

            $('#saveUpdateEtablissementBtn').on('click', function (event) {
                event.preventDefault();

                $('#FormUpdateEtablissementInfos').hide().html('');
                busyApp();

                $.post(
                    '<?= AGAPESHOTES_URL . 'process/ajaxEtablissementProcess.php'; ?>',
                    $('#updateEtablissementForm').serialize(),
                    function (data) {
                        if (data === true || data == 'true') {
                            $('#loader').fadeIn(400);
                            location.reload();
                        } else {
                            $('#FormUpdateEtablissementInfos')
                                .html('<p class="bg-danger text-white">' + data + '</p>').show();
                        }
                        availableApp();
                    }
                )
            });

            $('.updateEtablissement').on('click', function () {
                var idEtablissement = $(this).data('idetablissement');
                var $tr = $('tr[data-idetablissement="' + idEtablissement + '"]');
                var $oldSecteur = $tr.find('[data-name="siteNom"]');

                var oldName = $tr.find('[data-name="nom"]').text();
                var $oldSiteName = $oldSecteur.text();
                var oldSiteId = $oldSecteur.data('siteid');

                var $form = $('#updateEtablissementForm');

                $('#modalUpdateEtablissementTitle').html('Mettre à jour ' + oldName);
                $form.find('input#updateEtablissementInputId').val(idEtablissement);
                $form.find('input#nom').val(oldName);
                $form.find('select#site_id').val(oldSiteId);

            });

            $('.archiveEtablissement').on('click', function (event) {
                event.preventDefault();
                var idEtablissement = $(this).data('idetablissement');

                if (confirm('<?= trans('Vous allez archiver cet établissement'); ?>')) {
                    busyApp();
                    $.post(
                        '<?= AGAPESHOTES_URL . 'process/ajaxEtablissementProcess.php'; ?>',
                        {
                            ARCHIVEETABLISSEMENT: 'OK',
                            idEtablissementArchive: idEtablissement
                        },
                        function (data) {
                            if (data === true || data == 'true') {
                                $('tr[data-idetablissement="' + idEtablissement + '"]').slideUp();
                            } else {
                                alert(data);
                            }

                            availableApp();
                        }
                    );
                }
            });
        });
    </script>
<?php require('footer.php'); ?>