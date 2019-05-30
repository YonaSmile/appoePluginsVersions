<?php

namespace App\Plugin\Cms;
class CmsMenu
{
    private $id;
    private $idCms;
    private $name;
    private $position = null;
    private $parentId;
    private $location;
    private $statut = 1;

    private $dbh = null;

    public function __construct($id = null)
    {
        if(is_null($this->dbh)) {
            $this->dbh = \App\DB::connect();
        }

        if (!is_null($id)) {
            $this->id = $id;
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
    public function getIdCms()
    {
        return $this->idCms;
    }

    /**
     * @param mixed $idCms
     */
    public function setIdCms($idCms)
    {
        $this->idCms = $idCms;
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
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @param mixed $parentId
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }


    public function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `appoe_plugin_cms_menu` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`),
                `idCms` VARCHAR(255) NOT NULL,
                `name` VARCHAR(100) NOT NULL,
                `parentId` INT(11) NOT NULL,
                `position` INT(11) NULL DEFAULT NULL,
                `location` INT(11) NOT NULL DEFAULT 1,
                UNIQUE  (`idCms`, `name`, `parentId`, `location`),
                `statut` TINYINT(1) NOT NULL DEFAULT 1,
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=11';

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

        $sql = 'SELECT * FROM appoe_plugin_cms_menu WHERE id = :id';

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
     * @param $byLocation
     *
     * @return array|bool
     */
    public function showAll($byLocation = false)
    {
        $locationCondition = is_numeric($byLocation) ? ' AND acm.location = ' . $byLocation . ' ' : '';

        $sql = 'SELECT DISTINCT acm.id, acm.idCms, acm.name, acm.parentId, acm.position, acm.location, acm.statut, acm.updated_at, 
        ac.type, ac.description, ac.slug 
        FROM appoe_plugin_cms_menu AS acm 
        LEFT JOIN appoe_plugin_cms AS ac 
        ON (acm.idCms = ac.id) 
        WHERE (ac.statut = 1 OR acm.idCms like "http%" OR acm.idCms like "%#%" OR acm.idCms REGEXP "^[a-zA-Z0-9/-]+$")' . $locationCondition . ' 
        ORDER BY acm.parentId ASC, acm.position ASC';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            $data = $stmt->fetchAll(\PDO::FETCH_OBJ);

            return $data;
        }
    }

    /**
     * @return bool
     */
    public function save()
    {

        $sql = 'INSERT INTO appoe_plugin_cms_menu (idCms, name, position, parentId, location) 
                VALUES (:idCms, :name, :position, :parentId, :location)';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':idCms', $this->idCms);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':position', $this->position);
        $stmt->bindParam(':parentId', $this->parentId);
        $stmt->bindParam(':location', $this->location);
        $stmt->execute();

        $cmsId = $this->dbh->lastInsertId();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            $this->setId($cmsId);

            return true;
        }
    }

    /**
     * @return bool
     */
    public function update()
    {

        $sql = 'UPDATE appoe_plugin_cms_menu SET idCms = :idCms, name = :name, position = :position, parentId = :parentId, location = :location, statut = :statut WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':idCms', $this->idCms);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':position', $this->position);
        $stmt->bindParam(':parentId', $this->parentId);
        $stmt->bindParam(':location', $this->location);
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
        $sql = 'DELETE FROM appoe_plugin_cms_menu WHERE id = :id';

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
     * @return array|bool
     */
    public function existParent()
    {
        $sql = 'SELECT * FROM appoe_plugin_cms_menu WHERE id = :id AND location = :location';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':id', $this->parentId);
        $stmt->bindParam(':location', $this->location);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count == 1) {
                return true;
            } else {
                return false;
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