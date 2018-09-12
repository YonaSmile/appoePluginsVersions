<?php
if (checkPostAndTokenRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    $Response = new \App\Response();

    if (isset($_POST['ADDPAGE'])) {

        if (!empty($_POST['name'])
            && !empty($_POST['slug'])
        ) {

            $Cms = new \App\Plugin\Cms\Cms();

            //Add Page
            $Cms->feed($_POST);
            $Cms->setType('PAGE');
            if ($Cms->notExist()) {
                if ($Cms->save()) {

                    //Add Translation
                    $Traduction = new \App\Plugin\Traduction\Traduction();
                    $Traduction->setLang(LANG);
                    $Traduction->setMetaKey($Cms->getName());
                    $Traduction->setMetaValue($Cms->getName());
                    if($Traduction->save()){
                        $Traduction->setMetaKey(slugify($Cms->getSlug()));
                        $Traduction->setMetaValue(slugify($Cms->getSlug()));
                        $Traduction->save();
                    }

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
                $Response->error_msg = trans('Le nom ou le slug existe déjà');
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
            && !empty($_POST['slug'])
        ) {

            $Cms = new \App\Plugin\Cms\Cms($_POST['id']);

            //Update Page
            $Cms->feed($_POST);
            $Cms->setType('PAGE');
            if ($Cms->notExist(true)) {
                if ($Cms->update()) {

                    //Delete post data
                    unset($_POST);

                    $Response->status = 'success';
                    $Response->error_code = 0;
                    $Response->error_msg = trans('La page a été mise à jour');

                } else {

                    $Response->status = 'danger';
                    $Response->error_code = 1;
                    $Response->error_msg = trans('Un problème est survenu lors de la mise à jour de la page');
                }
            } else {
                $Response->status = 'danger';
                $Response->error_code = 1;
                $Response->error_msg = trans('Le nom ou le slug existe déjà');
            }
        } else {

            $Response->status = 'danger';
            $Response->error_code = 1;
            $Response->error_msg = trans('Tous les champs sont obligatoires');
        }
    }

    if (isset($_POST['ADDMENUPAGE'])) {
        if (!empty($_POST['idCms'])
            && !empty($_POST['name'])
            && !empty($_POST['parentId'])
            && !empty($_POST['location'])
        ) {
            //Add Menu
            $CmsMenu = new \App\Plugin\Cms\CmsMenu();
            $CmsMenu->feed($_POST);

            if($CmsMenu->existParent() || $CmsMenu->getParentId() == 10) {

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
            $Response->error_msg = trans('Tous les champs sont obligatoires');
        }
    }
}