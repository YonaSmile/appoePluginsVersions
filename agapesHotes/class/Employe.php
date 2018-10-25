<?php

namespace App\Plugin\AgapesHotes;
class Employe extends \App\Plugin\People\People
{

    function __construct($idEmploye = null)
    {
        parent::__construct();
        $this->type = 'AGAPESHOTES_EMPLOYE';

        if (!is_null($idEmploye)) {
            $this->id = $idEmploye;
            $this->show();
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

        $sql = 'SELECT id FROM appoe_plugin_people WHERE type = :type AND nature = :nature AND name = :name AND firstName = :firstName';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':nature', $this->nature);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':firstName', $this->firstName);
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