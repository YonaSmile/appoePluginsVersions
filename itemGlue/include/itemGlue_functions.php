<?php

use App\Category;
use App\CategoryRelations;
use App\Plugin\ItemGlue\Article;
use App\Plugin\ItemGlue\ArticleMedia;
use App\Plugin\ItemGlue\ArticleMeta;
use App\Plugin\ItemGlue\ArticleRelation;

/**
 * @param object|int $articleId
 * @param string $lang
 * @return Article|bool
 */
function getArticlesDataById($articleId, $lang = LANG)
{
    if (is_numeric($articleId)) {
        $Article = new Article($articleId);

    } elseif (is_object($articleId)) {
        $Article = $articleId;
    } else {
        $Article = false;
    }

    if ($Article) {

        //get article content
        $Article->setContent(htmlSpeCharDecode($Article->getContent()));

        //Get Media
        $ArticleMedia = new ArticleMedia($Article->getId());
        $Article->medias = $ArticleMedia->showFiles();

        //Get Metas
        $ArticleMeta = new ArticleMeta($Article->getId(), $lang);
        $Article->metas = $ArticleMeta->getData() ? extractFromObjToSimpleArr($ArticleMeta->getData(), 'metaKey', 'metaValue') : array();

        //get all categories in relation with article
        $Article->categories = extractFromObjToSimpleArr(getCategoriesByArticle($Article->getId()), 'categoryId', 'name');

        return $Article;
    }

    return false;
}

/**
 * @param stdClass $article
 * @return stdClass
 */
function getArticleData(stdClass $article)
{

    //Get Media
    $ArticleMedia = new ArticleMedia($article->id);
    $article->medias = $ArticleMedia->showFiles();

    //Get Metas
    $ArticleMeta = new ArticleMeta($article->id);
    $article->metas = $ArticleMeta->getData() ? extractFromObjToSimpleArr($ArticleMeta->getData(), 'metaKey', 'metaValue') : array();

    return $article;
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

    if (!$allArticles) return false;

    foreach ($allArticles as &$article) {
        $article = getArticleData($article);
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

    if (!$allArticles) return false;

    foreach ($allArticles as &$article) {
        $article = getArticleData($article);
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

    if (!$allArticles) return false;

    foreach ($allArticles as &$article) {
        $article = getArticleData($article);
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

            if (!array_key_exists($articleId, $allArticles)) {
                $allArticles[$articleId] = $article;
            }
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
 * @param $year
 * @param bool $month
 * @param int $status
 * @param int|bool $length
 * @param string $lang
 * @param int|bool $idCategory
 * @param bool $parentCategory
 * @return array|bool
 */
function getArticlesArchives($year, $month = false, $status = 1, $length = false, $lang = LANG, $idCategory = false, $parentCategory = false)
{
    if (is_numeric($year)) {

        $Article = new Article();
        $Article->setStatut($status);
        $allArticles = $Article->showArchives($year, $month, $length, $lang, $idCategory, $parentCategory);

        if (!$allArticles) return false;

        foreach ($allArticles as $key => &$article) {

            $article = getArticleData($article);
        }

        return $allArticles;
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
    if (!empty($slug)) {

        $slug = trad($slug, true);

        //get article
        $Article = new Article();
        $Article->setSlug($slug);
        if ($Article->showBySlug()) {

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
            $all['content'] = $Article->getContent();
            $all['meta'] = $allArticleMeta;
            $all['categories'] = $allCategoriesRelations;
            $all['media'] = $allArticleMedia;

            return $all;
        }
    }
    return false;
}


/**
 * @param $slug
 * @return Article|bool
 */
function getArticlesBySlug($slug)
{
    if (!empty($slug)) {

        //get article
        $Article = new Article();
        $Article->setSlug($slug);

        if ($Article->showBySlug()) {
            return getArticlesDataById($Article);
        }

        //Check for other languages
        $testedLang = array(LANG);

        foreach (getLangs() as $minLang => $largeLang) {
            if (!in_array($minLang, $testedLang)) {

                $testedLang[] = $minLang;
                $Article->setLang($minLang);

                if ($Article->showBySlug()) {
                    return getArticlesDataById($Article, $minLang);
                    break;
                }
            }
        }
    }
    return false;
}

/**
 * @param $articleId
 * @return null
 */
function getCategoriesByArticle($articleId)
{

    //get all categories in relation with article
    $CategoryRelation = new CategoryRelations('ITEMGLUE', $articleId);
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
            $cat = (false !== strpos($article->categoryNames, '||')) ? explode('||', $article->categoryNames) : $article->categoryNames;
            array_push($categories, $cat);
        }
        $categories = flatten($categories);
    }
    return $categories;
}

/**
 * @param $article
 * @param $property
 * @return array
 */
function getCategoriesInArticle($article, $property = 'categoryNames')
{
    $categories = array();

    if ($article) {
        if (false !== strpos($article->$property, '||')) {
            $categories = explode('||', $article->$property);
        } else {
            $categories[] = $article->$property;
        }
    }
    return $categories;
}

/**
 * @param string $categories
 * @param string $separator
 * @return string
 */
function getSlugifyCategories(string $categories, $separator = '||')
{

    if (false !== strpos($categories, $separator)) {
        $categories = explode($separator, $categories);
        $categories = array_map('slugify', $categories);
        return implode(' ', $categories);
    }
    return slugify($categories);
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

/**
 * @param array $articleMetas
 * @param $key
 * @return mixed|string
 */
function getArticleMeta($articleMetas, $key)
{
    if (is_array($articleMetas)) {
        return !empty($articleMetas[$key]) ? $articleMetas[$key] : '';
    }
    return '';
}

/**
 * get article web url
 *
 * @param $articleSlug
 * @param $articlePage
 *
 * @return string
 */
function articleUrl($articleSlug, $articlePage = '')
{
    $articlePage = !empty($articlePage) ? $articlePage :
        (defined('DEFAULT_ARTICLES_PAGE') ? DEFAULT_ARTICLES_PAGE . DIRECTORY_SEPARATOR : '/');
    return webUrl($articlePage, $articleSlug);
}

/**
 * @param $articleId
 * @param string $type
 * @return array|bool
 */
function getArticleRelation($articleId, $type = 'USERS')
{

    if (!empty($articleId)) {
        $ArticleRelation = new ArticleRelation($articleId, $type);
        if ($ArticleRelation->getData()) {
            return extractFromObjToSimpleArr($ArticleRelation->getData(), 'id', 'typeId');
        }
    }

    return false;
}

/**
 * @param $articleId
 * @return array
 */
function getArticleUsers($articleId)
{
    $articleUsers = array();
    if (!empty($articleId)) {

        $articleRelations = getArticleRelation($articleId);
        if ($articleRelations) {

            foreach ($articleRelations as $relationId => $userId) {
                $articleUsers[$userId] = getUserEntitled($userId);
            }
        }
    }

    return $articleUsers;
}

/**
 * @param $articleId
 * @param $by = "Par"
 * @param $separator = "et"
 * @return string
 */
function showArticleUsers($articleId, $by = 'Par', $separator = 'et')
{
    $html = '';

    if (!empty($articleId)) {

        $articleUsers = getArticleUsers($articleId);
        if ($articleUsers) {

            $html = trans($by) . ' ';
            $count = 1;

            foreach ($articleUsers as $userId => $userEntitled) {
                $html .= ($count > 1 ? (' ' . trans($separator) . ' ') : '') . $userEntitled;
                $count++;
            }
        }
    }

    return $html;
}