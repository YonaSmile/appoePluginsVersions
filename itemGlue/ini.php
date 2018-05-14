<?php
define('ITEMGLUE_PATH', WEB_PLUGIN_PATH . 'itemGlue/');
define('ITEMGLUE_URL', WEB_PLUGIN_URL . 'itemGlue/');

const PLUGIN_TABLES = array(
    'appoe_plugin_itemGlue_articles',
    'appoe_plugin_itemGlue_articles_content',
    'appoe_plugin_itemGlue_articles_meta',
);

const ITEMGLUE_ARTICLES_STATUS = array(
    2 => 'En vedette',
    1 => 'Accessible',
    0 => 'Non accessible'
);