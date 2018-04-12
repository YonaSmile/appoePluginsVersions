<?php

namespace App\Plugin\Traduction;
class Traduction
{

    private $id;
    private $metaKey;
    private $metaValue;
    private $lang;

    private $data = null;
    private $db_data = null;
    private $dbh = null;

    public function __construct($lang = null)
    {
        if(is_null($this->dbh)) {
            $this->dbh = \App\DB::connect();
        }

        if (!is_null($lang)) {
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

    /**
     * @return null
     */
    public function getDbData()
    {
        return $this->db_data;
    }

    /**
     * @param null $db_data
     */
    public function setDbData($db_data)
    {
        $this->db_data = $db_data;
    }

    public function trans($key)
    {
        $trad = $key;
        $trans = minimalizeText(html_entity_decode(htmlspecialchars_decode($key, ENT_QUOTES), ENT_QUOTES));

        if (is_array($this->data)) {

            //preparing to compare
            $langArray = array_map('htmlSpeCharDecode', array_keys($this->data));
            $langArray = array_map('htmlEntityDecode', array_values($langArray));
            $langArray = array_map('minimalizeText', array_values($langArray));

            //comparing
            $tradPos = (false !== array_search($trans, $langArray)) ? array_search($trans, $langArray) : null;

            $trad = !is_null($tradPos) ? $this->data[array_keys($this->data)[$tradPos]] : $key;
        }

        return $trad;
    }

    public function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `appoe_plugin_traduction` (
  					`id` INT(11) NOT NULL AUTO_INCREMENT,
                	PRIMARY KEY (`id`),
  					`metaKey` VARCHAR(250) NOT NULL,
  					`metaValue` TEXT NOT NULL,
  					`lang` VARCHAR(10) NOT NULL,
                	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                	UNIQUE (`metaKey`, `lang`)
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

        $sql = 'SELECT * FROM appoe_plugin_traduction WHERE id = :id';

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

        $sql = 'SELECT * FROM appoe_plugin_traduction WHERE lang = :lang ORDER BY updated_at DESC';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':lang', $this->lang);
        $stmt->execute();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $this->data[$row['metaKey']] = $row['metaValue'];
                $this->db_data[$row['metaKey']] = $row;
            }

            return $this->data;
        }
    }

    /**
     * @return bool
     */
    public function save()
    {
        $sql = 'INSERT INTO appoe_plugin_traduction (metaKey, metaValue, lang) 
                VALUES (:metaKey, :metaValue, :lang)';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':metaKey', $this->metaKey);
        $stmt->bindParam(':metaValue', $this->metaValue);

        foreach (LANGUAGES as $lang => $trad) {
            $stmt->bindParam(':lang', $lang);
            $stmt->execute();
        }

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
    public function saveMultiple()
    {
        $sql = 'INSERT INTO appoe_plugin_traduction (metaKey, metaValue, lang) 
                VALUES (:metaKey, :metaValue, :lang)';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':metaKey', $this->metaKey);
        $stmt->bindParam(':metaValue', $this->metaValue);
        $stmt->bindParam(':lang', $this->lang);
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
    public function update()
    {

        $sql = 'UPDATE appoe_plugin_traduction SET metaValue = :metaValue WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
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

        $sql = 'DELETE FROM appoe_plugin_traduction WHERE id = :id';

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
     * @return bool
     */
    public function deleteByKey()
    {

        $sql = 'DELETE FROM appoe_plugin_traduction WHERE metaKey = :metaKey';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':metaKey', $this->metaKey);

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

        $sql = 'SELECT id, metaKey, lang FROM appoe_plugin_traduction WHERE metaKey = :metaKey AND lang = :lang';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':metaKey', $this->metaKey);
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