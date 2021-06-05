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
abstract class BaseController extends ThkController {
	public function __construct( ThkRequest $request, ThkSession $session, ThkMessage $message, ThkResult $result, $controller, $action ) {
		parent::__construct( $request, $session, $message, $result, $controller, $action );
		$this->result->setCharset( ThkConfig::CHARSET );
		$this->result->setLayout('main');
		if( $this->controller === 'research' || ($this->controller === 'admin' ) ) {
			if( ThkUtil::isHttp() && !$this->checkSession('login') ) {
				$this->redirect('login', 'login');
			}
		}
		if(
			$this->checkParamYyyyMmDd(
				$request->get('yyyy_from'),
				$request->get('mm_from'), $request->get('dd_from'),
				$request->get('yyyy_to'), $request->get('mm_to'),
				$request->get('dd_to')
			)
		) {
			$this->session->set('yyyyFrom', $request->get('yyyy_from'));
			$this->session->set('mmFrom', $request->get('mm_from'));
			$this->session->set('ddFrom', $request->get('dd_from'));
			$this->session->set('yyyyTo', $request->get('yyyy_to'));
			$this->session->set('mmTo', $request->get('mm_to'));
			$this->session->set('ddTo', $request->get('dd_to'));
		}
	}

	protected function checkParamYyyyMmDd( $yyyyFrom, $mmFrom, $ddFrom, $yyyyTo, $mmTo, $ddTo ) {
		$rtn = false;
		if(
			( $yyyyFrom !== null && is_numeric( $yyyyFrom ) && strlen( $yyyyFrom ) === 4 ) &&
			( $mmFrom !== null && is_numeric( $mmFrom ) && strlen( $mmFrom ) === 2 ) &&
			( $ddFrom !== null && is_numeric( $ddFrom ) && strlen( $ddFrom ) === 2 ) &&
			( $yyyyTo !== null && is_numeric( $yyyyTo ) && strlen( $yyyyTo ) === 4 ) &&
			( $mmTo !== null && is_numeric( $mmTo ) && strlen( $mmTo ) === 2 ) &&
			( $ddTo !== null && is_numeric( $ddTo ) && strlen( $ddTo ) === 2) 
		) {
			if( ( $yyyyFrom . $mmFrom . $ddFrom ) <= ( $yyyyTo . $mmTo . $ddTo ) ) {
				$rtn = true;
			}
		}
		return $rtn;
	}

	protected function defineSiteurl( $url ) {
		$define = '';
		$define .= '<?php'. "\n";
		$define .= 'define(\''. Config::DEFINE_SITEURL . '\',\''. $url. '\');'. "\n";
		$define .= '?>';
		return File::writeFile( SETTING_SITEURL_FILE, $define );
	}

	protected function setNormalMessage( $message ) {
		$this->message->setCode( Config::NOTICE_ERR_CODE );
		$this->message->setMessage( $message );
	}

	protected function checkUrl( $keys ) {
		$controller = $this->controller;
		$action = $this->action;
		if( !$this->checkRequest( $keys ) ) throw new ThkException( '指定したURLに誤りがあります。' );
	}

	protected function checkToken() {
		$rtn = false;
		$sessionToken = $this->session->get('token');
		$requestToken = $this->request->get('token');
		if( $sessionToken !== null && $requestToken !== null ) {
			if( $sessionToken === $requestToken ) {
				$rtn = true;
			}
		}
		return $rtn;
	}

	private function _systemCheck() {
		SystemUtil::clearSystemMessage();
		if( is_writable( SETTING_DIR ) && !ThkUtil::isWindows()) {
			SystemUtil::setSystemMessage( '※セキュリティ警告：<br />以下の setting ディレクトリのアクセス権が<br />書き込み可能になっています。' );
			SystemUtil::setSystemMessage( SETTING_DIR );
		}

		$apploglevel = $this->session->get('apploglevel');
		if( $apploglevel === null ) {
			$system = new System();
			$systemData = $system->find( '*', array( 'condition' => array( 'id = ' . Config::SYSTEM_DEFAULT_ID ) ) );
			$apploglevel = $systemData['apploglevel'];
		}
		ThkLog::setLevel( $apploglevel );
	}
}
?>
