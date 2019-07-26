<?php

namespace App\Plugin\ItemGlue;

use App\DB;
use PDO;

class Article
{
    private $id;
    private $name;
    private $description = null;
    private $slug;
    private $content;
    private $statut = 1;
    private $userId;
    private $createdAt;
    private $updatedAt;

    private $lang = LANG;
    private $dbh = null;

    public function __construct($idArticle = null)
    {
        if (is_null($this->dbh)) {
            $this->dbh = DB::connect();
        }

        $this->userId = getUserIdSession();

        if (!is_null($idArticle)) {
            $this->id = $idArticle;
            $this->show();
        }
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param null $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * @param mixed $statut
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return bool|mixed|string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param bool|mixed|string $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    public function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `appoe_plugin_itemGlue_articles` (
  					`id` INT(11) NOT NULL AUTO_INCREMENT,
                	PRIMARY KEY (`id`),
  					`statut` BOOLEAN NOT NULL DEFAULT TRUE,
  					`userId` INT(11) NOT NULL,
                	`created_at` DATE NOT NULL,
                	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function show()
    {
        $sql = 'SELECT C.*,
        (SELECT cc1.content FROM appoe_plugin_itemGlue_articles_content AS cc1 WHERE cc1.type = "NAME" AND cc1.idArticle = C.id AND cc1.lang = :lang) AS name,
        (SELECT cc2.content FROM appoe_plugin_itemGlue_articles_content AS cc2 WHERE cc2.type = "DESCRIPTION" AND cc2.idArticle = C.id AND cc2.lang = :lang) AS description,
        (SELECT cc3.content FROM appoe_plugin_itemGlue_articles_content AS cc3 WHERE cc3.type = "SLUG" AND cc3.idArticle = C.id AND cc3.lang = :lang) AS slug,
        (SELECT cc4.content FROM appoe_plugin_itemGlue_articles_content AS cc4 WHERE cc4.type = "BODY" AND cc4.idArticle = C.id AND cc4.lang = :lang) AS content
        FROM appoe_plugin_itemGlue_articles AS C
        WHERE C.id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':lang', $this->lang);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count == 1) {

                $row = $stmt->fetch(PDO::FETCH_OBJ);
                $this->feed($row);

                return true;

            } else {

                return false;
            }
        }
    }

    /**
     * @return bool
     */
    public function showBySlug()
    {

        $sql = 'SELECT C.*,
        (SELECT cc1.content FROM appoe_plugin_itemGlue_articles_content AS cc1 WHERE cc1.type = "NAME" AND cc1.idArticle = C.id AND cc1.lang = :lang) AS name,
        (SELECT cc2.content FROM appoe_plugin_itemGlue_articles_content AS cc2 WHERE cc2.type = "DESCRIPTION" AND cc2.idArticle = C.id AND cc2.lang = :lang) AS description,
        (SELECT cc3.content FROM appoe_plugin_itemGlue_articles_content AS cc3 WHERE cc3.type = "SLUG" AND cc3.idArticle = C.id AND cc3.lang = :lang) AS slug,
        (SELECT cc4.content FROM appoe_plugin_itemGlue_articles_content AS cc4 WHERE cc4.type = "BODY" AND cc4.idArticle = C.id AND cc4.lang = :lang) AS content
        FROM appoe_plugin_itemGlue_articles AS C
        WHERE C.id = (SELECT idArticle FROM appoe_plugin_itemGlue_articles_content WHERE type = "SLUG" AND content = :slug AND lang = :lang) 
        AND C.statut >= :statut';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':lang', $this->lang);
        $stmt->bindParam(':slug', $this->slug);
        $stmt->bindParam(':statut', $this->statut);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count == 1) {

                $row = $stmt->fetch(PDO::FETCH_OBJ);
                $this->feed($row);

                return true;

            } else {

                return false;
            }
        }
    }

    /**
     * @param $idCategory
     * @param bool $parentId
     * @param bool $countArticles
     * @param bool $lang
     * @return bool|array
     */
    public function showByCategory($idCategory, $parentId = false, $countArticles = false, $lang = LANG)
    {
        $categorySQL = ' AND C.id = :idCategory ';
        if (true === $parentId) {
            $categorySQL = ' AND (C.id = :idCategory OR C.parentId = :idCategory) ';
        }

        $sql = 'SELECT DISTINCT ART.id, 
         (SELECT cc1.content FROM appoe_plugin_itemGlue_articles_content AS cc1 WHERE cc1.type = "NAME" AND cc1.idArticle = ART.id AND cc1.lang = :lang) AS name,
        (SELECT cc2.content FROM appoe_plugin_itemGlue_articles_content AS cc2 WHERE cc2.type = "DESCRIPTION" AND cc2.idArticle = ART.id AND cc2.lang = :lang) AS description,
        (SELECT cc3.content FROM appoe_plugin_itemGlue_articles_content AS cc3 WHERE cc3.type = "SLUG" AND cc3.idArticle = ART.id AND cc3.lang = :lang) AS slug,
        (SELECT cc4.content FROM appoe_plugin_itemGlue_articles_content AS cc4 WHERE cc4.type = "BODY" AND cc4.idArticle = ART.id AND cc4.lang = :lang) AS content,
         ART.userId, ART.created_at, ART.updated_at, ART.statut, 
        GROUP_CONCAT(DISTINCT C.id SEPARATOR "||") AS categoryIds, GROUP_CONCAT(DISTINCT C.name SEPARATOR "||") AS categoryNames
        FROM appoe_categoryRelations AS CR 
        RIGHT JOIN appoe_plugin_itemGlue_articles AS ART 
        ON(CR.typeId = ART.id) 
        INNER JOIN appoe_categories AS C
        ON(C.id = CR.categoryId)
        INNER JOIN appoe_plugin_itemGlue_articles_content AS AC
        ON(AC.idArticle = ART.id)
        WHERE CR.type = "ITEMGLUE" AND ART.statut > 0 AND C.status > 0 AND AC.lang = :lang' . $categorySQL . '
        GROUP BY ART.id ORDER BY ART.statut DESC, ART.created_at DESC';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':idCategory', $idCategory);
        $stmt->bindValue(':lang', $lang);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            return (!$countArticles) ? $stmt->fetchAll(PDO::FETCH_OBJ) : $count;
        }
    }

    /**
     * @param bool $countArticles
     * @param bool $length
     * @return array|bool
     */
    public function showAll($countArticles = false, $length = false)
    {
        $limit = $length ? ' LIMIT ' . $length . ' OFFSET 0' : '';
        $featured = $this->statut == 1 ? ' statut >= 1' : ' statut = ' . $this->statut . ' ';

        $sql = 'SELECT ART.*,
         (SELECT cc1.content FROM appoe_plugin_itemGlue_articles_content AS cc1 WHERE cc1.type = "NAME" AND cc1.idArticle = ART.id AND cc1.lang = :lang) AS name,
        (SELECT cc2.content FROM appoe_plugin_itemGlue_articles_content AS cc2 WHERE cc2.type = "DESCRIPTION" AND cc2.idArticle = ART.id AND cc2.lang = :lang) AS description,
        (SELECT cc3.content FROM appoe_plugin_itemGlue_articles_content AS cc3 WHERE cc3.type = "SLUG" AND cc3.idArticle = ART.id AND cc3.lang = :lang) AS slug,
        (SELECT cc4.content FROM appoe_plugin_itemGlue_articles_content AS cc4 WHERE cc4.type = "BODY" AND cc4.idArticle = ART.id AND cc4.lang = :lang) AS content 
         FROM appoe_plugin_itemGlue_articles AS ART
        WHERE ' . $featured . ' ORDER BY statut DESC, name ASC ' . $limit;

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':lang', $this->lang);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();

        if ($error[0] != '00000') {
            return false;
        } else {
            return (!$countArticles) ? $stmt->fetchAll(PDO::FETCH_OBJ) : $count;
        }
    }

    /**
     * @param bool $countArticles
     * @param bool $length
     * @param bool $lang
     * @return array|bool
     */
    public function showAllByLang($countArticles = false, $length = false, $lang = LANG)
    {
        $limit = $length ? ' LIMIT ' . $length . ' OFFSET 0' : '';
        $featured = $this->statut == 1 ? ' ART.statut >= 1' : ' ART.statut = ' . $this->statut . ' ';

        $sql = 'SELECT ART.*,
        (SELECT cc1.content FROM appoe_plugin_itemGlue_articles_content AS cc1 WHERE cc1.type = "NAME" AND cc1.idArticle = ART.id AND cc1.lang = :lang) AS name,
        (SELECT cc2.content FROM appoe_plugin_itemGlue_articles_content AS cc2 WHERE cc2.type = "DESCRIPTION" AND cc2.idArticle = ART.id AND cc2.lang = :lang) AS description,
        (SELECT cc3.content FROM appoe_plugin_itemGlue_articles_content AS cc3 WHERE cc3.type = "SLUG" AND cc3.idArticle = ART.id AND cc3.lang = :lang) AS slug,
        (SELECT cc4.content FROM appoe_plugin_itemGlue_articles_content AS cc4 WHERE cc4.type = "BODY" AND cc4.idArticle = ART.id AND cc4.lang = :lang) AS content
        FROM appoe_plugin_itemGlue_articles AS ART     
        WHERE ' . $featured . '
        ORDER BY ART.statut DESC, ART.created_at DESC ' . $limit;

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindValue(':lang', $lang);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();

        if ($error[0] != '00000') {
            return false;
        } else {
            return (!$countArticles) ? $stmt->fetchAll(PDO::FETCH_OBJ) : $count;
        }
    }

    /**
     * @param int $year
     * @param bool|int $month
     * @param bool $length
     * @param bool $lang
     * @return array|bool
     */
    public function showArchives($year, $month = false, $length = false, $lang = LANG)
    {
        if (!is_numeric($year) || strlen($year) != 4) {
            $year = date('Y');
        }

        $sqlArchives = ' AND (YEAR(ART.updated_at) = :year OR YEAR(AC.updated_at) = :year) ';

        if ($month && is_numeric($month) && checkdate($month, 1, $year)) {
            $sqlArchives = ' AND (YEAR(ART.updated_at) = :year OR YEAR(AC.updated_at) = :year) AND (MONTH(ART.updated_at) = :month OR MONTH(AC.updated_at) = :month) ';
        }

        $limit = $length ? ' LIMIT ' . $length . ' OFFSET 0' : '';
        $featured = $this->statut == 1 ? ' ART.statut >= 1' : ' ART.statut = ' . $this->statut . ' ';

        $sql = 'SELECT ART.*, 
         (SELECT cc1.content FROM appoe_plugin_itemGlue_articles_content AS cc1 WHERE cc1.type = "NAME" AND cc1.idArticle = ART.id AND cc1.lang = :lang) AS name,
        (SELECT cc2.content FROM appoe_plugin_itemGlue_articles_content AS cc2 WHERE cc2.type = "DESCRIPTION" AND cc2.idArticle = ART.id AND cc2.lang = :lang) AS description,
        (SELECT cc3.content FROM appoe_plugin_itemGlue_articles_content AS cc3 WHERE cc3.type = "SLUG" AND cc3.idArticle = ART.id AND cc3.lang = :lang) AS slug,
        (SELECT cc4.content FROM appoe_plugin_itemGlue_articles_content AS cc4 WHERE cc4.type = "BODY" AND cc4.idArticle = ART.id AND cc4.lang = :lang) AS content
         FROM appoe_plugin_itemGlue_articles AS ART
        LEFT JOIN appoe_plugin_itemGlue_articles_content AS AC
        ON(AC.idArticle = ART.id)
        WHERE ' . $featured . ' AND (AC.lang IS NULL OR AC.lang = :lang) ' . $sqlArchives . ' ORDER BY ART.statut DESC, ART.name ASC ' . $limit;

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindValue(':lang', $lang);
        $stmt->bindValue(':year', $year);
        if ($month) {
            $stmt->bindValue(':month', $month);
        }

        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            return $stmt->fetchAll(PDO::FETCH_OBJ);

        }
    }

    /**
     * @param $lang
     * @param $idCategory
     * @param $parent
     * @return bool
     */
    public function showNextArticle($lang = LANG, $idCategory = false, $parent = false)
    {
        $addSql = '';
        if (false !== $idCategory) {
            $addSql = ' AND C.id = :idCategory ';
            if (true === $parent) {
                $addSql = ' AND (C.id = :idCategory OR C.parentId = :idCategory) ';
            }
        }

        $sql = 'SELECT ART.*, 
        (SELECT cc1.content FROM appoe_plugin_itemGlue_articles_content AS cc1 WHERE cc1.type = "NAME" AND cc1.idArticle = ART.id AND cc1.lang = :lang) AS name,
        (SELECT cc2.content FROM appoe_plugin_itemGlue_articles_content AS cc2 WHERE cc2.type = "DESCRIPTION" AND cc2.idArticle = ART.id AND cc2.lang = :lang) AS description,
        (SELECT cc3.content FROM appoe_plugin_itemGlue_articles_content AS cc3 WHERE cc3.type = "SLUG" AND cc3.idArticle = ART.id AND cc3.lang = :lang) AS slug,
        (SELECT cc4.content FROM appoe_plugin_itemGlue_articles_content AS cc4 WHERE cc4.type = "BODY" AND cc4.idArticle = ART.id AND cc4.lang = :lang) AS content,
        GROUP_CONCAT(DISTINCT C.id SEPARATOR "||") AS categoryIds, GROUP_CONCAT(DISTINCT C.name SEPARATOR "||") AS categoryNames 
        FROM appoe_plugin_itemGlue_articles AS ART
        INNER JOIN appoe_plugin_itemGlue_articles_content AS AC
        ON(ART.id = AC.idArticle)
        INNER JOIN appoe_categoryRelations AS CR 
        ON(CR.typeId = ART.id) 
        INNER JOIN appoe_categories AS C
        ON(C.id = CR.categoryId)
        WHERE ART.id = (
        SELECT MIN(ART.id) FROM appoe_plugin_itemGlue_articles AS ART 
        INNER JOIN appoe_plugin_itemGlue_articles_content AS AC
        ON(ART.id = AC.idArticle)
        INNER JOIN appoe_categoryRelations AS CR 
        ON(CR.typeId = ART.id) 
        INNER JOIN appoe_categories AS C
        ON(C.id = CR.categoryId)
        WHERE ART.id > :id AND CR.type = "ITEMGLUE" AND ART.statut >= 1 AND C.status > 0 AND AC.lang = :lang ' . $addSql . ')
        AND AC.lang = :lang';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindValue(':lang', $lang);
        if (false !== $idCategory) {
            $stmt->bindParam(':idCategory', $idCategory);
        }
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count == 1) {

                $row = $stmt->fetch(PDO::FETCH_OBJ);
                $this->feed($row);

                return true;

            } else {

                return false;
            }
        }
    }

    /**
     * @param $lang
     * @param $idCategory
     * @param $parent
     * @return bool
     */
    public function showPreviousArticle($lang = LANG, $idCategory = false, $parent = false)
    {
        $addSql = '';
        if (false !== $idCategory) {
            $addSql = ' AND C.id = :idCategory ';
            if (true === $parent) {
                $addSql = ' AND (C.id = :idCategory OR C.parentId = :idCategory) ';
            }
        }

        $sql = 'SELECT ART.*,
        (SELECT cc1.content FROM appoe_plugin_itemGlue_articles_content AS cc1 WHERE cc1.type = "NAME" AND cc1.idArticle = ART.id AND cc1.lang = :lang) AS name,
        (SELECT cc2.content FROM appoe_plugin_itemGlue_articles_content AS cc2 WHERE cc2.type = "DESCRIPTION" AND cc2.idArticle = ART.id AND cc2.lang = :lang) AS description,
        (SELECT cc3.content FROM appoe_plugin_itemGlue_articles_content AS cc3 WHERE cc3.type = "SLUG" AND cc3.idArticle = ART.id AND cc3.lang = :lang) AS slug,
        (SELECT cc4.content FROM appoe_plugin_itemGlue_articles_content AS cc4 WHERE cc4.type = "BODY" AND cc4.idArticle = ART.id AND cc4.lang = :lang) AS content,
        GROUP_CONCAT(DISTINCT C.id SEPARATOR "||") AS categoryIds, GROUP_CONCAT(DISTINCT C.name SEPARATOR "||") AS categoryNames 
        FROM appoe_plugin_itemGlue_articles AS ART
        INNER JOIN appoe_plugin_itemGlue_articles_content AS AC
        ON(ART.id = AC.idArticle)
        INNER JOIN appoe_categoryRelations AS CR 
        ON(CR.typeId = ART.id) 
        INNER JOIN appoe_categories AS C
        ON(C.id = CR.categoryId)
        WHERE ART.id = (
        SELECT MAX(ART.id) FROM appoe_plugin_itemGlue_articles AS ART 
        INNER JOIN appoe_plugin_itemGlue_articles_content AS AC
        ON(ART.id = AC.idArticle)
        INNER JOIN appoe_categoryRelations AS CR 
        ON(CR.typeId = ART.id) 
        INNER JOIN appoe_categories AS C
        ON(C.id = CR.categoryId)
        WHERE ART.id < :id AND CR.type = "ITEMGLUE" AND ART.statut >= 1 AND C.status > 0 AND AC.lang = :lang ' . $addSql . ')
        AND AC.lang = :lang';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindValue(':lang', $lang);
        if (false !== $idCategory) {
            $stmt->bindParam(':idCategory', $idCategory);
        }
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count == 1) {

                $row = $stmt->fetch(PDO::FETCH_OBJ);
                $this->feed($row);

                return true;

            } else {

                return false;
            }
        }
    }

    /**
     * @param string $searching
     * @param string $lang
     * @return array|bool
     */
    public function searchFor($searching, $lang = LANG)
    {
        $featured = $this->statut == 1 ? ' ART.statut >= 1' : ' ART.statut = ' . $this->statut . ' ';

        $sql = 'SELECT DISTINCT ART.id, ART.name, ART.description, ART.slug, ART.userId, ART.created_at, ART.updated_at, ART.statut, 
        GROUP_CONCAT(DISTINCT C.id SEPARATOR "||") AS categoryIds, GROUP_CONCAT(DISTINCT C.name SEPARATOR "||") AS categoryNames,
        (SELECT cc1.content FROM appoe_plugin_itemGlue_articles_content AS cc1 WHERE cc1.type = "NAME" AND cc1.idArticle = ART.id AND cc1.lang = :lang) AS name,
        (SELECT cc2.content FROM appoe_plugin_itemGlue_articles_content AS cc2 WHERE cc2.type = "DESCRIPTION" AND cc2.idArticle = ART.id AND cc2.lang = :lang) AS description,
        (SELECT cc3.content FROM appoe_plugin_itemGlue_articles_content AS cc3 WHERE cc3.type = "SLUG" AND cc3.idArticle = ART.id AND cc3.lang = :lang) AS slug,
        (SELECT cc4.content FROM appoe_plugin_itemGlue_articles_content AS cc4 WHERE cc4.type = "BODY" AND cc4.idArticle = ART.id AND cc4.lang = :lang) AS content
        FROM appoe_categoryRelations AS CR 
        RIGHT JOIN appoe_plugin_itemGlue_articles AS ART 
        ON(CR.typeId = ART.id) 
        INNER JOIN appoe_categories AS C
        ON(C.id = CR.categoryId)
        INNER JOIN appoe_plugin_itemGlue_articles_content AS AC
        ON(AC.idArticle = ART.id)
        WHERE ' . $featured . ' AND CR.type = "ITEMGLUE" AND C.status > 0 AND (ART.name LIKE ? OR AC.content LIKE ?) 
        AND AC.lang = ? GROUP BY ART.id ORDER BY ART.statut DESC, ART.created_at DESC ';

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute(array('%' . $searching . '%', '%' . $searching . '%', $lang));

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        }
    }

    /**
     * @return bool
     */
    public function save()
    {

        $sql = 'INSERT INTO appoe_plugin_itemGlue_articles (statut, userId, created_at) 
                VALUES (:statut, :userId, CURDATE())';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':statut', $this->statut);
        $stmt->bindParam(':userId', $this->userId);
        $stmt->execute();

        $this->id = $this->dbh->lastInsertId();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            $this->setId($this->id);
            appLog('Creating Article -> id: ' . $this->id);
            return true;
        }
    }

    /**
     * @return bool
     */
    public function update()
    {

        $sql = 'UPDATE appoe_plugin_itemGlue_articles SET statut = :statut, userId = :userId, created_at = :created_at WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':statut', $this->statut);
        $stmt->bindParam(':userId', $this->userId);
        $stmt->bindParam(':created_at', $this->createdAt);
        $stmt->bindParam(':id', $this->id);

        $stmt->execute();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            appLog('Updating Article -> id: ' . $this->id . ' statut: ' . $this->statut);
            return true;
        }
    }

    /**
     * @return bool
     */
    public function delete()
    {
        //Get Media of Article
        $ArticleMedia = new ArticleMedia($this->id);
        $allMedia = $ArticleMedia->showFiles();

        foreach ($allMedia as $media) {
            $ArticleMedia->setId($media->id);
            $ArticleMedia->setName($media->name);
            $ArticleMedia->delete();
        }

        $sql = 'DELETE FROM appoe_categoryRelations WHERE type = "ITEMGLUE" AND typeId = :id;';
        $sql .= 'DELETE FROM appoe_plugin_itemGlue_articles_meta WHERE idArticle = :id;';
        $sql .= 'DELETE FROM appoe_plugin_itemGlue_articles_content WHERE idArticle = :id;';
        $sql .= 'DELETE FROM appoe_plugin_itemGlue_articles WHERE id = :id;';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            appLog('Deleting Article -> id: ' . $this->id);
            return true;
        }
    }

    /**
     * Feed class attributs
     *
     * @param $data
     */
    public function feed($data)
    {
        foreach ($data as $attribut => $value) {
            $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $attribut)));

            if (is_callable(array($this, $method))) {
                $this->$method($value);
            }
        }
    }
}