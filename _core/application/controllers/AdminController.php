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
class AdminController extends BaseController {
	public function __construct( ThkRequest $request, ThkSession $session, ThkMessage $message, ThkResult $result, $controller, $action ) {
		parent::__construct( $request, $session, $message, $result, $controller, $action );
	}

	public function aliaslist() {
		$result = $this->result;

		$alias = new Alias();
		$findOptions = array();
		if( $this->request->get('search') !== null ) {
			$conditions = array('');
			$conditions[0] .= 'name LIKE ?';
			$conditions[] = '%' . $this->request->get('search') . '%';
			$findOptions['condition'] = $conditions;
		}
		$findOptions['order'] = 'name ASC';
		$aliasData = $alias->findAll( '*', $findOptions );
		$result->set( 'aliasData', $aliasData );
		return $result;
	}

	public function domainlist() {
		$result = $this->result;

		$domain = new Domain();
		$findOptions = array();
		if( $this->request->get('search') !== null ) {
			$conditions = array('');
			$conditions[0] .= 'domain LIKE ?';
			$conditions[] = '%' . $this->request->get('search') . '%';
			$findOptions['condition'] = $conditions;
		}
		$findOptions['order'] = 'domain ASC';
		$domainData = $domain->findAll( '*', $findOptions );
		$result->set('domainData', $domainData );
		return $result;
	}

	public function pagelist() {
		return $this->titlelist( 'page' );
	}

	public function linklist() {
		return $this->titlelist( 'link' );
	}

	public function titlelist( $act ) {
		$result = $this->result;

		$title = new Title();
		$findOptions = array();
		$conditions = ( $act === 'page' ) ? array('logtype = 0') : array('logtype = 1');
		if( $this->request->get('search') !== null ) {
			$conditions[0] .= ' AND title LIKE ?';
			$conditions[] = '%' . $this->request->get('search') . '%';
		}
		$findOptions['condition'] = $conditions;
		$findOptions['order'] = 'url ASC';
		$titleData = $title->findAll( '*', $findOptions );
		$result->set('titleData', $titleData );
		return $result;
	}

	public function aliasedit() {
		$result = $this->result;

		$this->checkUrl('select');
		if( !$this->checkPost() ) {
			return $result;
		}

		if( !$this->checkReferer( 'admin', 'aliasedit' ) ) {
			$this->redirect( 'admin', 'aliaslist' );
		}

		$select = $this->request->get('select');
		$name = $this->request->get('name');

		if( trim( $select ) === '' ) {
			$this->message->setMessage( '訪問者IDが選択されていません。' );
			return $result;
		}

		if( trim( $name ) === '' ) {
			$this->message->setMessage( '訪問者名を入力してください。' );
			return $result;
		}

		if( ThkUtil::strLen( $select ) > 50 ) {
			$this->message->setMessage( '訪問者IDは50文字以内です。' );
			return $result;
		}

		if( ThkUtil::strLen( $name ) > 50 ) {
			$this->message->setMessage( '訪問者名は50文字以内で入力してください。' );
			return $result;
		}

		$alias = new Alias();
		$alias->find( '*', array( 'condition' => array( 'uid = ?', $select ) ) );
		$alias->setValue( 'uid', $select );
		$alias->setValue( 'name', $name );
		$alias->save();

		if( SystemUtil::checkPhpMemoryLimit() ) {
			$aliasData = $alias->findAll( '*', array( 'order' => 'name ASC' ) );
			$aliasData = $this->unset_unnecessary_session( $aliasData );
			$this->session->set( 'aliasData', $aliasData );
		}
		$this->redirect('admin', 'aliaslist');

		return $result;
	}

