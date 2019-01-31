<?php

namespace App\Plugin\AgapesHotes;
class NoteIk
{

    private $id;
    private $siteId;
    private $employeId;
    private $day;
    private $month;
    private $year;
    private $typeVehicule;
    private $puissance;
    private $taux;
    private $objetTrajet = null;
    private $trajet;
    private $km;
    private $affectation;
    private $commentaire = null;
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

        $this->userId = getUserIdSession();

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
    public function getEmployeId()
    {
        return $this->employeId;
    }

    /**
     * @param mixed $employeId
     */
    public function setEmployeId($employeId)
    {
        $this->employeId = $employeId;
    }

    /**
     * @return mixed
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param mixed $day
     */
    public function setDay($day)
    {
        $this->day = $day;
    }

    /**
     * @return mixed
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * @param mixed $month
     */
    public function setMonth($month)
    {
        $this->month = $month;
    }

    /**
     * @return mixed
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param mixed $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * @return mixed
     */
    public function getTypeVehicule()
    {
        return $this->typeVehicule;
    }

    /**
     * @param mixed $typeVehicule
     */
    public function setTypeVehicule($typeVehicule)
    {
        $this->typeVehicule = $typeVehicule;
    }

    /**
     * @return mixed
     */
    public function getPuissance()
    {
        return $this->puissance;
    }

    /**
     * @param mixed $puissance
     */
    public function setPuissance($puissance)
    {
        $this->puissance = $puissance;
    }

    /**
     * @return mixed
     */
    public function getTaux()
    {
        return $this->taux;
    }

    /**
     * @param mixed $taux
     */
    public function setTaux($taux)
    {
        $this->taux = $taux;
    }

    /**
     * @return null
     */
    public function getObjetTrajet()
    {
        return $this->objetTrajet;
    }

    /**
     * @param null $objetTrajet
     */
    public function setObjetTrajet($objetTrajet)
    {
        $this->objetTrajet = $objetTrajet;
    }

    /**
     * @return mixed
     */
    public function getTrajet()
    {
        return $this->trajet;
    }

    /**
     * @param mixed $trajet
     */
    public function setTrajet($trajet)
    {
        $this->trajet = $trajet;
    }

    /**
     * @return mixed
     */
    public function getKm()
    {
        return $this->km;
    }

    /**
     * @param mixed $km
     */
    public function setKm($km)
    {
        $this->km = $km;
    }

    /**
     * @return mixed
     */
    public function getAffectation()
    {
        return $this->affectation;
    }

    /**
     * @param mixed $affectation
     */
    public function setAffectation($affectation)
    {
        $this->affectation = $affectation;
    }

