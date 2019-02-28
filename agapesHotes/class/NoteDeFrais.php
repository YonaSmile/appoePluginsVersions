<?php

namespace App\Plugin\AgapesHotes;
class NoteDeFrais
{

    private $id;
    private $siteId;
    private $employeId;
    private $day;
    private $month;
    private $year;
    private $type;
    private $code;
    private $nom;
    private $motif;
    private $affectation;
    private $commentaire = null;
    private $montantHt;
    private $tva;
    private $montantTtc;
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
     * @param mixed $employe_id
     */
    public function setEmployeId($employe_id)
    {
        $this->employeId = $employe_id;
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
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
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
    public function getMotif()
    {
        return $this->motif;
    }

    /**
     * @param mixed $motif
     */
    public function setMotif($motif)
    {
        $this->motif = $motif;
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
     * @return mixed
     */
    public function getTva()
    {
        return $this->tva;
    }

    /**
     * @param mixed $tva
     */
    public function setTva($tva)
    {
        $this->tva = $tva;
    }

    /**
     * @return mixed
     */
    public function getMontantTtc()
    {
        return $this->montantTtc;
    }

    /**
     * @param mixed $montantTtc
     */
    public function setMontantTtc($montantTtc)
    {
        $this->montantTtc = $montantTtc;
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

        $sql = 'CREATE TABLE IF NOT EXISTS `appoe_plugin_agapesHotes_note_frais` (
  				`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  				PRIMARY KEY (`id`),
  				`site_id` int(11) UNSIGNED NOT NULL,
                `employe_id` int(11) UNSIGNED NOT NULL,
                `day` varchar(2) NOT NULL,
                `month` varchar(2) NOT NULL,
                `year` varchar(4) NOT NULL,
                `type` TINYINT(4) UNSIGNED NOT NULL,
                `code` varchar(10) NOT NULL,
                `nom` varchar(255) NOT NULL,
                `motif` varchar(255) NOT NULL,
                `affectation` int(11) UNSIGNED NOT NULL,
                `commentaire` varchar(255) NULL,
                `montantHt` decimal(7,2) UNSIGNED NOT NULL,
                `tva` decimal(4,2) UNSIGNED NOT NULL,
                `montantTtc` decimal(7,2) UNSIGNED NOT NULL,
                UNIQUE (`site_id`, `employe_id`, `type`, `nom`, `day`, `month`, `year`),
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

        $sql = 'SELECT * FROM appoe_plugin_agapesHotes_note_frais WHERE id = :id';

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

        $sql = 'SELECT * FROM appoe_plugin_agapesHotes_note_frais 
        WHERE site_id = :siteId AND status = :status ORDER BY created_at ASC';

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
        $sql = 'SELECT * FROM appoe_plugin_agapesHotes_note_frais 
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
    public function showByDateAndAffectation($countNoteDeFrais = false)
    {

        $addSql = !empty($this->month) ? ' AND month = :month ' : '';
        $sql = 'SELECT * FROM appoe_plugin_agapesHotes_note_frais 
        WHERE affectation  = :affectation AND year = :year ' . $addSql . ' AND status = :status';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':affectation ', $this->affectation);
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

        $sql = 'SELECT * FROM appoe_plugin_agapesHotes_note_frais 
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

        $sql = 'SELECT * FROM appoe_plugin_agapesHotes_note_frais 
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

        $sql = 'INSERT INTO appoe_plugin_agapesHotes_note_frais (site_id, employe_id, day, month, year, type, code, nom, motif, affectation, commentaire, montantHt, tva, montantTtc, status, userId, created_at) 
                VALUES (:siteId, :employe_id, :day, :month, :year, :type, :code, :nom, :motif, :affectation, :commentaire, :montantHt, :tva, :montantTtc, :status, :userId, CURDATE())';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':siteId', $this->siteId);
        $stmt->bindParam(':employe_id', $this->employeId);
        $stmt->bindParam(':day', $this->day);
        $stmt->bindParam(':month', $this->month);
        $stmt->bindParam(':year', $this->year);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':code', $this->code);
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':motif', $this->motif);
        $stmt->bindParam(':affectation', $this->affectation);
        $stmt->bindParam(':commentaire', $this->commentaire);
        $stmt->bindParam(':montantHt', $this->montantHt);
        $stmt->bindParam(':tva', $this->tva);
        $stmt->bindParam(':montantTtc', $this->montantTtc);
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
        $sql = 'UPDATE appoe_plugin_agapesHotes_note_frais 
        SET day = :day, type = :type, code = :code, nom = :nom, motif = :motif, affectation = :affectation, commentaire = :commentaire, montantHt = :montantHt, tva = :tva, montantTtc = :montantTtc, status = :status, userId = :userId WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':day', $this->day);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':code', $this->code);
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':motif', $this->motif);
        $stmt->bindParam(':affectation', $this->affectation);
        $stmt->bindParam(':commentaire', $this->commentaire);
        $stmt->bindParam(':montantHt', $this->montantHt);
        $stmt->bindParam(':tva', $this->tva);
        $stmt->bindParam(':montantTtc', $this->montantTtc);
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

        $sql = 'DELETE FROM appoe_plugin_agapesHotes_note_frais WHERE id = :id';

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

        $sql = 'SELECT * FROM appoe_plugin_agapesHotes_note_frais 
        WHERE site_id = :siteId AND employe_id = :employe_id AND type = :type AND nom = :nom AND day = :day AND month = :month AND year = :year';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':siteId', $this->siteId);
        $stmt->bindParam(':employe_id', $this->employeId);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':day', $this->day);
        $stmt->bindParam(':month', $this->month);
        $stmt->bindParam(':year', $this->year);
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