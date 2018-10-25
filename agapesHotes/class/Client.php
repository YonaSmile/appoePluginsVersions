<?php

namespace App\Plugin\AgapesHotes;
class Client extends \App\Plugin\People\People
{

    function __construct($idEmploye = null)
    {
        parent::__construct();
        $this->type = 'AGAPESHOTES_CLIENT';

        if (!is_null($idEmploye)) {
            $this->id = $idEmploye;
            $this->show();
        }

    }

    public function showByTypeAndNatureAndName()
    {
        $sql = 'SELECT id FROM appoe_plugin_people WHERE type = :type AND nature = :nature AND name = :name';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':nature', $this->nature);
        $stmt->bindParam(':name', $this->name);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count == 1) {
                $this->feed($stmt->fetch(\PDO::FETCH_OBJ));

                return true;

            } else {
                return true;
            }
        }
    }

    /**
     * Check if employé does not exist
     * @param bool $forUpdate
     *
     * @return bool
     */
    public function notExist($forUpdate = false)
    {

        $sql = 'SELECT id FROM appoe_plugin_people WHERE type = :type AND nature = :nature AND name = :name';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':nature', $this->nature);
        $stmt->bindParam(':name', $this->name);
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
}