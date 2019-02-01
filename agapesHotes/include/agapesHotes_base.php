<?php
/**
 * @param DateTime $startDay
 * @param $cycles
 * @param DateTime $endDay
 * @return bool|int
 */
function getDayInCycle(DateTime $startDay, $cycles, DateTime $endDay)
{
    if ($startDay <= $endDay) {

        $ptiInterval = $startDay->diff($endDay);
        $joursDiff = $ptiInterval->format('%r%a');

        if ($joursDiff > 0) {
            return ($joursDiff % ($cycles * 7)) + 1;
        }
    }
    return false;
}

/**
 * @param $idEmploye
 * @param $idSite
 * @return bool
 */
function employeHasContrat($idEmploye, $idSite)
{

    //Get Employe Contrat
    $EmployeContrat = new \App\Plugin\AgapesHotes\EmployeContrat();
    $EmployeContrat->setEmployeId($idEmploye);
    $allContrats = $EmployeContrat->showAllByEmploye();

    if ($allContrats) {

        $allContrats = extractFromObjToSimpleArr($allContrats, 'id', 'site_id');
        if (in_array($idSite, $allContrats)) {
            return true;
        }
    }

    return false;
}


/**
 * @param $idSite
 * @param $year
 * @param string $month
 * @return array
 * @noinspection PhpUnhandledExceptionInspection
 */
function getAllEmployeHasContratInSite($idSite, $year, $month = '')
{

    $allContratsDates = array();
    $day = '01';

    if (empty($month)) {
        $month = date('m');
    }

    try {
        $dateContrat = new \DateTime($year . '-' . $month . '-' . $day);

        //Get Employe Contrat
        $EmployeContrat = new \App\Plugin\AgapesHotes\EmployeContrat();
        $EmployeContrat->setDateDebut($dateContrat->format('Y-m-d'));
        $EmployeContrat->setSiteId($idSite);
        $allContratsDates = $EmployeContrat->showAllReelDateEmployesContrats();

        return extractFromObjArr($allContratsDates, 'employe_id');
    } catch (Exception $e) {
        error_log($e->getMessage());
    }
    return $allContratsDates;
}

/**
 * @param $idEmploye
 * @return bool|array
 */
function getEmployeContrats($idEmploye)
{

    $allReelContrats = array();

    //Get Sites
    $Site = new \App\Plugin\AgapesHotes\Site();
    $allSites = extractFromObjToSimpleArr($Site->showAll(), 'id', 'nom');

    //Get Employe Contrat
    $EmployeContrat = new \App\Plugin\AgapesHotes\EmployeContrat();
    $EmployeContrat->setEmployeId($idEmploye);

    if ($allSites) {
        foreach ($allSites as $id => $siteName) {
            $EmployeContrat->setDateDebut(date('Y-m-d'));
            $EmployeContrat->setSiteId($id);
            if ($EmployeContrat->showReelContrat()) {
                $allReelContrats[$EmployeContrat->getId()] = array(
                    'id' => $EmployeContrat->getId(),
                    'siteId' => $EmployeContrat->getSiteId(),
                    'siteName' => $siteName,
                    'employeId' => $idEmploye,
                    'dateDebut' => $EmployeContrat->getDateDebut(),
                    'nbHeuresSemaines' => $EmployeContrat->getNbHeuresSemaines(),
                    'typeContrat' => $EmployeContrat->getTypeContrat()
                );
            }
        }
    }
    return $allReelContrats;
}

/**
 * @param $siteId
 * @return array
 */
function getAllPtiBySite($siteId)
{

    //Get Pti
    $Pti = new \App\Plugin\AgapesHotes\Pti();
    $Pti->setSiteId($siteId);
    $allPti = groupMultipleKeysObjectsArray($Pti->showAllBySite(), 'employe_id');

    if ($allPti) {
        foreach ($allPti as $employeId => $allEmployePti) {
            $allPti[$employeId] = extractFromObjArr($allEmployePti, 'dateDebut');
        }

        foreach ($allPti as $employeId => $allEmployePti) {
            foreach ($allEmployePti as $dateDebut => $pti) {

                $PtiDetails = new \App\Plugin\AgapesHotes\PtiDetails($pti->id);
                if ($PtiDetails->getData()) {
                    $allPti[$employeId][$dateDebut]->details = $PtiDetails->getData();

                }
            }
        }
    }

    return $allPti;
}

