<?php

namespace App\Plugin\AgapesHotes;
class VivreCrue
{

    private $id;
    private $idCourse;
    private $etablissementId;
    private $prixHTunite;
    private $quantite;
    private $tauxTVA;
    private $total;
    private $date;
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
    public function getIdCourse()
    {
        return $this->idCourse;
    }

    /**
     * @param mixed $idCourse
     */
    public function setIdCourse($idCourse)
    {
        $this->idCourse = $idCourse;
    }

    /**
     * @return mixed
     */
    public function getEtablissementId()
    {
        return $this->etablissementId;
    }

    /**
     * @param mixed $etablissementId
     */
    public function setEtablissementId($etablissementId)
    {
        $this->etablissementId = $etablissementId;
    }

    /**
     * @return mixed
     */
    public function getPrixHTunite()
    {
        return $this->prixHTunite;
    }

    /**
     * @param mixed $prixHTunite
     */
    public function setPrixHTunite($prixHTunite)
    {
        $this->prixHTunite = $prixHTunite;
    }

    /**
     * @return mixed
     */
    public function getQuantite()
    {
        return $this->quantite;
    }

    /**
     * @param mixed $quantite
     */
    public function setQuantite($quantite)
    {
        $this->quantite = $quantite;
    }

    /**
     * @return mixed
     */
    public function getTauxTVA()
    {
        return $this->tauxTVA;
    }

    /**
     * @param mixed $tauxTVA
     */
    public function setTauxTVA($tauxTVA)
    {
        $this->tauxTVA = $tauxTVA;
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param mixed $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
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

    public function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `appoe_plugin_agapeshotes_vivre_crue` (
  				`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  				PRIMARY KEY (`id`),
  				`etablissement_id` int(11) UNSIGNED NOT NULL,
  				`idCourse` int(11) UNSIGNED NOT NULL,
                `prixHTunite` decimal(7,2) UNSIGNED NOT NULL,
                `quantite` int(11) UNSIGNED NOT NULL,
                `tauxTVA` decimal(7,2) UNSIGNED NOT NULL,
                `total` decimal(7,2) UNSIGNED NOT NULL,
                `date` date NOT NULL,
                UNIQUE (`etablissement_id`,`idCourse`, `date`),
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

        $sql = 'SELECT * FROM appoe_plugin_agapeshotes_vivre_crue WHERE id = :id';

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
     * @param $countVivreCrue
     * @return array|bool
     */
    public function showAll($countVivreCrue = false)
    {

        $sql = 'SELECT * FROM appoe_plugin_agapeshotes_vivre_crue WHERE etablissement_id = :etablissementId AND status = :status ORDER BY updated_at DESC';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':etablissementId', $this->etablissementId);
        $stmt->bindParam(':status', $this->status);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            return !$countVivreCrue ? $stmt->fetchAll(\PDO::FETCH_OBJ) : $count;
        }
    }

    /**
     * @param $countVivreCrueByDate
     * @return bool|array
     */
    public function showAllByMonth($countVivreCrueByDate = false)
    {

        $sql = 'SELECT * FROM appoe_plugin_agapeshotes_vivre_crue WHERE etablissement_id = :etablissementId AND MONTH(date) = :date';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':etablissementId', $this->etablissementId);
        $stmt->bindParam(':date', $this->date);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            return !$countVivreCrueByDate ? $stmt->fetchAll(\PDO::FETCH_OBJ) : $count;
        }
    }

    /**
     * @return bool
     */
    public function save()
    {
        $this->userId = getUserIdSession();
        $sql = 'INSERT INTO appoe_plugin_agapeshotes_vivre_crue (etablissement_id, idCourse, prixHTunite, quantite, tauxTVA, total, date, status, userId, created_at) 
                VALUES (:etablissementId, :idCourse, :prixHTunite, :quantite, :tauxTVA, :total, :date, :status, :userId, CURDATE())';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':etablissementId', $this->etablissementId);
        $stmt->bindParam(':idCourse', $this->idCourse);
        $stmt->bindParam(':prixHTunite', $this->prixHTunite);
        $stmt->bindParam(':quantite', $this->quantite);
        $stmt->bindParam(':tauxTVA', $this->tauxTVA);
        $stmt->bindParam(':total', $this->total);
        $stmt->bindParam(':date', $this->date);
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
        $sql = 'UPDATE appoe_plugin_agapeshotes_vivre_crue 
        SET etablissement_id = :etablissementId, idCourse = :idCourse, prixHTunite = :prixHTunite, quantite = :quantite, tauxTVA = :tauxTVA, total = :total, date = :date, status = :status, userId = :userId WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':etablissementId', $this->etablissementId);
        $stmt->bindParam(':idCourse', $this->idCourse);
        $stmt->bindParam(':prixHTunite', $this->prixHTunite);
        $stmt->bindParam(':quantite', $this->quantite);
        $stmt->bindParam(':tauxTVA', $this->tauxTVA);
        $stmt->bindParam(':total', $this->total);
        $stmt->bindParam(':date', $this->date);
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

        $sql = 'SELECT idFROM appoe_plugin_agapeshotes_vivre_crue 
        WHERE etablissement_id = :etablissementId AND idCourse = :idCourse AND date = :date';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':etablissementId', $this->etablissementId);
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