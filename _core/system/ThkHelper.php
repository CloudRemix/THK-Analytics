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
 * THK Base Helper
 */
abstract class ThkHelper {
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
	public function __construct( ThkRequest $request, ThkSession $session, ThkMessage $message, ThkResult $result=null, $controller, $action ) {
		$this->request = $request;
		$this->session = $session;
		$this->message = $message;
		$this->result = $result;
		$this->controller = $controller;
		$this->action = $action;
	}

	/**
	 * getTextData
	 * @return string textData
	 */
	public function getTextData() {
		$textData = $this->result->get('textData');
		return $textData !== null ? $textData : '';
	}
/* ------------------------------------------------------------------------ */

/* -- Protected Method -- */
	/**
	 * checkGet
	 * @return boolean checkResult
	 */
	protected final function checkGet() {
		return isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] !== 'GET' ? false : true;
	}
	/**
	 * checkPost
	 * @return boolean checkResult
	 */
	protected final function checkPost() {
		return isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] !== 'POST' ? false : true;
	}
/* ------------------------------------------------------------------------ */
}
?>
