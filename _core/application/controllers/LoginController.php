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
class LoginController extends BaseController {
	public function __construct(ThkRequest $request, ThkSession $session, ThkMessage $message, ThkResult $result, $controller, $action) {
		parent::__construct($request, $session, $message, $result, $controller, $action);
	}

	public function login() {
		$result = $this->result;

		if( file_exists( SETTING_INSTALL_COMPLETE_FILE ) && file_exists( UPGRADE_DIR ) ) {
			$this->redirect('upgrade', 'disp');
		}

		$system = new System();
		$systemData = $system->find( '*', array( 'condition' => array( 'id = ' . Config::SYSTEM_DEFAULT_ID ) ) );
		if( $systemData['version'] === null ) {
			$this->session->set( 'nextStop', true );
			if( !File::deleteFile( SETTING_DATABASE_FILE ) ) {
				$this->message->setMessage(
					'データベース接続設定の削除に失敗しました。<br />' .
					'setting ディレクトリのアクセス権と所有者を確認してください。<br />' . SETTING_DIR
				);
			}
			throw new ThkException( 'システムのバージョンが取得できませんでした。systemテーブルの内容を確認してください。' );
		}
		$version = File::replaceCrlf( File::readFile( THK_CORE_DIR . 'thk_version.txt' ), '' );
		if( SystemUtil::versionCompareThk( $systemData['version'], $version, '!=' ) ) {
			$this->session->set( 'nextStop', true );
			throw new ThkException(
				'システムとファイルのバージョンが異なります。<br />アップグレード処理を行なってください。<br />'.
				'システムバージョン: ' . $systemData['version'] . ', ファイルバージョン: ' . $version
			);
		}

		$this->session->set('version', $systemData['version']);
		$this->session->set('apploglevel', $systemData['apploglevel']);
		$this->session->set('timezone', $systemData['timezone']);
		$this->session->delete('install');

		if( !$this->checkPost() ) {
			if(
				isset( $_COOKIE[Config::COOKIE_LOGINID] ) &&
				isset( $_COOKIE[Config::COOKIE_LOGINKEY] ) &&
				trim( $_COOKIE[Config::COOKIE_LOGINID] ) != '' &&
				trim( $_COOKIE[Config::COOKIE_LOGINKEY] ) != ''
			) {
				$site = new Site();
				$siteData = $site->find( '*', array( 'condition' => array( 'id = ? and cookiekey = ?', $_COOKIE[Config::COOKIE_LOGINID], md5( $_COOKIE[Config::COOKIE_LOGINKEY] ) ) ) );
				$siteData = $this->_unsetsiteData( $siteData );
				if( $siteData['id'] !== null ) return $this->_loginSuccess( $siteData );
			}
			return $result;
		}

		$loginid = $this->request->get('loginid');
		$pswd = $this->request->get('pswd');
		if( trim( $loginid ) === '' || trim( $pswd ) === '' ) {
			$this->message->setMessage('ユーザー名とパスワードを入力してください。');
			return $result;
		}

		$site = new Site();
		$conditions = array( 'condition' => array( 'loginid = ? AND pswd = ?', $loginid, hash( 'SHA256', $pswd ) ) );
		$siteData = $site->find( '*', $conditions );
		$siteData = $this->_unsetsiteData( $siteData );
		if( $siteData['id'] === null ) {
			$this->message->setMessage('ユーザー名もしくはパスワードの<br />入力に誤りがあります。');
			return $result;
		}

		$cookieKey = md5( microtime() . rand( 0, 30000 ) );
		$site->setValue( 'cookiekey', md5( $cookieKey ) );
		$site->save();

		if( $this->request->get('memory') === Config::ON ) {
			Track::setCookie( Config::COOKIE_LOGINID, $siteData['id'], Config::AUTOLOGIN_DAYS );
			Track::setCookie( Config::COOKIE_LOGINKEY, $cookieKey, Config::AUTOLOGIN_DAYS );
			Track::setCookie( Config::COOKIE_MEMORY, 1, Config::AUTOLOGIN_DAYS );
		}
		else {
			Track::setCookie( Config::COOKIE_LOGINID, $siteData['id'], 0 );
			Track::setCookie( Config::COOKIE_LOGINKEY, $cookieKey, 0 );
			Track::setCookie( Config::COOKIE_MEMORY, 0, 0 );
		}

		return $this->_loginSuccess( $siteData );
	}

	public function logout() {
		$result = $this->result;
		Track::clearCookie( Config::COOKIE_LOGINKEY );
		setcookie( ThkConfig::SESSION_NAME, '', 1, '/' );
		$this->session->destroy();

		$this->redirect('login', 'login');
	}

	private function _loginSuccess( $siteData ) {
		$result = $this->result;

		$log = new Log();
		$log->createTable( 'log_' . Calendar::getNextMonth( Config::CREATE_LOG_MONTHS ) );

		$now = $_SERVER['REQUEST_TIME'];
		$yyyy = date( 'Y', $now );
		$mm = date( 'm', $now );
		$dd = date( 'd', $now );

		$aliasData = array();
		if( SystemUtil::checkPhpMemoryLimit() ) {
			$alias = new Alias();
			$aliasData = $alias->findAll( '*', array( 'order' => 'name ASC' ) );
			array_walk( $aliasData, function( &$v ) { unset( $v['id'] ); } );
			array_walk( $aliasData, function( &$v ) { unset( $v['created_on'] ); } );
			array_walk( $aliasData, function( &$v ) { unset( $v['updated_on'] ); } );
		}

		$this->session->set( 'login', $siteData['id'] );
		$this->session->set( 'siteData', $siteData );
		$this->session->set( 'aliasData', $aliasData );
		$this->session->set( 'yyyyFrom', $yyyy );
		$this->session->set( 'mmFrom', $mm );
		$this->session->set( 'ddFrom', $dd );
		$this->session->set( 'yyyyTo', $yyyy );
		$this->session->set( 'mmTo', $mm );
		$this->session->set( 'ddTo', $dd );

		$this->redirect('research', 'digest1');
		return $result;
	}

	private function _unsetsiteData( $siteData ) {
		unset( $siteData['pswd'] );
		unset( $siteData['created_on'] );
		unset( $siteData['updated_on'] );
		return $siteData;
	}
}
?>
