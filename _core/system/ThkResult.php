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
 * THK Result
 */
class ThkResult {
/* ------------------------------------------------------------------------ */

/* -- Private Property -- */
	/**
	 * result
	 * @var array
	 */
	private $_result = array();
	/**
	 * nextController
	 * @var string
	 */
	private $_nextController = null;
	/**
	 * nextAction
	 * @var string
	 */
	private $_nextAction = null;
	/**
	 * redirect
	 * @var string
	 */
	private $_redirect = null;
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
	 * set
	 * @param string $key Request Key
	 * @param string $value Request Value
	 */
	public function set( $key, $value ) {
		$this->_result[$key] = $value;
	}

	/**
	 * setCharset
	 * @param string $charset Charset
	 */
	public function setCharset( $charset ) {
		$this->_charset = $charset;
	}

	/**
	 * setLayout
	 * @param string $layout Layout
	 */
	public function setLayout( $layout ) {
		$this->_layout = $layout;
	}

	/**
	 * get
	 * @param string $key Request Key
	 * @return string $value Request Value
	 */
	public function get( $key ) {
		return isset( $this->_result[$key] ) ? $this->_result[$key] : null;
	}

	/**
	 * delete
	 * @param string $key Request Key
	 */
	public function delete( $key ) {
		unset( $this->_result[$key] );
		return $this->_result[$key] = null;
	}

	/**
	 * getNextController
	 * @return string $nextController Next Call Controller Name
	 */
	public function getNextController() {
		return $this->_nextController;
	}

	/**
	 * getNextAction
	 * @return string $nextAction Next Call Action Name
	 */
	public function getNextAction() {
		return $this->_nextAction;
	}

	/**
	 * getRedirect
	 * @return string $redirect Redirect URL
	 */
	public function getRedirect() {
		return $this->_redirect;
	}

	/**
	 * getCharset
	 * @return string $charset Charset
	 */
	public function getCharset() {
		return $this->_charset;
	}

	/**
	 * getLayout
	 * @return string $layout Layout
	 */
	public function getLayout() {
		return $this->_layout;
	}
/* ------------------------------------------------------------------------ */
}
?>
