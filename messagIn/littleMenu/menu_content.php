<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle sidebarLink" href="#" id="navbarDropdownMessageMenu" role="button"
       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-envelope"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMessageMenu">
        <a class="dropdown-item" href="<?= getPluginUrl('messagIn/page/allMessages/'); ?>">
            <small><?= trans('Tous les messages'); ?></small>
        </a>
        <a class="dropdown-item" href="<?= getPluginUrl('messagIn/page/addMessage/'); ?>">
            <small><?= trans('Nouveau message'); ?></small>
        </a>
    </div>
</li>