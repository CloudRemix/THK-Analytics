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
 * THK Request
 */
class ThkRequest {
/* ------------------------------------------------------------------------ */

/* -- Private Property -- */
	/**
	 * request
	 * @var array
	 */
	private $_request = array();
/* ------------------------------------------------------------------------ */

/* -- Public Method -- */
	/**
	 * Constructer
	 */
	public function __construct() {
		/**
		 * Do Not urldecode
		 * because =>
		 *	http://jp2.php.net/manual/en/function.urldecode.php
		 *	Warning
		 *	The superglobals $_GET and $_REQUEST are already decoded.
		 *	Using urldecode() on an element in $_GET or $_REQUEST could have unexpected and dangerous results.
		 */
		if( is_array( $_GET ) ) {
			$this->_requestConvert( $_GET );
		}
		if( is_array( $_POST ) ) {
			$this->_requestConvert( $_POST );
		}
		if( isset( $_SERVER['argv'] ) && is_array( $_SERVER['argv'] ) ) {
			if( !isset( $this->_request['controller'] ) && !isset( $this->_request['action'] ) ) {
				foreach( $_SERVER['argv'] as $key => $value ) {
					switch( $key ) {
						case 0:
							break;
						case 1:
							$this->_request['controller'] = $value;
							break;
						case 2:
							$this->_request['action'] = $value;
							break;
						default:
							$this->_request[$key - 3] = $value;
							break;
					}
				}
			}
		}
	}

	/**
	 * get
	 * @param string $key Request Key
	 * @return string $value Request Value
	 */
	public function get( $key ) {
		return isset( $this->_request[$key] ) ? $this->_request[$key] : null;
	}

	/**
	 * set
	 * @param string $key Request Key
	 * @param string $data Request Value
	 */
	public function set( $key, $data ) {
		$this->_request[$key] = $data;
	}

	private function _requestConvert( $postorget ) {
		foreach( $postorget as $key => $value ) {
			if( array_search( $key, MenuConfig::$getAbbrev ) ) $key = array_search( $key, MenuConfig::$getAbbrev );
			if( array_search( $value, MenuConfig::$getAbbrev ) ) $value = array_search( $value, MenuConfig::$getAbbrev );
			//$key = array_search( $key, MenuConfig::$getAbbrev );
			//$value = array_search( $value, MenuConfig::$getAbbrev );
			if( $key === 'f' ) {
				$this->_request['yyyy_from'] = substr( $value, 0, 4 );
				$this->_request['mm_from'] = substr( $value, 4, 2 );
				$this->_request['dd_from'] = substr( $value, 6, 2 );
			}
			elseif( $key === 't' ) {
				$this->_request['yyyy_to'] = substr( $value, 0, 4 );
				$this->_request['mm_to'] = substr( $value, 4, 2 );
				$this->_request['dd_to'] = substr( $value, 6, 2 );
			}
			else {
				$this->_request[$key] = $value;
			}
		}
		return $this->_request;
	}
/* ------------------------------------------------------------------------ */
}
?>