	public function aliasdelete() {
		$result = $this->result;

		$delete = $this->request->get('delete');
		if( is_array( $delete ) ) {
			if( !$this->checkReferer( 'admin', 'aliaslist' ) ) {
				$this->redirect( 'admin', 'aliaslist' );
			}
			foreach( $delete as $id ) {
				$alias = new Alias();
				$alias->find( '*', array( 'condition' => array( 'uid = ?', $id ) ) );
				if( $alias->getValue('uid') === $this->session->get('selectUid') ) {
					$this->session->delete('selectUid');
					$this->session->delete('selectName');
				}
				$alias->delete();
				$alias = null;
			}
		}

		if( SystemUtil::checkPhpMemoryLimit() ) {
			$alias = new Alias();
			$aliasData = $alias->findAll( '*', array( 'order' => 'name ASC' ) );
			$aliasData = $this->unset_unnecessary_session( $aliasData );
			$this->session->set( 'aliasData', $aliasData );
		}
		$this->redirect('admin', 'aliaslist');
	}

	public function domainedit() {
		$result = $this->result;

		$this->checkUrl('select');
		if( !$this->checkPost() ) {
			return $result;
		}

		if( !$this->checkReferer( 'admin', 'domainedit' ) ) {
			$this->redirect( 'admin', 'domainlist' );
		}

		$select = $this->request->get('select');
		$name = $this->request->get('name');

		if( trim( $select ) === '' ) {
			$this->message->setMessage( 'ドメインが選択されていません。' );
			return $result;
		}

		if( trim( $name ) === '' ) {
			$this->message->setMessage( 'ドメイン名を入力してください。' );
			return $result;
		}

		if( ThkUtil::strLen( $select ) > 100 ) {
			$this->message->setMessage( 'ドメインは100文字以内です。' );
			return $result;
		}

		if( ThkUtil::strLen( $name ) > 30 ) {
			$this->message->setMessage( 'ドメイン名は30文字以内で入力してください。' );
			return $result;
		}

		$domain = new Domain();
		$domain->find( '*', array('condition' => array( 'domain = ?', $select ) ) );
		$domain->setValue( 'domain', $select );
		$domain->setValue( 'name', $name );
		$domain->save();

		if( SystemUtil::checkPhpMemoryLimit() ) {
			$domainData = $domain->findAll( '*', array( 'order' => 'name ASC' ) );
			$domainData = $this->unset_unnecessary_session( $domainData );
			$this->session->set( 'domainData', $domainData );
		}
		$this->redirect( 'admin', 'domainlist' );

		return $result;
	}

	public function domaindelete() {
		$result = $this->result;

		$delete = $this->request->get('delete');
		if( is_array( $delete ) ) {
			if( !$this->checkReferer( 'admin', 'domainlist' ) ) {
				$this->redirect( 'admin', 'domainlist' );
			}
			foreach( $delete as $id ) {
				$domain = new Domain();
				$domain->find( '*', array( 'condition' => array( 'domain = ?', $id ) ) );
				$domain->delete();
				$domain = null;
			}
		}

		if( SystemUtil::checkPhpMemoryLimit() ) {
			$domain = new Domain();
			$domainData = $domain->findAll( '*', array( 'order' => 'name ASC' ) );
			$domainData = $this->unset_unnecessary_session( $domainData );
			$this->session->set( 'domainData', $domainData );
		}
		$this->redirect('admin', 'domainlist');
	}

	public function pageedit() {
		return $this->titleedit( 'page' );
	}

	public function linkedit() {
		return $this->titleedit( 'link' );
	}

