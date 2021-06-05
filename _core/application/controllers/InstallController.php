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
class InstallController extends BaseController {
	const DEFAULT_DATABASE_HOSTNAME = 'localhost';
	const DEFAULT_TABLE_PREFIX = 'thk_';

	public function __construct( ThkRequest $request, ThkSession $session, ThkMessage $message, ThkResult $result, $controller, $action ) {
		parent::__construct( $request, $session, $message, $result, $controller, $action );
	}

	public function step0() {
		$result = $this->result; 

		if( SystemUtil::isInstalled() ) {
			$this->redirect('login', 'login');
		}

		return $result;
	}
	public function step1() {
		$result = $this->result;

		if( !$this->checkReferer('install', 'step0') && !$this->checkReferer('install', 'step1') ) {
			$this->redirect('install', 'step0');
		}

		if( !$this->checkPost() ) {
			return $result;
		}
		if( file_exists( SETTING_DATABASE_FILE ) ) {
			$this->redirect('install', 'step2');
			return $result;
		}
		if( !$this->checkOwnPost('hostname') ) {
			if( !File::deleteFile( SETTING_DATABASE_FILE ) ) {
				$message  = 'データベース接続設定の削除に失敗しました。<br />';
				$message .= 'setting ディレクトリのアクセス権と所有者を確認してください。<br />';
				$message .= SETTING_DIR;
				$this->message->setMessage( $message );
			}
			return $result;
		}

		$hostname = $this->request->get('hostname');
		$username = $this->request->get('username');
		$pswd = $this->request->get('pswd');
		$dbname = $this->request->get('dbname');
		$tableprefix = $this->request->get('tableprefix');
		if( trim( $hostname ) === '') {
			$this->message->setMessage( 'ホスト名を入力してください。' );
			return $result;
		}
		if( trim( $username ) === '' ) {
			$this->message->setMessage( 'ユーザー名を入力してください。' );
			return $result;
		}
		if( trim( $dbname ) === '' ) {
			$this->message->setMessage( 'データベース名を入力してください。' );
			return $result;
		}
		if( trim( $tableprefix ) === '' ) {
			$this->message->setMessage( 'テーブルプレフィックスを入力してください。' );
			return $result;
		}

		if( $this->_defineDatabase( $hostname, $username, $pswd, $dbname, $tableprefix ) ) {
			try {
				if( !ThkModel::isDatabaseDefined() ) require_once( SETTING_DATABASE_FILE );
				$site = new Site();
				$site->checkMySQLVersion();
			}
			catch( Exception $exception ) {
				switch( $exception->getCode() ) {
					case ThkConfig::MYSQL_NOTSUPPORT_VERSION_ERR_CODE:
						$message  = 'PHPの環境設定に問題があります。<br />';
						$message .= '次のモジュールがインストールされていません。<br />';
						$message .= $exception->getMessage() . '<br />';
						$this->message->setMessage( $message );
						break;
					default:
						$message  = '入力内容に誤りがあるため<br />';
						$message .= 'データベースに接続できませんでした。';
						$this->message->setMessage( $message );
						break;
				}
				if( !File::deleteFile( SETTING_DATABASE_FILE ) ) {
					$message  = 'データベース接続設定の削除に失敗しました。<br />';
					$message .= 'setting ディレクトリのアクセス権と所有者を確認してください。<br />';
					$message .= SETTING_DIR;
					$this->message->setMessage( $message );
				}
				return $result;
			}
			$this->redirect('install', 'step2');
		}
		else {
			$message  = 'データベース接続設定に失敗しました。<br />';
			$message .= 'setting ディレクトリのアクセス権と所有者を確認してください。<br />';
			$message .= SETTING_DIR;
			$this->message->setMessage( $message );
			return $result;
		}

		return $result;
	}

