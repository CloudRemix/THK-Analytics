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
 * THK View
 */
class ThkView {
/* ------------------------------------------------------------------------ */

/* -- Define -- */
	/**
	 * LAYOUT_DIR
	 * @var string
	 */
	const LAYOUT_DIR = 'layout';
/* ------------------------------------------------------------------------ */

/* -- Private Property -- */
	/**
	 * request
	 * @var ThkRequest
	 */
	private $_request = null;
	/**
	 * session
	 * @var ThkSession
	 */
	private $_session = null;
	/**
	 * message
	 * @var ThkMessage
	 */
	private $_message = null;
	/**
	 * result
	 * @var ThkResult
	 */
	private $_result = null;
	/**
	 * charset
	 * @var string
	 */
	private $_charset = null;
	/**
	 * layout
	 * @var string
	 */
	private $_layout = null;
/* ------------------------------------------------------------------------ */

/* -- Public Method -- */
	/**
	 * Constructer
	 * @param ThkRequest $request Request
	 * @param ThkSession $session Session
	 * @param ThkMessage $message Message
	 * @param ThkResult $result Result
	 */
	public function __construct( ThkRequest $request=null, ThkSession $session=null, ThkMessage $message=null, ThkResult $result=null ) {
		$this->_request = $request;
		$this->_session = $session;
		$this->_message = $message;
		$this->_result = $result;
		if( $result !== null ) {
			$this->_charset = $result->getCharset() !== null ? $result->getCharset() : ThkConfig::CHARSET;
			$this->_layout = $result->getLayout();
		}
	}

	/**
	 * showView
	 * @param string controller
	 * @param string action
	 */
	public function showView( $controller, $action ) {
		$helper = $this->_loadHelper( $this->_request, $this->_session, $this->_message, $this->_result, $controller, $action );
		$this->_loadView( $controller, $action, $helper, $this->_charset, $this->_layout );
	}

	/**
	 * showError
	 * @param ThkException exception
	 */
	public function showError( $exception ) {
		if( ThkUtil::isHttp() ) {
			ob_start();
			header( 'Content-Type: text/html; charset=' . ThkConfig::CHARSET );
			require_once( THK_CORE_DIR . THK_SYSTEM_DIR . 'ThkError.php' );
			ob_end_flush();
		}
		else {
			echo $exception->getMessage() . '(' . $exception->getCode() . ')' . ThkLog::NEWLINE;
		}
	}
/* ------------------------------------------------------------------------ */

/* -- Private Method -- */
	/**
	 * loadHelper
	 * @param ThkRequest $request Request
	 * @param ThkSession $session Session
	 * @param ThkMessage $message Message
	 * @param ThkResult $result Result
	 * @param string controller
	 * @param string action
	 * @return ThkHelper $helper
	 */
	private function _loadHelper( ThkRequest $request, ThkSession $session, ThkMessage $message, ThkResult $result=null, $controller, $action ) {
		$this->_checkHelper( $controller );
		$helperName = ucfirst( $controller ) . 'Helper';
		return new $helperName( $request, $session, $message, $result, $controller, $action );
	}

	/**
	 * checkHelper
	 * @param string controller
	 */
	private function _checkHelper( $controller ) {
		$helperFile = THK_CORE_DIR . THK_HELPER_DIR . ucfirst( $controller ) . 'Helper.php';
		if( file_exists( $helperFile ) ) {
			require_once( $helperFile );
		}
		else {
			throw new ThkException( ThkConfig::HELPER_FILE_NOTFOUND_ERR_MSG, ThkConfig::HELPER_FILE_NOTFOUND_ERR_CODE, true );
		}
	}

	/**
	 * loadView
	 * @param string controller
	 * @param string action
	 * @param string helper
	 * @param string charset
	 * @param string layout
	 */
	private function _loadView( $controller, $action, $helper, $charset, $layout ) {
		$viewDir = THK_CORE_DIR . THK_VIEW_DIR;
		$viewFile = $this->_checkView( $viewDir, $controller, $action, array('.html') );
		if( $viewFile !== null ) {
			$this->_loadHtml( $viewDir, $viewFile, $controller, $action, $helper, $charset, $layout );
			return;
		}
		$viewFile = $this->_checkView( $viewDir, $controller, $action, array('.txt', '.csv') );
		if( $viewFile !== null ) {
			$this->_loadText( $viewDir, $viewFile, $controller, $action, $helper, $charset );
			return;
		}
		throw new ThkException( ThkConfig::VIEW_FILE_NOTFOUND_ERR_MSG, ThkConfig::VIEW_FILE_NOTFOUND_ERR_CODE, true );
	}

	/**
	 * loadHtml
	 * @param string viewDir
	 * @param string viewFile
	 * @param string controller
	 * @param string action
	 * @param string helper
	 * @param string charset
	 * @param string layout
	 */
	private function _loadHtml( $viewDir, $viewFile, $controller, $action, $helper, $charset, $layout ) {
		if( ThkUtil::isHttp() ) {
			ob_start();
			header( 'Content-Type: text/html; charset=' . $charset );
			header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
			header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
			header( 'Cache-Control: no-store, no-cache, must-revalidate' );
			header( 'Cache-Control: post-check=0, pre-check=0', false );
			header( 'Pragma: no-cache' );
			echo str_pad( '', 256 ) . "\n";
			//ob_end_flush();
			//ob_start();
			$showLayout = false;
			if( $layout !== null && trim( $layout ) !== '' ) {
				$layoutFile = $viewDir . self::LAYOUT_DIR . DIRECTORY_SEPARATOR . $layout . '.html';
				if( file_exists( $layoutFile ) ) $showLayout = true;
			}
			if( $showLayout && ThkUtil::isHttp() ) {
				require_once( $layoutFile );
			}
			else {
				require_once( $viewFile );
			}
			ob_end_flush();
		}
		else {
			require_once( $viewFile );
		}
	}

	/**
	 * loadText
	 * @param string viewDir
	 * @param string viewFile
	 * @param string controller
	 * @param string action
	 * @param string helper
	 * @param string charset
	 */
	private function _loadText( $viewDir, $viewFile, $controller, $action, $helper, $charset ) {
		$convertCharsets = array( 'Shift_JIS' => 'SJIS', 'EUC-JP' => 'EUC-JP', 'UTF-8' => 'UTF-8' );
		$convertCharset = isset( $convertCharsets[$charset] ) ? $convertCharsets[$charset] : 'auto';
		$text = file_get_contents( $viewFile );
		if( $text === false ) $text = '';
		$text .= $helper->getTextData();
		$text = ThkUtil::convertEncoding( $text, ThkConfig::CHARSET, $convertCharset );
		if( ThkUtil::isHttp() ) {
			ob_start();
			header( 'Content-Type: text/plain; charset=' . $charset );
			header( 'Content-Disposition: attachment; filename=' . basename( $viewFile ) );
			echo $text;
			ob_end_flush();
		}
		else {
			echo $text;
		}
	}

	/**
	 * checkView
	 * @param string viewDir
	 * @param string controller
	 * @param string action
	 * @param string exts
	 */
	private function _checkView( $viewDir, $controller, $action, $exts ) {
		$rtn = null;
		$viewFile = $viewDir . $controller . DIRECTORY_SEPARATOR . $action;
		foreach( $exts as $ext ) {
			if( file_exists( $viewFile . $ext ) ) {
				$rtn = $viewFile . $ext;
				break;
			}
		}
		return $rtn;
	}
/* ------------------------------------------------------------------------ */
}
?>
