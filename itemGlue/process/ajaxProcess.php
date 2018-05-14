<?php
require_once('../main.php');
if (checkAjaxRequest()) {

    if (getUserIdSession()) {

        includePluginsFiles();

        $_POST = cleanRequest($_POST);

        if (isset($_POST['archiveArticle']) && !empty($_POST['idArticleArchive'])) {
            $Article = new App\Plugin\ItemGlue\Article($_POST['idArticleArchive']);
            $Article->setStatut(0);
            if ($Article->update()) {
                echo 'true';
            }
        }

        if (isset($_POST['deleteArticle']) && !empty($_POST['idArticleDelete'])) {
            $Article = new App\Plugin\ItemGlue\Article($_POST['idArticleDelete']);
            if ($Article->delete()) {
                echo 'true';
            }
        }

        if (isset($_POST['featuredArticle']) && !empty($_POST['idArticleFeatured']) && !empty($_POST['newStatut'])) {
            $Article = new App\Plugin\ItemGlue\Article($_POST['idArticleFeatured']);
            $Article->setStatut($_POST['newStatut']);
            if ($Article->update()) {
                echo 'true';
            }
        }

        if (isset($_POST['deleteImage']) && !empty($_POST['idImage'])) {

            $ArticleMedia = new App\Plugin\ItemGlue\ArticleMedia();
            $ArticleMedia->setId($_POST['idImage']);
            if ($ArticleMedia->show()) {
                if ($ArticleMedia->delete()) {
                    echo 'true';
                }
            }
        }

        if (isset($_POST['ADDARTICLEMETA']) && !empty($_POST['idArticle'])) {
            if (!empty($_POST['metaKey']) && !empty($_POST['metaValue'])) {

                $ArticleMeta = new App\Plugin\ItemGlue\ArticleMeta();
                $ArticleMeta->feed($_POST);

                if ($ArticleMeta->notExist()) {

                    if ($ArticleMeta->save()) {

                        //Add translation
                        if(isset($_POST['addTradValue'])) {
                            $Traduction = new App\Plugin\Traduction\Traduction();
                            $Traduction->setLang(LANG);
                            $Traduction->setMetaKey($ArticleMeta->getMetaValue());
                            $Traduction->setMetaValue($ArticleMeta->getMetaValue());
                            $Traduction->save();
                        }

                        echo $ArticleMeta->getId();

                    } else {
                        echo trans('Une erreur s\'est produite');
                    }

                } else {
                    echo trans('Ce détail exist déjà');
                }

            } else {
                echo trans('Tous les champs sont obligatoires');
            }
        }

        if (isset($_POST['DELETEMETAARTICLE']) && !empty($_POST['idMetaArticle'])) {
            $ArticleMeta = new App\Plugin\ItemGlue\ArticleMeta();
            $ArticleMeta->setId($_POST['idMetaArticle']);
            if ($ArticleMeta->delete()) {
                echo 'true';
            }
        }
    }
}