/**
 * @param $etablissementId
 * @return array
 */
function getAllPrestationsPriceByEtablissement($etablissementId)
{

    $PrestationPrix = new \App\Plugin\AgapesHotes\PrixPrestation();
    $PrestationPrix->setEtablissementId($etablissementId);
    $PrestationPrix->setDateDebut(date('Y-m-d'));
    $allPrestationsPrice = groupMultipleKeysObjectsArray($PrestationPrix->showAll(), 'prestation_id');

    if ($allPrestationsPrice) {
        foreach ($allPrestationsPrice as $prestationId => $allPrices) {
            $allPrestationsPrice[$prestationId] = extractFromObjArr($allPrices, 'dateDebut');
        }
    }

    return $allPrestationsPrice;
}

/**
 * @param $etablissementId
 * @param string $month
 * @return array
 */
function getAllMainCouranteByEtablissementInMonth($etablissementId, $month = '')
{

    $month = !empty($month) ? $month : date('m');
    $MainCourante = new \App\Plugin\AgapesHotes\MainCourante();
    $MainCourante->setEtablissementId($etablissementId);
    $MainCourante->setDate($month);
    $allMainCourante = groupMultipleKeysObjectsArray($MainCourante->showAllByMonth(), 'prestation_id');

    if ($allMainCourante) {
        foreach ($allMainCourante as $prestationId => $allPrestations) {
            $allMainCourante[$prestationId] = extractFromObjArr($allPrestations, 'date');
        }
    }

    return $allMainCourante;
}

/**
 * @param $etablissementId
 * @param string $month
 * @return array
 */
function getAllVivreCrueByEtablissementInMonth($etablissementId, $month = '')
{

    $month = !empty($month) ? $month : date('m');
    $VivreCrue = new \App\Plugin\AgapesHotes\VivreCrue();
    $VivreCrue->setEtablissementId($etablissementId);
    $VivreCrue->setDate($month);
    $allVivresCrue = groupMultipleKeysObjectsArray($VivreCrue->showAllByMonth(), 'idCourse');

    if ($allVivresCrue) {
        foreach ($allVivresCrue as $idCourse => $allCourses) {
            $allVivresCrue[$idCourse] = extractFromObjArr($allCourses, 'date');
        }
    }

    return $allVivresCrue;
}

/**
 * @param $siteId
 * @param string $month
 * @return array
 */
function getAllPlanningBySite($siteId, $month = '')
{
    $month = !empty($month) ? $month : date('m');
    $Planning = new \App\Plugin\AgapesHotes\Planning();
    $Planning->setSiteId($siteId);
    $Planning->setDate($month);
    $allPlanning = groupMultipleKeysObjectsArray($Planning->showAllByDate(), 'employe_id');

    if ($allPlanning) {
        foreach ($allPlanning as $employeId => $planning) {
            $allPlanning[$employeId] = extractFromObjArr($planning, 'date');
        }
    }

    return $allPlanning;
}

/**
 * @param $a
 * @param $b
 * @return false|int
 */
function reelDatesSortDESC($a, $b)
{
    return strtotime($b) - strtotime($a);
}

/**
 * @param $siteId
 * @param string $year
 * @param string $month
 * @return array|bool
 * @throws Exception
 */
function getAllCommandes($siteId, $year = '', $month = '')
{

    $year = !empty($year) ? $year : date('Y');
    $month = !empty($month) ? $month : '';

    $Achat = new \App\Plugin\AgapesHotes\Achat();
    $Achat->setSiteId($siteId);
    $allAchat = $Achat->showBySite($year, $month);

    if (!isArrayEmpty($allAchat)) {
        foreach ($allAchat as &$achat) {

            $oldDate = new \Datetime($achat['date_livraison']);
            $achat['date_livraison'] = $oldDate->format('d/m/Y');
        }
    }
    return $allAchat;

}

