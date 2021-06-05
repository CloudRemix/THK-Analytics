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
 * THK Message
 */
class ThkMessage {
/* ------------------------------------------------------------------------ */

/* -- Private Property -- */
	/**
	 * code
	 * @var int
	 */
	private $_code = null;
	/**
	 * message
	 * @var string
	 */
	private $_message = null;
/* ------------------------------------------------------------------------ */

/* -- Public Method -- */
	/**
	 * setCode
	 * @param int $code code
	 */
	public function setCode( $code ) {
		$this->_code = $code;
	}

	/**
	 * setMessage
	 * @param string $message message
	 */
	public function setMessage( $message ) {
		$this->_message = $message;
	}

	/**
	 * getCode
	 * @return int $code Code
	 */
	public function getCode() {
		return $this->_code;
	}

	/**
	 * getMessage
	 * @return string $message Message
	 */
	public function getMessage() {
		return $this->_message;
	}
/* ------------------------------------------------------------------------ */
}
?>
