<?php

namespace App\Plugin\AgapesHotes;
class SiteAccess
{

    private $id;
    private $siteUserId;
    private $siteId;
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
    public function getSiteUserId()
    {
        return $this->siteUserId;
    }

    /**
     * @param mixed $siteUserId
     */
    public function setSiteUserId($siteUserId)
    {
        $this->siteUserId = $siteUserId;
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
        $sql = 'CREATE TABLE IF NOT EXISTS `appoe_plugin_agapeshotes_sites_access` (
  				`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  				PRIMARY KEY (`id`),
                `siteUserId` int(11) UNSIGNED NOT NULL,
                `site_id` int(11) UNSIGNED NOT NULL,
                UNIQUE (`siteUserId`,`site_id`),
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

        $sql = 'SELECT * FROM appoe_plugin_agapeshotes_sites_access WHERE id = :id';

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
     * @param $countSitesAccess
     * @return array|bool
     */
    public function showAll($countSitesAccess = false)
    {

        $sql = 'SELECT * FROM appoe_plugin_agapeshotes_sites_access WHERE status = :status ORDER BY updated_at DESC';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':status', $this->status);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            return !$countSitesAccess ? $stmt->fetchAll(\PDO::FETCH_OBJ) : $count;
        }
    }

    /**
     * @param $countSitesAccess
     * @return array|bool
     */
    public function showAllBySite($countSitesAccess = false)
    {

        $sql = 'SELECT * FROM appoe_plugin_agapeshotes_sites_access WHERE site_id = :siteId AND status = :status ORDER BY updated_at DESC';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':siteId', $this->siteId);
        $stmt->bindParam(':status', $this->status);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            return !$countSitesAccess ? $stmt->fetchAll(\PDO::FETCH_OBJ) : $count;
        }
    }

    /**
     * @return array|bool
     */
    public function showSiteByUser()
    {

        $sql = 'SELECT SITEACCESS.id AS idSiteAccess, SITE.*, SECTEUR.id AS secteurId, SECTEUR.nom AS secteurNom, SECTEUR.slug AS secteurSlug 
        FROM appoe_plugin_agapeshotes_sites_access AS SITEACCESS
        INNER JOIN appoe_plugin_agapeshotes_sites AS SITE
        ON(SITE.id = SITEACCESS.id)
        INNER JOIN appoe_plugin_agapeshotes_secteurs AS SECTEUR
        ON(SITE.secteur_id = SECTEUR.id)
        WHERE SITEACCESS.siteUserId = :siteUserId AND SITEACCESS.status = :status ORDER BY SITEACCESS.updated_at DESC';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':siteUserId', $this->siteUserId);
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
        $sql = 'INSERT INTO appoe_plugin_agapeshotes_sites_access (siteUserId, site_id, status, userId, created_at) 
                VALUES (:siteUserId, :siteId, :status, :userId, CURDATE())';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':siteUserId', $this->siteUserId);
        $stmt->bindParam(':siteId', $this->siteId);
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
        $sql = 'UPDATE appoe_plugin_agapeshotes_sites_access SET siteUserId = :siteUserId, site_id = :site_id, status = :status, userId = :userId WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':siteUserId', $this->siteUserId);
        $stmt->bindParam(':siteId', $this->siteId);
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
        $sql = 'DELETE FROM appoe_plugin_agapeshotes_sites_access WHERE id = :id';

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

        $sql = 'SELECT id FROM appoe_plugin_agapeshotes_sites_access WHERE siteUserId = :siteUserId AND site_id = :siteId';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':siteUserId', $this->siteUserId);
        $stmt->bindParam(':siteId', $this->siteId);
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