/**
 * @param $allCommandes
 * @return array
 */
function getCommandesServentest($allCommandes)
{

    $commandServentest = array(
        'denree' => array('total' => 0),
        'nonAlimentaire' => array('total' => 0)
    );

    if (!isArrayEmpty($allCommandes)) {
        foreach ($allCommandes as $key => $allDenree) {
            if (isNotUniqueEntretien($allDenree['fournisseur'])) {
                $commandServentest['denree']['total'] += $allDenree['total'];
                $commandServentest['denree'][] = $allDenree;
            } else {
                $commandServentest['nonAlimentaire']['total'] += $allDenree['total'];
                $commandServentest['nonAlimentaire'][] = $allDenree;
            }

        }
    }
    return $commandServentest;

}

/**
 * @param $allInventaire
 * @return array
 */
function getInventaireServentest($allInventaire)
{

    $inventaireServentest = array(
        'denree' => array('total' => 0),
        'nonAlimentaire' => array('total' => 0)
    );

    if (!isArrayEmpty($allInventaire)) {
        foreach ($allInventaire as $key => $allDenree) {
            if (trim($allDenree['fournisseur']) != 'C2M') {
                $inventaireServentest['denree']['total'] += $allDenree['total'];
                //$inventaireServentest['denree'][] = $allDenree;
            } else {
                $inventaireServentest['nonAlimentaire']['total'] += $allDenree['total'];
                //$inventaireServentest['nonAlimentaire'][] = $allDenree;
            }

        }
    }
    return $inventaireServentest;

}

/**
 * @param $siteId
 * @param $year
 * @param string $month
 * @return array
 */
function getSiteMeta($siteId, $year, $month = '')
{

    $siteMeta = array(
        'participationTournante' => 0,
        'fraisDePersonnel' => 0,
        'fraisFixes' => 0
    );

    $SiteMeta = new \App\Plugin\AgapesHotes\SiteMeta();
    $SiteMeta->setSiteId($siteId);
    if (!empty($month)) {
        $SiteMeta->setMonth($month);
    }
    $SiteMeta->setYear($year);

    $allSiteMetas = extractFromObjArr($SiteMeta->showBySite(), 'dataName');

    if (!isArrayEmpty($allSiteMetas)) {
        $siteMeta['participationTournante'] = array_key_exists('Participation tournant', $allSiteMetas) ? $allSiteMetas['Participation tournant']->data : 0;
        $siteMeta['fraisDePersonnel'] = array_key_exists('Frais de personnel', $allSiteMetas) ? $allSiteMetas['Frais de personnel']->data : 0;
        $siteMeta['fraisFixes'] = array_key_exists('Frais fixes', $allSiteMetas) ? $allSiteMetas['Frais fixes']->data : 0;
    }

    return $siteMeta;

}

/**
 * @param $siteId
 * @param $year
 * @param $month
 * @return array
 */
function getNoteDeFrais($siteId, $year, $month = '')
{

    $noteDeFraisArr = array(
        'denree' => 0,
        'nonAlimentaire' => 0,
        'autreAchat' => 0
    );

    $NoteDeFrais = new \App\Plugin\AgapesHotes\NoteDeFrais();
    $NoteDeFrais->setSiteId($siteId);
    $NoteDeFrais->setYear($year);
    if (!empty($month)) {
        $month = ($month < 10 && strlen($month) == 1) ? '0' . $month : $month;
        $NoteDeFrais->setMonth($month);
    }
    $allNotesFrais = $NoteDeFrais->showByDate();

    if ($allNotesFrais && !isArrayEmpty($allNotesFrais)) {
        foreach ($allNotesFrais as $noteDeFrais) {
            if ($noteDeFrais->type == 1) {
                $noteDeFraisArr['denree'] += $noteDeFrais->montantTtc;
            }
            if ($noteDeFrais->type == 2) {
                $noteDeFraisArr['nonAlimentaire'] += $noteDeFrais->montantTtc;
            }
            if ($noteDeFrais->type == 3) {
                $noteDeFraisArr['autreAchat'] += $noteDeFrais->montantTtc;
            }
        }
    }
    return $noteDeFraisArr;
}

