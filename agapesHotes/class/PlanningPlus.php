<?php

namespace App\Plugin\AgapesHotes;
class PlanningPlus
{

    private $id;
    private $siteId;
    private $employeId;
    private $year;
    private $month;
    private $nbRepas = null;
    private $primeObjectif = null;
    private $primeExept = null;
    private $accompte = null;
    private $nbHeurePlus = null;
    private $nbJoursFeries = null;
    private $commentaires = null;
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
    public function getNbRepas()
    {
        return $this->nbRepas;
    }

    /**
     * @param mixed $nbRepas
     */
    public function setNbRepas($nbRepas)
    {
        $this->nbRepas = $nbRepas;
    }

    /**
     * @return mixed
     */
    public function getPrimeObjectif()
    {
        return $this->primeObjectif;
    }

    /**
     * @param mixed $primeObjectif
     */
    public function setPrimeObjectif($primeObjectif)
    {
        $this->primeObjectif = $primeObjectif;
    }

    /**
     * @return mixed
     */
    public function getPrimeExept()
    {
        return $this->primeExept;
    }

    /**
     * @param mixed $primeExept
     */
    public function setPrimeExept($primeExept)
    {
        $this->primeExept = $primeExept;
    }

    /**
     * @return mixed
     */
    public function getAccompte()
    {
        return $this->accompte;
    }

    /**
     * @param mixed $accompte
     */
    public function setAccompte($accompte)
    {
        $this->accompte = $accompte;
    }

    /**
     * @return mixed
     */
    public function getNbHeurePlus()
    {
        return $this->nbHeurePlus;
    }

    /**
     * @param mixed $nbHeurePlus
     */
    public function setNbHeurePlus($nbHeurePlus)
    {
        $this->nbHeurePlus = $nbHeurePlus;
    }

    /**
     * @return mixed
     */
    public function getNbJoursFeries()
    {
        return $this->nbJoursFeries;
    }

    /**
     * @param mixed $nbJoursFeries
     */
    public function setNbJoursFeries($nbJoursFeries)
    {
        $this->nbJoursFeries = $nbJoursFeries;
    }

    /**
     * @return mixed
     */
    public function getCommentaires()
    {
        return $this->commentaires;
    }

