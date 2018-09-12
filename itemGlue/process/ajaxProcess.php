<?php
require_once('../main.php');
if (checkAjaxRequest()) {

    if (getUserIdSession()) {

        includePluginsFiles();

        $_POST = cleanRequest($_POST);

        if (isset($_POST['archiveArticle']) && !empty($_POST['idArticleArchive'])) {
            $Article = new \App\Plugin\ItemGlue\Article($_POST['idArticleArchive']);
            $Article->setStatut(0);
            if ($Article->update()) {
                echo 'true';
            }
        }

        if (isset($_POST['unpackArticle']) && !empty($_POST['idUnpackArticle'])) {
            $Article = new \App\Plugin\ItemGlue\Article($_POST['idUnpackArticle']);
            $Article->setStatut(1);
            if ($Article->update()) {
                echo 'true';
            }
        }

        if (isset($_POST['deleteArticle']) && !empty($_POST['idArticleDelete'])) {
            $Article = new \App\Plugin\ItemGlue\Article($_POST['idArticleDelete']);
            if ($Article->delete()) {
                echo 'true';
            }
        }

        if (isset($_POST['featuredArticle']) && !empty($_POST['idArticleFeatured']) && !empty($_POST['newStatut'])) {
            $Article = new \App\Plugin\ItemGlue\Article($_POST['idArticleFeatured']);
            $Article->setStatut($_POST['newStatut']);
            if ($Article->update()) {
                echo 'true';
            }
        }

        if (isset($_POST['deleteImage']) && !empty($_POST['idImage'])) {

            $ArticleMedia = new \App\Plugin\ItemGlue\ArticleMedia();
            $ArticleMedia->setId($_POST['idImage']);
            if ($ArticleMedia->show()) {
                if ($ArticleMedia->delete()) {
                    echo 'true';
                }
            }
        }

        /**
         * Meta Product
         */
        if (isset($_POST['DELETEMETAARTICLE']) && !empty($_POST['idMetaArticle'])) {
            $ArticleMeta = new \App\Plugin\ItemGlue\ArticleMeta();
            $ArticleMeta->setId($_POST['idMetaArticle']);
            if ($ArticleMeta->delete()) {
                echo json_encode(true);
            }
        }

        if (isset($_POST['ADDARTICLEMETA'])
            && !empty($_POST['idArticle'])
            && !empty($_POST['metaKey'])
            && !empty($_POST['metaValue'])) {

            $ArticleMeta = new \App\Plugin\ItemGlue\ArticleMeta();
            $ArticleMeta->feed($_POST);

            if (!empty($_POST['UPDATEMETAARTICLE'])) {

                $ArticleMeta->setId($_POST['UPDATEMETAARTICLE']);
                if ($ArticleMeta->notExist(true)) {
                    if ($ArticleMeta->update()) {

                        echo json_encode(true);
                    }
                }

            } else {
                if ($ArticleMeta->notExist()) {
                    if ($ArticleMeta->save()) {

                        echo json_encode(true);
                    }
                }
            }

            //Add translation
            if (isset($_POST['addTradValue'])) {
                $Traduction = new \App\Plugin\Traduction\Traduction();
                $Traduction->setLang(LANG);
                $Traduction->setMetaKey($ArticleMeta->getMetaValue());
                $Traduction->setMetaValue($ArticleMeta->getMetaValue());
                $Traduction->save();
            }

        }
    }
}