/**
 * @param $siteId
 * @param $year
 * @param $month
 * @return array
 */
function getIndemniteKm($siteId, $year, $month = '')
{

    $indemniteKm = 0;
    $NoteIk = new \App\Plugin\AgapesHotes\NoteIk();
    $NoteIk->setSiteId($siteId);
    $NoteIk->setYear($year);
    if (!empty($month)) {
        $NoteIk->setMonth($month);
    }
    $allNotesFrais = $NoteIk->showByDate();

    if ($allNotesFrais && !isArrayEmpty($allNotesFrais)) {
        foreach ($allNotesFrais as $noteDeFrais) {
            $indemniteKm += $noteDeFrais->montantHt;
        }
    }
    return $indemniteKm;
}

/**
 * @param $siteId
 * @param $year
 * @param string $month
 * @return int
 */
function getFacturation($siteId, $year, $month = '')
{

    $MainCourant = new \App\Plugin\AgapesHotes\MainCourante();
    $VivreCru = new \App\Plugin\AgapesHotes\VivreCrue();
    $MainSupp = new \App\Plugin\AgapesHotes\MainSupplementaire();

    $allMainCourant = $MainCourant->showTotalMainCourante($siteId, $year, $month);
    $allVivreCru = $VivreCru->showTotalVivreCru($siteId, $year, $month);
    $allFacturationHC = $MainSupp->showTotalFacturationHC($siteId, $year, $month);

    $totalFacturation = 0;
    foreach ($allMainCourant as $mainCourant) {
        $totalFacturation += $mainCourant->totalHT;
    }
    foreach ($allVivreCru as $vivreCrue) {
        $totalFacturation += $vivreCrue->totalHT;
    }
    foreach ($allFacturationHC as $facturationHC) {
        $totalFacturation += $facturationHC->totalHT;
    }


    return $totalFacturation;
}

/**
 * @param $siteId
 * @param $year
 * @param string $month
 * @return \App\Plugin\AgapesHotes\Budget|int
 */
function getBudget($siteId, $year, $month = '')
{

    $Budget = new \App\Plugin\AgapesHotes\Budget();

    $Budget->setSiteId($siteId);
    $Budget->setYear($year);
    if (!empty($month)) {
        $Budget->setMonth($month);
    }
    $Budget->showBySite();

    return $Budget ? $Budget : 0;
}

/**
 * @return array|bool
 */
function getSecteurAccess()
{

    $allSecteurs = array();
    $Secteur = new \App\Plugin\AgapesHotes\Secteur();

    if (getUserRoleId() == 2) {

        $SecteurAccess = new App\Plugin\AgapesHotes\SecteurAccess();
        $SecteurAccess->setSecteurUserId(getUserIdSession());
        $allSecteurs[] = $SecteurAccess->showSecteurByUser();

    } else {
        $allSecteurs = $Secteur->showAll();
    }
    return $allSecteurs;
}

/**
 * @return array|bool
 */
function getSitesAccess()
{

    $allSites = array();
    $Site = new \App\Plugin\AgapesHotes\Site();

    if (getUserRoleId() == 1) {

        $SiteAccess = new \App\Plugin\AgapesHotes\SiteAccess();
        $SiteAccess->setSiteUserId(getUserIdSession());
        $allSites[] = $SiteAccess->showSiteByUser();

    } elseif (getUserRoleId() == 2) {

        $SecteurAccess = new App\Plugin\AgapesHotes\SecteurAccess();
        $SecteurAccess->setSecteurUserId(getUserIdSession());
        $secteur = $SecteurAccess->showSecteurByUser();
        if ($secteur) {
            $Site->setSecteurId($secteur->id);
            $allSites = $Site->showBySecteur();
        }
    } else {
        $allSites = $Site->showAll();
    }
    return $allSites;
}

/**
 * @return array|bool
 */
