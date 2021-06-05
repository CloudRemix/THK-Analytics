<?php
/**
 * THK Analytics - free/libre analytics platform
 *
 * @copyright Copyright (C) 2015 Thought is free.
 * @link http://thk.kanzae.net/analytics/
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 * @author LunaNuko
 *
 * This program has been developed on the basis of the Research Artisan Lite.
 */

/**
 * Research Artisan Lite: Website Access Analyzer
 * Copyright (C) 2009 Research Artisan Project
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * @copyright Copyright (C) 2009 Research Artisan Project
 * @license GNU General Public License (see license.txt)
 * @author ossi
 */
class UpgradeController extends BaseController {
	public function __construct( ThkRequest $request, ThkSession $session, ThkMessage $message, ThkResult $result, $controller, $action ) {
		parent::__construct( $request, $session, $message, $result, $controller, $action );
	}

	public function disp() {
		$result = $this->result;
		$this->session->destroy();

		if( !file_exists( UPGRADE_DIR ) ) {
			$this->redirect('install', 'step0');
			return $result;
		}

		$system = new System();
		$systemData = $system->find( '*', array( 'condition' => array( 'id = '. Config::SYSTEM_DEFAULT_ID ) ) );
		$result->set( 'version', $systemData['version'] );

		return $result;
	}

	public function upgrade() {
		$result = $this->result;

		if( !$this->checkReferer('upgrade', 'disp') ) {
			$this->redirect('upgrade', 'disp');
		}
		if( !$this->checkPost() ) {
			$this->redirect('upgrade', 'disp');
		}

		$select = $this->request->get('select');
		$upgradeVersions = array();
		$upgrades = glob( UPGRADE_DIR. '*' );
		if( is_array( $upgrades ) ) {
			foreach( $upgrades as $upgradeDir ) {
				$dirs = array_reverse( explode( DIRECTORY_SEPARATOR, $upgradeDir ) );
				if( isset( $dirs[0] ) ) $version = $dirs[0];
				if( SystemUtil::versionCompareThk( $version, $select, '<=' ) ) array_push( $upgradeVersions, $version );
			}
		}
		sort( $upgradeVersions );
		$version = null;

		if( !in_array( $select, $upgradeVersions ) ) {
			throw new ThkException( '<p>アップグレードは行なわれませんでした。</p><p>ver.'. htmlentities( $select, ENT_QUOTES, THKConfig::CHARSET ). ' 用のアップグレードファイルが<br />設置されていません。</p>' );
		}
		$className = null;
		$methodName = __FUNCTION__;
		$upgradeCount = 0;
		foreach( $upgradeVersions as $version ) {
			$upgradeSuccess = false;
			$upgradeFileName = __CLASS__ . Config::UPGRADE_FILECHAR . str_replace( '.', '', $version ) . '.php';
			if( file_exists( UPGRADE_DIR . $version . DIRECTORY_SEPARATOR . $upgradeFileName ) ) {
				require_once( UPGRADE_DIR . $version . DIRECTORY_SEPARATOR . $upgradeFileName );
				$file = explode( '.', $upgradeFileName );
				if( isset( $file[0] ) ) $className = $file[0];
				if( $className !== null && class_exists( $className ) ) {
					$upgradeClass = new $className( $this->request, $this->session, $this->message, $this->result, $this->controller, $this->action );
					if( method_exists( $upgradeClass, $methodName ) ) {
						$rtn = $upgradeClass->$methodName();
						if( $rtn ) {
							$upgradeSuccess = true;
							$upgradeCount++;
						}
						else {
							throw new ThkException( 'アップグレード中にエラーが発生したため<br />アップグレードは行なわれませんでした。' );
						}
					}
				}
			}
			if( !$upgradeSuccess ) {
				throw new ThkException('<p>アップグレードは行なわれませんでした。</p><p>以下のファイルが存在しない<br />もしくは内容に誤りがあります。</p>'. $this->escapeHtml( $upgradeFileName ) );
			}
		}
		if( $upgradeCount > 0 ) {
			$system = new System();
			$system->find( '*', array( 'condition' => array( 'id = '. Config::SYSTEM_DEFAULT_ID ) ) );
			$system->setValue( 'version', $select );
			$system->save();
			$this->message->setMessage( '<p>ver.' .$select. 'へのアップグレードが完了しました。</p><p>アップロードしたアップグレード用ファイルを<br />削除してからログインしてください。</p>' );
			return $result;
		}
		throw new ThkException( '<p>アップグレードは行なわれませんでした。</p><p>アップグレードファイルが<br />正しく設置されているか確認してください。</p>' );
	}
}
?>
