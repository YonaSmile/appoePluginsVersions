<?php

use App\Category;
use App\CategoryRelations;
use App\Plugin\ItemGlue\Article;
use App\Plugin\ItemGlue\ArticleContent;
use App\Plugin\ItemGlue\ArticleMedia;
use App\Plugin\ItemGlue\ArticleMeta;

/**
 * @param $articleId
 * @return Article|bool
 */
function getArticlesDataById($articleId)
{
    if (is_numeric($articleId)) {

        $Article = new Article($articleId);

        if ($Article) {

            //Get Media
            $ArticleMedia = new ArticleMedia($Article->getId());
            $Article->medias = $ArticleMedia->showFiles();

            //Get Metas
            $ArticleMeta = new ArticleMeta($Article->getId());
            if ($ArticleMeta->getData()) {
                $Article->metas = extractFromObjToSimpleArr($ArticleMeta->getData(), 'metaKey', 'metaValue');
            }

            return $Article;
        }
    }
    return false;
}

/**
 * @param $categoryId
 * @param bool $parent
 * @param bool $length
 * @return array|bool
 */
function getArticlesByCategory($categoryId, $parent = false, $length = false)
{
    $Article = new Article();
    $allArticles = $Article->showByCategory($categoryId, $parent);

    foreach ($allArticles as &$article) {

        //Get Media
        $ArticleMedia = new ArticleMedia($article->id);
        $article->medias = $ArticleMedia->showFiles();

        //Get Metas
        $ArticleMeta = new ArticleMeta($article->id);
        if ($ArticleMeta->getData()) {
            $article->metas = extractFromObjToSimpleArr($ArticleMeta->getData(), 'metaKey', 'metaValue');
        }

        //Get Traduction
        if (LANG != "fr") {
            $article->name = trad($article->name);
            $article->slug = trad($article->slug);
        }
    }

    return $length ? array_slice($allArticles, 0, $length, true) : $allArticles;
}

/**
 * @param bool $length
 * @return array|bool
 */
function getRecentArticles($length = false)
{
    $Article = new Article();
    $allArticles = $Article->showAllByLang(false, $length);

    if ($allArticles) {
        foreach ($allArticles as &$article) {

            //Get Media
            $ArticleMedia = new ArticleMedia($article->id);
            $article->medias = $ArticleMedia->showFiles();

            //Get Metas
            $ArticleMeta = new ArticleMeta($article->id);
            if ($ArticleMeta->getData()) {
                $article->metas = extractFromObjToSimpleArr($ArticleMeta->getData(), 'metaKey', 'metaValue');
            }
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

    $Article = new Article();
    $allArticles = $Article->searchFor($searching);

    if ($allArticles) {
        foreach ($allArticles as &$article) {

            //Get Media
            $ArticleMedia = new ArticleMedia($article->id);
            $article->medias = $ArticleMedia->showFiles();

            //Get Metas
            $ArticleMeta = new ArticleMeta($article->id);
            if ($ArticleMeta->getData()) {
                $article->metas = extractFromObjToSimpleArr($ArticleMeta->getData(), 'metaKey', 'metaValue');
            }
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
 * @param $id
 * @param $idCategory
 * @param $parent
 * @return Article|bool
 */
function getNextArticle($id, $idCategory = false, $parent = false)
{

    if (is_numeric($id)) {

        $Article = new Article();
        $Article->setId($id);
        if ($Article->showNextArticle(LANG, $idCategory, $parent)) {
            return getArticlesDataById($Article->getId());
        }
    }

    return false;
}

/**
 * @param $id
 * @param $idCategory
 * @param $parent
 * @return Article|bool
 */
function getPreviousArticle($id, $idCategory = false, $parent = false)
{

    if (is_numeric($id)) {

        $Article = new Article();
        $Article->setId($id);
        if ($Article->showPreviousArticle(LANG, $idCategory, $parent)) {
            return getArticlesDataById($Article->getId());
        }
    }

    return false;
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
    $Category = new Category();
    $Category->setType('ITEMGLUE');
    $allCategories = extractFromObjArr($Category->showByType(), 'id');

    //get all articles
    $Article = new Article();
    $Article->setStatut($favorite);
    $allArticles = !$archives ? extractFromObjArr($Article->showAll(), 'id') : extractFromObjArr($Article->showArchives($archives['year'], $archives['month']), 'id');

    //get all categories in relation with all articles
    $CategoryRelation = new CategoryRelations();
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
 * Obsolète function
 * @param $slug
 * @return bool
 */
function getSpecificArticlesDetailsBySlug($slug)
{
    return getArticlesBySlug($slug);
}

/**
 * @param $slug
 * @return bool
 */
function getArticlesBySlug($slug)
{
    if (!empty($slug)) {

        if (LANG != 'fr') {
            $slug = trad($slug, true);
        }

        //get article
        $Article = new Article();
        $Article->setSlug($slug);
        if ($Article->showBySlug()) {

            //get article content
            $ArticleContent = new ArticleContent($Article->getId(), LANG);

            //get all categories in relation with article
            $CategoryRelation = new CategoryRelations('ITEMGLUE', $Article->getId());
            $allCategoriesRelations = $CategoryRelation->getData();

            //get article metas
            $ArticleMeta = new ArticleMeta($Article->getId());
            $allArticleMeta = $ArticleMeta->getData();

            //get article medias
            $ArticleMedia = new ArticleMedia($Article->getId());
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
    $Article = new Article($id);

    //get all categories in relation with article
    $CategoryRelation = new CategoryRelations('ITEMGLUE', $Article->getId());
    return $CategoryRelation->getData();
}

/**
 * @param $articles
 * @return array
 */
function getAllCategoriesInArticles($articles)
{
    $categories = array();

    if ($articles) {
        foreach ($articles as $article) {
            $cat = (false !== strpos($article->categoryNames, ';')) ? explode(';', $article->categoryNames) : $article->categoryNames;
            array_push($categories, $cat);
        }
        $categories = array_unique(flatten($categories));
    }
    return $categories;
}

/**
 * @param stdClass $Article
 * @param string $meta
 * @param string $page
 * @return string
 */
function getArticleUrl(stdClass $Article, $meta = 'link', $page = '')
{

    if (!empty($Article->metas[$meta])) {

        /*
         * If (meta "link" contains "http") return "link"
         * Else return the website url with "link"
         */
        return false !== strpos($Article->metas[$meta], 'http') ? $Article->metas[$meta] : webUrl($Article->metas[$meta] . DIRECTORY_SEPARATOR);
    }

    if (empty($page) && defined('DEFAULT_ARTICLES_PAGE')) {

        /*
         * Put Article in default articles page
         */
        $page = DEFAULT_ARTICLES_PAGE . DIRECTORY_SEPARATOR;
    }

    return articleUrl($Article->slug, $page);
}