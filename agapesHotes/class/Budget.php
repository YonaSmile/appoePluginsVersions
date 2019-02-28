<?php

namespace App\Plugin\AgapesHotes;
class Budget
{

    private $id;
    private $siteId;
    private $year;
    private $month;
    public $ca = 0;
    public $conso = 0;
    public $personnel = 0;
    public $fraisGeneraux = 0;
    public $caVariable = 0;
    public $masseDeFrais = 0;
    public $retourAchat = 0;
    public $retourFraisSiege = 0;
    private $status = 1;
    private $userId;
    private $createdAt;
    private $updated_at;

    public $resultatExploitation;
    public $resultats;
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
    public function getCa()
    {
        return $this->ca;
    }

    /**
     * @param mixed $ca
     */
    public function setCa($ca)
    {
        $this->ca = $ca;
    }

    /**
     * @return mixed
     */
    public function getConso()
    {
        return $this->conso;
    }

    /**
     * @param mixed $conso
     */
    public function setConso($conso)
    {
        $this->conso = $conso;
    }

    /**
     * @return mixed
     */
    public function getPersonnel()
    {
        return $this->personnel;
    }

    /**
     * @param mixed $personnel
     */
    public function setPersonnel($personnel)
    {
        $this->personnel = $personnel;
    }

    /**
     * @return mixed
     */
    public function getFraisGeneraux()
    {
        return $this->fraisGeneraux;
    }

    /**
     * @param mixed $fraisGeneraux
     */
    public function setFraisGeneraux($fraisGeneraux)
    {
        $this->fraisGeneraux = $fraisGeneraux;
    }

    /**
     * @return int
     */
    public function getCaVariable()
    {
        return $this->caVariable;
    }

    /**
     * @param int $caVariable
     */
    public function setCaVariable($caVariable)
    {
        $this->caVariable = $caVariable;
    }

    /**
     * @return int
     */
    public function getMasseDeFrais()
    {
        return $this->masseDeFrais;
    }

    /**
     * @param int $masseDeFrais
     */
    public function setMasseDeFrais($masseDeFrais)
    {
        $this->masseDeFrais = $masseDeFrais;
    }

    /**
     * @return mixed
     */
    public function getRetourAchat()
    {
        return $this->retourAchat;
    }

    /**
     * @param mixed $retourAchat
     */
    public function setRetourAchat($retourAchat)
    {
        $this->retourAchat = $retourAchat;
    }

    /**
     * @return mixed
     */
    public function getRetourFraisSiege()
    {
        return $this->retourFraisSiege;
    }