	public function titleedit( $act ) {
		$result = $this->result;

		if( !$this->checkPost() ) {
			return $result;
		}

		if( !$this->checkReferer( 'admin', 'pageedit' ) && !$this->checkReferer( 'admin', 'linkedit' ) ) {
			$this->redirect( 'admin', 'pagelist' );
		}

		$select = $this->request->get('select');
		$title = $this->request->get('title');

		if( trim( $select ) === '' ) {
			$this->message->setMessage( 'URLが選択されていません。' );
			return $result;
		}

		if( ThkUtil::strLen( $title ) > 80 ) {
			if( $act === 'page' ) {
				$this->message->setMessage( 'ページ名は80文字以内で入力してください。' );
			}
			else {
				$this->message->setMessage( 'リンク名は80文字以内で入力してください。' );
			}
			return $result;
		}

		$pname = new Title();
		$pname->find( '*', array('condition' => array( 'id = ?', $select ) ) );
		$pname->setValue( 'id', $select );
		$pname->setValue( 'title', $title );
		$pname->save();

		if( SystemUtil::checkPhpMemoryLimit() ) {
			$titleData = $pname->findAll( '*', array( 'order' => 'url ASC' ) );
			$titleData = $this->unset_unnecessary_session( $titleData );
			$this->session->set( 'titleData', $titleData );
		}
		( $act !== 'page' ) ? $this->redirect( 'admin', 'linklist' ) : $this->redirect( 'admin', 'pagelist' );

		return $result;
	}

	public function pagedelete() {
		return $this->titledelete( 'page' );
	}

	public function linkdelete() {
		return $this->titledelete( 'link' );
	}

	public function titledelete( $act ) {
		$result = $this->result;

		$delete = $this->request->get('delete');
		if( is_array( $delete ) ) {
			if( !$this->checkReferer( 'admin', 'pagelist' ) && !$this->checkReferer( 'admin', 'linklist' ) ) {
				$this->redirect( 'admin', 'pagelist' );
			}
			foreach( $delete as $id ) {
				$pname = new Title();
				$pname->find( '*', array( 'condition' => array( 'id = ?', $id ) ) );
				$pname->delete();
				$pname = null;
			}
		}

		if( SystemUtil::checkPhpMemoryLimit() ) {
			$pname = new Title();
			$titleData = $pname->findAll( '*', array( 'order' => 'url ASC' ) );
			$titleData = $this->unset_unnecessary_session( $titleData );
			$this->session->set( 'titleData', $titleData );
		}
		( $act !== 'page' ) ? $this->redirect( 'admin', 'linklist' ) : $this->redirect( 'admin', 'pagelist' );
	}

	public function tag() {
		return $this->result;
	}

	public function phpcode() {
		return $this->result;
	}

	public function jump() {
		$result = $this->result;
		$result->set( 'link', $this->request->get( 'link' ) );
		$result->setLayout( null );
		return $result;
	}

	public function jsdownload() {
		if( !class_exists( 'Track' ) ) return;

		$script = Track::generateScript();
		if( class_exists('JSMin') ) {
			$script = trim( JSMin::minify( $script ) );
		}
		header( 'Content-type: application/x-javascript' );
		header( 'Content-Disposition: attachment; filename=script.js' );
		echo $script;
		exit;
	}

