<?php
require('main.php');

$inventaireUrl = 'https://serventest.fr/pro/liaison_appoe/getInventaireServentest.php';
$inventaireParams = array(
    'key' => '123',
    'ref' => 'FALLER',
    'dateDebut' => date('Y-m-01'),
    'dateFin' => date('Y-m-t')
);

$inventaireRequest = json_decode(postHttpRequest($inventaireUrl, $inventaireParams), true);
$Site = new \App\Plugin\AgapesHotes\Site();
$allSites = $Site->showAll();
$allSitesBySecteur = groupMultipleKeysObjectsArray($allSites, 'secteur_id');
$Secteur = new \App\Plugin\AgapesHotes\Secteur();
?>
<div class="row mb-3">
    <div class="d-flex col-12 col-lg-8">
        <div class="card border-0 w-100">
            <div class="card-header bg-white pb-0 border-0 boardBlock1Title">
                <h5 class="m-0 pl-4 colorPrimary"><?= trans('Synthèse'); ?> AGAPES HÔTES</h5>
                <hr class="mx-4">
            </div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="sortableTable table table-striped tableNonEffect text-center">
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
                                                <td style="text-align:center !important;"
                                                    colspan="7"><?= $Secteur->getNom(); ?></td>
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
