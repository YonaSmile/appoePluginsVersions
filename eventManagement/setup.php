<?php
require('main.php');

$Auteur = new App\Plugin\EventManagement\Auteur();
$Event = new App\Plugin\EventManagement\Event();
$EventDate = new App\Plugin\EventManagement\EventsDates();

//Creating tables
$pluginSetup = $Auteur->createTable();
echo $pluginSetup ? trans('Table') . ' Auteurs ' . trans('activé') . '.<br>' : $pluginSetup;

$pluginSetup = $Event->createTable();
echo $pluginSetup ? trans('Table') . ' Évènements ' . trans('activé') . '.<br>' : $pluginSetup;

$pluginSetup = $EventDate->createTable();
echo $pluginSetup ? trans('Table') . ' Dates-Évènements ' . trans('activé') . '.<br>' : $pluginSetup;

//Creating autorisations
$Menu = new App\Menu();

$auteur_id = 90;
$event_id = 110;

$data = array(
    1 => array(
        'id' => $auteur_id,
        'slug' => 'auteurs',
        'name' => 'Auteurs',
        'min_role_id' => 1,
        'statut' => 1,
        'parent_id' => 10,
        'pluginName' => 'eventManagement',
        'order_menu' => '90'
    ),
    2 => array(
        'id' => $auteur_id + 1,
        'slug' => 'allAuteurs',
        'name' => 'Tous les auteurs',
        'min_role_id' => 1,
        'statut' => 1,
        'parent_id' => $auteur_id,
        'pluginName' => 'eventManagement',
        'order_menu' => '91'
    ),
    3 => array(
        'id' => $auteur_id + 2,
        'slug' => 'addAuteur',
        'name' => 'Nouvel auteur',
        'min_role_id' => 4,
        'statut' => 1,
        'parent_id' => $auteur_id,
        'pluginName' => 'eventManagement',
        'order_menu' => '92'
    ),
    4 => array(
        'id' => $auteur_id + 3,
        'slug' => 'updateAuteur',
        'name' => 'Fiche d\'auteur',
        'min_role_id' => 4,
        'statut' => 0,
        'parent_id' => $auteur_id,
        'pluginName' => 'eventManagement',
        'order_menu' => '93'
    ),
    5 => array(
        'id' => $event_id,
        'slug' => 'events',
        'name' => 'Évènements',
        'min_role_id' => 1,
        'statut' => 1,
        'parent_id' => 10,
        'pluginName' => 'eventManagement',
        'order_menu' => '110'
    ),
    6 => array(
        'id' => $event_id + 1,
        'slug' => 'allEvents',
        'name' => 'Tous les Évènements',
        'min_role_id' => 1,
        'statut' => 1,
        'parent_id' => $event_id,
        'pluginName' => 'eventManagement',
        'order_menu' => '111'
    ),
    7 => array(
        'id' => $event_id + 2,
        'slug' => 'event',
        'name' => 'Évènement',
        'min_role_id' => 1,
        'statut' => 0,
        'parent_id' => $event_id,
        'pluginName' => 'eventManagement',
        'order_menu' => '112'
    ),
    8 => array(
        'id' => $event_id + 3,
        'slug' => 'addEvent',
        'name' => 'Nouvel évènement',
        'min_role_id' => 4,
        'statut' => 1,
        'parent_id' => $event_id,
        'pluginName' => 'eventManagement',
        'order_menu' => '113'
    ),
    9 => array(
        'id' => $event_id + 4,
        'slug' => 'updateEvent',
        'name' => 'Mise à jour de l\'évènement',
        'min_role_id' => 4,
        'statut' => 0,
        'parent_id' => $event_id,
        'pluginName' => 'eventManagement',
        'order_menu' => '114'
    )
);
$dataCount = count($data);
$trueCount = 0;
foreach ($data as $key => $menuData) {
    if ($Menu->insertMenu($menuData['id'], $menuData['slug'], $menuData['name'], $menuData['min_role_id'], $menuData['statut'], $menuData['parent_id'], $menuData['pluginName'], $menuData['order_menu'])) {
        $trueCount++;
    }
}
if (unlink(WEB_PLUGIN_PATH . 'eventManagement/setup.php')) {
    echo trans('Autorisations installés') . ' : ' . $trueCount . '/' . $dataCount . '.<br>';
}

