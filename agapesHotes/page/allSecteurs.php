<?php require('header.php'); ?>
<?= getTitle($Page->getName(), $Page->getSlug());
$Secteur = new \App\Plugin\AgapesHotes\Secteur();
$Secteur->setStatus(1);
$allSecteurs = $Secteur->showAll();
?>
    <div class="container-fluid">
        <button id="addSecteur" type="button" class="btn btn-info btn-sm mb-4" data-toggle="modal"
                data-target="#modalAddSecteur">
            <?= trans('Ajouter un secteur'); ?>
        </button>
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table id="pagesTable"
                           class="sortableTable table table-striped">
                        <thead>
                        <tr>
                            <th><?= trans('Nom'); ?></th>
                            <th><?= trans('Modifié par'); ?></th>
                            <th><?= trans('Modifié le'); ?></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if ($allSecteurs):
                            foreach ($allSecteurs as $secteur): ?>
                                <tr data-idsecteur="<?= $secteur->id ?>">
                                    <td data-name="nom"><?= $secteur->nom ?></td>
                                    <td><?= getUserEntitled($secteur->userId); ?></td>
                                    <td><?= displayTimeStamp($secteur->updated_at) ?></td>
                                    <td>
                                        <button data-idsecteur="<?= $secteur->id ?>"
                                                class="btn btn-sm updateSecteur" title="<?= trans('Modifier'); ?>">
                                            <span class="btnEdit"><i class="fas fa-wrench"></i></span>
                                        </button>
                                        <button type="button" class="btn btn-sm archiveSecteur"
                                                title="<?= trans('Archiver'); ?>"
                                                data-idsecteur="<?= $secteur->id ?>">
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
    <div class="modal fade" id="modalAddSecteur" tabindex="-1" role="dialog" aria-labelledby="modalAddSecteurTitle"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="" method="post" id="addSecteurForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAddSecteurTitle"><?= trans('Ajouter un secteur'); ?></h5>
                    </div>
                    <div class="modal-body" id="modalSecteurBody">
                        <div class="row">
                            <div class="col-12 my-2">
                                <?= \App\Form::text('Nom', 'nom', 'text', !empty($_POST['nom']) ? $_POST['nom'] : '', true, 255); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 my-2" id="FormInfos"></div>
                        </div>
                    </div>
                    <div class="modal-footer" id="modalSecteurFooter">
                        <?= \App\Form::target('ADDSECTEUR'); ?>
                        <button type="submit" id="saveSecteurBtn"
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

            $('#saveSecteurBtn').on('click', function (event) {
                event.preventDefault();

                $('#FormInfos').hide().html('');
                busyApp();

                $.post(
                    '<?= AGAPESHOTES_URL . 'process/ajaxSecteurProcess.php'; ?>',
                    $('#addSecteurForm').serialize(),
                    function (data) {
                        if (data === true || data == 'true') {
                            $('#loader').fadeIn(400);
                            location.reload();
                        } else {
                            $('#FormInfos')
                                .html('<p class="bg-danger text-white">' + data + '</p>').show();
                        }
                        availableApp();
                    }
                )
            });

            $('.updateSecteur').on('click', function (event) {
                event.preventDefault();
                var idSecteur = $(this).data('idsecteur');

                var $tdNom = $('tr[data-idsecteur="' + idSecteur + '"] td[data-name="nom"]');
                var oldName = $tdNom.text();

                $tdNom.html('<div class="positionRelative"><input name="nom" value="' + oldName + '" class="form-control">' +
                    '<button type="button" data-idsecteur="' + idSecteur + '" class="btn btn-sm btn-success floatOnrightElement btnUpdateSecteur"><i class="fas fa-check"></i></button></div>');
            });

            $('body').on('click', '.btnUpdateSecteur', function (event) {
                event.preventDefault();
                var idSecteur = $(this).data('idsecteur');
                var newName = $(this).prev('input').val();

                $.post(
                    '<?= AGAPESHOTES_URL . 'process/ajaxSecteurProcess.php'; ?>',
                    {
                        UPDATESECTEUR: 'OK',
                        idSecteurUpdate: idSecteur,
                        newName: newName
                    },
                    function (data) {
                        if (data === true || data == 'true') {
                            var $tdNom = $('tr[data-idsecteur="' + idSecteur + '"] td[data-name="nom"]');
                            $tdNom.html(newName);
                        } else {
                            alert(data);
                        }

                        availableApp();
                    }
                );

            });

            $('.archiveSecteur').on('click', function (event) {
                event.preventDefault();
                var idSecteur = $(this).data('idsecteur');

                if (confirm('<?= trans('Vous allez archiver ce secteur'); ?>')) {
                    busyApp();
                    $.post(
                        '<?= AGAPESHOTES_URL . 'process/ajaxSecteurProcess.php'; ?>',
                        {
                            ARCHIVESECTEUR: 'OK',
                            idSecteurArchive: idSecteur
                        },
                        function (data) {
                            if (data === true || data == 'true') {
                                $('tr[data-idsecteur="' + idSecteur + '"]').slideUp();
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