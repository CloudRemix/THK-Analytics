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
 * THK Session
 */
class ThkSession {
/* ------------------------------------------------------------------------ */

/* -- Private Property -- */
	/**
	 * session
	 * @var array
	 */
	private $_session = array();
/* ------------------------------------------------------------------------ */

/* -- Public Method -- */
	/**
	 * Constructer
	 */
	public function __construct() {
		try {
			if( ThkUtil::isHttp() ) {
				$this->_clearSessionId();
				$scriptName = $_SERVER['SCRIPT_NAME'];
				$lifetime = ThkConfig::SESSION_DAYS * 86400;
				$path = substr( $scriptName, 0, strrpos( $scriptName, '/' ) + 1 );

				if( isset( $_COOKIE[Config::COOKIE_LOGINKEY] ) ) {
					if( is_writable( THK_CORE_DIR . THK_SESS_DIR ) === true ) {
						ini_set( 'session.save_path', THK_CORE_DIR . THK_SESS_DIR );
						ini_set( 'session.cookie_path', $path );
						ini_set( 'session.gc_probability', 1 );
						ini_set( 'session.gc_divisor', 1 );
						ini_set( 'session.gc_maxlifetime', $lifetime );
					}
					session_set_cookie_params( 0, $path );
					session_name( ThkConfig::SESSION_NAME );
					session_start();
				}
				if( isset( $_SESSION ) ) {
					if( !isset( $_SESSION['login'] ) ) {
						session_regenerate_id( true );
					}
					$this->_session = $_SESSION;
				}
			}
		}
		catch( Exception $exception ) {
		}
	}

	/**
	 * regenerate_id
	 */
	public function regenerate_id() {
		session_regenerate_id( true );
	}

	/**
	 * set
	 * @param string $key Session Key
	 * @param string $data Session Value
	 */
	public function set( $key, $data ) {
		$this->_session[$key] = $data;
		if( ThkUtil::isHttp() ) $_SESSION = $this->_session;
	}

	/**
	 * get
	 * @param string $key Session Key
	 * @return string $value Session Value
	 */
	public function get( $key ) {
		return isset( $this->_session[$key] ) ? $this->_session[$key] : null;
	}

	/**
	 * delete
	 * @param string $key Session Key
	 */
	public function delete( $key ) {
		unset( $this->_session[$key] );
		if( ThkUtil::isHttp() ) $_SESSION = $this->_session;
	}

	/**
	 * destroy
	 */
	public function destroy() {
		foreach( $this->_session as $key => $value ) {
			unset( $this->_session[$key] );
		}
		if( ThkUtil::isHttp() ) {
			if( !empty( $this->_session ) ) {
				session_destroy();
			}
		}
	}
/* ------------------------------------------------------------------------ */

/* -- Private Method -- */
	/**
	 * clearSessionId
	 */
	private function _clearSessionId() {
		$serverName = $_SERVER['SERVER_NAME'];
		$pos = substr_count( $serverName, '.' );
		if( $pos > 0 && !preg_match( '/\A([0-9])+\.([0-9])+\.([0-9])+\.([0-9])+\z/', $serverName ) ) {
			$serverName = strtolower( strrev( $serverName ) );
			$domains = explode( '.', $serverName );
			$domain = '';
			foreach( $domains as $value ) {
				$domain = strrev( $value ) . '.' . $domain;
				if( substr( $domain, strlen( $domain ) - 1, strlen( $domain ) ) === '.' ) {
					$domain = substr( $domain, 0, strlen( $domain ) - 1 );
				}
				ThkUtil::setCookie( ThkConfig::SESSION_NAME, '', 0, '/', $domain );
			}
		}
	}
/* ------------------------------------------------------------------------ */
}
?>
