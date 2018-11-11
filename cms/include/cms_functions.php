<?php
/**
 * Load page as include
 *
 * @param string $slug
 * @return mixed|string
 */
function loadPage($slug = 'home')
{
    $pageContent = getContainerErrorMsg('Cette page n\'existe pas');
    $Cms = new \App\Plugin\Cms\Cms();

    //Get Page parameters
    $Cms->setSlug($slug);
    $existPage = $Cms->showBySlug();

    //Check if Page exist and accessible
    if ((!$existPage && pageName() == 'Non définie') || $Cms->getStatut() != 1) {
        echo $pageContent;
        exit();
    }

    $CmsContent = new \App\Plugin\Cms\CmsContent($Cms->getId(), LANG);

    //Get page content in template
    \App\Template::setSecondary(TEMPLATES_PATH . $Cms->getSlug() . '.php', $CmsContent->getData());
    return \App\Template::getSecondary();
}

/**
 * @param bool $otherParentSlug
 * @return array
 */
function breadcrumb($otherParentSlug = false)
{
    $Cms = new \App\Plugin\Cms\Cms();

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