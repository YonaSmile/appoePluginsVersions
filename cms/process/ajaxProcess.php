<?php
require_once('../main.php');
if (checkAjaxRequest()) {

    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        if (!empty($_POST['idCmsArchive'])) {
            $Cms = new App\Plugin\Cms\Cms($_POST['idCmsArchive']);
            $Cms->setStatut(0);
            if ($Cms->update()) {
                echo 'true';
            }
        }

        if (!empty($_POST['idCmsDelete'])) {
            $Cms = new App\Plugin\Cms\Cms($_POST['idCmsDelete']);
            if ($Cms->delete()) {
                echo 'true';
            }
        }

        if (isset($_POST['id'])
            && !empty($_POST['idCms'])
            && !empty($_POST['metaKey'])
            && isset($_POST['metaValue'])) {

            $CmsContent = new App\Plugin\Cms\CmsContent();
            $CmsContent->feed($_POST);
            $CmsContent->setLang(LANG);

            if ($CmsContent->notExist()) {
                if ($CmsContent->save()) {
                    echo $CmsContent->getId();
                }
            } elseif ($CmsContent->notExist(true)) {

                if (!empty($CmsContent->getId()) && $CmsContent->update()) {
                    echo 'true';
                }
            }
        }

        if (isset($_POST['idCmsMenuDelete']) && !empty($_POST['idCmsMenuDelete'])) {

            $CmsMenu = new App\Plugin\Cms\CmsMenu($_POST['idCmsMenuDelete']);
            if ($CmsMenu->delete()) {
                echo 'true';
            }

        }

        if (isset($_POST['updateMenu'])
            && !empty($_POST['column'])
            && !empty($_POST['idMenu'])
            && isset($_POST['value'])) {

            $CmsMenu = new App\Plugin\Cms\CmsMenu($_POST['idMenu']);
            $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $_POST['column'])));
            if (is_callable(array($CmsMenu, $method))) {
                $CmsMenu->$method($_POST['value']);

                if ($CmsMenu->update()) {
                    echo 'true';
                }
            }
        }

        if (isset($_POST['getParentPageByLocation'])) {

            $CmsMenu = new App\Plugin\Cms\CmsMenu();

            $allMenu = extractFromObjToArrForList($CmsMenu->showAll($_POST['getParentPageByLocation']), 'id', 'name');
            $allMenu[10] = trans('Aucun parent');

            if ($allMenu) {
                echo App\Form::select(trans('Page Parente'), 'parentId', $allMenu, '', true);
            }
        }

    }
}