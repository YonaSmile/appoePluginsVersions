<?php
define('ITEMGLUE_PATH', WEB_PLUGIN_PATH . 'itemGlue/');
define('ITEMGLUE_URL', WEB_PLUGIN_URL . 'itemGlue/');

const PLUGIN_TABLES = array(
    'appoe_plugin_itemGlue_articles',
    'appoe_plugin_itemGlue_articles_content',
    'appoe_plugin_itemGlue_articles_meta',
);

const ITEMGLUE_ARTICLES_STATUS = array(
    0 => 'Non accessible',
    1 => 'Accessible',
    2 => 'En vedette'
);