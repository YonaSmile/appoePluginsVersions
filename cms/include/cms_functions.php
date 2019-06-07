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
    $Cms->setSlug($slug);
    $existPage = $Cms->showBySlug();

    //Check if Page exist and accessible
    if (!$existPage || $Cms->getStatut() != 1) {
        if (false === @include_once(WEB_PUBLIC_PATH . $folder . DIRECTORY_SEPARATOR . $slug . '.php')) {
            return 'Cette page n\'existe pas.';
        }
        return '';
    }

    $CmsContent = new CmsContent($Cms->getId(), $lang);

    //Get page content in template
    $Template = new Template(WEB_PATH . $Cms->getSlug() . '.php', $CmsContent->getData(), true);
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
 * @param bool $otherParentSlug
 * @return array
 */
function breadcrumb($otherParentSlug = false)
{
    $Cms = new Cms();

    $currentUrl = $_SERVER['REQUEST_URI'];
    $currentUrlSlug = basename($currentUrl);
    $currentName = '';
    $currentSlug = '';

    $parentUrlSlug = basename(dirname($currentUrl));
    $parentName = '';
    $parentSlug = '';

    //Get Home Data
    $Cms->setSlug('home');
    $Cms->showBySlug();
    $homeName = $Cms->getName();
    $homeSlug = webUrl('home/');

    //Get Parent Data
    if (!empty($parentUrlSlug) && $parentUrlSlug != '/' && $parentUrlSlug != '.') {

        if (false !== $otherParentSlug) {
            $Cms->setSlug($otherParentSlug);
        } else {
            $Cms->setSlug($parentUrlSlug);
        }

        $Cms->showBySlug();

        $parentName = $Cms->getName();
        $parentSlug = webUrl($Cms->getSlug() . '/');
    }

    //Get Current Data
    $Cms->setSlug($currentUrlSlug);

    if ($Cms->showBySlug()) {
        $currentName = $Cms->getName();
        $currentSlug = webUrl($Cms->getSlug() . '/');
    }

    //Response Data
    $breadcrumb = array(
        'homeUrl' => $homeSlug,
        'homeSlug' => $homeName,
        'parentUrl' => $parentSlug,
        'parentSlug' => $parentName,
        'currentUrl' => $currentSlug,
        'currentSlug' => $currentName
    );

    return $breadcrumb;

}