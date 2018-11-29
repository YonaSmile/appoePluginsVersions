<?php
require('main.php');
if (getUserRoleId() == 1):
    echo getFileContent(AGAPESHOTES_PATH . 'page/dashboardCuisinier.php');
elseif (getUserRoleId() == 2):
    echo getFileContent(AGAPESHOTES_PATH . 'page/dashboardChefSecteur.php');
else:
    echo getFileContent(AGAPESHOTES_PATH . 'page/dashboardAdministration.php');
endif;