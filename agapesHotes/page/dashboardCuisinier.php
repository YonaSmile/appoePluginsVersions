<?php
$SiteAccess = new \App\Plugin\AgapesHotes\SiteAccess();
$SiteAccess->setSiteUserId(getUserIdSession());
$Site = $SiteAccess->showSiteByUser();

$Etablissement = new \App\Plugin\AgapesHotes\Etablissement();
$Etablissement->setSiteId($Site->id);
$allEtablissements = $Etablissement->showAllBySite();

$inventaireUrl = 'https://serventest.fr/pro/liaison_appoe/getInventaireServentest.php';
$commandesUrl = 'https://serventest.fr/pro/liaison_appoe/getCommandServentest.php';
$refacturationUrl = 'https://serventest.fr/pro/liaison_appoe/getRefacturationServentest.php';

$paramsMonthNow = array(
    'key' => '123',
    'ref' => $Site->ref,
    'dateDebut' => date('Y-m-01'),
    'dateFin' => date('Y-m-t')
);

$paramsMonthAgo = array(
    'key' => '123',
    'ref' => $Site->ref,
    'dateDebut' => date('Y-' . (date('m') - 1) . '-01'),
    'dateFin' => date('Y-m-t')
);

$inventaireRequest = json_decode(postHttpRequest($inventaireUrl, $paramsMonthNow), true);
$inventaireRequestMonthAgo = json_decode(postHttpRequest($inventaireUrl, $paramsMonthAgo), true);
$commandesRequest = json_decode(postHttpRequest($commandesUrl, $paramsMonthNow), true);
$refacturationRequest = json_decode(postHttpRequest($refacturationUrl, $paramsMonthNow), true);
$View = new \App\Plugin\AgapesHotes\View();
?>
<div class="row mb-3">
    <div class="d-flex col-12 col-lg-4">
        <div class="card border-0 w-100">
            <div class="card-header bg-white pb-0 border-0 boardBlock1Title">
                <h5 class="m-0 pl-4 colorPrimary"><?= $Site->nom; ?></h5>
                <hr class="mx-4">
            </div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-12">
                        <a href="<?= AGAPESHOTES_URL; ?>page/mainCourante/<?= $Site->secteurSlug; ?>/<?= $Site->slug; ?>/"
                           class="btn btn-block btn-info py-4">Main Courante</a>
                        <a href="<?= AGAPESHOTES_URL; ?>page/planning/<?= $Site->secteurSlug; ?>/<?= $Site->slug; ?>/"
                           class="btn btn-block btn-info py-4">Planning</a>
                        <a href="<?= AGAPESHOTES_URL; ?>page/noteDeFrais/<?= $Site->secteurSlug; ?>/<?= $Site->slug; ?>/"
                           class="btn btn-block btn-info py-4">Note de frais</a>
                        <a href="<?= AGAPESHOTES_URL; ?>page/vivreCrue/<?= $Site->secteurSlug; ?>/<?= $Site->slug; ?>/"
                           class="btn btn-block btn-info py-4">Vivre cru</a>
                        <a href="<?= AGAPESHOTES_URL; ?>page/mainSupplementaire/<?= $Site->secteurSlug; ?>/<?= $Site->slug; ?>/"
                           class="btn btn-block btn-info py-4">Facturation HC</a>

                        <?php foreach ($allEtablissements as $etablissement): ?>
                            <small class="littleTitle colorPrimary mt-3"><?= $etablissement->nom; ?></small>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <a href="<?= AGAPESHOTES_URL; ?>page/allPrestations/<?= $Site->secteurSlug; ?>/<?= $Site->slug; ?>/<?= $etablissement->slug; ?>/"
                                       class="btn btn-outline-dark btn-block my-2">Liste prestations</a>
                                </div>
                                <div class="col-12 col-md-6">
                                    <a href="<?= AGAPESHOTES_URL; ?>page/allCourses/<?= $Site->secteurSlug; ?>/<?= $Site->slug; ?>/<?= $etablissement->slug; ?>/"
                                       class="btn btn-outline-dark btn-block my-2">Liste vivre crus</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex col-12 col-lg-8">
        <div class="card border-0 w-100">
            <div class="card-header bg-white pb-0 border-0 boardBlock2Title">
                <h5 class="m-0 pl-4 colorSecondary"><?= trans('Infos'); ?></h5>
                <hr class="mx-4">
            </div>
            <div class="card-body pt-0">
                <div class="accordion" id="infosAgapes">
                    <div class="card">
                        <div class="card-header" id="headingOne">
                            <h5 class="mb-0">
                                <button class="btn btn-link" type="button" data-toggle="collapse"
                                        data-target="#collapseAchat" aria-expanded="true" aria-controls="collapseAchat">
                                    Achats
                                </button>
                            </h5>
                        </div>

                        <div id="collapseAchat" class="collapse" aria-labelledby="headingOne"
                             data-parent="#infosAgapes">
                            <div class="card-body">
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
                                                style="text-align:center !important; background:#4fb99f;color:#fff;">
                                                Denrées
                                            </th>
                                        </tr>
                                        <?php
                                        $totalCommandDenree = 0;
                                        $totalCommandUniqueEntretien = 0;
                                        if ($commandesRequest):
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

                    <div class="card">
                        <div class="card-header" id="headingTwo">
                            <h5 class="mb-0">
                                <button class="btn btn-link" type="button" data-toggle="collapse"
                                        data-target="#collapseRefactServ" aria-expanded="true"
                                        aria-controls="collapseRefactServ">
                                    Refacturation ServenTest
                                </button>
                            </h5>
                        </div>

                        <div id="collapseRefactServ" class="collapse" aria-labelledby="headingTwo"
                             data-parent="#infosAgapes">
                            <div class="card-body">
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
                                                style="text-align:center !important; background:#4fb99f;color:#fff;">
                                                Denrées
                                            </th>
                                        </tr>
                                        <?php
                                        $totalRefraCommandDenree = 0;
                                        $totalRefraCommandUniqueEntretien = 0;
                                        if ($refacturationRequest):
                                            foreach ($refacturationRequest as $key => $allDenree):
                                                if (trim($allDenree['fournisseur']) != 'C2M'):
                                                    $totalRefraCommandDenree += $allDenree['total']; ?>
                                                    <tr>
                                                        <td><?= $allDenree['fournisseur']; ?></td>
                                                        <td><?= $allDenree['date_facturation']; ?></td>
                                                        <td><?= $allDenree['total']; ?>€</td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <tr>
                                                <td colspan="2">Total denrées</td>
                                                <th><?= $totalRefraCommandDenree; ?>€</th>
                                            </tr>
                                            <tr>
                                                <th colspan="3"
                                                    style="text-align:center !important; background:#4fb99f;color:#fff;">
                                                    Produits unique
                                                    entretien
                                                </th>
                                            </tr>
                                            <?php foreach ($refacturationRequest as $key => $allUniqueEntretien):
                                            if (trim($allUniqueEntretien['fournisseur']) == 'C2M'):
                                                $totalRefraCommandUniqueEntretien += $allUniqueEntretien['total']; ?>
                                                <tr>
                                                    <td><?= $allUniqueEntretien['fournisseur']; ?></td>
                                                    <td><?= $allUniqueEntretien['date_facturation']; ?></td>
                                                    <td><?= $allUniqueEntretien['total']; ?>€</td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                            <tr>
                                                <td colspan="2">Total produits unique entretien</td>
                                                <th><?= $totalRefraCommandUniqueEntretien; ?>€</th>
                                            </tr>
                                            <tr>
                                                <th colspan="3"
                                                    style="text-align:center !important; background:#b96d36;color:#fff;">
                                                    Total <?= financial($totalRefraCommandUniqueEntretien + $totalRefraCommandDenree); ?>
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
                        <div class="card-header" id="headingThree">
                            <h5 class="mb-0">
                                <button class="btn btn-link" type="button" data-toggle="collapse"
                                        data-target="#collapseSyntheseConso" aria-expanded="true"
                                        aria-controls="collapseSyntheseConso">
                                    Synthèse consommation
                                </button>
                            </h5>
                        </div>

                        <div id="collapseSyntheseConso" class="collapse" aria-labelledby="headingThree"
                             data-parent="#infosAgapes">
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
                                    <?php
                                    $totalInventaireDenreeMonthAgo = 0;
                                    $totalInventaireUniqueEntretienMonthAgo = 0;
                                    if ($inventaireRequestMonthAgo) {
                                        foreach ($inventaireRequestMonthAgo as $key => $inventaire) {
                                            if (trim($inventaire['fournisseur']) != 'C2M') {
                                                $totalInventaireDenreeMonthAgo += $inventaire['total'];
                                            } else {
                                                $totalInventaireUniqueEntretienMonthAgo += $inventaire['total'];
                                            }
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <th style="width: 200px;">Stock initial</th>
                                        <td style="text-align: center !important;"><?= $totalInventaireDenreeMonthAgo; ?>
                                            €
                                        </td>
                                        <td style="text-align: center !important;"><?= $totalInventaireUniqueEntretienMonthAgo; ?>
                                            €
                                        </td>
                                        <td style="text-align: center !important;">
                                            <?= $totalInventaireDenreeMonthAgo + $totalInventaireUniqueEntretienMonthAgo; ?>
                                            €
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 200px;">Refacturation Serventest</th>
                                        <td style="text-align: center !important;"><?= $totalRefraCommandDenree; ?></td>
                                        <td style="text-align: center !important;"><?= $totalRefraCommandUniqueEntretien; ?>
                                            €
                                        </td>
                                        <td style="text-align: center !important;">
                                            <?= financial($totalRefraCommandUniqueEntretien + $totalRefraCommandDenree); ?>
                                            €
                                        </td>
                                    </tr>
                                    <tr>
                                        <?php
                                        $View->clean();
                                        $View->setViewName('totalNoteDeFraisDenree');
                                        $View->setDataColumns(array('site_id', 'mois', 'annee'));
                                        $View->setDataValues(array($Site->id, date('m'), date('Y')));
                                        $View->prepareSql();
                                        $totalNotesDeFraisDenrees = $View->get();

                                        $View->setViewName('totalNoteDeFraisNonAlimentaire');
                                        $View->prepareSql();
                                        $totalNotesDeFraisNonAlimentaire = $View->get();

                                        $totalDenreeNotes = !empty($totalNotesDeFraisDenrees->totalHT) ? $totalNotesDeFraisDenrees->totalHT : 0;
                                        $totalNonAlimentaireNotes = !empty($totalNotesDeFraisNonAlimentaire->totalHT) ? $totalNotesDeFraisNonAlimentaire->totalHT : 0;
                                        ?>
                                        <th style="width: 200px;">Notes de frais</th>
                                        <td style="text-align: center !important;"><?= $totalDenreeNotes; ?>
                                            €
                                        </td>
                                        <td style="text-align: center !important;"><?= $totalNonAlimentaireNotes; ?>
                                            €
                                        </td>
                                        <td style="text-align: center !important;">
                                            <?= $totalDenreeNotes + $totalNonAlimentaireNotes; ?>
                                            €
                                        </td>
                                    </tr>
                                    <tr>
                                        <?php
                                        $totalInventaireDenree = 0;
                                        $totalInventaireUniqueEntretien = 0;
                                        if ($inventaireRequest) {
                                            foreach ($inventaireRequest as $key => $inventaire) {
                                                if (trim($inventaire['fournisseur']) != 'C2M') {
                                                    $totalInventaireDenree += $inventaire['total'];
                                                } else {
                                                    $totalInventaireUniqueEntretien += $inventaire['total'];
                                                }
                                            }
                                        }
                                        ?>
                                        <th style="width: 200px;">Stock final</th>
                                        <td style="text-align: center !important;"><?= $totalInventaireDenree; ?>€
                                        </td>
                                        <td style="text-align: center !important;"><?= $totalInventaireUniqueEntretien; ?>
                                            €
                                        </td>
                                        <td style="text-align: center !important;">
                                            <?= $totalInventaireDenree + $totalInventaireUniqueEntretien; ?>€
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 200px;">Consommation réelle</th>
                                        <?php
                                        $consoReelDenrees = ($totalRefraCommandDenree + $totalInventaireDenreeMonthAgo + $totalDenreeNotes) - $totalInventaireDenree;;
                                        $consoReelNonAlimentaires = ($totalRefraCommandUniqueEntretien + $totalInventaireUniqueEntretienMonthAgo + $totalNonAlimentaireNotes) - $totalInventaireUniqueEntretien;
                                        ?>
                                        <td style="text-align: center !important;"><?= $consoReelDenrees; ?>
                                            €
                                        </td>
                                        <td style="text-align: center !important;"><?= $consoReelNonAlimentaires; ?>
                                            €
                                        </td>
                                        <td style="text-align: center !important;">
                                            <?= $consoReelDenrees + $consoReelNonAlimentaires; ?>€
                                        </td>
                                    </tr>
                                    <tr>
                                        <?php
                                        $View->clean();
                                        $View->setViewName('totalFacturation');
                                        $View->setDataColumns(array('site_id', 'mois', 'annee'));
                                        $View->setDataValues(array($Site->id, date('m'), date('Y')));
                                        $View->prepareSql();
                                        $totalFacturation = $View->get();

                                        $facturation = !empty($totalFacturation->totalHT) ? $totalFacturation->totalHT : 0;
                                        $siteMeta = getSiteMeta($Site->id, date('Y'), date('m'));
                                        $facturation += $siteMeta['fraisFixes'];
                                        ?>
                                        <th style="width: 200px;">CA variable</th>
                                        <td style="text-align: center !important;"><?= $facturation; ?>€
                                        </td>
                                        <td class="table-secondary"></td>
                                        <td class="table-secondary"></td>
                                    </tr>
                                    <tr>
                                        <th style="width: 200px;">Marge réalisée</th>
                                        <td style="text-align: center !important;">
                                            <?= $facturation - $consoReelDenrees; ?>
                                            €
                                        </td>
                                        <td class="table-secondary"></td>
                                        <td class="table-secondary"></td>
                                    </tr>
                                    <tr>
                                        <?php
                                        $Budget = new \App\Plugin\AgapesHotes\Budget();
                                        $Budget->setSiteId($Site->id);
                                        $Budget->setYear(date('Y'));
                                        $Budget->setMonth(date('m'));
                                        $Budget->showBySite();
                                        ?>
                                        <th style="width: 200px;">Marge budget</th>
                                        <td style="text-align: center !important;"><?= $Budget->getConso(); ?>
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
                        <div class="card-header" id="headingFour">
                            <h5 class="mb-0">
                                <button class="btn btn-link" type="button" data-toggle="collapse"
                                        data-target="#collapseFraisGener" aria-expanded="true"
                                        aria-controls="collapseFraisGener">
                                    Frais généraux
                                </button>
                            </h5>
                        </div>

                        <?php

                        $siteMeta = getSiteMeta($Site->id, date('Y'), date('m'));
                        $ParticipationTournante = $siteMeta['participationTournante'];
                        $FraisDePersonnels = $siteMeta['fraisDePersonnels'];

                        $fraisDeSiege = financial($facturation * 0.04);
                        $fraisGenerauxTotal = financial($ParticipationTournante + $fraisDeSiege + $consoReelNonAlimentaires);
                        ?>
                        <div id="collapseFraisGener" class="collapse" aria-labelledby="headingFour"
                             data-parent="#infosAgapes">
                            <div class="littleContainer"><span
                                        class="littleTitle colorPrimary">Participation tournant</span>
                                <span class="littleText"><?= $ParticipationTournante; ?>€</span></div>
                            <div class="littleContainer"><span class="littleTitle colorPrimary">Frais de siège</span>
                                <span class="littleText"><?= $fraisDeSiege; ?>€</span></div>
                            <div class="littleContainer"><span class="littleTitle colorPrimary">Consommation produits d'entretien</span>
                                <span class="littleText"><?= $consoReelNonAlimentaires; ?>€</span></div>
                            <div class="littleContainer"><span class="littleTitle colorPrimary">Total HT</span>
                                <span class="littleText"><?= $fraisGenerauxTotal; ?>€</span></div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header" id="headingFive">
                            <h5 class="mb-0">
                                <button class="btn btn-link" type="button" data-toggle="collapse"
                                        data-target="#collapseResultats" aria-expanded="true"
                                        aria-controls="collapseResultats">
                                    Résultats
                                </button>
                            </h5>
                        </div>

                        <div id="collapseResultats" class="collapse" aria-labelledby="headingFive"
                             data-parent="#infosAgapes">
                            <div class="littleContainer"><span class="littleTitle colorPrimary">CA</span>
                                <span class="littleText"><?= $facturation; ?>€</span></div>
                            <div class="littleContainer"><span class="littleTitle colorPrimary">Consommation</span>
                                <span class="littleText"><?= $consoReelDenrees; ?>€</span></div>
                            <div class="littleContainer"><span
                                        class="littleTitle colorPrimary">Frais de personnels</span>
                                <span class="littleText"><?= $FraisDePersonnels; ?>€</span></div>
                            <div class="littleContainer"><span class="littleTitle colorPrimary">Frais généraux</span>
                                <span class="littleText"><?= $fraisGenerauxTotal; ?>€</span></div>
                            <div class="littleContainer"><span class="littleTitle colorPrimary">Résultats bruts d'éxploitation</span>
                                <span class="littleText"><?= $facturation - ($consoReelDenrees + $FraisDePersonnels + $fraisGenerauxTotal); ?>€</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {

    });
</script>