<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/main.php');
inc(WEB_PLUGIN_PATH . '/instagram/includeApp/instagram_functions.php');
if (checkAjaxRequest() && getUserIdSession()) {

    $_POST = cleanRequest($_POST);

    if (isset($_POST['updateTimeline'])) {

        if (defined('INSTAGRAM_TOKEN') && !empty(INSTAGRAM_TOKEN)) {

            $content = json_decode(instagram_getRecentMedia(), true);
            if (is_array($content)) {
                $content['lastUpdate'] = date('Y-m-d H:i:s');

                echo putJsonContent(WEB_PLUGIN_PATH . 'instagram/timeline.json', $content) ? json_encode(true) : json_encode(false);
                exit();
            }
        }

        echo json_encode(false);
        exit();
    }
}
echo json_encode(false);
exit();