<div class="modal fade" id="modalTwitterManager" tabindex="-1" role="dialog" aria-labelledby="modalTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTwitterTitle"><i class="fab fa-twitter"></i>
                    <?= trans('Partage Twitter'); ?></h5>
            </div>
            <div class="modal-body" id="modalTwitterBody">
                <nav>
                    <div class="nav nav-tabs" id="nav-twitter-tab" role="tablist">
                        <a class="nav-item nav-link colorPrimary active" id="nav-twitterGroup-tab" data-toggle="tab"
                           href="#twitterGroup"
                           role="tab" aria-controls="twitterGroup" aria-selected="true">Group Twitter</a>
                    </div>
                </nav>
                <div class="tab-content border border-top-0 bg-white p-3 mb-2" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="twitterGroup" role="tabpanel"
                         aria-labelledby="nav-twitterGroup-tab">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {

        var shareLink = '';

        $('#modalTwitterManager').on('shown.bs.modal', function (e) {
            $('#twitterGroup').html(loaderHtml());
            $('#twitterGroup').load('/app/plugin/twitter/data/twitterGroup.php');
        });

        $('body').on('click', 'button#articleTwitterShareButton', function () {
            shareLink = $(this).data('share-link');
        });

        $('body').on('click', '#submitTwitterGroup', function () {

            if ($('#twitterGroup input[type=checkbox]:checked').length) {

                busyApp();
                $('#twitterInfo').html('');

                var checkedListsName = [];
                $.each($('#twitterGroup input[type=checkbox]:checked'), function () {
                    checkedListsName.push($(this).data('list-name'));
                });

                $.post(
                    '/app/plugin/twitter/process/ajaxProcess.php',
                    {
                        sendMessageToLists: 'OK',
                        lists: checkedListsName,
                        url: shareLink
                    }
                ).done(function (data) {

                    if (data != 'false' && data !== false) {
                        $('#twitterGroup').html(data);
                    } else {
                        $('#twitterInfo').html(data);
                    }
                    availableApp();
                });
            }
        });
    });
</script>