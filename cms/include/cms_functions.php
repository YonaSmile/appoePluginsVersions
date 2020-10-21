<?php

use App\Plugin\Cms\Cms;
use App\Plugin\Cms\CmsContent;
use App\Template;

/**
 * Load page as include
 *
 * @param string $slug
 * @param string $folder
 * @param string $lang
 * @return mixed|string
 */
function loadPage($slug = 'home', $folder = 'html', $lang = LANG)
{
    $Cms = new Cms();

    //Get Page parameters
    $existPage = $Cms->showBySlug($slug, $lang);

    //Check if Page exist and accessible
    if (!$existPage || $Cms->getStatut() != 1) {
        if (inc(WEB_PUBLIC_PATH . $folder . DIRECTORY_SEPARATOR . $slug . '.php')) {
            return 'La page ' . $slug . ' n\'existe pas.';
        }
        return '';
    }

    $CmsContent = new CmsContent($Cms->getId(), $lang);

    //Get page content in template
    $Template = new Template(WEB_PATH . $Cms->getFilename() . '.php', $CmsContent->getData(), true);
    return $Template->get();
}

/**
 * Load plugin page with "loadPage"
 *
 * @param string $slug
 * @param string $lang
 * @return mixed|string
 */
function getPluginPage($slug = 'home', $lang = LANG)
{
    return loadPage($slug, 'plugins', $lang);
}

/**
 * Load cms headers
 *
 * @param $idCms
 * @param $lang
 * @return mixed
 */
function getCmsHeaders($idCms, $lang)
{
    $CmsContent = new CmsContent($idCms, $lang, true);
    return $CmsContent->getData();
}