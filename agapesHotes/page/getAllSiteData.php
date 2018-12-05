<?php
require_once('../main.php');
if (!empty($_POST['siteId']) && !empty($_POST['year'])) {

    $Site = new \App\Plugin\AgapesHotes\Site();
    $Site->setId($_POST['siteId']);
    if ($Site->show()) {

        $Secteur = new \App\Plugin\AgapesHotes\Secteur($Site->getSecteurId());

        $year = $_POST['year'];
        $month = !empty($_POST['month']) ? $_POST['month'] : '';
        $allSitesData = array();

        $inventaireUrl = 'https://serventest.fr/pro/liaison_appoe/getInventaireServentest.php';
        $commandesUrl = 'https://serventest.fr/pro/liaison_appoe/getRefacturationServentest.php';

        if (empty($month)) {
            $paramsNow = array(
                'key' => '123',
                'ref' => $Site->getRef(),
                'dateDebut' => $year,
                'dateFin' => $year + 1
            );

            $paramsAgo = array(
                'key' => '123',
                'ref' => $Site->getRef(),
                'dateDebut' => $year - 1,
                'dateFin' => $year
            );
        } else {
            $paramsNow = array(
                'key' => '123',
                'ref' => $Site->getRef(),
                'dateDebut' => date($year . '-' . $month . '-01'),
                'dateFin' => date($year . '-' . $month . '-t')
            );

            $paramsAgo = array(
                'key' => '123',
                'ref' => $Site->getRef(),
                'dateDebut' => date($year . '-' . (date('m') - 1) . '-01'),
                'dateFin' => date($year . '-m-t')
            );
        }

        $allSitesData[$Secteur->getId()]['data'] = $Secteur;
        $allSitesData[$Secteur->getId()][$Site->getId()]['data'] = $Site;
        $allSitesData[$Secteur->getId()][$Site->getId()]['inventaireRequest'] = json_decode(postHttpRequest($inventaireUrl, $paramsNow), true);
        $allSitesData[$Secteur->getId()][$Site->getId()]['inventaireRequestMonthAgo'] = json_decode(postHttpRequest($inventaireUrl, $paramsAgo), true);
        $allSitesData[$Secteur->getId()][$Site->getId()]['commandesRequest'] = json_decode(postHttpRequest($commandesUrl, $paramsNow), true);
        $allSitesData[$Secteur->getId()][$Site->getId()]['commandes'] = getCommandesServentest($allSitesData[$Secteur->getId()][$Site->getId()]['commandesRequest']);
        $allSitesData[$Secteur->getId()][$Site->getId()]['inventaire'] = getInventaireServentest($allSitesData[$Secteur->getId()][$Site->getId()]['inventaireRequest']);
        $allSitesData[$Secteur->getId()][$Site->getId()]['inventaireMonthAgo'] = getInventaireServentest($allSitesData[$Secteur->getId()][$Site->getId()]['inventaireRequestMonthAgo']);
        $allSitesData[$Secteur->getId()][$Site->getId()]['noteDeFrais'] = getNoteDeFrais($Site->getId(), $year, $month);
        $allSitesData[$Secteur->getId()][$Site->getId()]['facturation'] = getFacturation($Site->getId(), $year, $month);
        $allSitesData[$Secteur->getId()][$Site->getId()]['siteMeta'] = getSiteMeta($Site->getId(), $year, $month);
        $allSitesData[$Secteur->getId()][$Site->getId()]['budget'] = getBudget($Site->getId(), $year, $month);

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


        $allSitesData[$Secteur->getId()][$Site->getId()]['fraisDeSiege'] = $allSitesData[$Secteur->getId()][$Site->getId()]['facturation'] * 0.04;
        $allSitesData[$Secteur->getId()][$Site->getId()]['fraisGeneraux'] =
            $allSitesData[$Secteur->getId()][$Site->getId()]['siteMeta']['participationTournante']
            + $allSitesData[$Secteur->getId()][$Site->getId()]['fraisDeSiege']
            + $allSitesData[$Secteur->getId()][$Site->getId()]['consoReel']['nonAlimentaire'];

        $allSitesData[$Secteur->getId()][$Site->getId()]['resultatExploitation'] = $allSitesData[$Secteur->getId()][$Site->getId()]['facturation'] - ($allSitesData[$Secteur->getId()][$Site->getId()]['consoReel']['denree'] + $allSitesData[$Secteur->getId()][$Site->getId()]['siteMeta']['fraisDePersonnels'] + $allSitesData[$Secteur->getId()][$Site->getId()]['fraisGeneraux']);
        $allSitesData[$Secteur->getId()][$Site->getId()]['retourAchat'] = $allSitesData[$Secteur->getId()][$Site->getId()]['consoReel']['denree'] * 0.08;
        $allSitesData[$Secteur->getId()][$Site->getId()]['resultats'] = $allSitesData[$Secteur->getId()][$Site->getId()]['resultatExploitation'] + $allSitesData[$Secteur->getId()][$Site->getId()]['retourAchat'] + $allSitesData[$Secteur->getId()][$Site->getId()]['fraisDeSiege'];
        $allSitesData[$Secteur->getId()][$Site->getId()]['pourcentagesDeRentabilite'] = $allSitesData[$Secteur->getId()][$Site->getId()]['facturation'] > 0 ? $allSitesData[$Secteur->getId()][$Site->getId()]['resultats'] / $allSitesData[$Secteur->getId()][$Site->getId()]['facturation'] : 0;
        echo json_encode($allSitesData);
    }

}