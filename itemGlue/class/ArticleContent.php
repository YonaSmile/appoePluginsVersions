<?php
namespace App\Plugin\ItemGlue;
class ArticleContent
{
    private $id;
    private $idArticle;
    private $content;
    private $lang;

    private $dbh = null;

    public function __construct($idArticle = null, $lang = null)
    {
        if(is_null($this->dbh)) {
            $this->dbh = \App\DB::connect();
        }

        if (!is_null($idArticle) && !is_null($lang)) {
            $this->idArticle = $idArticle;
            $this->lang = $lang;
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
     * @return null
     */
    public function getIdArticle()
    {
        return $this->idArticle;
    }

    /**
     * @param null $idArticle
     */
    public function setIdArticle($idArticle)
    {
        $this->idArticle = $idArticle;
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
     * @return null
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param null $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }


    public function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `appoe_plugin_itemGlue_articles_content` (
  					`id` INT(11) NOT NULL AUTO_INCREMENT,
                	PRIMARY KEY (`id`),
                	`idArticle` INT(11) NOT NULL,
  					`content` TEXT NOT NULL,
  					`lang` VARCHAR(10) NOT NULL,
                	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                	UNIQUE (`idArticle`, `lang`)
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
     * @return array|bool
     */
    public function show()
    {

        $sql = 'SELECT * FROM appoe_plugin_itemGlue_articles_content WHERE idArticle = :idArticle AND lang = :lang';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':idArticle', $this->idArticle);
        $stmt->bindParam(':lang', $this->lang);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count == 1) {

                $row = $stmt->fetch(\PDO::FETCH_OBJ);
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
    public function save()
    {

        $sql = 'INSERT INTO appoe_plugin_itemGlue_articles_content (idArticle, content, lang) 
                VALUES (:idArticle, :content, :lang)';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':idArticle', $this->idArticle);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':lang', $this->lang);
        $stmt->execute();

        $id = $this->dbh->lastInsertId();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            $this->id = $id;

            return true;
        }
    }

    /**
     * @return bool
     */
    public function update()
    {

        $sql = 'UPDATE appoe_plugin_itemGlue_articles_content SET content = :content WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':id', $this->id);

        $stmt->execute();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @return bool
     */
    public function delete()
    {

        $sql = 'DELETE FROM appoe_plugin_itemGlue_articles_content WHERE idArticle = :idArticle';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':idArticle', $this->idArticle);

        $stmt->execute();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            return true;
        }
    }


    /**
     * @param bool $forUpdate
     *
     * @return bool
     */
    public function notExist($forUpdate = false)
    {

        $sql = 'SELECT id, idArticle, content, lang FROM appoe_plugin_itemGlue_articles_content WHERE idArticle = :idArticle AND content = :content AND lang = :lang';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':idArticle', $this->idArticle);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':lang', $this->lang);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count == 1) {
                if ($forUpdate) {
                    $data = $stmt->fetch(\PDO::FETCH_OBJ);
                    if ($data->id == $this->id) {
                        return true;
                    }
                }

                return false;
            } else {
                return true;
            }
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