	public function setting() {
		$result = $this->result;
		if( is_writable( SETTING_DIR ) && !ThkUtil::isWindows()) {
			$this->_systemCheckMsg();
		}

		if( !$this->checkPost() ) {
			return $result;
		}

		if( !$this->checkReferer( 'admin', 'setting' ) ) {
			$this->redirect( 'admin', 'setting' );
		}

		$sitename = $this->request->get('sitename');
		$url = $this->request->get('url');
		if( trim( $sitename ) == '' ) {
			$this->message->setMessage( 'サイト名を入力してください。' );
			return $result;
		}
		if( ThkUtil::strLen( $sitename ) > 100 ) {
			$this->message->setMessage( 'サイト名は100文字以内で入力してください。' );
			return $result;
		}
		if( trim( $url ) === '' ) {
			$this->message->setMessage( 'URLを入力してください。' );
			return $result;
		}
		if( !Track::checkUrl( $url ) ) {
			$this->message->setMessage('URLは、http://～、https://～の形で正しく入力してください。');
			return $result;
		}
		if( ThkUtil::strLen( $url ) > 100 ) {
			$this->message->setMessage( 'URLは100文字以内で入力してください。' );
			return $result;
		}

		$loginid = $this->request->get('loginid');
		if( trim( $loginid ) === '' ) {
			$this->message->setMessage( 'ユーザー名を入力してください。' );
			return $result;
		}
		if( ThkUtil::strLen( $loginid ) > 100 ) {
			$this->message->setMessage( 'ユーザー名は100文字以内で入力してください。' );
			return $result;
		}

		$pswd = $this->request->get('pswd');
		$pswdConfirm = $this->request->get('pswd_confirm');
		if( ( $pswd !== null && trim( $pswd ) !== '' ) || ( $pswdConfirm !== null && trim( $pswdConfirm ) != '' ) ) {
			if( $pswd !== $pswdConfirm ) {
				$this->message->setMessage( 'パスワードの入力を確認してください。' );
				return $result;
			}
			if( ThkUtil::strLen( $pswd ) > 100 ) {
				$this->message->setMessage( 'パスワードは100文字以内で入力してください。' );
				return $result;
			}
		}

		$id = $this->session->get('login');
		$site = new Site();
		$site->find( '*', array('condition' => array( 'id = ?', $id ) ) );
		if( $pswd !== null && trim( $pswd ) !== '' ) $site->setValue('pswd', hash( 'SHA256', $pswd ) );
		$site->setValue( 'loginid', $loginid );
		$site->setValue( 'sitename', $sitename );
		$site->setValue( 'url', $url );
		$site->setValue( 'oksecond', $this->request->get('oksecond') );
		$site->setValue( 'againsecond', $this->request->get('againsecond') );
		$site->setValue( 'dispview', $this->request->get('dispview') );
		if( $this->request->get('sortkey') === Config::ON ) {
			$site->setValue( 'sortkey', Config::ON );
		}
		else {
			$site->setValue( 'sortkey', Config::OFF );
		}
		if( $this->request->get('nocrawler') === Config::ON ) {
			$site->setValue( 'nocrawler', Config::ON );
		}
		else {
			$site->setValue( 'nocrawler', Config::OFF );
		}
		$site->setValue( 'filter', $this->request->get('filter') );
		$site->save();

		if( is_writable( SETTING_DIR ) === true ) {
			if( !$this->defineSiteurl( $url ) ) {
				$this->message->setMessage( 'サイト設定に失敗しました。<br />'. 'setting ディレクトリのアクセス権・所有者を確認してください。<br />'. SETTING_DIR );
				return $result;
			}
		}

		if( !ThkUtil::isWindows() ) {
			SystemUtil::clearSystemMessage();
			if( $this->request->get('writable') === Config::ON ) {
				if( ThkUtil::chMod( SETTING_DIR, 0555 ) === false ) {
					$this->_differentOwnerMsg();
				}
			}
			else {
				if( ThkUtil::chMod( SETTING_DIR, 0755 ) === false ) {
					$this->_differentOwnerMsg();
				}
				else {
					$this->_systemCheckMsg();
				}
			}
		}

		if( $this->request->get('nocount') === Config::ON ) {
			Track::setCookie( Config::getCookieKeyAdminNocount(), Config::ON, Config::COOKIE_ENABLE_DAYS, '/' );
		}
		else {
			Track::clearCookie( Config::getCookieKeyAdminNocount(), '/' );
		}

		$site = new Site();
		$siteData = $site->find( '*', array( 'condition' => array( 'id = ?', $id ) ) );
		$this->session->set( 'siteData', $siteData );

		$this->setNormalMessage( '更新しました' );

		return $result;
	}

	public function syssetting() {
		$result = $this->result;

		$system = new System();
		$systemData = $system->find( '*', array( 'condition' => array( 'id = '. Config::SYSTEM_DEFAULT_ID ) ) );
		$result->set( 'systemData', $systemData );

		if( !$this->checkPost() ) {
			return $result;
		}

		$apploglevel = $this->request->get('apploglevel');
		$timezone = $this->request->get('timezone');

		$system->setValue( 'apploglevel', $apploglevel );
		$system->setValue( 'timezone', $timezone );
		$system->save();

		$this->session->set( 'apploglevel', $apploglevel );
		$this->session->set( 'timezone', $timezone );

		$this->setNormalMessage( '更新しました' );

		$system = new System();
		$systemData = $system->find( '*', array( 'condition' => array( 'id = '. Config::SYSTEM_DEFAULT_ID ) ) );
		$result->set( 'systemData', $systemData );

		return $result;
	}

