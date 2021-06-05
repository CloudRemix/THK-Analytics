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
 * THK Exception
 */
class ThkException extends Exception {
/* ------------------------------------------------------------------------ */

/* -- Private Property -- */
	/**
	 * nativeError
	 * @var boolean
	 */
	private $_nativeError = false;
	/**
	 * app_error_file
	 * @var string
	 */
	private $_app_error_file = null;
	/**
	 * app_error_line
	 * @var int
	 */
	private $_app_error_line = null;
/* ------------------------------------------------------------------------ */

/* -- Public Method -- */
	/**
	 * Constructer
	 * @param string $message Err Message
	 * @param long $code Err Code
	 * @param boolean $nativeError nativeError
	 */
	public function __construct( $message, $code=null, $nativeError=false ) {
		if( $code === null ) $code = ThkConfig::DEFAULT_ERR_CODE;
		$this->_nativeError = $nativeError;
		parent::__construct( $message, $code );
		$traces = $this->getTrace();
		foreach( $traces as $k => $v ) {
			if( isset( $v['file'] ) ) {
				$file = $v['file'];
				if( stripos( $file, THK_CORE_DIR. THK_SYSTEM_DIR ) === false ) {
					$this->_app_error_file = $file;
					$this->_app_error_line = isset( $v['line'] ) ? $v['line'] : 0;
					break;
				}
			}
		}
		if( $nativeError ) {
			$source = '';
			$dbg = debug_backtrace();
			foreach( $dbg as $key => $traces ) {
				if( $key > 0 ) {
					if( isset( $traces['function']) && $traces['function'] !== 'ThkErrorHandler' ) {
						if( isset( $traces['file'] ) ) {
							$file = $traces['file'];
							$source .= pathinfo( $file, PATHINFO_FILENAME );
						}
						$method = $traces['function'];
						$source .= '::'. $method;
						break;
					}
				}
			}
			$source = trim( $source ) <> '' ? '(' . $source . ') ': $source;
			ThkLog::write( $source . $message . ' (' . $code . ')', ThkLog::LEVEL_FATAL );
		}
	}

	/**
	 * getNativeError
	 * @return boolean $nativeError Native Error
	 */
	public final function getNativeError() {
		return $this->_nativeError;
	}

	/**
	 * getAppErrorFile
	 * @return string $app_error_file ApplicationError File
	 */
	public final function getAppErrorFile() {
		return $this->_app_error_file;
	}

	/**
	 * getAppErrorLine
	 * @return int $app_error_line ApplicationError Line
	 */
	public final function getAppErrorLine() {
		return $this->_app_error_line;
	}

	/**
	 * ThkErrorHandler
	 * @param string $errno errno
	 * @param string $errstr errstr
	 * @param string $errfile errfile
	 * @param int $errline errline
	 * @param array $errcontext errcontext
	 */
	public static function ThkErrorHandler( $errno, $errstr, $errfile, $errline, $errcontext ) {
		$exception = new self( $errstr, $errno, true );
		throw $exception;
	}
/* ------------------------------------------------------------------------ */
}
?>
