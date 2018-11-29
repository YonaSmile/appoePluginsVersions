<?php

namespace App\Plugin\AgapesHotes;
class View extends \App\DbView
{
    public function __construct($viewName = null, $dataColumns = null, $dataValues = null)
    {
        parent::__construct($viewName, $dataColumns, $dataValues);
    }

    public function set()
    {
        //totalFacturation
        $sql = 'CREATE VIEW totalFacturation AS SELECT MC.site_id, MC.annee, MC.mois, SUM(MC.totalHT + MS.totalHT + VC.totalHT) AS totalHT
        FROM totalFacturationMainCourante AS MC
        INNER JOIN totalFacturationMainSupplementaire AS MS
        ON(MC.annee = MS.annee AND MC.mois = MS.mois)
        INNER JOIN totalFacturationVivreCrue AS VC
        ON(MC.annee = VC.annee AND MC.mois = VC.mois)
        GROUP BY mois;';

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        }

        return true;

    }
}