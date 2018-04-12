<?php
namespace App\Plugin\ItemGlue;
class ArticleMedia extends \App\File
{
    function __construct($idArticle = null)
    {
        parent::__construct();
        $this->type = 'ITEMGLUE';

        if (!is_null($idArticle)) {
            $this->typeId = $idArticle;
        }
    }
}