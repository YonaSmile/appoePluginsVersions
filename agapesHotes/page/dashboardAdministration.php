<?php
$inventaireUrl = 'https://serventest.fr/pro/liaison_appoe/getInventaireServentest.php';
$commandesUrl = 'https://serventest.fr/pro/liaison_appoe/getCommandServentest.php';
$params = array(
    'key' => '123',
    'ref' => 'FALLER',
    'dateDebut' => date('Y-m-01'),
    'dateFin' => date('Y-m-t')
);

$inventaireRequest = json_decode(postHttpRequest($inventaireUrl, $params), true);
$commandesRequest = json_decode(postHttpRequest($commandesUrl, $params), true);

$Site = new \App\Plugin\AgapesHotes\Site();
$allSites = $Site->showAll();
$allSitesBySecteur = groupMultipleKeysObjectsArray($allSites, 'secteur_id');
$Secteur = new \App\Plugin\AgapesHotes\Secteur();
?>
<div class="row mb-3">
    <div class="d-flex col-12 col-lg-8">
        <div class="card border-0 w-100">
            <div class="card-header bg-white pb-0 border-0 boardBlock1Title">
                <h5 class="m-0 pl-4 colorPrimary"><?= trans('Synthèse'); ?> ADMINISTRATION</h5>
                <hr class="mx-4">
            </div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-striped tableNonEffect text-center">
                                <thead>
                                <tr>
                                    <th><?= trans('Site'); ?></th>
                                    <th colspan="2"><?= trans('Chiffre d\'affaire'); ?></th>
                                    <th><?= trans('Consommation'); ?></th>
                                    <th><?= trans('Personnel'); ?></th>
                                    <th><?= trans('Frais généraux'); ?></th>
                                    <th><?= trans('Synthèse secteur'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if ($allSitesBySecteur):
                                    foreach ($allSitesBySecteur as $secteurId => $allSites):
                                        $Secteur->setId($secteurId);
                                        if ($Secteur->show() && $Secteur->getStatus()): ?>
                                            <tr>
                                                <th style="text-align:center !important; background:#4fb99f;color:#fff;"
                                                    colspan="7"><?= $Secteur->getNom(); ?></th>
                                            </tr>
                                            <?php foreach ($allSites as $site): ?>
                                                <tr>
                                                    <th rowspan="3"
                                                        style="vertical-align:middle;"><?= $site->nom ?></th>
                                                    <td style="text-align:left !important;">
                                                        <small><em>Budget</em></small>
                                                    </td>
                                                    <td>123</td>
                                                    <td>123</td>
                                                    <td>123</td>
                                                    <td>123</td>
                                                    <td style="text-align:center !important;">123</td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:left !important;">
                                                        <small><em><?= date('Y') - 1; ?></em></small>
                                                    </td>
                                                    <td>123</td>
                                                    <td>123</td>
                                                    <td>123</td>
                                                    <td>123</td>
                                                    <td style="text-align:center !important;">123</td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:left !important;">
                                                        <small><em><?= date('Y'); ?></em></small>
                                                    </td>
                                                    <td>123</td>
                                                    <td>123</td>
                                                    <td>123</td>
                                                    <td>123</td>
                                                    <td style="text-align:center !important;">123</td>
                                                </tr>
                                            <?php endforeach;
                                        endif;
                                    endforeach;
                                endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex col-12 col-lg-4">
        <div class="card border-0 w-100">
            <div class="card-header bg-white pb-0 border-0 boardBlock2Title">
                <h5 class="m-0 pl-4 colorSecondary"><?= trans('Infos'); ?></h5>
                <hr class="mx-4">
            </div>
            <div class="card-body pt-0"></div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="d-flex col-12 col-lg-8 px-3">
        <div class="card border-0 w-100">
            <div class="card-header bg-white pb-0 border-0 boardBlock1Title">
                <h5 class="m-0 pl-4 colorPrimary">Synthèse Achat cuisinier - Site FALLER - Pour le
                    mois <?= strftime("%B", strtotime(date('Y-m-d'))); ?></h5>
                <hr class="mx-4">
            </div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover tableNonEffect">
                                <thead>
                                <tr>
                                    <th><?= trans('Fournisseur'); ?></th>
                                    <th><?= trans('Date livraison'); ?></th>
                                    <th><?= trans('Total'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <th colspan="3"
                                        style="text-align:center !important; background:#4fb99f;color:#fff;">Denrées
                                    </th>
                                </tr>
                                <?php if ($commandesRequest):
                                    $totalCommandDenree = 0;
                                    $totalCommandUniqueEntretien = 0;
                                    foreach ($commandesRequest as $key => $allDenree):
                                        if (trim($allDenree['fournisseur']) != 'C2M'):
                                            $totalCommandDenree += $allDenree['total']; ?>
                                            <tr>
                                                <td><?= $allDenree['fournisseur']; ?></td>
                                                <td><?= $allDenree['date_livraison']; ?></td>
                                                <td><?= $allDenree['total']; ?>€</td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <tr>
                                        <td colspan="2">Total denrées</td>
                                        <th><?= $totalCommandDenree; ?>€</th>
                                    </tr>
                                    <tr>
                                        <th colspan="3"
                                            style="text-align:center !important; background:#4fb99f;color:#fff;">
                                            Produits unique
                                            entretien
                                        </th>
                                    </tr>
                                    <?php foreach ($commandesRequest as $key => $allUniqueEntretien):
                                    if (trim($allUniqueEntretien['fournisseur']) == 'C2M'):
                                        $totalCommandUniqueEntretien += $allUniqueEntretien['total']; ?>
                                        <tr>
                                            <td><?= $allUniqueEntretien['fournisseur']; ?></td>
                                            <td><?= $allUniqueEntretien['date_livraison']; ?></td>
                                            <td><?= $allUniqueEntretien['total']; ?>€</td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                    <tr>
                                        <td colspan="2">Total produits unique entretien</td>
                                        <th><?= $totalCommandUniqueEntretien; ?>€</th>
                                    </tr>
                                    <tr>
                                        <th colspan="3"
                                            style="text-align:center !important; background:#b96d36;color:#fff;">
                                            Total <?= financial($totalCommandUniqueEntretien + $totalCommandDenree); ?>
                                            €
                                        </th>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex col-12 col-lg-4">
        <div class="card border-0 w-100">
            <div class="card-header bg-white pb-0 border-0 boardBlock2Title">
                <h5 class="m-0 pl-4 colorSecondary"><?= trans('Infos'); ?></h5>
                <hr class="mx-4">
            </div>
            <div class="card-body pt-0"></div>
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="d-flex col-12 col-lg-8 px-3">
        <div class="card border-0 w-100">
            <div class="card-header bg-white pb-0 border-0 boardBlock1Title">
                <h5 class="m-0 pl-4 colorPrimary">Synthèse Conso cuisinier - Site FALLER - Pour le
                    mois <?= strftime("%B", strtotime(date('Y-m-d'))); ?></h5>
                <hr class="mx-4">
            </div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover tableNonEffect ">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th style="text-align: center !important;"><?= trans('Denrées'); ?></th>
                                    <th style="text-align: center !important;"><?= trans('Non alimentaire'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if ($inventaireRequest):
                                    $totalInventaireDenree = 0;
                                    $totalInventaireUniqueEntretien = 0;
                                    foreach ($inventaireRequest as $key => $inventaire) {
                                        if (trim($inventaire['fournisseur']) != 'C2M') {
                                            $totalInventaireDenree += $inventaire['total'];
                                        } else {
                                            $totalInventaireUniqueEntretien += $inventaire['total'];
                                        }
                                    } ?>
                                    <tr>
                                        <th style="width: 200px;">Stock initial</th>
                                        <td style="text-align: center !important;"><?= $totalInventaireDenree; ?>€
                                        </td>
                                        <td style="text-align: center !important;"><?= $totalInventaireUniqueEntretien; ?>
                                            €
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 200px;">Achats Serventest</th>
                                        <td style="text-align: center !important;"><?= $totalCommandDenree; ?>€</td>
                                        <td style="text-align: center !important;"><?= $totalCommandUniqueEntretien; ?>
                                            €
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 200px;">Notes de frais</th>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <th style="width: 200px;">Stock final</th>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <th style="width: 200px;">Consommation réelle</th>
                                        <td style="text-align: center !important;"><?= $totalCommandDenree + $totalInventaireDenree; ?>
                                            €
                                        </td>
                                        <td style="text-align: center !important;"><?= $totalCommandUniqueEntretien + $totalInventaireUniqueEntretien; ?>
                                            €
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex col-12 col-lg-4">
        <div class="card border-0 w-100">
            <div class="card-header bg-white pb-0 border-0 boardBlock2Title">
                <h5 class="m-0 pl-4 colorSecondary"><?= trans('Infos'); ?></h5>
                <hr class="mx-4">
            </div>
            <div class="card-body pt-0"></div>
        </div>
    </div>
</div>
