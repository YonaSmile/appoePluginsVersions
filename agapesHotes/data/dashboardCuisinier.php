<?php
require_once('../main.php');
$SiteAccess = new \App\Plugin\AgapesHotes\SiteAccess();
$SiteAccess->setSiteUserId(getUserIdSession());
$Site = $SiteAccess->showSiteByUser();

$year = !empty($_POST['year']) ? $_POST['year'] : date('Y');
$month = !empty($_POST['month']) ? $_POST['month'] : date('m');

$date = $year . '-' . $month . '-' . date('d');

$dateNow = new \DateTime();
$dateCurrent = new \DateTime($date);
$dateMonthLater = new DateTime($date);
$dateMonthLater->add(new \DateInterval('P1M'));

if ($dateMonthLater->format('n') < $dateNow->format('n')) {
    $dateCurrent = new \DateTime(date('Y-m-d'));
}

$dateMonthAgo = new \DateTime($dateCurrent->format('Y-m-d'));
$dateMonthAgo->sub(new \DateInterval('P1M'));
/*
$inventaireUrl = 'https://serventest.fr/pro/liaison_appoe/getInventaireServentest.php';
$commandesUrl = 'https://serventest.fr/pro/liaison_appoe/getCommandServentest.php';
$refacturationUrl = 'https://serventest.fr/pro/liaison_appoe/getRefacturationServentest.php';

$paramsMonthNow = array(
    'key' => '123',
    'ref' => $Site->ref,
    'dateDebut' => $dateCurrent->format('Y-m-01'),
    'dateFin' => $dateCurrent->format('Y-m-t')
);
$paramsMonthAgo = array(
    'key' => '123',
    'ref' => $Site->ref,
    'dateDebut' => $dateMonthAgo->format('Y-m') . '-01',
    'dateFin' => $dateMonthAgo->format('Y-m-t')
);
*/
$dbc = mysqli_connect('db504799960.db.1and1.com', 'dbo504799960', 'mdp_commandes', 'db504799960');
$array_inventaire_now = array();
$array_inventaire_ago = array();
$array_refacturation = array();
$array_commande = array();

$query_liste_com_now = "SELECT ref, date, total_avec_marge AS total, fournisseur  FROM siteInventaireS
			WHERE ref = '".$Site->ref."' AND id_fournisseur IS NOT NULL AND date >= '".$dateCurrent->format('Y-m-01')."' AND date <= '".$dateCurrent->format('Y-m-t')."'";
$data_memo_now = mysqli_query($dbc, $query_liste_com_now) or die("Error: " . mysqli_error($dbc));

while ($donnees_memo_now = mysqli_fetch_array($data_memo_now, MYSQLI_ASSOC)) {
    $array_inventaire_now[] = $donnees_memo_now;
}

$query_liste_com_ago = "SELECT ref, date, total_avec_marge AS total, fournisseur  FROM siteInventaireS
			WHERE ref = '".$Site->ref."' AND id_fournisseur IS NOT NULL AND date >= '".$dateMonthAgo->format('Y-m') . '-01'."' AND date <= '".$dateMonthAgo->format('Y-m-t')."'";
$data_memo_ago = mysqli_query($dbc, $query_liste_com_ago) or die("Error: " . mysqli_error($dbc));

while ($donnees_memo_ago = mysqli_fetch_array($data_memo_ago, MYSQLI_ASSOC)) {
    $array_inventaire_ago[] = $donnees_memo_ago;
}

$query_liste_com_refacturation = "SELECT ref, total_avec_marge AS total, fournisseur, date_facturation FROM c_refacturation_total
			WHERE STR_TO_DATE(date_facturation,'%d/%m/%Y') >= '".$dateCurrent->format('Y-m-01')."' AND STR_TO_DATE(date_facturation,'%d/%m/%Y') <= '".$dateCurrent->format('Y-m-t')."' AND ref = '".$Site->ref."'
			ORDER BY STR_TO_DATE(date_facturation,'%d/%m/%Y') ASC";
$data_memo_refacturation = mysqli_query($dbc, $query_liste_com_refacturation)or die("Error: ".mysqli_error($dbc));
while ($donnees_memo_refacturation = mysqli_fetch_array($data_memo_refacturation, MYSQLI_ASSOC)) {
    $array_refacturation[] = $donnees_memo_refacturation;
}

$query_liste_com_commande = "SELECT ref, site, total_avec_marge AS total, fournisseur, date_livraison
			FROM siteCommandeS
			WHERE STR_TO_DATE(date_livraison,'%d/%m/%Y') >= '".$dateCurrent->format('Y-m-01')."' AND STR_TO_DATE(date_livraison,'%d/%m/%Y') <= '".$dateCurrent->format('Y-m-t')."' AND ref = '".$Site->ref."'
			ORDER BY STR_TO_DATE(date_livraison,'%d/%m/%Y') ASC";
