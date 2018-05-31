<?php
require('main.php');
$Rating = new App\Plugin\Rating\Rating();

//Creating table
$pluginSetup = $Rating->createTable();
echo $pluginSetup ? trans('Table') . ' RATING ' . trans('activé') . '.<br>' : $pluginSetup;

//Creating autorisations
$Menu = new App\Menu();
$data = array(
    1 => array(
        'id' => 100,
        'slug' => 'allRating',
        'name' => 'Évaluations',
        'min_role_id' => 1,
        'statut' => 1,
        'parent_id' => 10,
        'pluginName' => 'rating',
        'order_menu' => '100'
    )
);

$dataCount = count($data);
$trueCount = 0;
foreach ($data as $key => $menuData) {
    if ($Menu->insertMenu($menuData['id'], $menuData['slug'], $menuData['name'], $menuData['min_role_id'], $menuData['statut'], $menuData['parent_id'], $menuData['pluginName'], $menuData['order_menu'])) {
        $trueCount++;
    }
}
if (unlink(WEB_PLUGIN_PATH . 'rating/setup.php')) {
    echo trans('Autorisations installés') . ' : ' . $trueCount . '/' . $dataCount . '.<br>';
}
