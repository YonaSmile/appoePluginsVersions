<?php

namespace App\Plugin\AgapesHotes;
class SecteurAccess
{

    private $id;
    private $secteurUserId;
    private $secteurId;
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
    public function getSecteurUserId()
    {
        return $this->secteurUserId;
    }

    /**
     * @param mixed $secteurUserId
     */
    public function setSecteurUserId($secteurUserId)
    {
        $this->secteurUserId = $secteurUserId;
    }

    /**
     * @return mixed
     */
    public function getSecteurId()
    {
        return $this->secteurId;
    }

    /**
     * @param mixed $secteurId
     */
    public function setSecteurId($secteurId)
    {
        $this->secteurId = $secteurId;
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
        $sql = 'CREATE TABLE IF NOT EXISTS `appoe_plugin_agapeshotes_secteurs_access` (
  				`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  				PRIMARY KEY (`id`),
                `secteurUserId` int(11) UNSIGNED NOT NULL,
                `secteur_id` int(11) UNSIGNED NOT NULL,
                UNIQUE (`secteurUserId`),
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

        $sql = 'SELECT * FROM appoe_plugin_agapeshotes_secteurs_access WHERE id = :id';

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
     * @param $countSecteursAccess
     * @return array|bool
     */
    public function showAll($countSecteursAccess = false)
    {
        $sql = 'SELECT * FROM appoe_plugin_agapeshotes_secteurs_access WHERE status = :status ORDER BY updated_at DESC';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':status', $this->status);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            return !$countSecteursAccess ? $stmt->fetchAll(\PDO::FETCH_OBJ) : $count;
        }
    }

    /**
     * @param $countSecteursAccess
     * @return array|bool
     */
    public function showAllBySecteur($countSecteursAccess = false)
    {
        $sql = 'SELECT * FROM appoe_plugin_agapeshotes_secteurs_access WHERE secteur_id = :secteurId AND status = :status ORDER BY updated_at DESC';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':secteurId', $this->secteurId);
        $stmt->bindParam(':status', $this->status);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            return !$countSecteursAccess ? $stmt->fetchAll(\PDO::FETCH_OBJ) : $count;
        }
    }

    /**
     * @return array|bool
     */
    public function showSecteurByUser()
    {

        $sql = 'SELECT SECTEURACCESS.id AS idSecteurAccess, SECTEUR.* 
        FROM appoe_plugin_agapeshotes_secteurs_access AS SECTEURACCESS
        INNER JOIN appoe_plugin_agapeshotes_secteurs AS SECTEUR
        ON(SECTEUR.id = SECTEURACCESS.secteur_id)
        WHERE SECTEURACCESS.secteurUserId = :secteurUserId AND SECTEUR.status = :status ORDER BY SECTEUR.updated_at DESC';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':secteurUserId', $this->secteurUserId);
        $stmt->bindParam(':status', $this->status);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count == 1) {
                return $stmt->fetch(\PDO::FETCH_OBJ);
            }
            return false;
        }
    }

    /**
     * @return bool
     */
    public function save()
    {
        $this->userId = getUserIdSession();
        $sql = 'INSERT INTO appoe_plugin_agapeshotes_secteurs_access (secteurUserId, secteur_id, status, userId, created_at) 
                VALUES (:secteurUserId, :secteurId, :status, :userId, CURDATE())';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':secteurUserId', $this->secteurUserId);
        $stmt->bindParam(':secteurId', $this->secteurId);
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
        $this->userId = getUserIdSession();
        $sql = 'UPDATE appoe_plugin_agapeshotes_secteurs_access SET secteurUserId = :secteurUserId, secteur_id = :secteurId, status = :status, userId = :userId WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':secteurUserId', $this->secteurUserId);
        $stmt->bindParam(':secteurId', $this->secteurId);
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
        $sql = 'DELETE FROM appoe_plugin_agapeshotes_secteurs_access WHERE id = :id';

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

        $sql = 'SELECT id FROM appoe_plugin_agapeshotes_secteurs_access WHERE secteurUserId = :secteurUserId';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':secteurUserId', $this->secteurUserId);
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