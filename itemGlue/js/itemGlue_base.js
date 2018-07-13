function addMeta(data) {

    return $.post(
        '/app/plugin/itemGlue/process/ajaxProcess.php',
        {
            ADDARTICLEMETA: 'OK',
            UPDATEMETAARTICLE: data.updateMeta,
            idArticle: data.idArticle,
            metaKey: data.metaKey,
            metaValue: data.metaValue
        }
    )
}

function deleteMeta(idMetaArticle) {

    return $.post(
        '/app/plugin/itemGlue/process/ajaxProcess.php',
        {
            DELETEMETAARTICLE: 'OK',
            idMetaArticle: idMetaArticle
        }
    );
}