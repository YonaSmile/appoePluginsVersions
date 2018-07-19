<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMessageMenu" role="button"
       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-envelope"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMessageMenu">
        <a class="dropdown-item" href="<?= getPluginUrl('messagIn/page/allMessages/'); ?>">
            <?= trans('Tous les messages'); ?>
        </a>
        <a class="dropdown-item" href="<?= getPluginUrl('messagIn/page/addMessage/'); ?>">
            <?= trans('Nouveau message'); ?>
        </a>
    </div>
</li>