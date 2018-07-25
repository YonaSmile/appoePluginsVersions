<?php
if (checkPostAndTokenRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    $Response = new App\Response();

    if (isset($_POST['ADDARTICLE'])) {

        if (!empty($_POST['name']) && !empty($_POST['slug'])) {

            $Article = new App\Plugin\ItemGlue\Article();

            //Add Article
            $Article->feed($_POST);
            $Article->setUserId(getUserIdSession());

            if ($Article->notExist()) {
                if ($Article->save()) {

                    //Add Translation
                    $Traduction = new App\Plugin\Traduction\Traduction();
                    $Traduction->setLang(LANG);
                    $Traduction->setMetaKey($Article->getName());
                    $Traduction->setMetaValue($Article->getName());
                    if($Traduction->save()){
                        $Traduction->setMetaKey(slugify($Article->getSlug()));
                        $Traduction->setMetaValue(slugify($Article->getSlug()));
                        $Traduction->save();
                    }

                    //Delete post data
                    unset($_POST);

                    $Response->status = 'success';
                    $Response->error_code = 0;
                    $Response->error_msg = trans('L\'article a été enregistré') . ' <a href="' . getPluginUrl('itemGlue/page/articleContent/', $Article->getId()) . '">' . trans('Voir l\'article') . '</a>';

                } else {
                    $Response->status = 'danger';
                    $Response->error_code = 1;
                    $Response->error_msg = trans('Un problème est survenu lors de l\'enregistrement de l\'article');
                }
            } else {
                $Response->status = 'danger';
                $Response->error_code = 1;
                $Response->error_msg = trans('Le nom ou le slug de l\'article existe déjà');
            }
        } else {
            $Response->status = 'danger';
            $Response->error_code = 1;
            $Response->error_msg = trans('Tous les champs sont obligatoires');
        }
    }


    if (isset($_POST['UPDATEARTICLE'])) {

        if (!empty($_POST['id'])
            && !empty($_POST['name'])
            && !empty($_POST['slug'])
        ) {

            $Article = new App\Plugin\ItemGlue\Article($_POST['id']);

            //Update Article
            $Article->feed($_POST);
            $Article->setUserId(getUserIdSession());
            if ($Article->notExist(true)) {
                if ($Article->update()) {

                    //Delete post data
                    unset($_POST);

                    $Response->status = 'success';
                    $Response->error_code = 0;
                    $Response->error_msg = trans('L\'article a été mise à jour');

                } else {

                    $Response->status = 'danger';
                    $Response->error_code = 1;
                    $Response->error_msg = trans('Un problème est survenu lors de la mise à jour de l\'article');
                }
            } else {
                $Response->status = 'danger';
                $Response->error_code = 1;
                $Response->error_msg = trans('Le nom ou le slug de l\'article existe déjà');
            }
        } else {

            $Response->status = 'danger';
            $Response->error_code = 1;
            $Response->error_msg = trans('Tous les champs sont obligatoires');
        }
    }

    if (isset($_POST['SAVEARTICLECONTENT'])) {

        if (!empty($_POST['articleContent']) && !empty($_POST['articleId'])) {

            $ArticleContent = new App\Plugin\ItemGlue\ArticleContent($_POST['articleId'], LANG);
            $ArticleContent->setContent($_POST['articleContent']);
            if (!empty($ArticleContent->getId())) {
                if ($ArticleContent->update()) {

                    $Response->status = 'success';
                    $Response->error_code = 0;
                    $Response->error_msg = trans('Le contenu de l\'article a été mise à jour');
                }
            } else {
                if ($ArticleContent->save()) {

                    $Response->status = 'success';
                    $Response->error_code = 0;
                    $Response->error_msg = trans('Le contenu de l\'article a été enregistré');
                }
            }

            $CategoryRelation = new App\CategoryRelations('ITEMGLUE', $_POST['articleId']);
            $allCategories = $CategoryRelation->getData();
            $allSimpleCategories = extractFromObjToSimpleArr($allCategories, 'id', 'categoryId');

            if (!empty($_POST['categories'])) {

                if (!is_null($allCategories)) {
                    foreach ($allCategories as $category) {
                        if (!in_array($category->categoryId, $_POST['categories'])) {
                            $CategoryRelation->setId($category->id);
                            $CategoryRelation->delete();
                        }
                    }
                }

                foreach ($_POST['categories'] as $chosenCategory) {
                    if (!in_array($chosenCategory, $allSimpleCategories)) {
                        $CategoryRelation->setCategoryId($chosenCategory);
                        $CategoryRelation->save();
                    }
                }

            } else {

                if (!is_null($allCategories)) {
                    foreach ($allCategories as $category) {
                        $CategoryRelation->setId($category->id);
                        $CategoryRelation->delete();
                    }
                }
            }

            //Delete post data
            unset($_POST);

        } else {

            $Response->status = 'danger';
            $Response->error_code = 1;
            $Response->error_msg = trans('Le contenu de l\'article est obligatoire');
        }
    }

    if (isset($_POST['ADDIMAGESTOARTICLE'])) {
        if (!empty($_POST['articleId']) && !empty($_FILES)) {

            $ArticleMedia = new App\Plugin\ItemGlue\ArticleMedia($_POST['articleId']);
            $ArticleMedia->setUploadFiles($_FILES['inputFile']);
            $ArticleMedia->setUserId(getUserIdSession());

            //Delete post data
            unset($_POST);

            $files = $ArticleMedia->upload();

            $Response->status = 'success';
            $Response->error_code = 0;
            $Response->error_msg = trans('Images téléchargées') . ' : ' . $files['countUpload'];
        }
    }
}