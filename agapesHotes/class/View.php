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
        $sql = 'CREATE VIEW totalFacturation AS SELECT MC.site_id, MC.annee, MC.mois, SUM(COALESCE(MC.totalHT,0) + COALESCE(MS.totalHT,0) + COALESCE(VC.totalHT,0)) AS totalHT
        FROM totalFacturationMainCourante AS MC
        LEFT JOIN totalFacturationMainSupplementaire AS MS
        ON(MC.site_id = MS.site_id AND MC.annee = MS.annee AND MC.mois = MS.mois)
        LEFT JOIN totalFacturationVivreCrue AS VC
        ON(MC.site_id = VC.site_id AND MC.annee = VC.annee AND MC.mois = VC.mois)
        GROUP BY MC.mois;';

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        }

        return true;

    }
}