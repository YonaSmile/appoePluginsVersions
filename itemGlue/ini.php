<?php
define('ITEMGLUE_PATH', WEB_PLUGIN_PATH . 'itemGlue/');
define('ITEMGLUE_URL', WEB_PLUGIN_URL . 'itemGlue/');

const PLUGIN_TABLES = array(
	TABLEPREFIX.'appoe_plugin_itemGlue_articles',
	TABLEPREFIX.'appoe_plugin_itemGlue_articles_content',
	TABLEPREFIX.'appoe_plugin_itemGlue_articles_meta',
	TABLEPREFIX.'appoe_plugin_itemGlue_articles_relations'
);

const ITEMGLUE_STATUS = array(
    0 => 'Archivé',
    1 => 'Brouillon',
    2 => 'Publié',
    3 => 'En vedette'
);