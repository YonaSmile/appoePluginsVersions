<?php require('header.php'); ?>
<?= getTitle($Page->getName(), $Page->getSlug());
$Site = new \App\Plugin\AgapesHotes\Site();
$allSites = $Site->showAll();
$Secteur = new \App\Plugin\AgapesHotes\Secteur();
?>
    <div class="container-fluid">
        <button id="addSite" type="button" class="btn btn-info btn-sm mb-4" data-toggle="modal"
                data-target="#modalAddSite">
            <?= trans('Ajouter un site'); ?>
        </button>
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table id="pagesTable"
                           class="sortableTable table table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th><?= trans('Nom'); ?></th>
                            <th><?= trans('Secteur'); ?></th>
                            <th><?= trans('Modifié par'); ?></th>
                            <th><?= trans('Modifié le'); ?></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if ($allSites):
                            foreach ($allSites as $site):
                                $Secteur->setId($site->secteur_id);
                                if ($Secteur->show() && $Secteur->getStatus()):
                                    ?>
                                    <tr data-idsite="<?= $site->id ?>" class="displayHiddenListOnHover">
                                        <td data-name="siteRef"><?= $site->ref ?></td>
                                        <td>
                                            <span data-name="nom"><?= $site->nom ?></span>
                                            <small class="hiddenList">
                                                <a href="<?= AGAPESHOTES_URL; ?>page/allPrestations/<?= $Secteur->getSlug(); ?>/<?= $site->slug; ?>/">Liste
                                                    des prestations</a>&nbsp;
                                                <a href="<?= AGAPESHOTES_URL; ?>page/mainCourante/<?= $Secteur->getSlug(); ?>/<?= $site->slug; ?>/">
                                                    Main courante
                                                </a>&nbsp;
                                                <a href="<?= AGAPESHOTES_URL; ?>page/mainSupplementaire/<?= $Secteur->getSlug(); ?>/<?= $site->slug; ?>/">
                                                    Main Supplémentaire
                                                </a>&nbsp;
                                                <a href="<?= AGAPESHOTES_URL; ?>page/planning/<?= $Secteur->getSlug(); ?>/<?= $site->slug; ?>/">
                                                    Planning
                                                </a>
                                            </small>
                                        </td>
                                        <td data-name="secteurNom"
                                            data-secteurid="<?= $Secteur->getId() ?>"><?= $Secteur->getNom() ?></td>
                                        <td><?= getUserEntitled($site->userId); ?></td>
                                        <td><?= displayTimeStamp($site->updated_at) ?></td>
                                        <td>
                                            <button data-idsite="<?= $site->id ?>" data-toggle="modal"
                                                    data-target="#modalUpdateSite"
                                                    class="btn btn-sm updateSite" title="<?= trans('Modifier'); ?>">
                                                <span class="btnEdit"><i class="fas fa-wrench"></i></span>
                                            </button>
                                            <button type="button" class="btn btn-sm archiveSite"
                                                    title="<?= trans('Archiver'); ?>"
                                                    data-idsite="<?= $site->id ?>">
                                                <span class="btnArchive"><i class="fas fa-archive"></i></span>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endif;
                            endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalAddSite" tabindex="-1" role="dialog" aria-labelledby="modalAddSiteTitle"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="" method="post" id="addSiteForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAddSiteTitle"><?= trans('Ajouter un site'); ?></h5>
                    </div>
                    <div class="modal-body" id="modalAddSiteBody">
                        <div class="row">
                            <div class="col-12 col-lg-6 my-2">
                                <?= \App\Form::text('Référence', 'ref', 'text', !empty($_POST['ref']) ? $_POST['ref'] : '', true, 50); ?>
                            </div>
                            <div class="col-12 col-lg-6 my-2">
                                <?= \App\Form::text('Nom', 'nom', 'text', !empty($_POST['nom']) ? $_POST['nom'] : '', true, 255); ?>
                            </div>
                            <div class="col-12 my-2">
                                <?php
                                $Secteur->setStatus(1);
                                $allSecteurs = extractFromObjToSimpleArr($Secteur->showAll(), 'id', 'nom');
                                echo \App\Form::select('Secteur', 'secteur_id', $allSecteurs, !empty($_POST['secteur_id']) ? $_POST['secteur_id'] : '', true); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 my-2" id="FormAddSiteInfos"></div>
                        </div>
                    </div>
                    <div class="modal-footer" id="modalAddSiteFooter">
                        <?= \App\Form::target('ADDSITE'); ?>
                        <button type="submit" id="saveAddSiteBtn"
                                class="btn btn-primary"><?= trans('Enregistrer'); ?></button>
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal"><?= trans('Fermer'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalUpdateSite" tabindex="-1" role="dialog" aria-labelledby="modalUpdateSiteTitle"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="" method="post" id="updateSiteForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalUpdateSiteTitle"></h5>
                    </div>
                    <div class="modal-body" id="modalUpdateSiteBody">
                        <div class="row">
                            <input type="hidden" id="updateSiteInputId" name="idSiteUpdate" value="">
                            <div class="col-12 col-lg-6 my-2">
                                <?= \App\Form::text('Référence', 'ref', 'text', !empty($_POST['ref']) ? $_POST['ref'] : '', true, 50); ?>
                            </div>
                            <div class="col-12 col-lg-6 my-2">
                                <?= \App\Form::text('Nom', 'nom', 'text', '', true, 255); ?>
                            </div>
                            <div class="col-12 my-2">
                                <?php
                                $allSecteurs = extractFromObjToSimpleArr($Secteur->showAll(), 'id', 'nom');
                                echo \App\Form::select('Secteur', 'secteur_id', $allSecteurs, '', true); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 my-2" id="FormUpdateSiteInfos"></div>
                        </div>
                    </div>
                    <div class="modal-footer" id="modalUpdateSiteFooter">
                        <?= \App\Form::target('UPDATESITE'); ?>
                        <button type="submit" id="saveUpdateSiteBtn"
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

            $('#saveAddSiteBtn').on('click', function (event) {
                event.preventDefault();

                $('#FormAddSiteInfos').hide().html('');
                busyApp();

                $.post(
                    '<?= AGAPESHOTES_URL . 'process/ajaxSiteProcess.php'; ?>',
                    $('#addSiteForm').serialize(),
                    function (data) {
                        if (data === true || data == 'true') {
                            $('#loader').fadeIn(400);
                            location.reload();
                        } else {
                            $('#FormAddSiteInfos')
                                .html('<p class="bg-danger text-white">' + data + '</p>').show();
                        }
                        availableApp();
                    }
                )
            });

            $('#saveUpdateSiteBtn').on('click', function (event) {
                event.preventDefault();

                $('#FormUpdateSiteInfos').hide().html('');
                busyApp();

                $.post(
                    '<?= AGAPESHOTES_URL . 'process/ajaxSiteProcess.php'; ?>',
                    $('#updateSiteForm').serialize(),
                    function (data) {
                        if (data === true || data == 'true') {
                            $('#loader').fadeIn(400);
                            location.reload();
                        } else {
                            $('#FormUpdateSiteInfos')
                                .html('<p class="bg-danger text-white">' + data + '</p>').show();
                        }
                        availableApp();
                    }
                )
            });

            $('.updateSite').on('click', function () {
                var idSite = $(this).data('idsite');
                var $tr = $('tr[data-idsite="' + idSite + '"]');
                var $oldSecteur = $tr.find('[data-name="secteurNom"]');

                var oldName = $tr.find('[data-name="nom"]').text();
                var oldRef = $tr.find('[data-name="siteRef"]').text();
                var $oldSecteurName = $oldSecteur.text();
                var oldSecteurId = $oldSecteur.data('secteurid');

                var $form = $('#updateSiteForm');

                $('#modalUpdateSiteTitle').html('Mettre à jour ' + oldName);
                $form.find('input#updateSiteInputId').val(idSite);
                $form.find('input#ref').val(oldRef);
                $form.find('input#nom').val(oldName);
                $form.find('select#secteur_id').val(oldSecteurId);

            });

            $('.archiveSite').on('click', function (event) {
                event.preventDefault();
                var idSite = $(this).data('idsite');

                if (confirm('<?= trans('Vous allez archiver ce site'); ?>')) {
                    busyApp();
                    $.post(
                        '<?= AGAPESHOTES_URL . 'process/ajaxSiteProcess.php'; ?>',
                        {
                            ARCHIVESITE: 'OK',
                            idSiteArchive: idSite
                        },
                        function (data) {
                            if (data === true || data == 'true') {
                                $('tr[data-idsite="' + idSite + '"]').slideUp();
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