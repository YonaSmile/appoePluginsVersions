<?php require('header.php'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1 class="display-4 bigTitle"><?= trans('Messagerie'); ?></h1>
            </div>
        </div>
        <hr class="my-4">
        <?php $MessagIn = new App\Plugin\MessagIn\MessagIn();
        $MessagIn->setToUser($User->getId());
        $allMessages = $MessagIn->showAll();
        $listUsers = $User->showAll();
        $counter = 0;
        $displayList = ''; ?>
        <div class="row">
            <div class="col-12 col-sm-4 col-lg-3 col-xl-2 mb-4">
                <h2 class="h5 mb-3"><?= trans('Les utilisateurs'); ?></h2>
                <div class="nav navUserMessages flex-column nav-pills" id="v-pills-tab" role="tablist">
                    <?php foreach ($listUsers as $user): ?>
                        <?php if ($user->id != $User->getId()): ?>
                            <a class="nav-link userMessages" id="v-pills-user-<?= $user->id; ?>-tab" data-toggle="pill"
                               href="#v-pills-user-<?= $user->id; ?>"
                               role="tab" aria-controls="v-pills-user-<?= $user->id; ?>"
                               aria-expanded="true"
                               data-iduser="<?= $user->id; ?>"><?= $user->nom . ' ' . $user->prenom; ?> <span
                                        class="nbMessageSpan badge badge-light"></span></a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-12 col-sm-8 col-lg-9 col-xl-10 mb-4">
                <h2 class="h5 mb-3"><?= trans('Les messages'); ?></h2>
                <?php if ($allMessages): ?>
                    <div class="tab-content" id="v-pills-tabContent">
                        <?php foreach ($listUsers as $user): ?>
                            <?php $counter = 0;
                            if ($user->id != $User->getId()): ?>
                                <div class="tab-pane fade show msgContainer" id="v-pills-user-<?= $user->id; ?>"
                                     role="tabpanel" aria-labelledby="v-pills-user-<?= $user->id; ?>-tab">
                                    <div class="list-group" data-iduser="<?= $user->id; ?>">
                                        <?php foreach ($allMessages as $message): ?>
                                            <?php if (!$message): ?>
                                                <div class="list-group-item list-group-item-action">
                                                    <p class="p-3"><?= trans('Pas de messages'); ?></p>
                                                </div>
                                            <?php else: ?>
                                                <?php if ($message->fromUser == $user->id): ?>
                                                    <?php
                                                    $counter++;
                                                    $displayList = ($counter >= 11) ? 'tooMuchMessage' : 0;
                                                    ?>
                                                    <div class="list-group-item list-group-item-action <?= $displayList; ?> fileContent msgContent">
                                                        <button type="button" class="deleteBtn deleteMessage"
                                                                data-idmessage="<?= $message->id; ?>"
                                                                aria-label="Close" data-iduser="<?= $user->id; ?>">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>

                                                        <div class="d-inline-block">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" <?= $message->statut ? 'checked' : ''; ?>
                                                                       class="custom-control-input changeStatut"
                                                                       data-idmessage="<?= $message->id; ?>"
                                                                       id="message<?= $message->id; ?>">
                                                                <label class="custom-control-label"
                                                                       for="message<?= $message->id; ?>"></label>
                                                            </div>
                                                        </div>
                                                        <div class="d-inline-block msgTextContainer">
                                                            <div>
                                                                <small><?= formatDateDiff(new DateTime(date('Y-m-d')), new DateTime($message->created_at)); ?></small>
                                                            </div>
                                                            <p class="mb-1"><?= nl2br($message->text); ?></p>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                        <div class="nbMessage d-none"
                                             data-iduser="<?= $user->id; ?>"><?= $counter; ?></div>
                                        <?php if ($counter > 10): ?>
                                            <button class="list-group-item list-group-item-action seeMoreMessages">
                                                <?= trans('Voir tous'); ?>
                                            </button>
                                        <?php elseif ($counter == 0): ?>
                                            <p class="p-3"><?= trans('Pas de messages'); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="p-3"><?= trans('Pas de messages'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            var clickTimes = 0;
            countNbMessage();

            function removeOneOnUserNbMessage(idUser) {
                var $msgBadge = $('div.navUserMessages').find('a.userMessages[data-iduser="' + idUser + '"]').children('span.nbMessageSpan');
                var $msgContainer = $('div.msgContainer').find('div.list-group[data-iduser="' + idUser + '"]');
                var $nbMsgContainer = $msgContainer.children('div.nbMessage');
                var $nbMsgBadge = $msgBadge.text();
                var $nbMsgCounter = $nbMsgContainer.text();
                $msgBadge.text($nbMsgBadge - 1);
                $nbMsgContainer.text($nbMsgCounter - 1);

                if ($nbMsgCounter == 1) {
                    $msgBadge.remove();
                }
                else if ($nbMsgCounter <= 11) {
                    $msgContainer.find('div.msgContent').removeClass('tooMuchMessage');
                }
            }

            function countNbMessage() {
                $('.nbMessage').each(function () {
                    var $countMsg = $(this);
                    var nbMessage = parseFloat($countMsg.text());
                    if (nbMessage > 0) {
                        var idUser = $countMsg.data('iduser');
                        $('div.navUserMessages').find('a.userMessages[data-iduser="' + idUser + '"]').children('span.nbMessageSpan').text(nbMessage);
                        if (nbMessage <= 10) {
                            $countMsg.parent().children('.seeMoreMessages').remove();
                        }
                    }
                });
            }

            $('.seeMoreMessages').unbind().click(function () {
                clickTimes++;
                if (clickTimes == 2) {
                    clickTimes = 0;
                    $(this).html('Voir plus');
                    $('.tooMuchMessage').fadeOut('fast');
                } else {
                    $(this).html('Voir moins');
                    $('.tooMuchMessage').fadeIn('fast');
                }

            });

            $('.deleteMessage').on('click', function (event) {
                event.preventDefault();
                event.stopPropagation();
                var $parent = $(this).parent('div');
                var idMessage = $(this).data('idmessage');
                var idUser = $(this).data('iduser');

                if (confirm('<?= trans('Vous allez supprimer ce message'); ?>')) {
                    $.post(
                        '<?= MESSAGERIE_URL . 'process/ajaxProcess.php'; ?>',
                        {
                            idMessageToDelete: idMessage
                        },
                        function (data) {
                            if (data === true || data == 'true') {
                                $parent.remove();
                                removeOneOnUserNbMessage(idUser);
                                countNbMessage();
                            }
                        }
                    );
                }
            });

            $('.changeStatut').on('click', function () {

                var $input = $(this);
                $input.attr('disabled', 'disabled');

                var idMessage = $input.data('idmessage');
                var statut = 0;
                if ($input.is(':checked')) {
                    statut = 1;
                }

                setTimeout(function () {
                    $.post(
                        '<?= MESSAGERIE_URL . 'process/ajaxProcess.php'; ?>',
                        {
                            idMessageTochangeStatut: idMessage,
                            statutMessage: statut
                        },
                        function (data) {
                            if (data !== true && data != 'true') {
                                alert('Un problème est survenu !')
                            } else {
                                $input.attr('disabled', false);
                            }
                        }
                    );
                }, 1000);
            });
        });
    </script>
<?php require('footer.php'); ?>