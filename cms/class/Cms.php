<?php

namespace App\Plugin\Cms;
class Cms
{
    private $id;
    private $type;
    private $name;
    private $description = null;
    private $slug;
    private $content = null;
    private $statut = 1;

    private $dbh = null;

    public function __construct($idCms = null)
    {
        if(is_null($this->dbh)) {
            $this->dbh = \App\DB::connect();
        }

        if (!is_null($idCms)) {
            $this->id = $idCms;
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
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
     * @return null
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param null $content
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


    public function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `appoe_plugin_cms` (
  					`id` INT(11) NOT NULL AUTO_INCREMENT,
                	PRIMARY KEY (`id`),
                	`type` VARCHAR(100) NOT NULL,
  					`name` VARCHAR(70) NOT NULL,
  					UNIQUE (`name`),
  					`description` VARCHAR(300) NULL DEFAULT NULL,
  					`slug` VARCHAR(100) DEFAULT NULL,
  					UNIQUE (`slug`),
  					`content` TEXT,
  					`statut` BOOLEAN NOT NULL DEFAULT TRUE,
                	`created_at` DATE NOT NULL,
                	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=11;';

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

        $sql = 'SELECT * FROM appoe_plugin_cms WHERE id = :id';

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
     * @return bool
     */
    public function showBySlug()
    {

        $sql = 'SELECT * FROM appoe_plugin_cms WHERE slug = :slug';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':slug', $this->slug);
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
     * @param $pageCount
     * @return array|bool
     */
    public function showAll($pageCount = false)
    {
        $sql = 'SELECT * FROM appoe_plugin_cms ORDER BY created_at DESC';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            $data = $stmt->fetchAll(\PDO::FETCH_OBJ);

            return (!$pageCount) ? $data : $count;
        }
    }

    /**
     * @param $forMenu
     * @param $countPage
     *
     * @return array|bool|int
     */
    public function showAllPages($forMenu = true, $countPage = false)
    {

        $statut = $forMenu ? ' statut = 1 ' : ' statut >= 1 ';
        $sql = 'SELECT * FROM appoe_plugin_cms WHERE type = "PAGE" AND ' . $statut . ' ORDER BY created_at ASC';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            $data = $stmt->fetchAll(\PDO::FETCH_OBJ);

            return (!$countPage) ? $data : $count;
        }
    }

    /**
     * @return bool
     */
    public function save()
    {

        $sql = 'INSERT INTO appoe_plugin_cms (type, name, description, slug, created_at) 
                VALUES (:type, :name, :description, :slug, CURDATE())';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':slug', $this->slug);
        $stmt->execute();

        $this->id = $this->dbh->lastInsertId();

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

        $sql = 'UPDATE appoe_plugin_cms SET type = :type, name = :name, description = :description, slug = :slug, statut = :statut WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':slug', $this->slug);
        $stmt->bindParam(':statut', $this->statut);
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

        $this->statut = 0;
        if ($this->update()) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * @param bool $forUpdate
     *
     * @return bool
     */
    public function notExist($forUpdate = false)
    {

        $sql = 'SELECT id, name, slug FROM appoe_plugin_cms WHERE name = :name OR slug = :slug';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':slug', $this->slug);
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