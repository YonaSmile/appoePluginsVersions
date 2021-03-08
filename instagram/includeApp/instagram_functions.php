<?php

function instagram_getIdByToken()
{
    $return = getHttpRequest(INSTAGRAM_API_URL . 'me?fields=id,username&access_token=' . INSTAGRAM_TOKEN);
    debug($return);
}

function instagram_getRecentMedia()
{
    return getHttpRequest(INSTAGRAM_API_URL . 'me/media?fields=caption,id,media_type,media_url,permalink,thumbnail_url,timestamp,username&access_token=' . INSTAGRAM_TOKEN);
}