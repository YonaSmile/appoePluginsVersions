<?php
define('CMS_PATH', WEB_PLUGIN_PATH . 'cms/');
define('CMS_URL', WEB_PLUGIN_URL . 'cms/');

const PLUGIN_TABLES = array(
    'appoe_plugin_cms',
    'appoe_plugin_cms_menu',
    'appoe_plugin_cms_content',
);

const CMS_PAGE_STATUS = array(
    1 => 'Accessible',
    2 => 'Non accessible'
);

const CMS_LOCATIONS = array(
    1 => 'Menu Principal',
    2 => 'Menu Secondaire',
    3 => 'Menu En tête 1',
    4 => 'Menu En tête 2',
    5 => 'Menu En tête 3',
    6 => 'Menu En tête 4',
    7 => 'Menu Pied de page 1',
    8 => 'Menu Pied de page 2',
    9 => 'Menu Pied de page 3',
    10 => 'Menu Pied de page 4',
    11 => 'Menu Latéral 1',
    12 => 'Menu Latéral 2',
    13 => 'Menu Latéral 3',
    14 => 'Menu Latéral 4',
    15 => 'Menu Special',
    16 => 'Menu Autre'
);