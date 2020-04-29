<?php
require( 'header.php' );

use App\Plugin\Cms\Cms;
use App\Plugin\Cms\CmsContent;
use App\Plugin\Cms\CmsMenu;
use App\Template;

require( CMS_PATH . 'process/postProcess.php' );

if ( ! empty( $_GET['id'] ) ):

	$Cms = new Cms();
	$Cms->setId( $_GET['id'] );
	$Cms->setLang( APP_LANG );

	if ( $Cms->show() ):

		// get page content
		$CmsContent = new CmsContent( $Cms->getId(), APP_LANG );

		//get all pages for navigations
		$allCmsPages = $Cms->showAll();

		//get all html files
		$files = getFilesFromDir( WEB_PUBLIC_PATH . 'html/', [
			'onlyFiles'             => true,
			'onlyExtension'         => 'php',
			'noExtensionDisplaying' => true
		] );

		echo getAsset( 'mediaLibrary', true );
		echo getTitle( trans( 'Contenu de la page' ) . '<strong> ' . $Cms->getName() . '</strong>', getAppPageSlug() );
		showPostResponse(); ?>
        <div class="row my-2">
            <div class="table-responsive">
                <table class="table noEffect">
                    <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th><?= trans( 'Type' ); ?></th>
                        <th><?= trans( 'Fichier' ); ?></th>
                        <th><?= trans( 'Slug' ); ?></th>
                        <th><?= trans( 'Nom du menu' ); ?></th>
                        <th><?= trans( 'Nom de la page' ); ?></th>
                        <th class="text-left"><?= trans( 'Description' ); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><?= $Cms->getId(); ?></td>
                        <td><?= $Cms->getType(); ?></td>
                        <td><?= $Cms->getFilename(); ?></td>
                        <td><?= $Cms->getSlug(); ?></td>
                        <td><?= $Cms->getMenuName(); ?></td>
                        <td><?= $Cms->getName(); ?></td>
                        <td class="text-left"><?= $Cms->getDescription(); ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-12">
                <div class="row my-3">
                    <div class="col-12 col-lg-8 my-2">
						<?php if ( $Menu->checkUserPermission( getUserRoleId(), 'updatePage' ) ): ?>
                            <button id="updatePageBtn" data-toggle="modal" data-target="#updatePageModal"
                                    class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-wrench"></i> <?= trans( 'Modifier les en têtes' ); ?>
                            </button>
						<?php endif;
						if ( $Cms->getType() === 'PAGE' ): ?>
                            <a href="<?= webUrl( $Cms->getSlug() . '/' ); ?>"
                               class="btn btn-outline-info btn-sm" id="takeLookToPage" target="_blank">
                                <i class="fas fa-external-link-alt"></i> <?= trans( 'Visualiser la page' ); ?>
                            </a>
						<?php endif; ?>
                    </div>
                    <div class="col-12 col-lg-4 my-2 text-right">
                        <select class="custom-select custom-select-sm otherPagesSelect"
                                title="<?= trans( 'Parcourir les pages' ); ?>...">
                            <option selected="selected" disabled><?= trans( 'Parcourir les pages' ); ?>...</option>
							<?php foreach ( $allCmsPages as $pageSelect ):
								if ( $Cms->getId() != $pageSelect->id ): ?>
                                    <option data-href="<?= getPluginUrl( 'cms/page/pageContent/', $pageSelect->id ); ?>"><?= $pageSelect->menuName; ?></option>
								<?php endif;
							endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-2">
			<?php if ( file_exists( WEB_PATH . $Cms->getFilename() . '.php' ) ): ?>
                <span id="pageContentLoader" class="col-12"><i
                            class="fas fa-circle-notch fa-spin"></i> Chargement...</span>
                <form action="" method="post" class="col-12" id="pageContentManageForm" style="display:none;">
					<?php
					$Template = new Template( WEB_PATH . $Cms->getFilename() . '.php', $CmsContent->getData() );
					$Template->show(); ?>
                </form>
                <nav id="headerLinks" class="btn-group-vertical"
                     data-title="<?= ucfirst( mb_strtolower( $Cms->getMenuName() ) ); ?>"></nav>
			<?php else: ?>
                <p><?= trans( 'Model manquant' ); ?></p>
			<?php endif; ?>
        </div>
        <div class="modal fade" id="updatePageModal" tabindex="-1" role="dialog"
             aria-labelledby="updatePageModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="" method="post" id="updatePageForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="updatePageModalLabel">Modifier les en têtes</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="custom-control custom-checkbox my-3">
                                <input type="checkbox" class="custom-control-input" id="updateSlugAuto">
                                <label class="custom-control-label"
                                       for="updateSlugAuto"><?= trans( 'Mettre à jour le lien de la page automatiquement' ); ?></label>
                            </div>

							<?= getTokenField(); ?>
                            <input type="hidden" name="id" value="<?= $Cms->getId(); ?>">
                            <div class="row my-2">
                                <div class="col-12 my-2">
									<?= \App\Form::text( 'Nom', 'name', 'text', $Cms->getName(), true, 70, 'data-seo="title"' ); ?>
                                </div>
                                <div class="col-12 my-2">
									<?= \App\Form::textarea( 'Description', 'description', $Cms->getDescription(), 2, true, 'maxlength="158" data-seo="description"' ); ?>
                                </div>
                                <div class="col-12 my-2">
									<?= \App\Form::text( 'Nom du menu', 'menuName', 'text', $Cms->getMenuName(), true, 70 ); ?>
                                </div>
                                <div class="col-12 my-2">
									<?= \App\Form::text( 'Nom du lien URL' . ' (slug)', 'slug', 'text', $Cms->getSlug(), true, 70, 'data-seo="slug"' ); ?>
                                </div>
								<?php if ( isTechnicien( getUserRoleId() ) ): ?>
                                    <hr class="hrStyle">
                                    <div class="col-12 my-2">
										<?= \App\Form::select( 'Fichier', 'filename', array_combine( $files, $files ), $Cms->getFilename(), true ); ?>
                                    </div>
                                    <div class="col-12 my-2">
										<?= \App\Form::select( 'Type de page', 'type', array_combine( CMS_TYPES, CMS_TYPES ), $Cms->getType(), true ); ?>
                                    </div>
								<?php endif; ?>
                            </div>
                        </div>
                        <div class="modal-footer">
							<?= \App\Form::target( 'UPDATEPAGE' ); ?>
                            <button type="submit" name="UPDATEPAGESUBMIT"
                                    class="btn btn-outline-info"><?= trans( 'Enregistrer' ); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script type="text/javascript">


            function hideElementsOnPreviewFrame(idToKeep) {

                let body = $('.previewPageFrame[data-id="' + idToKeep + '"]').contents().find('body');
                $('header, footer, #adminDashPublic', body).remove();

                $('nav#headerLinks').find('a').each(function (num, el) {
                    let idNav = $(el).data('target');
                    if (idNav !== '#collapse' + idToKeep) {
                        //TODO -------- and not find parent with this child----------
                        $(idNav.replace('collapse', ''), body).attr('style', 'display: none !important;opacity:0 !important;height:0 !important;width:0 !important;position: absolute !important;z-index:0 !important;');
                    }
                });

                $('.previewPageFrame[data-id="' + idToKeep + '"]').fadeIn();
                $('.previewLoader-' + idToKeep).fadeOut().remove();

            }

            function updateCmsContent($input, metaValue) {

                busyApp();

                var idCms = '<?= $Cms->getId(); ?>';
                var idCmsContent = $input.data('idcmscontent');
                var metaKey = $input.attr('id');

                delay(function () {
                    $.post(
                        '<?= CMS_URL; ?>process/ajaxProcess.php',
                        {
                            id: idCmsContent,
                            idCms: idCms,
                            metaKey: metaKey,
                            metaValue: metaValue
                        },
                        function (data) {
                            if (data) {
                                if ($.isNumeric(data)) {
                                    $input.data('idcmscontent', data);
                                }

                                $('small.categoryIdFloatContenaire').stop().fadeOut(function () {
                                    $('small.' + metaKey).html('<?= trans( 'Enregistré' ); ?>').stop().fadeIn();
                                });
                                availableApp();
                            }
                        }
                    );
                }, 300);
            }


            $(document).ready(function () {

                let pageSrc = $('#takeLookToPage').attr('href');
                var zoning = true;

                jQuery.ajaxSetup({async: false});
                $.get(pageSrc, function (data) {
                    if ($('.templateZoneTitle').length) {
                        $('#pageContentManageForm .templateZoneTitle').each(function (num, el) {
                            if (!$(data).find('#' + $(el).attr('id')).length) {
                                zoning = false;
                                return false;
                            }
                        });
                    } else {
                        zoning = false;
                    }

                    if (!zoning) {
                        $('#pageContentLoader').css('opacity', 0).slideUp(500);
                        $('#pageContentManageForm').show().addClass('row');
                    }

                    $('#headerLinks').append('<small class="d-block text-center w-100"><strong>' + $('#headerLinks').data('title') + '</strong></small>');
                    $.each($('.templateZoneTitle'), function () {

                        //Add anchor
                        let id = $(this).attr('id');

                        if (zoning) {

                            $(this).removeAttr('id');
                            $('#headerLinks').append('<a class="btn btn-sm btn-outline-info" data-id="' + id + '" type="button" data-toggle="collapse" data-target="#collapse' + id + '">' + $(this).text() + '</a>');

                            //Add zone
                            $(this).nextUntil('.templateZoneTitle').addBack().wrapAll('<div id="' + id + '" class="templateZone row my-2"></div>');
                        } else {
                            $('#headerLinks').append('<a class="btn btn-sm btn-outline-info" href="#' + id + '">' + $(this).text() + '</a>');
                        }
                    });

                    if (zoning) {
                        var html = '<div class="accordion" id="pageContentManageFormAccordion">';

                        $('.templateZone').each(function (num, el) {

                            let id = $(this).attr('id');
                            let title = $(this).find('h5').text();
                            let frame = '<iframe src="' + pageSrc + '" class="previewPageFrame" data-id="' + id + '" style="display:none;left: 0;top: 0;height: 100%;width: 100%;position: absolute;border: 0" onload="hideElementsOnPreviewFrame(\'' + id + '\')">your browser needs to be updated.</iframe>';
                            $(el).find('h5').remove();

                            //Card
                            html += '<div class="card"><div class="card-header bgColorPrimary" id="heading' + id + '"><h2 class="mb-0"><button class="btn btn-link collapsed zoneTitleBtn" type="button" data-id="' + id + '" data-toggle="collapse" data-target="#collapse' + id + '" aria-expanded="false" aria-controls="collapse' + id + '">' + title + ' </button> </h2></div>';
                            html += '<div id="collapse' + id + '" class="collapse" aria-labelledby="heading' + id + '" data-parent="#pageContentManageFormAccordion">';
                            html += '<div class="card-body"><small class="previewLoader-' + id + '" style="margin-left:10px;">' + loaderHtml() + ' Chargement du visuel...</small><div class="previewZone" data-id="' + id + '" style="max-height: 500px;overflow-y: auto;border: 6px solid #CCC;padding-bottom: 30.25%;position: relative;display: none;">' + frame + '</div>';
                            html += '<button type="button" class="btn btn-info bt-sm btn-block seePreview" data-id="' + id + '">Voir le rendu</button>';
                            html += $(el).get(0).outerHTML;
                            html += '</div></div></div>';
                        });

                        html += '</div>';
                        $('#pageContentLoader').css('opacity', 0).slideUp(500);
                        $('form#pageContentManageForm').html(html).fadeIn(500);

                        $(document.body).on('click', 'button.seePreview', function () {
                            let id = $(this).data('id');
                            $(this).hide().remove();
                            $('.previewZone[data-id="' + id + '"]').slideDown();
                        });

                        let userNavbarHeight = $('#site header nav.navbar').height();
                        $(document.body).on('shown.bs.collapse', '.collapse', function () {
                            var $panel = $(this).closest('.card');
                            $('html,body').animate({
                                scrollTop: $panel.offset().top - userNavbarHeight
                            }, 500);
                        })

                        for (name in CKEDITOR.instances) {
                            CKEDITOR.instances[name].destroy()
                        }

                        CKEDITOR.replaceAll('ckeditor');
                    }

                    $('input[rel=cms-img-popover]').popover({
                        container: 'body',
                        html: true,
                        trigger: 'hover',
                        delay: 200,
                        placement: 'top',
                        content: function () {
                            return '<img src="' + $(this).val() + '" />';
                        }
                    });

                    $('#libraryModal').on('click', '.copyLinkOnClick', function (e) {
                        e.preventDefault();

                        var src = $(this).parent().data('src');
                        $('input#' + $('#libraryModal').data('inputid')).val(src).trigger('input');
                        $('#libraryModal').modal('hide');
                    });

                    $('form#pageContentManageForm').submit(function (event) {
                        event.preventDefault();
                    });

                    $.each($('#pageContentManageForm input, #pageContentManageForm textarea, #pageContentManageForm select'), function () {
                        var id = $(this).attr('name');
                        $('<small class="' + id + ' categoryIdFloatContenaire">').insertAfter($(this));
                    });

                    $('form#pageContentManageForm').on('input', 'input, textarea, select', function (event) {
                        event.preventDefault();
                        var $input = $(this);
                        var metaValue = $input.val();

                        updateCmsContent($input, metaValue);
                    });
                });

                CKEDITOR.on("instanceReady", function (event) {
                    for (var i in CKEDITOR.instances) {

                        CKEDITOR.instances[i].on('blur', function () {
                            var id = this.element.$.id;
                            var $input = $('#' + id);
                            var metaValue = this.getData();

                            updateCmsContent($input, metaValue);
                        });
                    }
                });

                $('#updateSlugAuto').on('change', function () {
                    $('form#updatePageForm input#slug').val(convertToSlug($('form#updatePageForm input#name').val()));
                });

                $('form#updatePageForm input#name').on('keyup', function () {
                    if ($('form#updatePageForm #updateSlugAuto').is(':checked')) {
                        $('form#updatePageForm input#slug').val(convertToSlug($(this).val()));
                        countChars($('form#updatePageForm input#slug'), 'slug');
                    }
                });

                //Stop adding automaticly slug and description from the name of article
                $('form#updatePageForm input#slug').on('focus', function () {
                    $('form#updatePageForm input#name').unbind('keyup');
                });

                if ($('#headerLinks').find('a').length) {
                    $(window).scroll(function () {

                        $('#headerLinks').css('transform', 'translateX(0)');

                        clearTimeout($.data(this, 'scrollTimer'));
                        $.data(this, 'scrollTimer', setTimeout(function () {
                            if ($('#headerLinks a:hover').length == 0) {
                                $('#headerLinks a').blur();
                                $('#headerLinks').css('transform', 'translateX(100%)');
                            }
                        }, 3000));
                    });
                }


                $(document).on('dblclick', 'input.urlFile', function (event) {
                    event.stopPropagation();
                    event.preventDefault();

                    $('input[rel=cms-img-popover]').popover('hide');
                    var idInput = $(this).attr('id');
                    $('#libraryModal').data('inputid', idInput);
                    $('#libraryModal').modal('show');
                });


                $(document.body).on('change', '.otherPagesSelect', function () {
                    var otherEventslink = $('option:selected', this).data('href');
                    location.assign(otherEventslink);
                });

            });
        </script>
	<?php else:
		echo getContainerErrorMsg( trans( 'Cette page n\'existe pas' ) );
	endif;
else:
	echo trans( 'Cette page n\'existe pas' );
endif;
require( 'footer.php' ); ?>