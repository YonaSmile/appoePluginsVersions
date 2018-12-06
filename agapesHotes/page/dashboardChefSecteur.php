<?php
$SecteurAccess = new \App\Plugin\AgapesHotes\SecteurAccess();
$SecteurAccess->setSecteurUserId(getUserIdSession());
$Secteur = $SecteurAccess->showSecteurByUser();

if ($Secteur):
    $Site = new \App\Plugin\AgapesHotes\Site();
    $Site->setSecteurId($Secteur->id);
    $allSites = $Site->showBySecteur();
    $allSitesData = array();

    $inventaireUrl = 'https://serventest.fr/pro/liaison_appoe/getInventaireServentest.php';
    $commandesUrl = 'https://serventest.fr/pro/liaison_appoe/getRefacturationServentest.php';

    foreach ($allSites as $site) {

        $paramsMonthNow = array(
            'key' => '123',
            'ref' => $site->ref,
            'dateDebut' => date('Y-m-01'),
            'dateFin' => date('Y-m-t')
        );

        $paramsMonthAgo = array(
            'key' => '123',
            'ref' => $site->ref,
            'dateDebut' => date('Y-' . (date('m') - 1) . '-01'),
            'dateFin' => date('Y-m-t')
        );

        $allSitesData[$site->id]['inventaireRequest'] = json_decode(postHttpRequest($inventaireUrl, $paramsMonthNow), true);
        $allSitesData[$site->id]['inventaireRequestMonthAgo'] = json_decode(postHttpRequest($inventaireUrl, $paramsMonthAgo), true);
        $allSitesData[$site->id]['commandesRequest'] = json_decode(postHttpRequest($commandesUrl, $paramsMonthNow), true);
        $allSitesData[$site->id]['commandes'] = getCommandesServentest($allSitesData[$site->id]['commandesRequest']);
        $allSitesData[$site->id]['inventaire'] = getInventaireServentest($allSitesData[$site->id]['inventaireRequest']);
        $allSitesData[$site->id]['inventaireMonthAgo'] = getInventaireServentest($allSitesData[$site->id]['inventaireRequestMonthAgo']);
        $allSitesData[$site->id]['noteDeFrais'] = getNoteDeFrais($site->id, date('Y'), date('m'));
        $allSitesData[$site->id]['siteMeta'] = getSiteMeta($site->id, date('Y'), date('m'));
        $allSitesData[$site->id]['facturation'] = getFacturation($site->id, date('Y'), date('m')) + $allSitesData[$site->id]['siteMeta']['fraisFixes'];
        $allSitesData[$site->id]['budget'] = getBudget($site->id, date('Y'), date('m'));

        $allSitesData[$site->id]['consoReel']['denree'] = (
                $allSitesData[$site->id]['commandes']['denree']['total']
                + $allSitesData[$site->id]['inventaireMonthAgo']['denree']['total']
                + $allSitesData[$site->id]['noteDeFrais']['denree']
            ) - $allSitesData[$site->id]['inventaire']['denree']['total'];

        $allSitesData[$site->id]['consoReel']['nonAlimentaire'] = (
                $allSitesData[$site->id]['commandes']['nonAlimentaire']['total']
                + $allSitesData[$site->id]['inventaireMonthAgo']['nonAlimentaire']['total']
                + $allSitesData[$site->id]['noteDeFrais']['nonAlimentaire']
            ) - $allSitesData[$site->id]['inventaire']['nonAlimentaire']['total'];


        $allSitesData[$site->id]['fraisDeSiege'] = financial($allSitesData[$site->id]['facturation'] * 0.04);
        $allSitesData[$site->id]['fraisGeneraux'] = financial(
            $allSitesData[$site->id]['siteMeta']['participationTournante']
            + $allSitesData[$site->id]['fraisDeSiege']
            + $allSitesData[$site->id]['consoReel']['nonAlimentaire']
        );

        $allSitesData[$site->id]['resultatExploitation'] = $allSitesData[$site->id]['facturation'] - ($allSitesData[$site->id]['consoReel']['denree'] + $allSitesData[$site->id]['siteMeta']['fraisDePersonnels'] + $allSitesData[$site->id]['fraisGeneraux']);
        $allSitesData[$site->id]['retourAchat'] = $allSitesData[$site->id]['consoReel']['denree'] * 0.08;
        $allSitesData[$site->id]['resultats'] = $allSitesData[$site->id]['resultatExploitation'] + $allSitesData[$site->id]['retourAchat'] + $allSitesData[$site->id]['fraisDeSiege'];
    }
    ?>
    <div class="row mb-3">
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
                        <th><?= trans('Résultat d\'exploitation'); ?></th>
                        <th><?= trans('Retour achat 8%'); ?></th>
                        <th><?= trans('Frais de siège 4%'); ?></th>
                        <th><?= trans('Résultats'); ?></th>
                        <th><?= trans('Pourcentages de rentabilité'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($allSites):
                        foreach ($allSites as $key => $site): ?>
                            <tr>
                                <th rowspan="2"
                                    style="vertical-align:middle;"><?= $site->nom ?></th>
                                <td style="text-align:left !important;">
                                    <small><em>Budget</em></small>
                                </td>
                                <td><?= $allSitesData[$site->id]['budget']->getCa(); ?>€</td>
                                <td><?= $allSitesData[$site->id]['budget']->getConso(); ?>€</td>
                                <td><?= $allSitesData[$site->id]['budget']->getPersonnel(); ?>€</td>
                                <td><?= $allSitesData[$site->id]['budget']->getFraisGeneraux(); ?>€</td>
                                <td><?= $allSitesData[$site->id]['budget']->getResultatExploitation(); ?>€</td>
                                <td><?= $allSitesData[$site->id]['budget']->getRetourAchat(); ?>%</td>
                                <td><?= $allSitesData[$site->id]['budget']->getRetourFraisSiege(); ?>€</td>
                                <td><?= $allSitesData[$site->id]['budget']->getResultats(); ?>€</td>
                                <td class="table-secondary"></td>
                            </tr>
                            <tr>
                                <td style="text-align:left !important;">
                                    <small><em><?= date('Y'); ?></em></small>
                                </td>
                                <td><?= $allSitesData[$site->id]['facturation']; ?>€</td>
                                <td><?= $allSitesData[$site->id]['consoReel']['denree']; ?>€</td>
                                <td><?= $allSitesData[$site->id]['siteMeta']['fraisDePersonnels']; ?>€</td>
                                <td><?= $allSitesData[$site->id]['fraisGeneraux']; ?>€</td>
                                <td><?= $allSitesData[$site->id]['resultatExploitation']; ?>€</td>
                                <td><?= $allSitesData[$site->id]['retourAchat']; ?>%</td>
                                <td><?= $allSitesData[$site->id]['fraisDeSiege']; ?>€</td>
                                <td><?= $allSitesData[$site->id]['resultats']; ?>€</td>
                                <td style="text-align:center !important;">
                                    <?php if ($allSitesData[$site->id]['facturation'] > 0) {
                                        echo financial($allSitesData[$site->id]['resultats'] / $allSitesData[$site->id]['facturation']) . '%';
                                    } ?>
                                </td>
                            </tr>
                        <?php endforeach;
                    endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php foreach ($allSites as $site): ?>
            <div class="col-12 col-lg-6 my-3">
                <div class="card border-0 w-100">
                    <div class="card-header bg-white pb-0 border-0 boardBlock2Title">
                        <h5 class="m-0 pl-4 colorSecondary"><?= $site->nom; ?></h5>
                        <hr class="mx-4">
                    </div>
                    <div class="card-body pt-0">
                        <div class="accordion" id="infosAgapes-<?= $site->id; ?>">

                            <div class="card">
                                <div class="card-header" id="headingTwo-<?= $site->id; ?>">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse"
                                                data-target="#collapseRefactServ-<?= $site->id; ?>"
                                                aria-expanded="true"
                                                aria-controls="collapseRefactServ-<?= $site->id; ?>">
                                            Refacturation ServenTest
                                        </button>
                                    </h5>
                                </div>

                                <div id="collapseRefactServ-<?= $site->id; ?>" class="collapse"
                                     aria-labelledby="headingTwo-<?= $site->id; ?>"
                                     data-parent="#infosAgapes-<?= $site->id; ?>">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover tableNonEffect">
                                                <thead>
                                                <tr>
                                                    <th><?= trans('Fournisseur'); ?></th>
                                                    <th><?= trans('Date facturation'); ?></th>
                                                    <th><?= trans('Total'); ?></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <th colspan="3"
                                                        style="text-align:center !important; background:#4fb99f;color:#fff;">
                                                        Denrées
                                                    </th>
                                                </tr>
                                                <?php if ($allSitesData[$site->id]['commandesRequest']):
                                                    foreach ($allSitesData[$site->id]['commandes']['denree'] as $key => $allDenree): ?>
                                                        <tr>
                                                            <td><?= $allDenree['fournisseur']; ?></td>
                                                            <td><?= $allDenree['date_facturation']; ?></td>
                                                            <td><?= $allDenree['total']; ?>€</td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                    <tr>
                                                        <td colspan="2">Total denrées</td>
                                                        <th><?= $allSitesData[$site->id]['commandes']['denree']['total']; ?>
                                                            €
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="3"
                                                            style="text-align:center !important; background:#4fb99f;color:#fff;">
                                                            Produits unique
                                                            entretien
                                                        </th>
                                                    </tr>
                                                    <?php foreach ($allSitesData[$site->id]['commandes']['nonAlimentaire'] as $key => $allUniqueEntretien): ?>
                                                    <tr>
                                                        <td><?= $allUniqueEntretien['fournisseur']; ?></td>
                                                        <td><?= $allUniqueEntretien['date_facturation']; ?></td>
                                                        <td><?= $allUniqueEntretien['total']; ?>€</td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                    <tr>
                                                        <td colspan="2">Total produits unique entretien</td>
                                                        <th><?= $allSitesData[$site->id]['commandes']['nonAlimentaire']['total']; ?>
                                                            €
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="3"
                                                            style="text-align:center !important; background:#b96d36;color:#fff;">
                                                            Total <?= financial($allSitesData[$site->id]['commandes']['nonAlimentaire']['total'] + $allSitesData[$site->id]['commandes']['denree']['total']); ?>
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

                            <div class="card">
                                <div class="card-header" id="headingThree-<?= $site->id; ?>">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse"
                                                data-target="#collapseSyntheseConso-<?= $site->id; ?>"
                                                aria-expanded="true"
                                                aria-controls="collapseSyntheseConso-<?= $site->id; ?>">
                                            Synthèse consommation
                                        </button>
                                    </h5>
                                </div>

                                <div id="collapseSyntheseConso-<?= $site->id; ?>" class="collapse"
                                     aria-labelledby="headingThree-<?= $site->id; ?>"
                                     data-parent="#infosAgapes-<?= $site->id; ?>">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover tableNonEffect">
                                            <thead>
                                            <tr>
                                                <th></th>
                                                <th style="text-align: center !important;"><?= trans('Denrées'); ?></th>
                                                <th style="text-align: center !important;"><?= trans('Non alimentaire'); ?></th>
                                                <th style="text-align: center !important;"><?= trans('Total'); ?></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <th style="width: 200px;">Stock initial</th>
                                                <td style="text-align: center !important;"><?= $allSitesData[$site->id]['inventaireMonthAgo']['denree']['total']; ?>
                                                    €
                                                </td>
                                                <td style="text-align: center !important;"><?= $allSitesData[$site->id]['inventaireMonthAgo']['nonAlimentaire']['total']; ?>
                                                    €
                                                </td>
                                                <td style="text-align: center !important;">
                                                    <?= $allSitesData[$site->id]['inventaireMonthAgo']['denree']['total'] + $allSitesData[$site->id]['inventaireMonthAgo']['nonAlimentaire']['total']; ?>
                                                    €
                                                </td>

                                            </tr>
                                            <tr>
                                                <th style="width: 200px;">Refacturation Serventest</th>
                                                <td style="text-align: center !important;"> <?= $allSitesData[$site->id]['commandes']['denree']['total']; ?>€</td>
                                                <td style="text-align: center !important;">
                                                    <?= $allSitesData[$site->id]['commandes']['nonAlimentaire']['total']; ?>€
                                                </td>
                                                <td style="text-align: center !important;">
                                                    <?= financial($allSitesData[$site->id]['commandes']['denree']['total'] + $allSitesData[$site->id]['commandes']['nonAlimentaire']['total']); ?>
                                                    €
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 200px;">Notes de frais</th>
                                                <td style="text-align: center !important;"><?= $allSitesData[$site->id]['noteDeFrais']['denree']; ?>
                                                    €
                                                </td>
                                                <td style="text-align: center !important;"><?= $allSitesData[$site->id]['noteDeFrais']['nonAlimentaire']; ?>
                                                    €
                                                </td>
                                                <td style="text-align: center !important;">
                                                    <?= $allSitesData[$site->id]['noteDeFrais']['denree'] + $allSitesData[$site->id]['noteDeFrais']['nonAlimentaire']; ?>
                                                    €
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 200px;">Stock final</th>
                                                <td style="text-align: center !important;"><?= $allSitesData[$site->id]['inventaire']['denree']['total']; ?>
                                                    €
                                                </td>
                                                <td style="text-align: center !important;"><?= $allSitesData[$site->id]['inventaire']['nonAlimentaire']['total']; ?>
                                                    €
                                                </td>
                                                <td style="text-align: center !important;">
                                                    <?= $allSitesData[$site->id]['inventaire']['denree']['total'] + $allSitesData[$site->id]['inventaire']['nonAlimentaire']['total']; ?>
                                                    €
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 200px;">Consommation réelle</th>
                                                <td style="text-align: center !important;"><?= $allSitesData[$site->id]['consoReel']['denree']; ?>
                                                    €
                                                </td>
                                                <td style="text-align: center !important;"><?= $allSitesData[$site->id]['consoReel']['nonAlimentaire']; ?>
                                                    €
                                                </td>
                                                <td style="text-align: center !important;">
                                                    <?= $allSitesData[$site->id]['consoReel']['denree'] + $allSitesData[$site->id]['consoReel']['nonAlimentaire']; ?>
                                                    €
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 200px;">CA variable</th>
                                                <td style="text-align: center !important;"><?= $allSitesData[$site->id]['facturation']; ?>
                                                    €
                                                </td>
                                                <td class="table-secondary"></td>
                                                <td class="table-secondary"></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 200px;">Marge réalisée</th>
                                                <td style="text-align: center !important;">
                                                    <?= $allSitesData[$site->id]['facturation'] - $allSitesData[$site->id]['consoReel']['denree']; ?>
                                                    €
                                                </td>
                                                <td class="table-secondary"></td>
                                                <td class="table-secondary"></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 200px;">Marge budget</th>
                                                <td style="text-align: center !important;"><?= $allSitesData[$site->id]['budget']->getConso(); ?>
                                                    €
                                                </td>
                                                <td class="table-secondary"></td>
                                                <td class="table-secondary"></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header" id="headingFour-<?= $site->id; ?>">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse"
                                                data-target="#collapseFraisGener-<?= $site->id; ?>"
                                                aria-expanded="true"
                                                aria-controls="collapseFraisGener-<?= $site->id; ?>">
                                            Frais généraux
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapseFraisGener-<?= $site->id; ?>" class="collapse"
                                     aria-labelledby="headingFour-<?= $site->id; ?>"
                                     data-parent="#infosAgapes-<?= $site->id; ?>">
                                    <div class="littleContainer"><span
                                                class="littleTitle colorPrimary">Participation tournant</span>
                                        <span class="littleText"><?= $allSitesData[$site->id]['siteMeta']['participationTournante']; ?>€</span>
                                    </div>
                                    <div class="littleContainer"><span
                                                class="littleTitle colorPrimary">Frais de siège</span>
                                        <span class="littleText"><?= $allSitesData[$site->id]['fraisDeSiege']; ?>€</span>
                                    </div>
                                    <div class="littleContainer"><span class="littleTitle colorPrimary">Consommation produits d'entretien</span>
                                        <span class="littleText"><?= $allSitesData[$site->id]['consoReel']['nonAlimentaire']; ?>€</span>
                                    </div>
                                    <div class="littleContainer"><span
                                                class="littleTitle colorPrimary">Total HT</span>
                                        <span class="littleText"><?= $allSitesData[$site->id]['fraisGeneraux']; ?>€</span>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header" id="headingFive-<?= $site->id; ?>">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse"
                                                data-target="#collapseResultats-<?= $site->id; ?>"
                                                aria-expanded="true"
                                                aria-controls="collapseResultats-<?= $site->id; ?>">
                                            Résultats
                                        </button>
                                    </h5>
                                </div>

                                <div id="collapseResultats-<?= $site->id; ?>" class="collapse"
                                     aria-labelledby="headingFive-<?= $site->id; ?>"
                                     data-parent="#infosAgapes-<?= $site->id; ?>">
                                    <div class="littleContainer"><span class="littleTitle colorPrimary">CA</span>
                                        <span class="littleText"><?= $allSitesData[$site->id]['facturation']; ?>€</span>
                                    </div>
                                    <div class="littleContainer"><span
                                                class="littleTitle colorPrimary">Consommation</span>
                                        <span class="littleText"><?= $allSitesData[$site->id]['consoReel']['denree']; ?>€</span>
                                    </div>
                                    <div class="littleContainer"><span
                                                class="littleTitle colorPrimary">Frais de personnels</span>
                                        <span class="littleText"><?= $allSitesData[$site->id]['siteMeta']['fraisDePersonnels']; ?>€</span>
                                    </div>
                                    <div class="littleContainer"><span
                                                class="littleTitle colorPrimary">Frais généraux</span>
                                        <span class="littleText"><?= $allSitesData[$site->id]['fraisGeneraux']; ?>€</span>
                                    </div>
                                    <div class="littleContainer"><span class="littleTitle colorPrimary">Résultats bruts d'éxploitation</span>
                                        <span class="littleText"><?= $allSitesData[$site->id]['resultatExploitation']; ?>€</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>