	public function deletelog() {
		$result = $this->result;

		$existLog = array();
		$existLogDataSize = array();

		if( $this->checkPost() ) {
			if( !$this->checkReferer( 'admin', 'deletelog' ) ) {
				$this->redirect( 'admin', 'deletelog' );
			}

			$delete = $this->request->get('delete');
			if( is_array( $delete ) ) {
				foreach( $delete as $yyyymm ) {
					$existLog[] = $yyyymm;
					$log = new Log( true, $yyyymm );
					$existLogDataSize[] = $log->getDataSize();
				}
			}
			if( !empty( $existLog ) ) {
				$this->session->set( 'existLog', $existLog );
				$this->session->set( 'existLogDataSize', $existLogDataSize );
				$this->redirect( 'admin', 'deletelog_confirm' );
			}
			else {
				$this->message->setMessage( 'ログファイルを選択してください。' );
			}
		}

		$month = 0;
		$logDataSize = 0;
		while( true ) {
			try {
				$yyyymm = Calendar::getNextMonth( $month );
				if( $yyyymm < ThkConfig::FIRST_RELEASE ) break;
				$log = new Log( true, $yyyymm );
				$logDataSize = $log->getDataSize();
				$log = null;
				if( $logDataSize > 0 ) {
					$existLog[] = $yyyymm;
					$existLogDataSize[] = $logDataSize;
				}
				$month--;
			}
			catch( Exception $exception ) {
				if( $exception->getCode() == ThkModel::TABLE_NOTFOUND_ERR_CODE ) {
					$month--;
					continue;
				}
				else {
					throw $exception;
				}
			}
		}

		$result->set( 'existLog', $existLog );
		$result->set( 'existLogDataSize', $existLogDataSize );

		return $result;
	}

	public function deletelog_confirm() {
		$result = $this->result;

		if( !$this->checkReferer( 'admin', 'deletelog' ) && !$this->checkReferer( 'admin', 'deletelog_confirm' ) ) {
			$this->redirect( 'admin', 'deletelog' );
		}

		if( $this->checkPost() ) {
			$existLog = $this->session->get( 'existLog' );
			if( is_array( $existLog ) ) {
				foreach( $existLog as $yyyymm ) {
					$log = new Log( true, $yyyymm );
					$log->drop();
				}
			}
			$this->session->delete('existLog');
			$this->session->delete('existLogDataSize');
			$this->redirect('admin', 'deletelog');
		}
		$this->message->setMessage( '選択したログファイルを削除してもよろしいですか？' );

		return $result;
	}

	private function unset_unnecessary_session( $data ) {
		array_walk( $data, function( &$v ) { unset( $v['id'] ); } );
		array_walk( $data, function( &$v ) { unset( $v['created_on'] ); } );
		array_walk( $data, function( &$v ) { unset( $v['updated_on'] ); } );
		return $data;
	}

	private function _differentOwnerMsg() {
		SystemUtil::setSystemMessage( '※setting ディレクトリのアクセス権を変更できませんでした。<br />'. 'setting ディレクトリの所有者を確認してください。<br />もしくは、手動で setting ディレクトリのアクセス権を変更してください。' );
		SystemUtil::setSystemMessage( SETTING_DIR );
	}

	private function _systemCheckMsg() {
		SystemUtil::setSystemMessage('※セキュリティ上の注意：<br />以下の setting ディレクトリのアクセス権が<br />書き込み可能になっています<br />セキュリティ上の安全のため、書き込み不可にしてください。' );
		SystemUtil::setSystemMessage( SETTING_DIR );
	}
}
?>