    /**
     * @param mixed $retourFraisSiege
     */
    public function setRetourFraisSiege($retourFraisSiege)
    {
        $this->retourFraisSiege = $retourFraisSiege;
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
     * @return mixed
     */
    public function getResultatExploitation()
    {
        return $this->resultatExploitation;
    }

    /**
     * @param mixed $resultatExploitation
     */
    public function setResultatExploitation($resultatExploitation)
    {
        $this->resultatExploitation = $resultatExploitation;
    }

    /**
     * @return mixed
     */
    public function getResultats()
    {
        return $this->resultats;
    }

    /**
     * @param mixed $resultats
     */
    public function setResultats($resultats)
    {
        $this->resultats = $resultats;
    }

    public function createTable()
    {

        $sql = 'CREATE TABLE IF NOT EXISTS `appoe_plugin_agapesHotes_budget` (
  				`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  				PRIMARY KEY (`id`),
                `site_id` int(11) UNSIGNED NOT NULL,
                `year` year NOT NULL,
                `month` tinyint(4) NOT NULL,
                `ca` decimal(8,3) UNSIGNED NOT NULL,
                `conso` decimal(8,3) UNSIGNED NOT NULL,
                `personnel` decimal(8,3) UNSIGNED NOT NULL,
                `frais_generaux` decimal(8,3) UNSIGNED NOT NULL,
                `caVariable` decimal(8,3) UNSIGNED NOT NULL,
                `masseDeFrais` decimal(8,3) UNSIGNED NOT NULL,
                `retourAchat` decimal(8,3) UNSIGNED NOT NULL,
                `retourFraisSiege` decimal(8,3) UNSIGNED NOT NULL,
                UNIQUE (`site_id`,`year`, `month`),
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

        $sql = 'SELECT * FROM appoe_plugin_agapesHotes_budget WHERE id = :id';

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
    public function showBySite()
    {
        $sqlAdd = '';
        if (!empty($this->month)) {
            $sqlAdd = ' AND month = :month ';
        }

        $sql = 'SELECT * FROM appoe_plugin_agapesHotes_budget WHERE site_id = :siteId AND year = :year ' . $sqlAdd . ' AND status = :status ORDER BY updated_at DESC';
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
            if (!empty($this->month)) {
                if ($count == 1) {

                    $row = $stmt->fetch(\PDO::FETCH_OBJ);
                    $this->feed($row);
                    $this->retourAchat = $this->conso * 0.065;
                    $this->retourFraisSiege = $this->ca * 0.04;
                    $this->resultatExploitation = $this->ca - ($this->conso + $this->personnel + $this->fraisGeneraux);
                    $this->resultats = $this->resultatExploitation + $this->retourAchat + $this->retourFraisSiege;

                    return true;

                } else {

                    return false;
                }
            } else {
                while ($row = $stmt->fetch(\PDO::FETCH_OBJ)) {
                    $this->ca += $row->ca;
                    $this->conso += $row->conso;
                    $this->personnel += $row->personnel;
                    $this->fraisGeneraux += $row->frais_generaux;
                    /*$this->retourAchat += $row->retourAchat;
                    $this->retourFraisSiege += $row->retourFraisSiege;*/
                }
                $this->retourAchat = $this->conso * 0.065;
                $this->retourFraisSiege = $this->ca * 0.04;
                $this->resultatExploitation = $this->ca - ($this->conso + $this->personnel + $this->fraisGeneraux);
                $this->resultats = $this->resultatExploitation + $this->retourAchat + $this->retourFraisSiege;
                return true;
            }
        }
    }

    /**
     * @param $countPrixPrestations
     * @return array|bool
     */
    public function showAll($countPrixPrestations = false)
    {

        $sql = 'SELECT * FROM appoe_plugin_agapesHotes_budget WHERE site_id = :siteId AND year = :year AND month = :month AND status = :status ORDER BY updated_at DESC';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':siteId', $this->siteId);
        $stmt->bindParam(':year', $this->year);
        $stmt->bindParam(':month', $this->month);
        $stmt->bindParam(':status', $this->status);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            return !$countPrixPrestations ? $stmt->fetchAll(\PDO::FETCH_OBJ) : $count;
        }
    }

    /**
     * @return bool
     */
    public function save()
    {
        $this->userId = getUserIdSession();
        $sql = 'INSERT INTO appoe_plugin_agapesHotes_budget (site_id, year, month, ca, conso, personnel, fraisGeneraux, caVariable, masseDeFrais, status, userId, created_at) 
                VALUES (:siteId, :year, :month, :ca, :conso, :personnel, :fraisGeneraux,  :caVariable, :masseDeFrais, :status, :userId, CURDATE())';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':siteId', $this->siteId);
        $stmt->bindParam(':year', $this->year);
        $stmt->bindParam(':month', $this->month);
        $stmt->bindParam(':ca', $this->ca);
        $stmt->bindParam(':conso', $this->conso);
        $stmt->bindParam(':personnel', $this->personnel);
        $stmt->bindParam(':fraisGeneraux', $this->fraisGeneraux);
        $stmt->bindParam(':caVariable', $this->caVariable);
        $stmt->bindParam(':masseDeFrais', $this->masseDeFrais);
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
        $sql = 'UPDATE appoe_plugin_agapesHotes_budget 
        SET site_id = :siteId, year = :year, month = :month, ca = :ca, conso = :conso, personnel = :personnel, fraisGeneraux = :fraisGeneraux, caVariable = :caVariable, masseDeFrais = :masseDeFrais, status = :status, userId = :userId WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':siteId', $this->siteId);
        $stmt->bindParam(':year', $this->year);
        $stmt->bindParam(':month', $this->month);
        $stmt->bindParam(':ca', $this->ca);
        $stmt->bindParam(':conso', $this->conso);
        $stmt->bindParam(':personnel', $this->personnel);
        $stmt->bindParam(':fraisGeneraux', $this->fraisGeneraux);
        $stmt->bindParam(':caVariable', $this->caVariable);
        $stmt->bindParam(':masseDeFrais', $this->masseDeFrais);
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

        $sql = 'SELECT id FROM appoe_plugin_agapesHotes_budget 
        WHERE site_id = :siteId AND  year = :year AND month = :month';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':siteId', $this->siteId);
        $stmt->bindParam(':year', $this->year);
        $stmt->bindParam(':month', $this->month);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count >= 1) {
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

    /**
     * Clean class attributs
     */
    public function clean()
    {
        foreach (get_object_vars($this) as $attribut => $value) {
            $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $attribut)));

            if (is_callable(array($this, $method))) {
                $this->$method(null);
            }
        }
    }
}