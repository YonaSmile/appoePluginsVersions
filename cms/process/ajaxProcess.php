<?php

use App\Plugin\Cms\Cms;
use App\Plugin\Cms\CmsContent;
use App\Plugin\Cms\CmsMenu;

require_once( '../main.php' );
if ( checkAjaxRequest() ) {

	if ( getUserIdSession() ) {

		$_POST = cleanRequest( $_POST );

		if ( ! empty( $_POST['idCmsArchive'] ) ) {
			$Cms = new Cms( $_POST['idCmsArchive'] );
			$Cms->setStatut( 0 );
			if ( $Cms->update() ) {
				echo 'true';
			}
			exit();
		}

		if ( isset( $_POST['unpackPage'] ) && ! empty( $_POST['idUnpackPage'] ) ) {
			$Page = new Cms( $_POST['idUnpackPage'] );
			$Page->setStatut( 1 );
			if ( $Page->update() ) {
				echo 'true';
			}
			exit();
		}

		if ( ! empty( $_POST['idCmsDelete'] ) ) {
			$Cms = new Cms( $_POST['idCmsDelete'] );
			if ( $Cms->delete() ) {
				echo 'true';
			}
			exit();
		}

		if ( isset( $_POST['id'] )
		     && ! empty( $_POST['idCms'] )
		     && ! empty( $_POST['metaKey'] )
		     && isset( $_POST['metaValue'] ) ) {

			$CmsContent = new CmsContent();
			$CmsContent->feed( $_POST );
			$CmsContent->setLang( APP_LANG );

			if ( $CmsContent->notExist() ) {
				if ( $CmsContent->save() ) {
					echo $CmsContent->getId();
				}
			} elseif ( $CmsContent->notExist( true ) ) {

				if ( ! empty( $CmsContent->getId() ) && $CmsContent->update() ) {
					echo 'true';
				}
			}
			exit();
		}

		if ( isset( $_POST['idCmsMenuDelete'] ) && ! empty( $_POST['idCmsMenuDelete'] ) ) {

			$CmsMenu = new CmsMenu( $_POST['idCmsMenuDelete'] );
			if ( $CmsMenu->delete() ) {
				echo 'true';
			}
			exit();
		}

		if ( isset( $_POST['updateMenu'] )
		     && ! empty( $_POST['column'] )
		     && ! empty( $_POST['idMenu'] )
		     && isset( $_POST['value'] ) ) {

			$CmsMenu = new CmsMenu( $_POST['idMenu'] );
			$method  = 'set' . str_replace( ' ', '', ucwords( str_replace( '_', ' ', $_POST['column'] ) ) );
			if ( is_callable( array( $CmsMenu, $method ) ) ) {
				$CmsMenu->$method( $_POST['value'] );

				if ( $CmsMenu->update() ) {
					echo 'true';
				}
			}
			exit();
		}

		if ( isset( $_POST['getParentPageByLocation'] ) ) {

			$CmsMenu = new CmsMenu();

			$allMenu     = extractFromObjToArrForList( $CmsMenu->showAll( $_POST['getParentPageByLocation'], 'fr' ), 'id', 'name' );
			$allMenu[10] = trans( 'Aucun parent' );

			if ( $allMenu ) {
				echo \App\Form::select( trans( 'Page Parente' ), 'parentId', $allMenu, '', true );
			}
			exit();
		}
	}
}