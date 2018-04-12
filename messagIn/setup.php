<?php
require('main.php');
$MessagIn = new App\Plugin\MessageIn\MessagIn();

//Creating table
$pluginSetup = $MessagIn->createTable();
echo $pluginSetup ? trans('Table') . ' Messag\'In ' . trans('activé') . '.<br>' : $pluginSetup;

//Creating autorisations
$Menu = new App\Menu();
$data = array(
    1 => array(
        'id' => 500,
        'slug' => 'messages',
        'name' => 'Messag\'In',
        'min_role_id' => 1,
        'statut' => 1,
        'parent_id' => 10,
        'pluginName' => 'messagIn',
        'order_menu' => '500'
    ),
    2 => array(
        'id' => 501,
        'slug' => 'allMessages',
        'name' => 'Tous les messages',
        'min_role_id' => 1,
        'statut' => 1,
        'parent_id' => 500,
        'pluginName' => 'messagIn',
        'order_menu' => '501'
    ),
    3 => array(
        'id' => 502,
        'slug' => 'addMessage',
        'name' => 'Nouveau message',
        'min_role_id' => 1,
        'statut' => 1,
        'parent_id' => 500,
        'pluginName' => 'messagIn',
        'order_menu' => '502'
    )
);

$dataCount = count($data);
$trueCount = 0;
foreach ($data as $key => $menuData) {
    if ($Menu->insertMenu($menuData['id'], $menuData['slug'], $menuData['name'], $menuData['min_role_id'], $menuData['statut'], $menuData['parent_id'], $menuData['pluginName'], $menuData['order_menu'])) {
        $trueCount++;
    }
}
if (unlink(WEB_PLUGIN_PATH . 'messageIn/setup.php')) {
    echo trans('Autorisations installés') . ' : ' . $trueCount . '/' . $dataCount . '.<br>';
}
