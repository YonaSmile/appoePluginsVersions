<?php

use App\Plugin\Cms\Cms;
use App\Plugin\Cms\CmsContent;
use App\Plugin\Cms\CmsMenu;

if (checkPostAndTokenRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    $Response = new \App\Response();

    if (isset($_POST['ADDPAGE'])) {

        if (!empty($_POST['name'])
            && !empty($_POST['slug'])
            && !empty($_POST['description'])
            && !empty($_POST['menuName'])
            && !empty($_POST['filename'])
        ) {

            $Cms = new Cms();

            //Add Page
            $Cms->setFilename($_POST['filename']);

            if ($Cms->notExist()) {
                if ($Cms->save()) {

                    $CmsContent = new CmsContent();
                    $CmsContent->setIdCms($Cms->getId());
                    $CmsContent->saveHeaders($_POST);

                    //Delete post data
                    unset($_POST);

                    $Response->status = 'success';
                    $Response->error_code = 0;
                    $Response->error_msg = trans('La page a été enregistré') . ' <a href="' . getPluginUrl('cms/page/pageContent/', $Cms->getId()) . '">' . trans('Voir la page') . '</a>';

                } else {
                    $Response->status = 'danger';
                    $Response->error_code = 1;
                    $Response->error_msg = trans('Un problème est survenu lors de l\'enregistrement de la page');
                }
            } else {
                $Response->status = 'danger';
                $Response->error_code = 1;
                $Response->error_msg = trans('Le fichier est utilisé pour une autre page');
            }
        } else {
            $Response->status = 'danger';
            $Response->error_code = 1;
            $Response->error_msg = trans('Tous les champs sont obligatoires');
        }
    }


    if (isset($_POST['UPDATEPAGE'])) {

        if (!empty($_POST['id'])
            && !empty($_POST['name'])
            && !empty($_POST['description'])
            && !empty($_POST['menuName'])
            && !empty($_POST['slug'])
        ) {

            $cmsUpdate = true;
            if (!empty($_POST['filename'])) {

                $cmsUpdate = false;

                $Cms = new Cms($_POST['id']);
                if ($Cms->getFilename() != $_POST['filename']) {

                    $Cms->setFilename($_POST['filename']);

                    if ($Cms->notExist(true)) {
                        if (!$Cms->update()) {
                            $cmsUpdate = false;
                        } else {
                            $cmsUpdate = true;
                        }
                    }
                } else {
                    $cmsUpdate = true;
                }
            }

            if ($cmsUpdate) {
                $CmsContent = new CmsContent();
                $CmsContent->setIdCms($_POST['id']);
                $CmsContent->setType('HEADER');
                $CmsContent->setMetaKey('name');
                $CmsContent->setLang(APP_LANG);

                if ($CmsContent->notExist()) {

                    if ($CmsContent->saveHeaders($_POST)) {

                        //Delete post data
                        unset($_POST);

                        $Response->status = 'success';
                        $Response->error_code = 0;
                        $Response->error_msg = trans('Les en têtes ont étés enregistrés');

                    } else {

                        $Response->status = 'danger';
                        $Response->error_code = 1;
                        $Response->error_msg = trans('Un problème est survenu lors de la création des en têtes de la page');
                    }

                } else {
                    if ($CmsContent->updateHeaders($_POST)) {

                        //Delete post data
                        unset($_POST);

                        $Response->status = 'success';
                        $Response->error_code = 0;
                        $Response->error_msg = trans('Les en têtes ont étés mises à jour');

                    } else {

                        $Response->status = 'danger';
                        $Response->error_code = 1;
                        $Response->error_msg = trans('Un problème est survenu lors de la mise à jour des en têtes de la page');
                    }
                }
            } else {
                $Response->status = 'danger';
                $Response->error_code = 1;
                $Response->error_msg = trans('Le fichier est utilisé pour une autre page');
            }
        } else {

            $Response->status = 'danger';
            $Response->error_code = 1;
            $Response->error_msg = trans('Tous les champs sont obligatoires');
        }
    }


    if (isset($_POST['ADDMENUPAGE'])) {
        if (!empty($_POST['parentId']) && !empty($_POST['location'])) {

            if (!empty($_POST['idArticle']) && !empty($_POST['slugArticlePage'])) {
                $_POST['idCms'] = $_POST['slugArticlePage'] . DIRECTORY_SEPARATOR . $_POST['idArticle'];
            }

            if (!empty($_POST['idCms'])) {

                //Add Menu
                $CmsMenu = new CmsMenu();
                $CmsMenu->feed($_POST);

                if ($CmsMenu->existParent() || $CmsMenu->getParentId() == 10) {

                    if ($CmsMenu->save()) {

                        //Delete post data
                        unset($_POST);

                        $Response->status = 'success';
                        $Response->error_code = 0;
                        $Response->error_msg = trans('La menu a été enregistré');

                    } else {
                        $Response->status = 'danger';
                        $Response->error_code = 1;
                        $Response->error_msg = trans('Un problème est survenu lors de l\'enregistrement du menu');
                    }
                } else {
                    $Response->status = 'danger';
                    $Response->error_code = 1;
                    $Response->error_msg = trans('Ce menu n\'existe pas');
                }
            } else {
                $Response->status = 'danger';
                $Response->error_code = 1;
                $Response->error_msg = trans('Aucune page n\'a été choisi');
            }
        } else {
            $Response->status = 'danger';
            $Response->error_code = 1;
            $Response->error_msg = trans('Tous les champs sont obligatoires');
        }
    }

    if (isset($_POST['UPDATEMENUPAGE'])) {
        if (!empty($_POST['id']) && !empty($_POST['parentId']) && !empty($_POST['location'])) {

            $CmsMenu = new CmsMenu($_POST['id']);

            if ($CmsMenu->existParent() || $CmsMenu->getParentId() == 10) {

                if ($CmsMenu->update()) {

                    //Delete post data
                    unset($_POST);

                    $Response->status = 'success';
                    $Response->error_code = 0;
                    $Response->error_msg = trans('La menu a été mise à jour');

                } else {
                    $Response->status = 'danger';
                    $Response->error_code = 1;
                    $Response->error_msg = trans('Un problème est survenu lors de la mise à jour du menu');
                }
            } else {
                $Response->status = 'danger';
                $Response->error_code = 1;
                $Response->error_msg = trans('Ce menu n\'existe pas');
            }
        } else {
            $Response->status = 'danger';
            $Response->error_code = 1;
            $Response->error_msg = trans('Tous les champs sont obligatoires');
        }
    }
}