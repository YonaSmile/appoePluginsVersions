<?php
require('main.php');
$Rating = new App\Plugin\Rating\Rating();

//Creating table
$pluginSetup = $Rating->createTable();
if (unlink(WEB_PLUGIN_PATH . 'rating/setup.php')) {
    echo $pluginSetup ? trans('Table') . ' RATING ' . trans('activé') . '.<br>' : $pluginSetup;
}