$data_memo_commande = mysqli_query($dbc, $query_liste_com_commande)or die("Error: ".mysqli_error($dbc));
while ($donnees_memo_commande = mysqli_fetch_array($data_memo_commande, MYSQLI_ASSOC)) {
    $array_commande[] = $donnees_memo_commande;

}

//$inventaireRequest = json_decode(postHttpRequest($inventaireUrl, $paramsMonthNow), true);
//$inventaireRequestMonthAgo = json_decode(postHttpRequest($inventaireUrl, $paramsMonthAgo), true);
//$commandesRequest = json_decode(postHttpRequest($commandesUrl, $paramsMonthNow), true);
//$refacturationRequest = json_decode(postHttpRequest($refacturationUrl, $paramsMonthNow), true);
$inventaireRequest = $array_inventaire_now;
$inventaireRequestMonthAgo = $array_inventaire_ago;
$refacturationRequest = $array_refacturation;
$commandesRequest = $array_commande;
$View = new \App\Plugin\AgapesHotes\View();
$otherFournisseurs = array('BOULANGER' => 'BOULANGER', 'TRANSGOURMET' => 'TRANSGOURMET');
?>
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
                            $otherAchat = getAllCommandes($Site->id, $dateCurrent->format('Y'), $dateCurrent->format('m'));
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
                        $noteDeFrais = getNoteDeFrais($Site->id, $dateCurrent->format('Y'), $dateCurrent->format('m'));
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
                        $consoReelDenrees = (($totalRefraCommandDenree + $totalInventaireDenreeMonthAgo + $noteDeFrais['denree']) - $totalInventaireDenree);
                        $consoReelNonAlimentaires = (($totalRefraCommandUniqueEntretien + $totalInventaireUniqueEntretienMonthAgo + $noteDeFrais['nonAlimentaire']) - $totalInventaireUniqueEntretien);
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
                        <?php $facturation = getFacturation($Site->id, $dateCurrent->format('Y'), $dateCurrent->format('m')); ?>
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
                        $Budget->setYear($dateCurrent->format('Y'));
                        $Budget->setMonth($dateCurrent->format('m'));
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

        $siteMeta = getSiteMeta($Site->id, $dateCurrent->format('Y'), $dateCurrent->format('m'));
        $ParticipationTournante = $siteMeta['participationTournante'];
        $FraisDePersonnels = $siteMeta['fraisDePersonnel'];
        $indemniteKm = getIndemniteKm($Site->id, $dateCurrent->format('Y'), $dateCurrent->format('m'));
        $fraisDeSiege = ($facturation * 0.04);
        $fraisGenerauxTotal = ($ParticipationTournante + $fraisDeSiege + $consoReelNonAlimentaires + $noteDeFrais['autreAchat'] + $indemniteKm);
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
                <span class="littleText"><?= financial($fraisDeSiege); ?>€</span></div>
            <div class="littleContainer"><span class="littleTitle colorPrimary">Consommation produits d'entretien</span>
                <span class="littleText"><?= financial($consoReelNonAlimentaires); ?>€</span></div>
            <div class="littleContainer"><span class="littleTitle colorPrimary">Total HT</span>
                <span class="littleText"><?= financial($fraisGenerauxTotal); ?>€</span></div>
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
            <div class="littleContainer"><span class="littleTitle colorPrimary">Frais Fixes</span>
                <span class="littleText"><?= financial($siteMeta['fraisFixes']); ?>€</span>
            </div>
            <div class="littleContainer"><span class="littleTitle colorPrimary">CA</span>
                <span class="littleText"><?= financial($facturation + $siteMeta['fraisFixes']); ?>€</span>
            </div>
            <div class="littleContainer"><span class="littleTitle colorPrimary">Consommation</span>
                <span class="littleText"><?= financial($consoReelDenrees); ?>€</span></div>
            <div class="littleContainer"><span
                        class="littleTitle colorPrimary">Frais de personnel</span>
                <span class="littleText"><?= financial($FraisDePersonnels); ?>€</span></div>
            <div class="littleContainer"><span class="littleTitle colorPrimary">Frais généraux</span>
                <span class="littleText"><?= financial($fraisGenerauxTotal); ?>€</span></div>
            <div class="littleContainer"><span class="littleTitle colorPrimary">Résultats bruts d'éxploitation</span>
                <span class="littleText"><?= financial((($facturation + $siteMeta['fraisFixes']) - ($consoReelDenrees + $FraisDePersonnels + $fraisGenerauxTotal))); ?>€</span>
            </div>
        </div>
    </div>
</div>
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
                            <?= \App\Form::text('Date de livraison', 'date', 'date', '', true, 10, '', '', 'datepicker'); ?>
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