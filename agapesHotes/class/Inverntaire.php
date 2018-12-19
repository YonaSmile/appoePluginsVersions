<?php

namespace App\Plugin\AgapesHotes;
class Inverntaire
{

    private $id;
    private $siteId;
    private $date;
    private $totalNonAlimentaire;
    private $totalDenree;
    private $status = 1;
    private $userId;
    private $createdAt;
    private $updated_at;

    private $dbh = null;

    public function __construct($id = null)
    {
        if (is_null($this->dbh)) {
            $this->dbh = \App\DB::connect();
        }

        if (!is_null($id)) {
            $this->id = $id;
            $this->show();
        }
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param null $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getSiteId()
    {
        return $this->siteId;
    }

    /**
     * @param mixed $siteId
     */
    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getTotalNonAlimentaire()
    {
        return $this->totalNonAlimentaire;
    }

    /**
     * @param mixed $totalNonAlimentaire
     */
    public function setTotalNonAlimentaire($totalNonAlimentaire)
    {
        $this->totalNonAlimentaire = $totalNonAlimentaire;
    }

    /**
     * @return mixed
     */
    public function getTotalDenree()
    {
        return $this->totalDenree;
    }

    /**
     * @param mixed $totalDenree
     */
    public function setTotalDenree($totalDenree)
    {
        $this->totalDenree = $totalDenree;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
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
        return $this->updated_at;
    }

    /**
     * @param mixed $updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }

    public function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `appoe_plugin_agapesHotes_inventaire` (
  				`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  				PRIMARY KEY (`id`),
                `site_id` int(11) UNSIGNED NOT NULL,
                `date` date NOT NULL,
                `totalNonAlimentaire` decimal(7,2) UNSIGNED NOT NULL,
                `totalDenree` decimal(7,2) UNSIGNED NOT NULL,
                UNIQUE (`site_id`,`date`),
                `status` tinyint(4) UNSIGNED NOT NULL DEFAULT 1,
                `userId` int(11) UNSIGNED NOT NULL,
                `created_at` date NOT NULL,
                `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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

        $sql = 'SELECT * FROM appoe_plugin_agapesHotes_inventaire WHERE id = :id';

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
     * @param $countInventaire
     * @return array|bool
     */
    public function showAll($countInventaire = false)
    {

        $sql = 'SELECT * FROM appoe_plugin_agapesHotes_inventaire WHERE site_id = :siteId AND status = 1 ORDER BY updated_at DESC';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':siteId', $this->siteId);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            return !$countInventaire ? $stmt->fetchAll(\PDO::FETCH_OBJ) : $count;
        }
    }

    /**
     * @return bool
     */
    public function save()
    {
        $sql = 'INSERT INTO appoe_plugin_agapesHotes_inventaire (site_id, date, totalNonAlimentaire, totalDenree, status, userId, created_at) 
                VALUES (:siteId, :date, :totalNonAlimentaire, :totalDenree, :status, :userId, CURDATE())';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':siteId', $this->siteId);
        $stmt->bindParam(':date', $this->date);
        $stmt->bindParam(':totalNonAlimentaire', $this->totalNonAlimentaire);
        $stmt->bindParam(':totalDenree', $this->totalDenree);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':userId', $this->userId);
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

        $sql = 'UPDATE appoe_plugin_agapesHotes_inventaire 
        SET site_id = :siteId, date = :date, totalNonAlimentaire = :totalNonAlimentaire, totalDenree = :totalDenree, status = :status, userId = :userId WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':siteId', $this->siteId);
        $stmt->bindParam(':date', $this->date);
        $stmt->bindParam(':totalNonAlimentaire', $this->totalNonAlimentaire);
        $stmt->bindParam(':totalDenree', $this->totalDenree);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':userId', $this->userId);
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

        $this->status = 0;
        if ($this->update()) {
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

        $sql = 'SELECT id, nom FROM appoe_plugin_agapesHotes_inventaire 
        WHERE site_id = :siteId AND date = :date';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':siteId', $this->siteId);
        $stmt->bindParam(':date', $this->date);
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