<?php
require('main.php');
$People = new App\Plugin\People\People();

//Creating table
$pluginSetup = $People->createTable();
echo $pluginSetup ? trans('Table') . ' PEOPLE ' . trans('activé') . '.<br>' : $pluginSetup;

//Creating autorisations
$Menu = new App\Menu();
$data = array(
    1 => array(
        'id' => 70,
        'slug' => 'people',
        'name' => 'Personnes',
        'min_role_id' => 1,
        'statut' => 1,
        'parent_id' => 10,
        'pluginName' => 'people',
        'order_menu' => '70'
    ),
    2 => array(
        'id' => 71,
        'slug' => 'allPeople',
        'name' => 'Toutes les personnes',
        'min_role_id' => 1,
        'statut' => 1,
        'parent_id' => 70,
        'pluginName' => 'people',
        'order_menu' => '71'
    ),
    3 => array(
        'id' => 72,
        'slug' => 'addPerson',
        'name' => 'Nouvelle personne',
        'min_role_id' => 1,
        'statut' => 1,
        'parent_id' => 70,
        'pluginName' => 'people',
        'order_menu' => '72'
    ),
    4 => array(
        'id' => 73,
        'slug' => 'updatePerson',
        'name' => 'Fiche de la personne',
        'min_role_id' => 1,
        'statut' => 0,
        'parent_id' => 70,
        'pluginName' => 'people',
        'order_menu' => '73'
    )
);

$dataCount = count($data);
$trueCount = 0;
foreach ($data as $key => $menuData) {
    if ($Menu->insertMenu($menuData['id'], $menuData['slug'], $menuData['name'], $menuData['min_role_id'], $menuData['statut'], $menuData['parent_id'], $menuData['pluginName'], $menuData['order_menu'])) {
        $trueCount++;
    }
}
if (unlink(WEB_PLUGIN_PATH . 'people/setup.php')) {
    echo trans('Autorisations installés') . ' : ' . $trueCount . '/' . $dataCount . '.<br>';
}