	public function step2() {
		$result = $this->result;
		if( SystemUtil::isInstalled() ) {
			if( !$this->checkReferer('install', 'step1') && !$this->checkReferer('install', 'step2') ) {
				$this->redirect('install', 'step0');
			}
		}
		if( !$this->checkPost() ) {
			return $result;
		}

		$sitename = $this->request->get('sitename');
		$url = $this->request->get('url');
		$loginid = $this->request->get('loginid');
		$pswd = $this->request->get('pswd');
		$pswdConfirm = $this->request->get('pswd_confirm');
		if( trim( $sitename ) === '' ) {
			$this->message->setMessage( 'サイト名を入力してください。' );
			return $result;
		}
		if( ThkUtil::strLen( $sitename ) > 100 ) {
			$this->message->setMessage( 'サイト名は100文字以内で入力してください。' );
			return $result;
		}
		if( trim( $url ) === '' ) {
			$this->message->setMessage('URLを入力してください。');
			return $result;
		}
		if( !Track::checkUrl( $url ) ) {
			$this->message->setMessage( 'URLは「http://、https://」の形で正しく入力してください。' );
			return $result;
		}
		if( ThkUtil::strLen( $url ) > 100 ) {
			$this->message->setMessage( 'URLは100文字以内で入力してください。' );
			return $result;
		}

		if( trim($loginid) === '' ) {
			$this->message->setMessage( 'ユーザー名を入力してください。' );
			return $result;
		}
		if( ThkUtil::strLen( $loginid ) > 100 ) {
			$this->message->setMessage( 'ユーザー名は100文字以内で入力してください。' );
			return $result;
		}

		if( trim( $pswd ) === '' ) {
			$this->message->setMessage( 'パスワードを入力してください。' );
			return $result;
		}
		if( $pswd !== $pswdConfirm ) {
			$this->message->setMessage( 'パスワードの入力を確認してください。' );
			return $result;
		}
		if( ThkUtil::strLen( $pswd ) > 100 ) {
			$this->message->setMessage( 'パスワードは100文字以内で入力してください。' );
			return $result;
		}

		if( !$this->defineSiteurl( $url ) ) {
			$message  = 'サイト設定に失敗しました。<br />';
			$message .= 'setting ディレクトリのアクセス権と所有者を確認してください。<br />';
			$message .= SETTING_DIR;
			return $result;
		}

		$site = new Site();
		$site->setValue( 'sitename', $sitename );
		$site->setValue( 'url', $url );
		$site->setValue( 'loginid', $loginid );
		$site->setValue( 'pswd', hash( 'SHA256', $pswd ) );
		$site->save();

		$version = File::replaceCrlf( File::readFile( THK_CORE_DIR . 'thk_version.txt' ), '' );
		$system = new System();
		$system->setValue( 'version', $version );
		$system->save();

		$alias = new Alias();
		$domain = new Domain();
		$domain->loadSqlData();
		$title = new Title();
		$log = new Log();

		$this->redirect('install', 'step3');

		return $result;
	}

	public function step3() {
		$result = $this->result;
		if( SystemUtil::isInstalled() ) {
			$this->redirect('login', 'login');
		}
		if( !$this->checkReferer('install', 'step2') ) {
			$this->redirect('install', 'step0');
		}
		if( !File::writeFile( SETTING_INSTALL_COMPLETE_FILE, Config::ON ) ) {
			throw new ThkException(
				'インストール設定に失敗しました。<br />' .
				'setting ディレクトリのアクセス権と所有者を確認してください。<br />' .
				SETTING_DIR
			);
		}
		/*
		if( !ThkUtil::isWindows() ) {
			@chmod( SETTING_DIR, 0555 );
		}
		*/
		return $result;
	}

	public function step4() {
		$result = $this->result;
		if (!$this->checkReferer('install', 'step3')) {
			$this->redirect('install', 'step0');
		}
		return $result;
	}

	private function _defineDatabase( $hostname, $username, $pswd, $dbname, $tableprefix ) {
		$define = '';
		$define .= '<?php'. "\n";
		$define .= 'define(\''. ThkConfig::DATABASE_DEFINE_HOST. '\',\''. $hostname. '\');'. "\n";
		$define .= 'define(\''. ThkConfig::DATABASE_DEFINE_USER. '\',\''. $username. '\');'. "\n";
		$define .= 'define(\''. ThkConfig::DATABASE_DEFINE_PASS. '\',\''. $pswd. '\');'. "\n";
		$define .= 'define(\''. ThkConfig::DATABASE_DEFINE_DB_NAME. '\',\''. $dbname. '\');'. "\n";
		$define .= 'define(\''. ThkConfig::DATABASE_DEFINE_TABLE_PREFIX. '\',\''. $tableprefix. '\');'. "\n";
		$define .= '?>';
		return File::writeFile( SETTING_DATABASE_FILE, $define );
	}
}
?>
