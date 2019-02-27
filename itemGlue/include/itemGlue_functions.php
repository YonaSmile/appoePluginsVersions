<?php
/**
 * @param $categoryId
 * @param bool $parent
 * @param bool $length
 * @return array|bool
 */
function getArticlesByCategory($categoryId, $parent = false, $length = false)
{
    $Article = new \App\Plugin\ItemGlue\Article();
    $allArticles = $Article->showByCategory($categoryId, $parent);

    foreach ($allArticles as &$article) {

        //Get Media
        $ArticleMedia = new \App\Plugin\ItemGlue\ArticleMedia($article->id);
        $article->medias = $ArticleMedia->showFiles();

        //Get Metas
        $ArticleMeta = new \App\Plugin\ItemGlue\ArticleMeta($article->id);
        $article->metas = extractFromObjToSimpleArr($ArticleMeta->getData(), 'metaKey', 'metaValue');
    }

    return $length ? array_slice($allArticles, 0, $length, true) : $allArticles;
}

/**
 * @param bool $length
 * @return array|bool
 */
function getRecentArticles($length = false)
{
    $Article = new \App\Plugin\ItemGlue\Article();
    $allArticles = $Article->showAll(false, $length);

    if ($allArticles) {
        foreach ($allArticles as &$article) {

            //Get Media
            $ArticleMedia = new \App\Plugin\ItemGlue\ArticleMedia($article->id);
            $article->medias = $ArticleMedia->showFiles();

            //Get Metas
            $ArticleMeta = new \App\Plugin\ItemGlue\ArticleMeta($article->id);
            $article->metas = extractFromObjToSimpleArr($ArticleMeta->getData(), 'metaKey', 'metaValue');
        }
    }
    return $allArticles;
}

/**
 * @param string $searching
 * @return array|bool
 */
function getSearchingArticles($searching)
{
    $searching = cleanData($searching);

    $Article = new \App\Plugin\ItemGlue\Article();
    $allArticles = $Article->searchFor($searching);

    if ($allArticles) {
        foreach ($allArticles as &$article) {

            //Get Media
            $ArticleMedia = new \App\Plugin\ItemGlue\ArticleMedia($article->id);
            $article->medias = $ArticleMedia->showFiles();

            //Get Metas
            $ArticleMeta = new \App\Plugin\ItemGlue\ArticleMeta($article->id);
            $article->metas = extractFromObjToSimpleArr($ArticleMeta->getData(), 'metaKey', 'metaValue');
        }
    }
    return $allArticles;
}

/**
 * @param $articleId
 * @param $categories
 * @param bool $length
 * @return array
 */
function getSimilarArticles($articleId, $categories, $length = false)
{
    $relatedArticles = array();
    $allArticles = array();

    if (is_numeric($categories)) {
        $relatedArticles[$categories] = unsetSameKeyInArr(extractFromObjArr(getArticlesByCategory($categories, true), 'id'), $articleId);

    } elseif (is_array($categories)) {
        foreach ($categories as $key => $category) {
            $relatedArticles[$key] = unsetSameKeyInArr(extractFromObjArr(getArticlesByCategory($key, true), 'id'), $articleId);
        }
    }

    foreach ($relatedArticles as $categoryId => $articles) {
        foreach ($articles as $articleId => $article) {
            $allArticles[] = $article;
        }
    }

    return $length ? array_slice($allArticles, 0, $length, true) : $allArticles;

}

/**
 * @param $categoryId
 * @param bool $parentId
 * @param int $favorite
 * @param bool|array $archives
 * @return mixed
 */
function getSpecificArticlesCategory($categoryId, $parentId = false, $favorite = 1, $archives = false)
{
    //get all articles categories
    $Category = new \App\Category();
    $Category->setType('ITEMGLUE');
    $allCategories = extractFromObjArr($Category->showByType(), 'id');

    //get all articles
    $Article = new \App\Plugin\ItemGlue\Article();
    $Article->setStatut($favorite);
    $allArticles = !$archives ? extractFromObjArr($Article->showAll(), 'id') : extractFromObjArr($Article->showArchives($archives['year'], $archives['month']), 'id');

    //get all categories in relation with all articles
    $CategoryRelation = new \App\CategoryRelations();
    $CategoryRelation->setType('ITEMGLUE');
    $allCategoriesRelations = extractFromObjArr($CategoryRelation->showAll(), 'id');

    $all['articles'] = array();
    $all['categories'] = array();
    $all['countCategories'] = array();

    if ($allCategoriesRelations) {

        //search only in categories relations
        foreach ($allCategoriesRelations as $relationId => $categoryRelation) {

            //check parent Id and Id
            if (false !== $parentId) {

                if ($allCategories[$categoryRelation->categoryId]->parentId != $categoryId) {
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
                }
            }
        }
    }

    return $all;
}

/**
 * @param $slug
 * @return bool
 */
function getSpecificArticlesDetailsBySlug($slug)
{
    if (!empty($slug)) {
        $slug = trad($slug, true);

        //get article
        $Article = new \App\Plugin\ItemGlue\Article();
        $Article->setSlug($slug);
        if ($Article->showBySlug()) {

            //get article content
            $ArticleContent = new \App\Plugin\ItemGlue\ArticleContent($Article->getId(), LANG);

            //get all categories in relation with article
            $CategoryRelation = new \App\CategoryRelations('ITEMGLUE', $Article->getId());
            $allCategoriesRelations = $CategoryRelation->getData();

            //get article metas
            $ArticleMeta = new \App\Plugin\ItemGlue\ArticleMeta($Article->getId());
            $allArticleMeta = $ArticleMeta->getData();

            //get article medias
            $ArticleMedia = new \App\Plugin\ItemGlue\ArticleMedia($Article->getId());
            $allArticleMedia = $ArticleMedia->showFiles();

            $all['article'] = $Article;
            $all['content'] = $ArticleContent;
            $all['meta'] = $allArticleMeta;
            $all['categories'] = $allCategoriesRelations;
            $all['media'] = $allArticleMedia;

            return $all;
        }
    }
    return false;
}

/**
 * @param $id
 * @return null
 */
function getCategoriesByArticle($id)
{
    //get article
    $Article = new \App\Plugin\ItemGlue\Article($id);

    //get all categories in relation with article
    $CategoryRelation = new \App\CategoryRelations('ITEMGLUE', $Article->getId());
    return $CategoryRelation->getData();
}