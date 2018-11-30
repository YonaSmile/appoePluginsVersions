<?php
require_once('../main.php');
if(!empty($_POST['siteId']) && !empty($_POST['month']) && !empty($_POST['year'])){

    $Site = new \App\Plugin\AgapesHotes\Site();
    $Site->setId($_POST['siteId']);
    if($Site->show()){

        $Secteur = new \App\Plugin\AgapesHotes\Secteur($Site->getSecteurId());

        $year = $_POST['year'];
        $month = $_POST['month'];
        $allSitesData = array();

        $inventaireUrl = 'https://serventest.fr/pro/liaison_appoe/getInventaireServentest.php';
        $commandesUrl = 'https://serventest.fr/pro/liaison_appoe/getCommandServentest.php';

        $paramsMonthNow = array(
            'key' => '123',
            'ref' => $Site->getRef(),
            'dateDebut' => date($year.'-'.$month.'-01'),
            'dateFin' => date($year.'-'.$month.'-t')
        );

        $paramsMonthAgo = array(
            'key' => '123',
            'ref' => $Site->getRef(),
            'dateDebut' => date('Y-' . (date('m') - 1) . '-01'),
            'dateFin' => date('Y-m-t')
        );

        $allSitesData[$Secteur->getId()]['data'] = $Secteur;
        $allSitesData[$Secteur->getId()][$Site->getId()]['data'] = $Site;
        $allSitesData[$Secteur->getId()][$Site->getId()]['inventaireRequest'] = json_decode(postHttpRequest($inventaireUrl, $paramsMonthNow), true);
        $allSitesData[$Secteur->getId()][$Site->getId()]['inventaireRequestMonthAgo'] = json_decode(postHttpRequest($inventaireUrl, $paramsMonthAgo), true);
        $allSitesData[$Secteur->getId()][$Site->getId()]['commandesRequest'] = json_decode(postHttpRequest($commandesUrl, $paramsMonthNow), true);
        $allSitesData[$Secteur->getId()][$Site->getId()]['commandes'] = getCommandesServentest($allSitesData[$Secteur->getId()][$Site->getId()]['commandesRequest']);
        $allSitesData[$Secteur->getId()][$Site->getId()]['inventaire'] = getInventaireServentest($allSitesData[$Secteur->getId()][$Site->getId()]['inventaireRequest']);
        $allSitesData[$Secteur->getId()][$Site->getId()]['inventaireMonthAgo'] = getInventaireServentest($allSitesData[$Secteur->getId()][$Site->getId()]['inventaireRequestMonthAgo']);
        $allSitesData[$Secteur->getId()][$Site->getId()]['noteDeFrais'] = getNoteDeFrais($Site->getId(), date('m'), date('Y'));
        $allSitesData[$Secteur->getId()][$Site->getId()]['facturation'] = getFacturation($Site->getId(), date('m'), date('Y'));
        $allSitesData[$Secteur->getId()][$Site->getId()]['siteMeta'] = getSiteMeta($Site->getId(), date('m'), date('Y'));
        $allSitesData[$Secteur->getId()][$Site->getId()]['budget'] = getBudget($Site->getId(), date('m'), date('Y'));

        $allSitesData[$Secteur->getId()][$Site->getId()]['consoReel']['denree'] = (
                $allSitesData[$Secteur->getId()][$Site->getId()]['commandes']['denree']['total']
                + $allSitesData[$Secteur->getId()][$Site->getId()]['inventaireMonthAgo']['denree']['total']
                + $allSitesData[$Secteur->getId()][$Site->getId()]['noteDeFrais']['denree']
            ) - $allSitesData[$Secteur->getId()][$Site->getId()]['inventaire']['denree']['total'];

        $allSitesData[$Secteur->getId()][$Site->getId()]['consoReel']['nonAlimentaire'] = (
                $allSitesData[$Secteur->getId()][$Site->getId()]['commandes']['nonAlimentaire']['total']
                + $allSitesData[$Secteur->getId()][$Site->getId()]['inventaireMonthAgo']['nonAlimentaire']['total']
                + $allSitesData[$Secteur->getId()][$Site->getId()]['noteDeFrais']['nonAlimentaire']
            ) - $allSitesData[$Secteur->getId()][$Site->getId()]['inventaire']['nonAlimentaire']['total'];


        $allSitesData[$Secteur->getId()][$Site->getId()]['fraisDeSiege'] = financial($allSitesData[$Secteur->getId()][$Site->getId()]['facturation'] * 0.04);
        $allSitesData[$Secteur->getId()][$Site->getId()]['fraisGeneraux'] = financial(
            $allSitesData[$Secteur->getId()][$Site->getId()]['siteMeta']['participationTournante']
            + $allSitesData[$Secteur->getId()][$Site->getId()]['fraisDeSiege']
            + $allSitesData[$Secteur->getId()][$Site->getId()]['consoReel']['nonAlimentaire']
        );

        $allSitesData[$Secteur->getId()][$Site->getId()]['resultatExploitation'] = financial($allSitesData[$Secteur->getId()][$Site->getId()]['facturation'] - ($allSitesData[$Secteur->getId()][$Site->getId()]['consoReel']['denree'] + $allSitesData[$Secteur->getId()][$Site->getId()]['siteMeta']['fraisDePersonnels'] + $allSitesData[$Secteur->getId()][$Site->getId()]['fraisGeneraux']));
        $allSitesData[$Secteur->getId()][$Site->getId()]['retourAchat'] = financial($allSitesData[$Secteur->getId()][$Site->getId()]['consoReel']['denree'] * 0.08);
        $allSitesData[$Secteur->getId()][$Site->getId()]['resultats'] = financial($allSitesData[$Secteur->getId()][$Site->getId()]['resultatExploitation'] + $allSitesData[$Secteur->getId()][$Site->getId()]['retourAchat'] + $allSitesData[$Secteur->getId()][$Site->getId()]['fraisDeSiege']);
        $allSitesData[$Secteur->getId()][$Site->getId()]['pourcentagesDeRentabilite'] = $allSitesData[$Secteur->getId()][$Site->getId()]['facturation'] > 0 ? financial($allSitesData[$Secteur->getId()][$Site->getId()]['resultats'] / $allSitesData[$Secteur->getId()][$Site->getId()]['facturation']) : 0;
        echo json_encode($allSitesData);
    }

}