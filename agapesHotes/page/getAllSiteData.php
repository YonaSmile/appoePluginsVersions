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

        /*$inventaireUrl = 'https://serventest.fr/pro/liaison_appoe/getInventaireServentest.php';
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

            $dateMonthAgo = new \DateTime(date($year . '-' . $month . '-01'));
            $dateMonthAgo->sub(new \DateInterval('P1M'));
            $paramsAgo = array(
                'key' => '123',
                'ref' => $Site->getRef(),
                'dateDebut' => $dateMonthAgo->format('Y-m') . '-01',
                'dateFin' => $dateMonthAgo->format('Y-m-t')
            );
        }*/

        if ($year == 2018) {

            $json = getJsonContent(AGAPESHOTES_PATH . 'data/general.json');
            $CA_Total = 0;
            $Conso = 0;
            $FP = 0;
            $FG = 0;
            $RBE = 0;
            $helpArr = array();

            if (array_key_exists($Site->getRef(), $json)) {

                if (!empty($month)) {

                    $caTotal = 'CA_Total' . $month;
                    $conso = 'Conso' . $month;
                    $fraisPersonnel = 'FP' . $month;
                    $fraisGeneraux = 'FG' . $month;
                    $resultatBrutExploitation = 'RBE' . $month;

                    $Conso = ($json[$Site->getRef()][$conso]) * 1000;
                    $CA_Total = ($json[$Site->getRef()][$caTotal]) * 1000;
                    $FP = ($json[$Site->getRef()][$fraisPersonnel]) * 1000;
                    $FG = ($json[$Site->getRef()][$fraisGeneraux]) * 1000;
                    $RBE = ($json[$Site->getRef()][$resultatBrutExploitation]) * 1000;

                } else {

                    foreach ($json[$Site->getRef()] as $key => $val) {
                        $formatedKey = preg_replace('/[^a-zA-Z_]/', '', $key);
                        if (!array_key_exists($formatedKey, $helpArr)) {
                            $helpArr[$formatedKey] = 0;
                        }
                        $helpArr[$formatedKey] += ($val * 1000);
                    }
                    extract($helpArr);
                }
            }

            $allSitesData[$Secteur->getId()][$Site->getId()]['siteMeta']['fraisDePersonnel'] = financial($FP, true);
            $allSitesData[$Secteur->getId()][$Site->getId()]['facturation'] = financial($CA_Total, true);
            $allSitesData[$Secteur->getId()][$Site->getId()]['consoReel']['denree'] = financial($Conso, true);
            $allSitesData[$Secteur->getId()][$Site->getId()]['fraisDeSiege'] = financial($CA_Total * 0.04, true);
            $allSitesData[$Secteur->getId()][$Site->getId()]['fraisGeneraux'] = financial($FG, true);
            $allSitesData[$Secteur->getId()][$Site->getId()]['resultatExploitation'] = financial($RBE, true);
            $allSitesData[$Secteur->getId()][$Site->getId()]['retourAchat'] = financial($Conso * 0.065, true);
            $allSitesData[$Secteur->getId()][$Site->getId()]['resultats'] = financial($allSitesData[$Secteur->getId()][$Site->getId()]['resultatExploitation']
                + $allSitesData[$Secteur->getId()][$Site->getId()]['retourAchat']
                + $allSitesData[$Secteur->getId()][$Site->getId()]['fraisDeSiege'], true);

            $allSitesData[$Secteur->getId()][$Site->getId()]['pourcentagesDeRentabilite'] =
                financial($CA_Total > 0 ? $allSitesData[$Secteur->getId()][$Site->getId()]['resultats'] / $CA_Total : 0, true);
        } else {

            //connexion à la base de données commandes_en_ligne
            $dbc = mysqli_connect('db504799960.db.1and1.com', 'dbo504799960', 'mdp_commandes', 'db504799960');
            $array_inventaire_now = array();
            $array_inventaire_ago = array();
            $array_refacturation = array();

            if (empty($month)) {

                $dateNowDebut = $year;
                $dateNowFin = $year + 1;

                $dateAgoDebut = $year - 1;
                $dateAgoFin = $year;

                $inventaireNowAddedSql = 'YEAR(date) >= ' . $dateNowDebut . ' AND YEAR(date) <= ' . $dateNowFin;
                $inventaireAgoAddedSql = 'YEAR(date) >= ' . $dateAgoDebut . ' AND YEAR(date) <= ' . $dateAgoFin;
                $facturationAgoAddedSql = " (YEAR(STR_TO_DATE(date_facturation,'%d/%m/%Y')) = '" . $dateNowDebut . "') ";

            } else {
                $month = strlen($month) < 2 ? '0' . $month : $month;

                $date = new \DateTime($year . '-' . $month . '-01');
                $dateNowDebut = $date->format('Y-m-d');
                $dateNowFin = $date->format('Y-m-t');

                $dateMonthAgo = new \DateTime($date->format('Y-m-d'));
                $dateMonthAgo->sub(new \DateInterval('P1M'));

                $dateAgoDebut = $dateMonthAgo->format('Y-m-01');
                $dateAgoFin = $dateMonthAgo->format('Y-m-t');

                $inventaireNowAddedSql = 'date >= "' . $dateNowDebut . '" AND date <= "' . $dateNowFin . '"';
                $inventaireAgoAddedSql = 'date >= "' . $dateAgoDebut . '" AND date <= "' . $dateAgoFin . '"';
                $facturationAgoAddedSql = ' (STR_TO_DATE(date_facturation,"%d/%m/%Y") BETWEEN "' . $dateNowDebut . '" AND "' . $dateNowFin . '") ';
            }

            $query_liste_com_now = 'SELECT ref, date, total_avec_marge AS total, fournisseur  FROM siteInventaireS
			WHERE ref = "' . $Site->getRef() . '" AND ' . $inventaireNowAddedSql;
            $data_memo_now = mysqli_query($dbc, $query_liste_com_now) or die("Error: " . mysqli_error($dbc));

            while ($donnees_memo_now = mysqli_fetch_array($data_memo_now, MYSQLI_ASSOC)) {
                $array_inventaire_now[] = $donnees_memo_now;
            }

            $query_liste_com_ago = 'SELECT ref, date, total_avec_marge AS total, fournisseur  FROM siteInventaireS
			WHERE ref = "' . $Site->getRef() . '" AND ' . $inventaireAgoAddedSql;
            $data_memo_ago = mysqli_query($dbc, $query_liste_com_ago) or die("Error: " . mysqli_error($dbc));

            while ($donnees_memo_ago = mysqli_fetch_array($data_memo_ago, MYSQLI_ASSOC)) {
                $array_inventaire_ago[] = $donnees_memo_ago;
            }

            $query_liste_com_refacturation = 'SELECT ref, total_avec_marge AS total, fournisseur, date_facturation FROM c_refacturation_total
			WHERE ' . $facturationAgoAddedSql . ' AND ref = "' . $Site->getRef() . '"
			ORDER BY STR_TO_DATE(date_facturation,"%d/%m/%Y") ASC';
            $data_memo_refacturation = mysqli_query($dbc, $query_liste_com_refacturation) or die("Error: " . mysqli_error($dbc));
            while ($donnees_memo_refacturation = mysqli_fetch_array($data_memo_refacturation, MYSQLI_ASSOC)) {
                $array_refacturation[] = $donnees_memo_refacturation;
            }

            $allSitesData[$Secteur->getId()]['data'] = $Secteur;
            $allSitesData[$Secteur->getId()][$Site->getId()]['data'] = $Site;
            //$allSitesData[$Secteur->getId()][$Site->getId()]['inventaireRequest'] = json_decode(postHttpRequest($inventaireUrl, $paramsNow), true);
            //$allSitesData[$Secteur->getId()][$Site->getId()]['inventaireRequestMonthAgo'] = json_decode(postHttpRequest($inventaireUrl, $paramsAgo), true);
            //$allSitesData[$Secteur->getId()][$Site->getId()]['commandesRequest'] = json_decode(postHttpRequest($commandesUrl, $paramsNow), true);
            $allSitesData[$Secteur->getId()][$Site->getId()]['commandesRequest'] = $array_refacturation;
            //$allSitesData[$Secteur->getId()][$Site->getId()]['commandes'] = getCommandesServentest(array_merge($allSitesData[$Secteur->getId()][$Site->getId()]['commandesRequest'], getAllCommandes($Site->getId(), $year, $month)));
            $allSitesData[$Secteur->getId()][$Site->getId()]['commandes'] = getCommandesServentest($array_refacturation);
            $allSitesData[$Secteur->getId()][$Site->getId()]['inventaire'] = getInventaireServentest($array_inventaire_now);
            $allSitesData[$Secteur->getId()][$Site->getId()]['inventaireMonthAgo'] = getInventaireServentest($array_inventaire_ago);
            $allSitesData[$Secteur->getId()][$Site->getId()]['noteDeFrais'] = getNoteDeFrais($Site->getId(), $year, $month);
            $allSitesData[$Secteur->getId()][$Site->getId()]['indemniteKm'] = getIndemniteKm($Site->getId(), $year, $month);
            $allSitesData[$Secteur->getId()][$Site->getId()]['siteMeta'] = getSiteMeta($Site->getId(), $year, $month);
            $allSitesData[$Secteur->getId()][$Site->getId()]['facturation'] = getFacturation($Site->getId(), $year, $month) + $allSitesData[$Secteur->getId()][$Site->getId()]['siteMeta']['fraisFixes'];
            $allSitesData[$Secteur->getId()][$Site->getId()]['budget'] = getBudget($Site->getId(), $year, $month);

            $allSitesData[$Secteur->getId()][$Site->getId()]['consoReel']['denree'] = financial(((
                    $allSitesData[$Secteur->getId()][$Site->getId()]['commandes']['denree']['total']
                    + $allSitesData[$Secteur->getId()][$Site->getId()]['inventaireMonthAgo']['denree']['total']
                    + $allSitesData[$Secteur->getId()][$Site->getId()]['noteDeFrais']['denree']
                ) - $allSitesData[$Secteur->getId()][$Site->getId()]['inventaire']['denree']['total']), true);

            $allSitesData[$Secteur->getId()][$Site->getId()]['consoReel']['nonAlimentaire'] = financial(((
                    $allSitesData[$Secteur->getId()][$Site->getId()]['commandes']['nonAlimentaire']['total']
                    + $allSitesData[$Secteur->getId()][$Site->getId()]['inventaireMonthAgo']['nonAlimentaire']['total']
                    + $allSitesData[$Secteur->getId()][$Site->getId()]['noteDeFrais']['nonAlimentaire']
                ) - $allSitesData[$Secteur->getId()][$Site->getId()]['inventaire']['nonAlimentaire']['total']), true);


            $allSitesData[$Secteur->getId()][$Site->getId()]['fraisDeSiege'] =
                financial(($allSitesData[$Secteur->getId()][$Site->getId()]['facturation'] * 0.04), true);

            $allSitesData[$Secteur->getId()][$Site->getId()]['fraisGeneraux'] =
                financial(($allSitesData[$Secteur->getId()][$Site->getId()]['siteMeta']['participationTournante']
                    + $allSitesData[$Secteur->getId()][$Site->getId()]['fraisDeSiege']
                    + $allSitesData[$Secteur->getId()][$Site->getId()]['noteDeFrais']['autreAchat']
                    + $allSitesData[$Secteur->getId()][$Site->getId()]['indemniteKm']
                    + $allSitesData[$Secteur->getId()][$Site->getId()]['consoReel']['nonAlimentaire']), true);

            $allSitesData[$Secteur->getId()][$Site->getId()]['resultatExploitation'] =
                financial(($allSitesData[$Secteur->getId()][$Site->getId()]['facturation']
                    - ($allSitesData[$Secteur->getId()][$Site->getId()]['consoReel']['denree']
                        + $allSitesData[$Secteur->getId()][$Site->getId()]['siteMeta']['fraisDePersonnel']
                        + $allSitesData[$Secteur->getId()][$Site->getId()]['fraisGeneraux'])), true);

            $allSitesData[$Secteur->getId()][$Site->getId()]['retourAchat'] =
                financial(($allSitesData[$Secteur->getId()][$Site->getId()]['consoReel']['denree'] * 0.065), true);

            $allSitesData[$Secteur->getId()][$Site->getId()]['resultats'] =
                financial(($allSitesData[$Secteur->getId()][$Site->getId()]['resultatExploitation']
                    + $allSitesData[$Secteur->getId()][$Site->getId()]['retourAchat']
                    + $allSitesData[$Secteur->getId()][$Site->getId()]['fraisDeSiege']), true);

            $allSitesData[$Secteur->getId()][$Site->getId()]['pourcentagesDeRentabilite'] =
                financial($allSitesData[$Secteur->getId()][$Site->getId()]['facturation'] > 0
                    ? $allSitesData[$Secteur->getId()][$Site->getId()]['resultats'] / $allSitesData[$Secteur->getId()][$Site->getId()]['facturation']
                    : 0, true);

        }
        echo json_encode($allSitesData);
    }

}