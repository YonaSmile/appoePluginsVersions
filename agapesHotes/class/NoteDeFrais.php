<?php

namespace App\Plugin\AgapesHotes;
class NoteDeFrais
{

    private $id;
    private $siteId;
    private $nom;
    private $type;
    private $date;
    private $montantHt;
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
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
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
    public function getMontantHt()
    {
        return $this->montantHt;
    }

    /**
     * @param mixed $montantHt
     */
    public function setMontantHt($montantHt)
    {
        $this->montantHt = $montantHt;
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
        $sql = 'CREATE TABLE IF NOT EXISTS `appoe_plugin_agapeshotes_note_frais` (
  				`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  				PRIMARY KEY (`id`),
  				`site_id` int(11) UNSIGNED NOT NULL,
                `nom` varchar(255) NOT NULL,
                `type` varchar(255) NOT NULL,
                `date` date NOT NULL,
                `montantHt` decimal(7,2) UNSIGNED NOT NULL,
                UNIQUE (`site_id`, `nom`, `type`, `date`),
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

    public function createView()
    {
        $sql = 'CREATE VIEW totalNoteDeFraisDenree AS SELECT site_id, YEAR(date) AS annee, MONTH(date) AS mois, SUM(montantHt) AS totalHT 
        FROM appoe_plugin_agapeshotes_note_frais WHERE type = "Denrée Alimentaire" GROUP BY site_id;
        CREATE VIEW totalNoteDeFraisNonAlimentaire AS SELECT site_id, YEAR(date) AS annee, MONTH(date) AS mois, SUM(montantHt) AS totalHT 
        FROM appoe_plugin_agapeshotes_note_frais WHERE type = "Unique Entretien" GROUP BY site_id;';

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

        $sql = 'SELECT * FROM appoe_plugin_agapeshotes_note_frais WHERE id = :id';

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
     * @param $countNoteDeFrais
     * @return array|bool
     */
    public function showAllBySite($countNoteDeFrais = false)
    {

        $sql = 'SELECT * FROM appoe_plugin_agapeshotes_note_frais WHERE site_id = :siteId AND status = :status ORDER BY created_at ASC';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':siteId', $this->siteId);
        $stmt->bindParam(':status', $this->status);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            return !$countNoteDeFrais ? $stmt->fetchAll(\PDO::FETCH_OBJ) : $count;
        }
    }

    /**
     * @param $dateDebut
     * @param $dateFin
     * @param $countNoteDeFrais
     * @return array|bool
     */
    public function showByDate($dateDebut, $dateFin, $countNoteDeFrais = false)
    {

        $sql = 'SELECT * FROM appoe_plugin_agapeshotes_note_frais WHERE site_id = :siteId AND (date BETWEEN :dateDebut AND :dateFin) AND status = :status ORDER BY date ASC';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':siteId', $this->siteId);
        $stmt->bindParam(':dateDebut', $dateDebut);
        $stmt->bindParam(':dateFin', $dateFin);
        $stmt->bindParam(':status', $this->status);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            return !$countNoteDeFrais ? $stmt->fetchAll(\PDO::FETCH_OBJ) : $count;
        }
    }

    /**
     * @param $countNoteDeFrais
     * @return array|bool
     */
    public function showAll($countNoteDeFrais = false)
    {

        $sql = 'SELECT * FROM appoe_plugin_agapeshotes_note_frais WHERE status = :status ORDER BY updated_at DESC';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':status', $this->status);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            return !$countNoteDeFrais ? $stmt->fetchAll(\PDO::FETCH_OBJ) : $count;
        }
    }

    /**
     * @return bool
     */
    public function save()
    {
        $this->userId = getUserIdSession();
        $sql = 'INSERT INTO appoe_plugin_agapeshotes_note_frais (site_id, nom, type, date, montantHt, status, userId, created_at) 
                VALUES (:siteId, :nom, :type, :date, :montantHt, :status, :userId, CURDATE())';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':siteId', $this->siteId);
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':date', $this->date);
        $stmt->bindParam(':montantHt', $this->montantHt);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':userId', $this->userId);
        $stmt->execute();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {

            $this->id = $this->dbh->lastInsertId();
            return true;
        }
    }

    /**
     * @return bool
     */
    public function update()
    {
        $this->userId = getUserIdSession();
        $sql = 'UPDATE appoe_plugin_agapeshotes_note_frais SET nom = :nom, type = :type, date = :date, montantHt = :montantHt, status = :status, userId = :userId WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':date', $this->date);
        $stmt->bindParam(':montantHt', $this->montantHt);
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

        $sql = 'DELETE FROM appoe_plugin_agapeshotes_note_frais WHERE id = :id';

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

        $sql = 'SELECT id, nom FROM appoe_plugin_agapeshotes_note_frais WHERE site_id = :siteId AND type = :type AND nom = :nom AND date = :date';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':siteId', $this->siteId);
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':date', $this->date);;
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