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

    return $allPti;
}

function reelPtiDatesSort($a, $b)
{
    return strtotime($b) - strtotime($a);
}