    /**
     * @param mixed $commentaires
     */
    public function setCommentaires($commentaires)
    {
        $this->commentaires = $commentaires;
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
        $sql = 'CREATE TABLE IF NOT EXISTS `appoe_plugin_agapesHotes_planning_plus` (
  				`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  				PRIMARY KEY (`id`),
  				`site_id` int(11) UNSIGNED NOT NULL,
                `employe_id` int(11) UNSIGNED NOT NULL,
                `year` year NOT NULL,
                `month` tinyint(4) NOT NULL,
                `nbRepas` int(11) NULL DEFAULT NULL,
                `primeObjectif` decimal(7,2) NULL DEFAULT NULL,
                `primeExept` decimal(7,2) NULL DEFAULT NULL,
                `accompte` decimal(7,2) NULL DEFAULT NULL,
                `nbHeurePlus`  decimal(7,2) NULL DEFAULT NULL,
                `nbJoursFeries`  decimal(7,2) NULL DEFAULT NULL,
                `commentaires` text NULL DEFAULT NULL,
                UNIQUE (`year`, `month`, `employe_id`, `site_id`),
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

        $sql = 'SELECT * FROM appoe_plugin_agapesHotes_planning_plus WHERE id = :id';

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
    public function showByDate()
    {

        $sql = 'SELECT * FROM appoe_plugin_agapesHotes_planning_plus WHERE site_id = :siteId AND employe_id = :employeId AND year = :year  AND month = :month';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':siteId', $this->siteId);
        $stmt->bindParam(':employeId', $this->employeId);
        $stmt->bindParam(':year', $this->year);
        $stmt->bindParam(':month', $this->month);
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
     * @param $countPlanningPlus
     * @return array|bool
     */
    public function showAll($countPlanningPlus = false)
    {

        $sql = 'SELECT * FROM appoe_plugin_agapesHotes_planning_plus 
        WHERE status = :status ORDER BY updated_at DESC';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':status', $this->status);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            return !$countPlanningPlus ? $stmt->fetchAll(\PDO::FETCH_OBJ) : $count;
        }
    }

    /**
     * @param $countPlanningPlus
     * @return bool|array
     */
    public function showAllByDate($countPlanningPlus = false)
    {

        $sql = 'SELECT * FROM appoe_plugin_agapesHotes_planning_plus WHERE site_id = :siteId AND year = :year  AND month = :month';

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
            return !$countPlanningPlus ? $stmt->fetchAll(\PDO::FETCH_OBJ) : $count;
        }
    }

    /**
     * @param $countPlanningPlus
     * @return array|bool
     */
    public function showAllBySite($countPlanningPlus = false)
    {

        $sql = 'SELECT * FROM appoe_plugin_agapesHotes_planning_plus 
        WHERE site_id = :siteId AND status = :status ORDER BY updated_at DESC';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':siteId', $this->siteId);
        $stmt->bindParam(':status', $this->status);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            return !$countPlanningPlus ? $stmt->fetchAll(\PDO::FETCH_OBJ) : $count;
        }
    }

    /**
     * @param $countPlanningPlus
     * @return array|bool
     */
    public function showAllByEmploye($countPlanningPlus = false)
    {

        $sql = 'SELECT * FROM appoe_plugin_agapesHotes_planning_plus 
        WHERE employe_id = :employeId AND status = :status ORDER BY updated_at DESC';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':employeId', $this->employeId);
        $stmt->bindParam(':status', $this->status);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            return !$countPlanningPlus ? $stmt->fetchAll(\PDO::FETCH_OBJ) : $count;
        }
    }

    /**
     * @return bool
     */
    public function save()
    {
        $this->userId = getUserIdSession();
        $sql = 'INSERT INTO appoe_plugin_agapesHotes_planning_plus (site_id, employe_id, year, month, nbRepas, primeObjectif, primeExept, accompte, nbHeurePlus, nbJoursFeries, commentaires, status, userId, created_at) 
                VALUES (:siteId, :employeId, :year, :month, :nbRepas, :primeObjectif, :primeExept, :accompte, :nbHeurePlus, :nbJoursFeries, :commentaires, :status, :userId, CURDATE())';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':siteId', $this->siteId);
        $stmt->bindParam(':employeId', $this->employeId);
        $stmt->bindParam(':year', $this->year);
        $stmt->bindParam(':month', $this->month);
        $stmt->bindParam(':nbRepas', $this->nbRepas);
        $stmt->bindParam(':primeObjectif', $this->primeObjectif);
        $stmt->bindParam(':primeExept', $this->primeExept);
        $stmt->bindParam(':accompte', $this->accompte);
        $stmt->bindParam(':nbHeurePlus', $this->nbHeurePlus);
        $stmt->bindParam(':nbJoursFeries', $this->nbJoursFeries);
        $stmt->bindParam(':commentaires', $this->commentaires);
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
        $sql = 'UPDATE appoe_plugin_agapesHotes_planning_plus 
        SET site_id = :siteId, employe_id = :employeId, year = :year, month = :month, 
        nbRepas = :nbRepas, primeObjectif = :primeObjectif, primeExept = :primeExept, 
        accompte = :accompte, nbHeurePlus = :nbHeurePlus, nbJoursFeries = :nbJoursFeries, 
        commentaires = :commentaires, status = :status, userId = :userId WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':siteId', $this->siteId);
        $stmt->bindParam(':employeId', $this->employeId);
        $stmt->bindParam(':year', $this->year);
        $stmt->bindParam(':month', $this->month);
        $stmt->bindParam(':nbRepas', $this->nbRepas);
        $stmt->bindParam(':primeObjectif', $this->primeObjectif);
        $stmt->bindParam(':primeExept', $this->primeExept);
        $stmt->bindParam(':accompte', $this->accompte);
        $stmt->bindParam(':nbHeurePlus', $this->nbHeurePlus);
        $stmt->bindParam(':nbJoursFeries', $this->nbJoursFeries);
        $stmt->bindParam(':commentaires', $this->commentaires);
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

        $sql = 'SELECT id FROM appoe_plugin_agapesHotes_planning_plus 
        WHERE year = :year AND month = :month AND site_id = :siteId AND employe_id = :employeId';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':year', $this->year);
        $stmt->bindParam(':month', $this->month);
        $stmt->bindParam(':siteId', $this->siteId);
        $stmt->bindParam(':employeId', $this->employeId);
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