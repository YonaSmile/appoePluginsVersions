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
 * @return array
 */
function getAllEmployeHasContratInSite($idSite)
{

    //Get Employe Contrat
    $EmployeContrat = new \App\Plugin\AgapesHotes\EmployeContrat();
    $EmployeContrat->setDateDebut(date('Y-M-d'));
    $EmployeContrat->setSiteId($idSite);
    $allContratsDates = $EmployeContrat->showAllReelDateEmployesContrats();

    return extractFromObjArr($allContratsDates, 'employe_id');
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

function getAllPrestationsPriceByEtablissement($etablissementId)
{

    $PrestationPrix = new \App\Plugin\AgapesHotes\PrixPrestation();
    $PrestationPrix->setEtablissementId($etablissementId);
    $PrestationPrix->setDateDebut(date('Y-m-01'));
    $allPrestationsPrice = groupMultipleKeysObjectsArray($PrestationPrix->showAll(), 'prestation_id');

    if ($allPrestationsPrice) {
        foreach ($allPrestationsPrice as $prestationId => $allPrices) {
            $allPrestationsPrice[$prestationId] = extractFromObjArr($allPrices, 'dateDebut');
        }
    }

    return $allPrestationsPrice;
}

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

function reelDatesSortDESC($a, $b)
{
    return strtotime($b) - strtotime($a);
}

function getCommandesServentest($allCommandes)
{

    $commandServentest = array(
        'denree' => array('total' => 0),
        'nonAlimentaire' => array('total' => 0)
    );

    if (!isArrayEmpty($allCommandes)) {
        foreach ($allCommandes as $key => $allDenree) {
            if (trim($allDenree['fournisseur']) != 'C2M') {
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

function getSiteMeta($siteId, $month, $year)
{

    $siteMeta = array(
        'participationTournante' => 0,
        'fraisDePersonnels' => 0
    );

    $SiteMeta = new \App\Plugin\AgapesHotes\SiteMeta();
    $SiteMeta->setSiteId($siteId);
    $SiteMeta->setMonth($month);
    $SiteMeta->setYear($year);

    $allSiteMetas = extractFromObjArr($SiteMeta->showBySite(), 'dataName');

    if (!isArrayEmpty($allSiteMetas)) {
        $siteMeta['participationTournante'] = $allSiteMetas['Participation tournant']->data;
        $siteMeta['fraisDePersonnels'] = $allSiteMetas['Frais de personnels']->data;
    }

    return $siteMeta;

}

function getNoteDeFrais($siteId, $month, $year)
{
    $noteDeFrais = array(
        'denree' => 0,
        'nonAlimentaire' => 0
    );

    $View = new \App\Plugin\AgapesHotes\View();

    $View->setViewName('totalNoteDeFraisDenree');
    $View->setDataColumns(array('site_id', 'mois', 'annee'));
    $View->setDataValues(array($siteId, $month, $year));
    $View->prepareSql();
    $totalDenrees = $View->get();
    $noteDeFrais['denree'] = $totalDenrees ? $totalDenrees->totalHT : 0;

    $View->setViewName('totalNoteDeFraisNonAlimentaire');
    $View->prepareSql();
    $totalNonAlimentaire = $View->get();
    $noteDeFrais['nonAlimentaire'] = $totalNonAlimentaire ? $totalNonAlimentaire->totalHT : 0;

    return $noteDeFrais;
}

function getFacturation($siteId, $month, $year)
{

    $View = new \App\Plugin\AgapesHotes\View();

    $View->setViewName('totalFacturation');
    $View->setDataColumns(array('site_id', 'mois', 'annee'));
    $View->setDataValues(array($siteId, $month, $year));
    $View->prepareSql();
    $totalFacturation = $View->get();

    return $totalFacturation ? $totalFacturation->totalHT : 0;
}

function getBudget($siteId, $month, $year)
{

    $Budget = new \App\Plugin\AgapesHotes\Budget();

    $Budget->setSiteId($siteId);
    $Budget->setYear($year);
    $Budget->setMonth($month);
    $Budget->showBySite();

    return $Budget ? $Budget : 0;
}