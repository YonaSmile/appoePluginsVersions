function addMetaArticle(data) {
    return $.post('/app/plugin/itemGlue/process/ajaxProcess.php', data);
}

function deleteMetaArticle(idMetaArticle) {

    return $.post(
        '/app/plugin/itemGlue/process/ajaxProcess.php',
        {
            DELETEMETAARTICLE: 'OK',
            idMetaArticle: idMetaArticle
        }
    );
}