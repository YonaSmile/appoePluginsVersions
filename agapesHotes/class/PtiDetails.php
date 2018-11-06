<?php

namespace App\Plugin\AgapesHotes;
class PtiDetails
{

    private $id;
    private $ptiId;
    private $numeroJour;
    private $nbHeures;
    private $horaires = null;
    private $status = 1;
    private $userId;
    private $createdAt;
    private $updated_at;

    private $data = null;
    private $dbh = null;

    public function __construct($ptiId = null)
    {
        if (is_null($this->dbh)) {
            $this->dbh = \App\DB::connect();
        }

        if (!is_null($ptiId)) {
            $this->ptiId = $ptiId;
            $this->data = $this->showAll();
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
    public function getPtiId()
    {
        return $this->ptiId;
    }

    /**
     * @param mixed $ptiId
     */
    public function setPtiId($ptiId)
    {
        $this->ptiId = $ptiId;
    }

    /**
     * @return mixed
     */
    public function getNumeroJour()
    {
        return $this->numeroJour;
    }

    /**
     * @param mixed $numeroJour
     */
    public function setNumeroJour($numeroJour)
    {
        $this->numeroJour = $numeroJour;
    }

    /**
     * @return mixed
     */
    public function getNbHeures()
    {
        return $this->nbHeures;
    }

    /**
     * @param mixed $nbHeures
     */
    public function setNbHeures($nbHeures)
    {
        $this->nbHeures = $nbHeures;
    }

    /**
     * @return mixed
     */
    public function getHoraires()
    {
        return $this->horaires;
    }

    /**
     * @param mixed $horaires
     */
    public function setHoraires($horaires)
    {
        $this->horaires = $horaires;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
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

    /**
     * @return array|bool|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array|bool|null $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }


    public function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `appoe_plugin_agapeshotes_pti_details` (
  				`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  				PRIMARY KEY (`id`),
                `pti_id` int(11) UNSIGNED NOT NULL,
                `numeroJour` tinyint(1) UNSIGNED NOT NULL,
                `nbHeures` decimal (7,2) NOT NULL,
                `horaires` varchar (250) NULL DEFAULT NULL,
                UNIQUE (`pti_id`,`numeroJour`),
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

        $sql = 'SELECT * FROM appoe_plugin_agapeshotes_pti_details WHERE id = :id';

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
     * @param $countPti
     * @return array|bool
     */
    public function showAll($countPti = false)
    {

        $sql = 'SELECT * FROM appoe_plugin_agapeshotes_pti_details WHERE pti_id = :ptiId AND status = :status ORDER BY updated_at DESC';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':ptiId', $this->ptiId);
        $stmt->bindParam(':status', $this->status);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            return !$countPti ? $stmt->fetchAll(\PDO::FETCH_OBJ) : $count;
        }
    }

    /**
     * @return bool
     */
    public function save()
    {
        $this->userId = getUserIdSession();
        $sql = 'INSERT INTO appoe_plugin_agapeshotes_pti_details (pti_id, numeroJour, nbHeures, horaires, status, userId, created_at) 
                VALUES (:ptiId, :numeroJour, :nbHeures, :horaires, :status, :userId, CURDATE())';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':ptiId', $this->ptiId);
        $stmt->bindParam(':numeroJour', $this->numeroJour);
        $stmt->bindParam(':nbHeures', $this->nbHeures);
        $stmt->bindParam(':horaires', $this->horaires);
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
     *
     */
    public function update()
    {
        $this->userId = getUserIdSession();
        $sql = 'UPDATE appoe_plugin_agapeshotes_pti_details 
        SET pti_id = :ptiId, numeroJour = :numeroJour, nbHeures = :nbHeures, horaires = :horaires,  status = :status, userId = :userId WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':ptiId', $this->ptiId);
        $stmt->bindParam(':numeroJour', $this->numeroJour);
        $stmt->bindParam(':nbHeures', $this->nbHeures);
        $stmt->bindParam(':horaires', $this->horaires);
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
    public function delete()
    {

        $this->status = 0;
        if ($this->update()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param bool $forUpdate
     * @return bool
     */
    public function notExist($forUpdate = false)
    {

        $sql = 'SELECT id FROM appoe_plugin_agapeshotes_pti_details WHERE pti_id = :ptiId AND employe_id = :employeId AND dateDebut = :dateDebut';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':ptiId', $this->ptiId);
        $stmt->bindParam(':employeId', $this->employeId);
        $stmt->bindParam(':dateDebut', $this->dateDebut);
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