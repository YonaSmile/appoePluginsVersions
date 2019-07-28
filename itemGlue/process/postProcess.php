<?php

use App\CategoryRelations;
use App\Plugin\ItemGlue\Article;
use App\Plugin\ItemGlue\ArticleContent;
use App\Plugin\ItemGlue\ArticleMedia;
use App\Plugin\ItemGlue\ArticleMeta;

if (checkPostAndTokenRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    $Response = new \App\Response();
    $Response->MediaTabactive = false;

    if (isset($_POST['ADDARTICLE'])) {

        if (!empty($_POST['name'])
            && !empty($_POST['description'])
            && !empty($_POST['slug'])
            && !empty($_POST['statut'])
        ) {

            $Article = new Article();

            $lastCharSlug = substr($_POST['slug'], -1);
            if ($lastCharSlug == '-') {
                $_POST['slug'] = substr($_POST['slug'], 0, -1);
            }

            //Add Article
            $Article->setStatut($_POST['statut']);
            if ($Article->save()) {

                $ArticleContent = new ArticleContent();
                $ArticleContent->setIdArticle($Article->getId());

                $headers = array(
                    'NAME' => $_POST['name'],
                    'DESCRIPTION' => $_POST['description'],
                    'SLUG' => $_POST['slug']
                );

                $ArticleContent->saveHeaders($headers);

                //Add Meta
                if (defined('ARTICLE_META') && is_array(ARTICLE_META)) {

                    $ArticleMeta = new ArticleMeta();

                    foreach (getLangs() as $minLang => $largeLang) {
                        foreach (ARTICLE_META as $metaKey => $metaValue) {
                            $ArticleMeta->setIdArticle($Article->getId());
                            $ArticleMeta->setMetaKey($metaKey);
                            $ArticleMeta->setMetaValue($metaValue);
                            $ArticleMeta->setLang($minLang);
                            $ArticleMeta->save();
                        }
                    }

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
            $Response->error_msg = trans('Tous les champs sont obligatoires');
        }
    }


    if (isset($_POST['UPDATEARTICLEHEADERS'])) {

        if (!empty($_POST['id'])
            && !empty($_POST['name'])
            && !empty($_POST['description'])
            && !empty($_POST['slug'])
            && !empty($_POST['createdAt'])
            && !empty($_POST['statut'])
        ) {

            $Article = new Article();
            $Article->setId($_POST['id']);

            if ($Article->show()) {
                $Article->setStatut($_POST['statut']);
                $Article->setCreatedAt($_POST['createdAt']);

                if ($Article->update()) {

                    $ArticleContent = new ArticleContent();
                    $ArticleContent->setIdArticle($Article->getId());

                    $lastCharSlug = substr($_POST['slug'], -1);
                    if ($lastCharSlug == '-') {
                        $_POST['slug'] = substr($_POST['slug'], 0, -1);
                    }

                    $headers = array(
                        'NAME' => $_POST['name'],
                        'DESCRIPTION' => $_POST['description'],
                        'SLUG' => $_POST['slug']
                    );

                    //Update Headers
                    if ($ArticleContent->updateHeaders($headers)) {

                        //Delete post data
                        unset($_POST);

                        $Response->status = 'success';
                        $Response->error_code = 0;
                        $Response->error_msg = trans('Les en têtes de l\'article ont étés mises à jour');

                    } else {

                        $Response->status = 'danger';
                        $Response->error_code = 1;
                        $Response->error_msg = trans('Un problème est survenu lors de la mise à jour des en têtes de l\'article');
                    }
                } else {

                    $Response->status = 'danger';
                    $Response->error_code = 1;
                    $Response->error_msg = trans('Un problème est survenu lors de la mise à jour du statut de l\'article');
                }
            } else {

                $Response->status = 'danger';
                $Response->error_code = 1;
                $Response->error_msg = trans('Cet article n\'existe pas');
            }
        } else {

            $Response->status = 'danger';
            $Response->error_code = 1;
            $Response->error_msg = trans('Tous les champs sont obligatoires');
        }
    }

    if (isset($_POST['SAVEARTICLECONTENT'])) {

        if (!empty($_POST['articleContent']) && !empty($_POST['articleId'])) {

            $ArticleContent = new ArticleContent($_POST['articleId'], 'BODY', APP_LANG);
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

            $CategoryRelation = new CategoryRelations('ITEMGLUE', $_POST['articleId']);
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

    if (isset($_POST['ADDIMAGESTOARTICLE']) && !empty($_POST['articleId'])) {

        $html = '';
        $selectedFilesCount = 0;

        $ArticleMedia = new ArticleMedia($_POST['articleId']);
        $ArticleMedia->setUserId(getUserIdSession());

        //Get uploaded files
        if (!empty($_FILES)) {
            $ArticleMedia->setUploadFiles($_FILES['inputFile']);
            $files = $ArticleMedia->upload();
            $html .= trans('Fichiers importés') . ' : <strong>' . $files['countUpload'] . '</strong>. ' . (!empty($files['errors']) ? '<br><span class="text-danger">' . $files['errors'] . '</span>' : '');
        }

        //Get selected files
        if (!empty($_POST['textareaSelectedFile'])) {

            $selectedFiles = $_POST['textareaSelectedFile'];

            if (strpos($selectedFiles, '|||')) {
                $files = explode('|||', $selectedFiles);
            } else {
                $files = array($selectedFiles);
            }

            foreach ($files as $key => $file) {
                $ArticleMedia->setName($file);
                if ($ArticleMedia->save()) $selectedFilesCount++;
            }

            $html .= trans('Fichiers sélectionnés enregistrés') . ' <strong>' . $selectedFilesCount . '</strong>.';
        }

        $Response->status = 'info';
        $Response->error_code = 1;
        $Response->error_msg = $html;
        $Response->MediaTabactive = true;
    }
}