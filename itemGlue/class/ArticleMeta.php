<?php

namespace App\Plugin\ItemGlue;
class ArticleMeta
{
    private $id;
    private $idArticle;
    private $metaKey;
    private $metaValue;

    private $data = null;
    private $dbh = null;

    public function __construct($idArticle = null)
    {
        if (is_null($this->dbh)) {
            $this->dbh = \App\DB::connect();
        }

        if (!is_null($idArticle)) {
            $this->idArticle = $idArticle;
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
    public function getMetaKey()
    {
        return $this->metaKey;
    }

    /**
     * @param mixed $metaKey
     */
    public function setMetaKey($metaKey)
    {
        $this->metaKey = $metaKey;
    }

    /**
     * @return mixed
     */
    public function getMetaValue()
    {
        return $this->metaValue;
    }

    /**
     * @param mixed $metaValue
     */
    public function setMetaValue($metaValue)
    {
        $this->metaValue = $metaValue;
    }

    /**
     * @return null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param null $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    public function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `appoe_plugin_itemGlue_articles_meta` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        PRIMARY KEY (`id`),
        `idArticle` INT(11) NOT NULL,
        `metaKey` VARCHAR(150) NOT NULL,
        `metaValue` TEXT NOT NULL,
        `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE (`idArticle`, `metaKey`)
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

        $sql = 'SELECT * FROM appoe_plugin_itemGlue_articles_meta WHERE idArticle = :idArticle';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':idArticle', $this->idArticle);
        $stmt->execute();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {

            return false;
        } else {

            $this->data = $stmt->fetchAll(\PDO::FETCH_OBJ);
            return true;
        }
    }

    /**
     * @return bool
     */
    public function save()
    {

        $sql = 'INSERT INTO appoe_plugin_itemGlue_articles_meta (idArticle, metaKey, metaValue) 
                VALUES (:idArticle, :metaKey, :metaValue)';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':idArticle', $this->idArticle);
        $stmt->bindParam(':metaKey', $this->metaKey);
        $stmt->bindParam(':metaValue', $this->metaValue);
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

        $sql = 'UPDATE appoe_plugin_itemGlue_articles_meta SET metaKey = :metaKey, metaValue = :metaValue WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':metaKey', $this->metaKey);
        $stmt->bindParam(':metaValue', $this->metaValue);
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

        $sql = 'DELETE FROM appoe_plugin_itemGlue_articles_meta WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
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
     * @param bool $forUpdate
     *
     * @return bool
     */
    public function notExist($forUpdate = false)
    {

        $sql = 'SELECT id FROM appoe_plugin_itemGlue_articles_meta WHERE idArticle = :idArticle AND metaKey = :metaKey';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':idArticle', $this->idArticle);
        $stmt->bindParam(':metaKey', $this->metaKey);
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