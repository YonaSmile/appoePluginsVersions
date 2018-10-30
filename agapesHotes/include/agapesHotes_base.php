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
 * @param $idEmploye
 * @return bool|array
 */
function getEmployeContrats($idEmploye)
{

    $allReelContrats = array();

    //Get Sites
    $Site = new \App\Plugin\AgapesHotes\Site();
    $allSites = $Site->showAll();

    //Get Employe Contrat
    $EmployeContrat = new \App\Plugin\AgapesHotes\EmployeContrat();
    $EmployeContrat->setEmployeId($idEmploye);

    if ($allSites) {
        foreach ($allSites as $site) {
            $EmployeContrat->setDateDebut(date('Y-m-d'));
            $EmployeContrat->setSiteId($site->id);
            if ($EmployeContrat->showReelContrat()) {
                $allReelContrats[$EmployeContrat->getId()] = array(
                    'id' => $EmployeContrat->getId(),
                    'siteId' => $EmployeContrat->getSiteId(),
                    'employeId' => $idEmploye,
                    'dateDebut' => $EmployeContrat->getDateDebut()
                );
            }
        }
    }
    return $allReelContrats;
}