    /**
     * @return mixed
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * @param mixed $commentaire
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;
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
        $sql = 'CREATE TABLE IF NOT EXISTS `appoe_plugin_agapesHotes_note_ik` (
  				`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  				PRIMARY KEY (`id`),
  				`site_id` int(11) UNSIGNED NOT NULL,
                `employe_id` int(11) UNSIGNED NOT NULL,
                `day` varchar(2) NOT NULL,
                `month` varchar(2) NOT NULL,
                `year` varchar(4) NOT NULL,
                `type_vehicule` TINYINT(4) NOT NULL,
                `puissance` varchar(10) NOT NULL,
                `taux` decimal(5,3) UNSIGNED NOT NULL,
                `objet_du_trajet` varchar(255) NULL DEFAULT NULL,
                `trajet` varchar(255) NOT NULL,
                `km` decimal(5,1) UNSIGNED NOT NULL,
                `affectation` int(11) UNSIGNED NOT NULL,
                `commentaire` varchar(255) NULL,
                `montantHt` decimal(7,2) UNSIGNED NOT NULL,
                UNIQUE (`site_id`, `employe_id`, `day`, `month`, `year`, `puissance`, `trajet`),
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

        $sql = 'SELECT * FROM appoe_plugin_agapesHotes_note_ik WHERE id = :id';

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

        $sql = 'SELECT * FROM appoe_plugin_agapesHotes_note_ik WHERE site_id = :siteId AND status = :status ORDER BY created_at ASC';

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
     * @param bool $countNoteDeFrais
     * @return array|int|bool
     */
    public function showByDate($countNoteDeFrais = false)
    {
        $addSql = !empty($this->month) ? ' AND month = :month ' : '';
        $sql = 'SELECT * FROM appoe_plugin_agapesHotes_note_ik 
        WHERE site_id = :siteId AND year = :year ' . $addSql . ' AND status = :status';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':siteId', $this->siteId);
        $stmt->bindParam(':year', $this->year);
        if (!empty($this->month)) {
            $stmt->bindParam(':month', $this->month);
        }
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
     * @param bool $countNoteDeFrais
     * @return array|int|bool
     */
    public function showByDateAndEmploye($countNoteDeFrais = false)
    {

        $sql = 'SELECT * FROM appoe_plugin_agapesHotes_note_ik 
        WHERE site_id = :siteId AND employe_id = :employeId AND year = :year AND month = :month AND status = :status';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':siteId', $this->siteId);
        $stmt->bindParam(':employeId', $this->employeId);
        $stmt->bindParam(':year', $this->year);
        $stmt->bindParam(':month', $this->month);
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

        $sql = 'SELECT * FROM appoe_plugin_agapesHotes_note_ik 
        WHERE status = :status ORDER BY updated_at DESC';
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

        $sql = 'INSERT INTO appoe_plugin_agapesHotes_note_ik (site_id, employe_id, day, month, year, type_vehicule, puissance, taux, objet_du_trajet, trajet, km, affectation, commentaire, montantHt, status, userId, created_at) 
                VALUES (:siteId, :employe_id, :day, :month, :year, :type_vehicule, :puissance, :taux, :objet_du_trajet, :trajet, :km, :affectation, :commentaire, :montantHt, :status, :userId, CURDATE())';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':siteId', $this->siteId);
        $stmt->bindParam(':employe_id', $this->employeId);
        $stmt->bindParam(':day', $this->day);
        $stmt->bindParam(':month', $this->month);
        $stmt->bindParam(':year', $this->year);
        $stmt->bindParam(':type_vehicule', $this->typeVehicule);
        $stmt->bindParam(':puissance', $this->puissance);
        $stmt->bindParam(':taux', $this->taux);
        $stmt->bindParam(':objet_du_trajet', $this->objetTrajet);
        $stmt->bindParam(':trajet', $this->trajet);
        $stmt->bindParam(':km', $this->km);
        $stmt->bindParam(':affectation', $this->affectation);
        $stmt->bindParam(':commentaire', $this->commentaire);
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
        $sql = 'UPDATE appoe_plugin_agapesHotes_note_ik 
        SET day = :day, type_vehicule = :type_vehicule, puissance = :puissance, taux = :taux, objet_du_trajet = :objet_du_trajet, trajet = :trajet, km = :km, affectation = :affectation, commentaire = :commentaire, montantHt = :montantHt, status = :status, userId = :userId 
        WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':day', $this->day);
        $stmt->bindParam(':type_vehicule', $this->typeVehicule);
        $stmt->bindParam(':puissance', $this->puissance);
        $stmt->bindParam(':taux', $this->taux);
        $stmt->bindParam(':objet_du_trajet', $this->objetTrajet);
        $stmt->bindParam(':trajet', $this->trajet);
        $stmt->bindParam(':km', $this->km);
        $stmt->bindParam(':affectation', $this->affectation);
        $stmt->bindParam(':commentaire', $this->commentaire);
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

        $sql = 'DELETE FROM appoe_plugin_agapesHotes_note_ik WHERE id = :id';

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

        $sql = 'SELECT * FROM appoe_plugin_agapesHotes_note_ik 
        WHERE site_id = :siteId AND employe_id = :employe_id AND day = :day AND month = :month AND year = :year AND puissance = :puissance AND trajet = :trajet';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':siteId', $this->siteId);
        $stmt->bindParam(':employe_id', $this->employeId);
        $stmt->bindParam(':day', $this->day);
        $stmt->bindParam(':month', $this->month);
        $stmt->bindParam(':year', $this->year);
        $stmt->bindParam(':puissance', $this->puissance);
        $stmt->bindParam(':trajet', $this->trajet);
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