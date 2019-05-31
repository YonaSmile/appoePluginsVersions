<?php
namespace App\Plugin\Cms;
class CmsContent
{
    private $id;
    private $idCms;
    private $metaKey;
    private $metaValue;
    private $lang;

    private $data = null;
    private $dbh = null;

    public function __construct($idCms = null, $lang = null)
    {
        if(is_null($this->dbh)) {
            $this->dbh = \App\DB::connect();
        }

        if (!is_null($idCms) && !is_null($lang)) {
            $this->idCms = $idCms;
            $this->lang = $lang;
            $this->showAll();
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
    public function getIdCms()
    {
        return $this->idCms;
    }

    /**
     * @param null $idCms
     */
    public function setIdCms($idCms)
    {
        $this->idCms = $idCms;
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
     * @return mixed
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param mixed $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    public function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `appoe_plugin_cms_content` (
  					`id` INT(11) NOT NULL AUTO_INCREMENT,
                	PRIMARY KEY (`id`),
                	`idCms` INT(11) NOT NULL,
  					`metaKey` VARCHAR(250) NOT NULL,
  					`metaValue` TEXT NOT NULL,
  					`lang` VARCHAR(10) NOT NULL,
                	`created_at` DATE NOT NULL,
                	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                	UNIQUE (`idCms`, `metaKey`, `lang`)
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

        $sql = 'SELECT * FROM appoe_plugin_cms_content WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id);
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
     * @return array|bool
     */
    public function showAll()
    {

        $sql = 'SELECT * FROM appoe_plugin_cms_content WHERE idCms = :idCms AND lang = :lang ORDER BY created_at ASC';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':idCms', $this->idCms);
        $stmt->bindParam(':lang', $this->lang);
        $stmt->execute();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            $this->data = $stmt->fetchAll(\PDO::FETCH_OBJ);

            return $this->data;
        }
    }

    /**
     * @return bool
     */
    public function save()
    {

        $sql = 'INSERT INTO appoe_plugin_cms_content (idCms, metaKey, metaValue, lang, created_at) 
                VALUES (:idCms, :metaKey, :metaValue, :lang, CURDATE())';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':idCms', $this->idCms);
        $stmt->bindParam(':metaKey', $this->metaKey);
        $stmt->bindParam(':metaValue', $this->metaValue);
        $stmt->bindParam(':lang', $this->lang);
        $stmt->execute();

        $id = $this->dbh->lastInsertId();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            $this->id = $id;
            appLog('Creating page content -> idCms: ' . $this->idCms . ' metaKey: ' . $this->metaKey . ' metaValue: ' . $this->metaValue . ' lang: ' . $this->lang);
            return true;
        }
    }

    /**
     * @return bool
     */
    public function update()
    {

        $sql = 'UPDATE appoe_plugin_cms_content SET metaKey = :metaKey, metaValue = :metaValue WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':metaKey', $this->metaKey);
        $stmt->bindParam(':metaValue', $this->metaValue);
        $stmt->bindParam(':id', $this->id);

        $stmt->execute();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            appLog('Updating page content -> id: ' . $this->id . ' metaKey: ' . $this->metaKey . ' metaValue: ' . $this->metaValue);
            return true;
        }
    }

    /**
     * @return bool
     */
    public function delete()
    {

        $sql = 'DELETE FROM appoe_plugin_cms_content WHERE idCms = :idCms';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':idCms', $this->idCms);

        $stmt->execute();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            appLog('Delete page content -> idCms: ' . $this->idCms);
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

        $sql = 'SELECT id, idCms, metaKey, lang FROM appoe_plugin_cms_content WHERE metaKey = :metaKey AND idCms = :idCms AND lang = :lang';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':metaKey', $this->metaKey);
        $stmt->bindParam(':idCms', $this->idCms);
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