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

$dateMonthAgo = new \DateTime();
$dateMonthAgo->sub(new \DateInterval('P1M'));

$paramsMonthAgo = array(
    'key' => '123',
    'ref' => $Site->ref,
    'dateDebut' => $dateMonthAgo->format('Y-m') . '-01',
    'dateFin' => $dateMonthAgo->format('Y-m-t')
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
                        <!--<a href="<?= AGAPESHOTES_URL; ?>page/planning/<?= $Site->secteurSlug; ?>/<?= $Site->slug; ?>/"
                           class="btn btn-block btn-info py-4">Planning</a>-->
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
                                       class="btn btn-outline-dark btn-block my-2">Liste vivre cru</a>
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
                                <span class="float-right">
                                    <button type="button" class="btn btn-link btn-sm" data-toggle="modal"
                                            data-target="#modalAddOtherAchat">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </span>
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
                                                if (isNotUniqueEntretien($allDenree['fournisseur'])):
                                                    $totalCommandDenree += $allDenree['total']; ?>
                                                    <tr>
                                                        <td><?= $allDenree['fournisseur']; ?></td>
                                                        <td><?= $allDenree['date_livraison']; ?></td>
                                                        <td data-name="totalDenree"><?= financial($allDenree['total']); ?>
                                                            €
                                                        </td>
                                                    </tr>
                                                <?php endif;
                                            endforeach;
                                            $otherAchat = getAllCommandes($Site->id, date('Y'), date('m'));
                                            if ($otherAchat):
                                                foreach ($otherAchat as $key => $allDenree):
                                                    if (isNotUniqueEntretien($allDenree['fournisseur'])):
                                                        $totalCommandDenree += $allDenree['total']; ?>
                                                        <tr class="table-warning">
                                                            <td>
                                                                <button type="button"
                                                                        data-idotherachat="<?= $allDenree['id']; ?>"
                                                                        data-influence="totalDenree"
                                                                        class="btn btn-link btn-sm deleteOtherAchat">
                                                                    <i class="fas fa-times"></i></button>
                                                                <?= $allDenree['fournisseur']; ?></td>
                                                            <td><?= $allDenree['date_livraison']; ?></td>
                                                            <td data-name="totalDenree"><?= financial($allDenree['total']); ?>
                                                                €
                                                            </td>
                                                        </tr>
                                                    <?php endif;
                                                endforeach;
                                            endif; ?>
                                            <tr>
                                                <td colspan="2">Total denrées</td>
                                                <th data-name="totalDenree"><?= financial($totalCommandDenree); ?>€</th>
                                            </tr>
                                            <tr>
                                                <th colspan="3"
                                                    style="text-align:center !important; background:#4fb99f;color:#fff;">
                                                    Produits unique entretien
                                                </th>
                                            </tr>
                                            <?php foreach ($commandesRequest as $key => $allUniqueEntretien):
                                            if (isUniqueEntretien($allUniqueEntretien['fournisseur'])):
                                                $totalCommandUniqueEntretien += $allUniqueEntretien['total']; ?>
                                                <tr>
                                                    <td><?= $allUniqueEntretien['fournisseur']; ?></td>
                                                    <td><?= $allUniqueEntretien['date_livraison']; ?></td>
                                                    <td data-name="totalUniqueEntretien"><?= financial($allUniqueEntretien['total']); ?>
                                                        €
                                                    </td>
                                                </tr>
                                            <?php endif;
                                        endforeach;
                                            if ($otherAchat):
                                                foreach ($otherAchat as $key => $allUniqueEntretien):
                                                    if (isUniqueEntretien($allUniqueEntretien['fournisseur'])):
                                                        $totalCommandUniqueEntretien += $allUniqueEntretien['total']; ?>
                                                        <tr class="table-warning">
                                                            <td>
                                                                <button type="button"
                                                                        data-idotherachat="<?= $allUniqueEntretien['id']; ?>"
                                                                        data-influence="totalUniqueEntretien"
                                                                        class="btn btn-link btn-sm deleteOtherAchat">
                                                                    <i class="fas fa-times"></i>
                                                                </button><?= $allUniqueEntretien['fournisseur']; ?></td>
                                                            <td><?= $allUniqueEntretien['date_livraison']; ?></td>
                                                            <td data-name="totalUniqueEntretien"><?= financial($allUniqueEntretien['total']); ?>
                                                                €
                                                            </td>
                                                        </tr>
                                                    <?php endif;
                                                endforeach;
                                            endif; ?>
                                            <tr>
                                                <td colspan="2">Total produits unique entretien</td>
                                                <th data-name="totalUniqueEntretien"><?= financial($totalCommandUniqueEntretien); ?>
                                                    €
                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="3" data-name="totalAchats"
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
                                    Refacturation Serventest
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
                                                if (isNotUniqueEntretien($allDenree['fournisseur'])):
                                                    $totalRefraCommandDenree += $allDenree['total']; ?>
                                                    <tr>
                                                        <td><?= $allDenree['fournisseur']; ?></td>
                                                        <td><?= $allDenree['date_facturation']; ?></td>
                                                        <td><?= financial($allDenree['total']); ?>€</td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <tr>
                                                <td colspan="2">Total denrées</td>
                                                <th><?= financial($totalRefraCommandDenree); ?>€</th>
                                            </tr>
                                            <tr>
                                                <th colspan="3"
                                                    style="text-align:center !important; background:#4fb99f;color:#fff;">
                                                    Produits unique
                                                    entretien
                                                </th>
                                            </tr>
                                            <?php foreach ($refacturationRequest as $key => $allUniqueEntretien):
                                            if (isUniqueEntretien($allUniqueEntretien['fournisseur'])):
                                                $totalRefraCommandUniqueEntretien += $allUniqueEntretien['total']; ?>
                                                <tr>
                                                    <td><?= $allUniqueEntretien['fournisseur']; ?></td>
                                                    <td><?= $allUniqueEntretien['date_facturation']; ?></td>
                                                    <td><?= financial($allUniqueEntretien['total']); ?>€</td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                            <tr>
                                                <td colspan="2">Total produits unique entretien</td>
                                                <th><?= financial($totalRefraCommandUniqueEntretien); ?>€</th>
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
                                            if (isNotUniqueEntretien($inventaire['fournisseur'])) {
                                                $totalInventaireDenreeMonthAgo += $inventaire['total'];
                                            } else {
                                                $totalInventaireUniqueEntretienMonthAgo += $inventaire['total'];
                                            }
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <th style="width: 200px;">Stock initial</th>
                                        <td style="text-align: center !important;"><?= financial($totalInventaireDenreeMonthAgo); ?>
                                            €
                                        </td>
                                        <td style="text-align: center !important;"><?= financial($totalInventaireUniqueEntretienMonthAgo); ?>
                                            €
                                        </td>
                                        <td style="text-align: center !important;">
                                            <?= financial($totalInventaireDenreeMonthAgo + $totalInventaireUniqueEntretienMonthAgo); ?>
                                            €
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 200px;">Refacturation Serventest</th>
                                        <td style="text-align: center !important;"><?= financial($totalRefraCommandDenree); ?></td>
                                        <td style="text-align: center !important;"><?= financial($totalRefraCommandUniqueEntretien); ?>
                                            €
                                        </td>
                                        <td style="text-align: center !important;">
                                            <?= financial($totalRefraCommandUniqueEntretien + $totalRefraCommandDenree); ?>
                                            €
                                        </td>
                                    </tr>
                                    <tr>
                                        <?php
                                        $noteDeFrais = getNoteDeFrais($Site->id, date('Y'), date('m'));
                                        ?>
                                        <th style="width: 200px;">Notes de frais</th>
                                        <td style="text-align: center !important;"><?= financial($noteDeFrais['denree']); ?>
                                            €
                                        </td>
                                        <td style="text-align: center !important;"><?= financial($noteDeFrais['nonAlimentaire']); ?>
                                            €
                                        </td>
                                        <td style="text-align: center !important;">
                                            <?= financial($noteDeFrais['denree'] + $noteDeFrais['nonAlimentaire']); ?>
                                            €
                                        </td>
                                    </tr>
                                    <tr>
                                        <?php
                                        $totalInventaireDenree = 0;
                                        $totalInventaireUniqueEntretien = 0;
                                        if ($inventaireRequest) {
                                            foreach ($inventaireRequest as $key => $inventaire) {
                                                if (isNotUniqueEntretien($inventaire['fournisseur'])) {
                                                    $totalInventaireDenree += $inventaire['total'];
                                                } else {
                                                    $totalInventaireUniqueEntretien += $inventaire['total'];
                                                }
                                            }
                                        }
                                        ?>
                                        <th style="width: 200px;">Stock final</th>
                                        <td style="text-align: center !important;"><?= financial($totalInventaireDenree); ?>
                                            €
                                        </td>
                                        <td style="text-align: center !important;"><?= financial($totalInventaireUniqueEntretien); ?>
                                            €
                                        </td>
                                        <td style="text-align: center !important;">
                                            <?= financial($totalInventaireDenree + $totalInventaireUniqueEntretien); ?>€
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 200px;">Consommation réelle</th>
                                        <?php
                                        $consoReelDenrees = ($totalRefraCommandDenree + $totalInventaireDenreeMonthAgo + $noteDeFrais['denree']) - $totalInventaireDenree;;
                                        $consoReelNonAlimentaires = ($totalRefraCommandUniqueEntretien + $totalInventaireUniqueEntretienMonthAgo + $noteDeFrais['nonAlimentaire']) - $totalInventaireUniqueEntretien;
                                        ?>
                                        <td style="text-align: center !important;"><?= financial($consoReelDenrees); ?>€
                                        </td>
                                        <td style="text-align: center !important;"><?= financial($consoReelNonAlimentaires); ?>
                                            €
                                        </td>
                                        <td style="text-align: center !important;">
                                            <?= financial($consoReelDenrees + $consoReelNonAlimentaires); ?>€
                                        </td>
                                    </tr>
                                    <tr>
                                        <?php $facturation = getFacturation($Site->id, date('Y'), date('m')); ?>
                                        <th style="width: 200px;">CA variable</th>
                                        <td style="text-align: center !important;"><?= financial($facturation); ?>€
                                        </td>
                                        <td class="table-secondary"></td>
                                        <td class="table-secondary"></td>
                                    </tr>
                                    <tr>
                                        <th style="width: 200px;">Marge réalisée</th>
                                        <td style="text-align: center !important;">
                                            <?= financial($facturation - $consoReelDenrees); ?>
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
                                        <td style="text-align: center !important;"><?= financial($Budget->getCaVariable() - $Budget->getConso()); ?>
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
                        $FraisDePersonnels = $siteMeta['fraisDePersonnel'];
                        $indemniteKm = getIndemniteKm($Site->id, date('Y'), date('m'));
                        $fraisDeSiege = financial($facturation * 0.04);
                        $fraisGenerauxTotal = financial($ParticipationTournante + $fraisDeSiege + $consoReelNonAlimentaires + $noteDeFrais['autreAchat'] + $indemniteKm);
                        ?>
                        <div id="collapseFraisGener" class="collapse" aria-labelledby="headingFour"
                             data-parent="#infosAgapes">
                            <div class="littleContainer">
                                <span class="littleTitle colorPrimary">Participation tournant</span>
                                <span class="littleText"><?= financial($ParticipationTournante); ?>€</span></div>
                            <div class="littleContainer">
                                <span class="littleTitle colorPrimary">Note de frais autre achat</span>
                                <span class="littleText"><?= financial($noteDeFrais['autreAchat']); ?>€</span></div>
                            <div class="littleContainer">
                                <span class="littleTitle colorPrimary">Note de frais indemnité kilométrique</span>
                                <span class="littleText"><?= financial($indemniteKm); ?>€</span></div>
                            <div class="littleContainer"><span class="littleTitle colorPrimary">Frais de siège</span>
                                <span class="littleText"><?= $fraisDeSiege; ?>€</span></div>
                            <div class="littleContainer"><span class="littleTitle colorPrimary">Consommation produits d'entretien</span>
                                <span class="littleText"><?= financial($consoReelNonAlimentaires); ?>€</span></div>
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
                                <span class="littleText"><?= financial($facturation + $siteMeta['fraisFixes']); ?>€</span>
                            </div>
                            <div class="littleContainer"><span class="littleTitle colorPrimary">Consommation</span>
                                <span class="littleText"><?= financial($consoReelDenrees); ?>€</span></div>
                            <div class="littleContainer"><span
                                        class="littleTitle colorPrimary">Frais de personnel</span>
                                <span class="littleText"><?= financial($FraisDePersonnels); ?>€</span></div>
                            <div class="littleContainer"><span class="littleTitle colorPrimary">Frais généraux</span>
                                <span class="littleText"><?= $fraisGenerauxTotal; ?>€</span></div>
                            <div class="littleContainer"><span class="littleTitle colorPrimary">Résultats bruts d'éxploitation</span>
                                <span class="littleText"><?= financial($facturation - ($consoReelDenrees + $FraisDePersonnels + $fraisGenerauxTotal)); ?>€</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$otherFournisseurs = array('BOULANGER' => 'BOULANGER', 'TRANSGOURMET' => 'TRANSGOURMET');
?>
<div class="modal fade" id="modalAddOtherAchat" tabindex="-1" role="dialog"
     aria-labelledby="modalAddOtherAchat"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" method="post" id="addOtherAchatForm">
                <input type="hidden" name="siteId" value="<?= $Site->id; ?>"
                <?= getTokenField(); ?>
                <div class="modal-header">
                    <h5 class="modal-title"
                        id="modalAddOtherAchat"><?= trans('Ajouter un achat'); ?></h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 my-2">
                            <?= \App\Form::select('Fournisseur', 'fournisseur', $otherFournisseurs, '', true); ?>
                        </div>
                        <div class="col-12 my-2">
                            <?= \App\Form::text('Date de livraison', 'date', 'text', '', true, 10, '', '', 'datepicker'); ?>
                        </div>
                        <div class="col-12 my-2">
                            <?= \App\Form::text('Total', 'total', 'text', '', true); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 my-2" id="FormAddOtherAchatInfos"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <?= \App\Form::target('ADDOTHERACHAT'); ?>
                    <button type="submit" id="saveAddOtherAchatBtn"
                            class="btn btn-primary"><?= trans('Enregistrer'); ?></button>
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal"><?= trans('Fermer'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>

    function calculeTotal(totalType) {

        var total = 0;
        $('td[data-name="' + totalType + '"]').each(function (index, val) {
            total += parseReelFloat($(this).text());
        });

        $('th[data-name="' + totalType + '"]').html(financial(total) + '€');

        var totalDenree = parseReelFloat($('th[data-name="totalDenree"]').text());
        var totalUnique = parseReelFloat($('th[data-name="totalUniqueEntretien"]').text());

        $('th[data-name="totalAchats"]').html('Total ' + financial(totalDenree + totalUnique) + ' €')

    }

    $(document).ready(function () {

        $('form#addOtherAchatForm').on('submit', function (event) {
            event.preventDefault();

            busyApp();
            var $form = $(this);
            $('#FormAddOtherAchatInfos').hide().html('');

            $.post(
                '<?= AGAPESHOTES_URL . 'process/ajaxOther.php'; ?>',
                $form.serialize(),
                function (data) {
                    if (data === true || data == 'true') {
                        $('#loader').fadeIn(400);
                        location.reload();
                    } else {
                        $('#FormAddOtherAchatInfos')
                            .html('<p class="bg-danger text-white">' + data + '</p>').show();
                    }
                    availableApp();
                }
            );
        });

        $('.deleteOtherAchat').on('click', function (event) {
            event.preventDefault();

            var $btn = $(this);
            var $parent = $btn.closest('tr');
            var idAchat = $btn.data('idotherachat');
            var influence = $btn.data('influence');

            if (confirm('Vous allez supprimer cette achat !')) {
                $.post(
                    '<?= AGAPESHOTES_URL . 'process/ajaxOther.php'; ?>',
                    {
                        DELETEACHAT: 'OK',
                        idAchat: idAchat
                    },
                    function (data) {
                        if (data) {
                            $parent.fadeOut().remove();
                            calculeTotal(influence);
                        }
                    }
                );
            }
        });
    });
</script>