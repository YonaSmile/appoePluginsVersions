<?php

/**
 * write on Map json file
 *
 * @param $data
 * @param $title
 * @return string
 */
function interMap_writeMapFile($data, $title)
{
    $json_file = fopen(WEB_PLUGIN_PATH . 'interactiveMap/' . slugify($title) . '.json', 'w');
    fwrite($json_file, $data);
    fclose($json_file);

    return true;
}

/**
 * read the Map json file
 *
 * @param $title
 * @return array
 */
function interMap_readMapFile($title)
{
    $json = file_get_contents(WEB_PLUGIN_PATH . 'interactiveMap/' . slugify($title) . '.json');
    $parsed_json = json_decode($json, true);

    return $parsed_json;
}