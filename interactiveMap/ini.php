<?php
define('INTERACTIVE_MAP_PATH', WEB_PLUGIN_PATH . 'interactiveMap/');
define('INTERACTIVE_MAP_URL', WEB_PLUGIN_URL . 'interactiveMap/');

const PLUGIN_TABLES = array(
    'appoe_plugin_interactiveMap',
);

const INTERACTIVE_MAP_STATUS = array(
    0 => 'Non accessible',
    1 => 'Accessible'
);

const INTERACTIVE_MAP_PINS = array(
    'red' => 'pin.png',
    'orange' => 'pin-orange.png',
    'yellow' => 'pin-yellow.png',
    'green' => 'pin-green.png',
    'blue' => 'pin-blue.png',
    'purple' => 'pin-purple.png',
    'white' => 'pin-white.png',
    'circle' => 'circle',
    'circular' => 'circular',
    'transparent' => 'transparent',
    'iconpin' => 'iconpin'
);