function getEtablissementAccess()
{

    $allEtablissements = array();
    $Secteur = new \App\Plugin\AgapesHotes\Secteur();
    $Etablissement = new \App\Plugin\AgapesHotes\Etablissement();

    if (getUserRoleId() == 1) {
        $SiteAccess = new \App\Plugin\AgapesHotes\SiteAccess();
        $SiteAccess->setSiteUserId(getUserIdSession());
        $site = $SiteAccess->showSiteByUser();
        if ($site) {
            $Etablissement->setSiteId($site->id);
            $allEtablissements = $Etablissement->showAllBySite();
        }

    } elseif (getUserRoleId() == 2) {

        $SecteurAccess = new App\Plugin\AgapesHotes\SecteurAccess();
        $SecteurAccess->setSecteurUserId(getUserIdSession());
        $secteur = $SecteurAccess->showSecteurByUser();

        if ($secteur) {
            $Secteur->setId($secteur->id);
            $allEtablissements = $Secteur->showAllEtablissements();
        }

    } else {
        $allEtablissements = $Etablissement->showAll();
    }
    return $allEtablissements;
}

/**
 * @param $date
 * @param bool $alsaceMoselle
 * @return string
 */
function getDayColor($date, $alsaceMoselle = false)
{

    //Aujourd'hui et weekend
    if ($date->format('Y-m-d') == date('Y-m-d') && ($date->format('N') == 7 || $date->format('N') == 6)) {
        return 'background: linear-gradient(135deg, #4fb99f 0%,#4fb99f 50%,#aaa 51%,#aaa 100%);color:#fff;font-weight:bold;';
    }

    //Aujourd'hui et férié
    if (isferie($date->format('Y-m-d'), $alsaceMoselle) && $date->format('Y-m-d') == date('Y-m-d')) {
        return 'background: linear-gradient(135deg, #4fb99f 0%,#4fb99f 50%,#eaaa6f 51%,#eaaa6f 100%);color:#fff;font-weight:bold;';
    }

    //Aujourd'hui
    if ($date->format('Y-m-d') == date('Y-m-d')) {
        return 'background:#4fb99f;color:#fff;font-weight:bold;';
    }

    //Férié et weekend
    if (isferie($date->format('Y-m-d'), $alsaceMoselle) && ($date->format('N') == 7 || $date->format('N') == 6)) {
        return 'background: #aaa;color:#fff;font-weight:bold;';
    }

    //Férié
    if (isferie($date->format('Y-m-d'), $alsaceMoselle)) {
        return 'background:#eaaa6f;color:#fff;font-weight:bold;';
    }

    //Weekend
    if ($date->format('N') == 7 || $date->format('N') == 6) {
        return 'background:#aaa;color:#4b5b68;';
    }
    return '';
}

/**
 * @param $fournisseur
 * @return bool
 */
function isNotUniqueEntretien($fournisseur)
{

    $fournisseur = trim($fournisseur);
    $uniqueEntretien = array('C2M', 'SILLIKER', 'EUROFINS', 'AGRIVALOR');
    return !in_array($fournisseur, $uniqueEntretien);
}

/**
 * @param $fournisseur
 * @return bool
 */
function isUniqueEntretien($fournisseur)
{

    $fournisseur = trim($fournisseur);
    $uniqueEntretien = array('C2M', 'SILLIKER', 'EUROFINS', 'AGRIVALOR');
    return in_array($fournisseur, $uniqueEntretien);
}

/**
 * @param $date
 * @return bool
 * @throws Exception
 */
function haveUserPermissionToUpdate($date)
{

    if (getUserRoleId() == 1) {

        $dateNow = new \DateTime();

        $dateMonthAgo = new DateTime();
        $dateMonthAgo->sub(new \DateInterval('P1M'));

        $processDate = new \DateTime($date);

        if ($dateNow->format('j') >= 10) {
            if ($processDate->format('n') < $dateNow->format('n')
                && $processDate->format('Y') < $dateNow->format('Y')) {
                return false;
            }
        } else {
            if ($processDate->format('n') < $dateMonthAgo->format('n')
                && $processDate->format('Y') <= $dateMonthAgo->format('Y')) {
                return false;
            }
        }
    }
    return true;
}

/**
 * @param $word
 * @return string
 */
function getFirstLetters($word)
{
    preg_match_all('/(?<=\s|^)[a-z]/i', $word, $matches);
    return implode('', $matches[0]);
}