<?php
require('main.php');
$Traduction = new App\Plugin\Traduction\Traduction();

//Creating table
$pluginSetup = $Traduction->createTable();
echo $pluginSetup ? trans('Table') . ' TRADUCTION ' . trans('activé') . '.<br>' : $pluginSetup;

//Creating autorisations
$Menu = new App\Menu();
$data = array(
    1 => array(
        'id' => 530,
        'slug' => 'updateTraduction',
        'name' => 'Traduction',
        'min_role_id' => 1,
        'statut' => 1,
        'parent_id' => 10,
        'pluginName' => 'traduction',
        'order_menu' => '530'
    )
);

$dataCount = count($data);
$trueCount = 0;
foreach ($data as $key => $menuData) {
    if ($Menu->insertMenu($menuData['id'], $menuData['slug'], $menuData['name'], $menuData['min_role_id'], $menuData['statut'], $menuData['parent_id'], $menuData['pluginName'], $menuData['order_menu'])) {
        $trueCount++;
    }
}
if (unlink(WEB_PLUGIN_PATH . 'traduction/setup.php')) {
    echo trans('Autorisations installés') . ' : ' . $trueCount . '/' . $dataCount . '.<br>';
}
