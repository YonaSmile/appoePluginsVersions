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
        echo getContainerErrorMsg('Cette page n\'existe pas');
        exit();
    }

    $CmsContent = new \App\Plugin\Cms\CmsContent($Cms->getId(), LANG);
    $allContentArr = $CmsContent->getData();

    $pageContent = showTemplateContent(TEMPLATES_PATH . $Cms->getSlug() . '.php', extractFromObjArr($allContentArr, 'metaKey'));

    return $pageContent;
}