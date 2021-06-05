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

/**
 * THK Base Controller
 */
abstract class ThkController {
/* ------------------------------------------------------------------------ */

/* -- Protected Property -- */
	/**
	 * request
	 * @var ThkRequest
	 */
	protected $request = null;
	/**
	 * session
	 * @var ThkSession
	 */
	protected $session = null;
	/**
	 * message
	 * @var ThkMessage
	 */
	protected $message = null;
	/**
	 * result
	 * @var ThkResult
	 */
	protected $result = null;
	/**
	 * controller
	 * @var string
	 */
	protected $controller = null;
	/**
	 * action
	 * @var string
	 */
	protected $action = null;
/* ------------------------------------------------------------------------ */

/* -- Public Method -- */
	/**
	 * Constructer
	 * @param ThkRequest $request Request
	 * @param ThkSession $session Session
	 * @param ThkMessage $message Message
	 * @param ThkResult $result Result
	 * @param string $controller controllerName
	 * @param string $action actionName
	 */
	public function __construct( ThkRequest $request, ThkSession $session, ThkMessage $message, ThkResult $result, $controller, $action ) {
		$this->_checkPhpVersion();
		$this->_checkPhpExtensions();
		$this->request = $request;
		$this->session = $session;
		$this->message = $message;
		$this->result = $result;
		$this->controller = $controller;
		$this->action = $action;
	}
/* ------------------------------------------------------------------------ */

/* -- Protected Method -- */
	/**
	 * checkSession
	 * @param string $key Session Key Name
	 */
	protected final function checkSession( $key ) {
		$rtn = true;
		if( $this->session->get( $key) === null ) $rtn = false;
		return $rtn;
	}

	/**
	 * checkPost
	 * @return boolean checkResult
	 */
	protected final function checkPost() {
		return isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] !== 'POST' ? false : true;
	}

	/**
	 * checkOwnPost
	 * @param string $necessaryInputId Necessary InputType Id
	 * @return boolean checkResult
	 */
	protected final function checkOwnPost( $necessaryInputId ) {
		$rtn = false;
		$controller = $this->controller;
		$action = $this->action;
		$refererController = null;
		$refererAction = null;
		if( $this->checkPost() ) {
			$referers = @parse_url( $_SERVER['HTTP_REFERER'] );
			if( $referers !== false && isset( $referers['query'] ) ) {
				$referer = $referers['query'];
				$params = explode( '&', $referer );
				foreach( $params as $value ) {
					$param = explode( '=', $value );
					if( count( $param ) === 2 ) {
						/*
						if( $param[0] === 'controller' ) $refererController = $param[1];
						if( $param[0] === 'action' ) $refererAction = $param[1];
						*/
						if( $param[0] === 'c' ) $refererController = $param[1];
						if( $param[0] === 'a' ) $refererAction = $param[1];
					}
				}
				if(
					( $refererController !== null && $refererAction !== null ) &&
					( $refererController === $controller && $refererAction === $action )
				) {
					if($this->request->get( $necessaryInputId ) !== null ) $rtn = true;
				}
			}
		}
		return $rtn;
	}

	/**
	 * checkRequest
	 * @param array or string $keys Request Key Name
	 * @return boolean checkResult
	 */
	protected final function checkRequest( $keys ) {
		$rtn = true;
		$request = $this->request;
		if( is_array( $keys ) ) {
			foreach( $keys as $key ) {
				if( $request->get( $key ) === null ) {
					$rtn = false;
					break;
				}
			}
		}
		if( is_string( $keys ) ) if( $request->get( $keys ) === null ) $rtn = false;
		return $rtn;
	}

	/**
	 * redirect
	 * @param string $controller Controller Name
	 * @param string $action Action Name
	 * @param string $querystring QueryString Value
	 */
	protected final function redirect( $controller, $action, $querystring=null ) {
		if( trim( $controller ) !== '' && trim( $action ) !== '' ) {
			$act = $action;
			if( isset( MenuConfig::$getAbbrev[$action] ) ) $act = MenuConfig::$getAbbrev[$action];
			$con = $controller;
			if( isset( MenuConfig::$getAbbrev[$controller] ) ) $con = MenuConfig::$getAbbrev[$controller];

			$url = ThkUtil::getBaseUrl() . '?c=' . $con . '&a=' . $act; 
			if( $querystring !== null ) $url .= $querystring; 
			header( 'Location:' . $url );
			exit;
		}
	}

	/**
	 * checkReferer
	 * @param string $controller Controller Name
	 * @param string $action Action Name
	 * @result boolean CheckResult
	 */
	protected final function checkReferer( $controller, $action ) {
		if( trim( $controller ) !== '' && trim( $action ) !== '' && isset( $_SERVER['HTTP_REFERER'] ) ) {
			$referer = $_SERVER['HTTP_REFERER'];
			$act = $action;
			if( isset( MenuConfig::$getAbbrev[$action] ) ) $act = MenuConfig::$getAbbrev[$action];
			$con = $controller;
			if( isset( MenuConfig::$getAbbrev[$controller] ) ) $con = MenuConfig::$getAbbrev[$controller];

			$matchUrl = preg_quote( ThkUtil::getBaseUrl() . '?c=' . $con . '&a=' . $act, '/' );
			if( preg_match( '/\A' . $matchUrl . '.*\z/', $referer ) ) {
				return true;
			}

			$matchUrl = str_replace( 'index\.php', '', $matchUrl );
			if( preg_match( '/\A' . $matchUrl . '.*\z/', $referer ) ) {
				return true;
			}
		}
		return false;
	}
/* ------------------------------------------------------------------------ */

/* -- Private Method -- */
	/**
	 * _checkPhpVersion
	 * @return boolean checkResult
	 */
	private function _checkPhpVersion() {
		if( ThkUtil::versionComparePhp( PHP_VERSION, ThkConfig::SUPPORT_PHPVERSION, '<' ) ) {
			throw new ThkException( ThkConfig::PHP_NOTSUPPORT_VERSION_ERR_MSG . ' => ' . PHP_VERSION, ThkConfig::PHP_NOTSUPPORT_VERSION_ERR_CODE, true );
		}
	}

	/**
	 * _checkPhpExtensions
	 * @return boolean checkResult
	 */
	private function _checkPhpExtensions() {
		$check = true;
		$extensionString = '';
		$loadExtensions = get_loaded_extensions();
		$phpExtensions = ThkConfig::$phpExtensions;
		foreach( $phpExtensions as $phpExtension ) {
			if( !in_array( $phpExtension, $loadExtensions ) ) {
				$extensionString .= $phpExtension . ',';
				$check = false;
			}
		}
		if( !$check ) {
			throw new ThkException (
				ThkConfig::PHP_EXTENSION_NOTFOUND_ERR_MSG . ' => ' . substr( $extensionString, 0, strlen( $extensionString ) - 1 ),
				ThkConfig::PHP_EXTENSION_NOTFOUND_ERR_CODE, true
			);
		}
	}
/* ------------------------------------------------------------------------ */
}
?>
