<?php
function getSpecificArticlesCategory($categoryId, $parentId = false, $favorite = 1)
{
    //get all articles categories
    $Category = new App\Category();
    $Category->setType('ITEMGLUE');
    $allCategories = extractFromObjArr($Category->showByType(), 'id');

    //get all articles
    $Article = new App\Plugin\ItemGlue\Article();
    $Article->setStatut($favorite);
    $allArticles = extractFromObjArr($Article->showAll(), 'id');

    //get all categories in relation with all articles
    $CategoryRelation = new App\CategoryRelations();
    $CategoryRelation->setType('ITEMGLUE');
    $allCategoriesRelations = extractFromObjArr($CategoryRelation->showAll(), 'id');

    $all['articles'] = array();
    $all['categories'] = array();
    $all['onlyCategories'] = array();
    $all['countCategories'] = array();

    if ($allCategoriesRelations) {

        //search only in categories relations
        foreach ($allCategoriesRelations as $relationId => $categoryRelation) {

            //check parent Id and Id
            if (false !== $parentId) {

                if ($allCategories[$categoryRelation->categoryId]->parentId != $categoryId && $categoryRelation->categoryId != $categoryId) {
                    continue;
                }

            } else {
                if ($categoryRelation->id != $categoryId) {
                    continue;
                }
            }

            //count categories
            if (!array_key_exists($allCategories[$categoryRelation->categoryId]->name, $all['countCategories'])) {
                $all['countCategories'][$allCategories[$categoryRelation->categoryId]->name] = 0;
            }

            $all['countCategories'][$allCategories[$categoryRelation->categoryId]->name] += 1;

            if (array_key_exists($categoryRelation->typeId, $allArticles)) {

                //push into Articles
                if (!array_key_exists($categoryRelation->typeId, $all['articles'])) {
                    $all['articles'][$categoryRelation->typeId] = $allArticles[$categoryRelation->typeId];
                    $all['categories'][$categoryRelation->typeId] = array();
                }

                //push into categories
                if (!in_array($categoryRelation->categoryId, $all['categories'][$categoryRelation->typeId])) {
                    $all['categories'][$categoryRelation->typeId][] = $allCategories[$categoryRelation->categoryId]->name;

                    //push into only-categories
                    if (!in_array($allCategories[$categoryRelation->categoryId]->name, $all['onlyCategories'])) {
                        $all['onlyCategories'][] = $allCategories[$categoryRelation->categoryId]->name;
                    }
                }
            }
        }
    }

    return $all;
}

function getSpecificArticlesDetailsBySlug($slug)
{
    $Traduction = new App\Plugin\Traduction\Traduction(LANG);
    $slug = $Traduction->transToOrigin($slug);

    //get article
    $Article = new App\Plugin\ItemGlue\Article();
    $Article->setSlug($slug);
    $Article->showBySlug();

    //get article content
    $ArticleContent = new App\Plugin\ItemGlue\ArticleContent($Article->getId(), LANG);

    //get all categories in relation with article
    $CategoryRelation = new App\CategoryRelations('ITEMGLUE', $Article->getId());
    $allCategoriesRelations = $CategoryRelation->getData();

    //get article metas
    $ArticleMeta = new App\Plugin\ItemGlue\ArticleMeta($Article->getId());
    $allArticleMeta = $ArticleMeta->getData();

    //get article medias
    $ArticleMedia = new App\Plugin\ItemGlue\ArticleMedia($Article->getId());
    $allArticleMedia = $ArticleMedia->showFiles();

    $all['article'] = $Article;
    $all['content'] = $ArticleContent;
    $all['meta'] = $allArticleMeta;
    $all['categories'] = $allCategoriesRelations;
    $all['media'] = $allArticleMedia;

    return $all;
}

function getCategoriesByArticle($id)
{
    //get article
    $Article = new App\Plugin\ItemGlue\Article($id);

    //get all categories in relation with article
    $CategoryRelation = new App\CategoryRelations('ITEMGLUE', $Article->getId());
    return $CategoryRelation->getData();
}