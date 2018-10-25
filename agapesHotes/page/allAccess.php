<?php require('header.php');
$Secteur = new \App\Plugin\AgapesHotes\Secteur();
$Site = new \App\Plugin\AgapesHotes\Site();

$allSecteurs = extractFromObjToSimpleArr($Secteur->showAll(), 'id', 'nom');
$allSites = extractFromObjToSimpleArr($Site->showAll(), 'id', 'nom');

$User = new \App\Users();
foreach ($User->showAll() as $user) {
    if (getRoleId($user->role) < 3) {
        $allUsers[$user->id] = $user;
    }
}
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
        <div class="modal fade" id="modalAddSiteAccess" tabindex="-1" role="dialog"
             aria-labelledby="modalAddSiteAccessTitle"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="" method="post" id="addSiteAccessForm">
                        <div class="modal-header">
                            <h5 class="modal-title"
                                id="modalAddSiteAccessTitle"><?= trans('Ajouter un accès à un site'); ?></h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12 my-2">
                                    <?= \App\Form::select('Utilisateur', 'secteurUserId', extractFromObjToSimpleArr($allUsers, 'id', 'nom', 'prenom'), '', true); ?>
                                </div>
                                <div class="col-12 my-2">
                                    <?= \App\Form::select('Site', 'site_id', $allSites, '', true); ?>
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
                            <?php foreach ($allSecteursAccess as $secteurAccess): ?>

                                <tr>
                                    <td><?= $secteurAccess->secteur_id; ?></td>
                                    <td><?= $secteurAccess->secteurUserId; ?></td>
                                    <td></td>
                                </tr>

                            <?php endforeach; ?>
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
                            <?php foreach ($allSitesAccess as $siteAccess): ?>

                                <tr>
                                    <td><?= $secteurAccess->site_id; ?></td>
                                    <td><?= $secteurAccess->siteUserId; ?></td>
                                    <td></td>
                                </tr>

                            <?php endforeach; ?>
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
                    <div class="modal-header">
                        <h5 class="modal-title"
                            id="modalAddSecteurAccessTitle"><?= trans('Ajouter un accès à un secteur'); ?></h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 my-2">
                                <?= \App\Form::select('Utilisateur', 'secteurUserId', extractFromObjToSimpleArr($allUsers, 'id', 'nom', 'prenom'), '', true); ?>
                            </div>
                            <div class="col-12 my-2">
                                <?= \App\Form::select('Secteur', 'secteur_id', $allSecteurs, '', true); ?>
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
<?php require('footer.php'); ?>