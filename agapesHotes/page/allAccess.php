<?php require('header.php');
$Secteur = new \App\Plugin\AgapesHotes\Secteur();
$Site = new \App\Plugin\AgapesHotes\Site();

$allSecteurs = extractFromObjToSimpleArr($Secteur->showAll(), 'id', 'nom');
$allSites = extractFromObjToSimpleArr($Site->showAll(), 'id', 'nom');

$allUsers = array();
global $ALLUSERS;
foreach ($ALLUSERS as $userId => $user) {
    if (getRoleId($user->role) < 3) {
        $allUsers[$userId] = $user;
    }
}
$allUsers = extractFromObjToSimpleArr($allUsers, 'id', 'nom', 'prenom');
$SiteAccess = new \App\Plugin\AgapesHotes\SiteAccess();
$allSitesAccess = $SiteAccess->showAll();

$SecteurAccess = new \App\Plugin\AgapesHotes\SecteurAccess();
$allSecteursAccess = $SecteurAccess->showAll();
?>
<?= getTitle($Page->getName(), $Page->getSlug()); ?>
    <div class="container-fluid">
        <button id="addSecteurAccess" type="button" class="btn btn-info btn-sm mb-4" data-toggle="modal"
                data-target="#modalAddSecteurAccess">
            <?= trans('Ajouter un accès à un secteur'); ?>
        </button>
        <button id="addSiteAccess" type="button" class="btn btn-info btn-sm mb-4" data-toggle="modal"
                data-target="#modalAddSiteAccess">
            <?= trans('Ajouter un accès à un site'); ?>
        </button>
        <a href="<?= getUrl('addUser/'); ?>" class="btn btn-secondary btn-sm mb-4">
            <?= trans('Ajouter un utilisateur'); ?>
        </a>
        <div class="row">
            <?php if ($allSecteursAccess): ?>
                <div class="col-12 col-lg-6">
                    <div class="table-responsive">
                        <table id="clientTable"
                               class="sortableTable table table-bordered">
                            <thead>
                            <tr>
                                <th><?= trans('Secteur'); ?></th>
                                <th><?= trans('Utilisateur'); ?></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($allSecteursAccess as $secteurAccess):
                                if (array_key_exists($secteurAccess->secteurUserId, $allUsers)): ?>
                                    <tr data-idsecteur="<?= $secteurAccess->id ?>">
                                        <td><?= $allSecteurs[$secteurAccess->secteur_id]; ?></td>
                                        <td><?= $allUsers[$secteurAccess->secteurUserId]; ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm deleteSecteurAccess"
                                                    title="<?= trans('Supprimer cet accès au secteur'); ?>"
                                                    data-idsecteur="<?= $secteurAccess->id ?>">
                                                <span class="btnArchive"><i class="fas fa-times"></i></span>
                                            </button>
                                        </td>
                                    </tr>

                                <?php endif;
                            endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($allSitesAccess): ?>
                <div class="col-12 col-lg-6">
                    <div class="table-responsive">
                        <table id="clientTable"
                               class="sortableTable table table-bordered">
                            <thead>
                            <tr>
                                <th><?= trans('Site'); ?></th>
                                <th><?= trans('Utilisateur'); ?></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($allSitesAccess as $siteAccess):
                                if (array_key_exists($siteAccess->siteUserId, $allUsers)): ?>
                                    <tr data-idsite="<?= $siteAccess->id ?>">
                                        <td><?= $allSites[$siteAccess->site_id]; ?></td>
                                        <td><?= $allUsers[$siteAccess->siteUserId]; ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm deleteSiteAccess"
                                                    title="<?= trans('Supprimer cet accès au site'); ?>"
                                                    data-idsite="<?= $siteAccess->id ?>">
                                                <span class="btnArchive"><i class="fas fa-times"></i></span>
                                            </button>
                                        </td>
                                    </tr>

                                <?php endif;
                            endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="modal fade" id="modalAddSecteurAccess" tabindex="-1" role="dialog"
         aria-labelledby="modalAddSecteurAccessTitle"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="" method="post" id="addSecteurAccessForm">
                    <?= getTokenField(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title"
                            id="modalAddSecteurAccessTitle"><?= trans('Ajouter un accès à un secteur'); ?></h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 my-2">
                                <?= \App\Form::select('Utilisateur', 'secteurUserId', $allUsers, '', true); ?>
                            </div>
                            <div class="col-12 my-2">
                                <?= \App\Form::select('Secteur', 'secteurId', $allSecteurs, '', true); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 my-2" id="FormAddSecteurAccessInfos"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <?= \App\Form::target('ADDSECTEURACCESS'); ?>
                        <button type="submit" id="saveAddSecteurAccessBtn"
                                class="btn btn-primary"><?= trans('Enregistrer'); ?></button>
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal"><?= trans('Fermer'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalAddSiteAccess" tabindex="-1" role="dialog"
         aria-labelledby="modalAddSiteAccessTitle"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="" method="post" id="addSiteAccessForm">
                    <?= getTokenField(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title"
                            id="modalAddSiteAccessTitle"><?= trans('Ajouter un accès à un site'); ?></h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 my-2">
                                <?= \App\Form::select('Utilisateur', 'siteUserId', $allUsers, '', true); ?>
                            </div>
                            <div class="col-12 my-2">
                                <?= \App\Form::select('Site', 'siteId', $allSites, '', true); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 my-2" id="FormAddSiteAccessInfos"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <?= \App\Form::target('ADDSITEACCESS'); ?>
                        <button type="submit" id="saveAddSiteAccessBtn"
                                class="btn btn-primary"><?= trans('Enregistrer'); ?></button>
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal"><?= trans('Fermer'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#saveAddSiteAccessBtn').on('click', function (event) {
                event.preventDefault();

                $('#FormAddSiteAccessInfos').hide().html('');
                busyApp();

                $.post(
                    '<?= AGAPESHOTES_URL . 'process/ajaxSiteAccessProcess.php'; ?>',
                    $('#addSiteAccessForm').serialize(),
                    function (data) {
                        if (data === true || data == 'true') {
                            $('#loader').fadeIn(400);
                            location.reload();
                        } else {
                            $('#FormAddSiteAccessInfos')
                                .html('<p class="bg-danger text-white">' + data + '</p>').show();
                        }
                        availableApp();
                    }
                );
            });
            $('#saveAddSecteurAccessBtn').on('click', function (event) {
                event.preventDefault();

                $('#FormAddSecteurAccessInfos').hide().html('');
                busyApp();

                $.post(
                    '<?= AGAPESHOTES_URL . 'process/ajaxSecteurAccessProcess.php'; ?>',
                    $('#addSecteurAccessForm').serialize(),
                    function (data) {
                        if (data === true || data == 'true') {
                            $('#loader').fadeIn(400);
                            location.reload();
                        } else {
                            $('#FormAddSecteurAccessInfos')
                                .html('<p class="bg-danger text-white">' + data + '</p>').show();
                        }
                        availableApp();
                    }
                );
            });

            $('.deleteSecteurAccess').on('click', function (event) {
                event.preventDefault();

                if (confirm('Vous allez supprimer cet accès !')) {
                    var idSecteur = $(this).data('idsecteur');
                    busyApp();

                    $.post(
                        '<?= AGAPESHOTES_URL . 'process/ajaxSecteurAccessProcess.php'; ?>',
                        {
                            DELETESECTEURACCESS: 'OK',
                            idSecteur: idSecteur
                        },
                        function (data) {
                            if (data === true || data == 'true') {
                                $('tr[data-idsecteur="' + idSecteur + '"]').slideUp();
                            }
                            availableApp();
                        }
                    );
                }
            });

            $('.deleteSiteAccess').on('click', function (event) {
                event.preventDefault();

                if (confirm('Vous allez supprimer cet accès !')) {
                    var idSite = $(this).data('idsite');
                    busyApp();

                    $.post(
                        '<?= AGAPESHOTES_URL . 'process/ajaxSiteAccessProcess.php'; ?>',
                        {
                            DELETESITEACCESS: 'OK',
                            idSite: idSite
                        },
                        function (data) {
                            if (data === true || data == 'true') {
                                $('tr[data-idsite="' + idSite + '"]').slideUp();
                            }
                            availableApp();
                        }
                    );
                }
            });
        });
    </script>
<?php require('footer.php'); ?>