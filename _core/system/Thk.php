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
 * THK Main
 */
class Thk {
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
	 * controller
	 * @var ThkController
	 */
	private $_controller = null;
	/**
	 * action
	 * @var ThkAction
	 */
	private $_action = null;
	/**
	 * exception
	 * @var ThkException
	 */
	private $_exception = null;
	/**
	 * nextControllCount
	 * @var int
	 */
	private $_nextControllCount = 0;
/* ------------------------------------------------------------------------ */

/* -- Public Method -- */
	/**
	 * Constructer
	 */
	public function __construct() {
		try {
			set_error_handler( array( 'ThkException', 'ThkErrorHandler' ), error_reporting() );
			$this->_request = new ThkRequest();
			$this->_session = new ThkSession();
			$this->_message = new ThkMessage();
			$this->_controller = $this->_request->get('controller');
			$this->_action = $this->_request->get('action');
			$this->_postCheck( $this->_controller, $this->_action );
			if( ThkModel::isDatabaseDefined() ) {
				$system = new system();
				$systemData = $system->find( '*' );
				if( !empty( $systemData['timezone'] ) && $systemData['timezone'] !== 'default' ) {
					date_default_timezone_set( $systemData['timezone'] );
				}
			}
			restore_error_handler();
		}
		catch( Exception $exception ) {
			$result = new ThkResult();
			$this->_showError( $exception, $this->_request, $this->_session, $this->_message, $result );
			restore_error_handler();
		}
	}

	/**
	 * Execute
	 */
	public function execute() {
		try {
			set_error_handler( array( 'ThkException', 'ThkErrorHandler' ), error_reporting() );
			$result = new ThkResult();
			$this->_checkController( $this->_controller );
			$controller = $this->_loadController($this->_request, $this->_session, $this->_message, $result, $this->_controller, $this->_action);
			$result = $this->_doAction( $controller, $this->_action );
			$this->_postLoginCheck( $this->_controller );
			if( !is_object( $result ) ) {
				$msg = ThkConfig::CTRL_RTN_NONE_ERR_MSG. ' (Controller: ' . $this->_controller . ', Action: ' . $this->_action. ')';
				$result = new ThkResult();
				$this->_controller = ThkConfig::ERR_CTRL_NAME;
				$this->_action = ThkConfig::ERR_ACTION_NAME;
				throw new ThkException( $msg, ThkConfig::CTRL_RTN_NONE_ERR_CODE, true );
			}
			$this->_showView($this->_request, $this->_session, $this->_message, $this->_controller, $this->_action, $result, $this->_exception);
			restore_error_handler();
		}
		catch ( Exception $exception ) {
			if( ( $exception->getNativeError() ) || ( $this->_controller == ThkConfig::ERR_CTRL_NAME && $this->_action == ThkConfig::ERR_ACTION_NAME ) ) {
				$this->_showError( $exception, $this->_request, $this->_session, $this->_message, $result );
				restore_error_handler();
			}
			else {
				$this->_exception = $exception;
				$this->_controller = ThkConfig::ERR_CTRL_NAME;
				$this->_action = ThkConfig::ERR_ACTION_NAME;
				$this->execute();
			}
		}
	}
/* ------------------------------------------------------------------------ */

/* -- Private Method -- */
	/**
	 * checkController
	 * @param string $controller controllerName
	 */
	private function _checkController( $controller ) {
		$controllerFile = THK_CORE_DIR. THK_CTRL_DIR . ucfirst( $controller ) . 'Controller.php';
		$controllerName = ucfirst( $controller ) . 'Controller';
		if( file_exists( $controllerFile ) ) {
			$decleardClasses = get_declared_classes();
			if( !in_array( $controllerName, $decleardClasses ) ) include $controllerFile;
		}
		else {
			throw new ThkException( ThkConfig::CONTROLLER_NOTFOUND_ERR_MSG, ThkConfig::CONTROLLER_NOTFOUND_ERR_CODE );
		}
	}

	/**
	 * loadController
	 * @param ThkRequest $request Request
	 * @param ThkSession $session Session
	 * @param ThkMessage $message Message
	 * @param ThkResult $result Result
	 * @param string $controller controllerName
	 * @param string $action actionName
	 * @return ThkController controller
	 */
	private function _loadController( ThkRequest $request, ThkSession $session, ThkMessage $message, ThkResult $result, $controller, $action ) {
		$controllerName = ucfirst( $controller ) . 'Controller';
		return new $controllerName( $request, $session, $message, $result, $controller, $action );
	}

	/**
	 * doAction
	 * @param ThkController $controller controller
	 * @param string $action actionName
	 * @return function user_func
	 */
	private function _doAction( ThkController $controller, $action ) {
		if( !method_exists( $controller, $action ) ) throw new ThkException( ThkConfig::ACTION_NOTFOUND_ERR_MSG, ThkConfig::ACTION_NOTFOUND_ERR_CODE );
		return $controller->$action();
	}

	/**
	 * showView
	 * @param ThkRequest $request Request
	 * @param ThkSession $session Session
	 * @param ThkMessage $message Message
	 * @param string $controller controllerName
	 * @param string $action actionName
	 * @param ThkResult $result Result
	 * @param Exception $exception Exception
	 */
	private function _showView( ThkRequest $request, ThkSession $session, ThkMessage $message, $controller, $action, ThkResult $result, $exception=null ) {
		if( $exception !== null ) {
			$message->setCode( $exception->getCode() );
			$message->setMessage( $exception->getMessage() );
		}
		$rv = new ThkView( $request, $session, $message, $result );
		$rv->showView( $controller, $action );
	}

	/**
	 * showError
	 * @param Exception $exception Exception
	 * @param ThkRequest $request Request
	 * @param ThkSession $session Session
	 * @param ThkMessage $message Message
	 * @param ThkResult $result Result
	 */
	private function _showError( Exception $exception, ThkRequest $request=null, ThkSession $session=null, ThkMessage $message=null, ThkResult $result=null ) {
		$rv = new ThkView( $request, $session, $message, $result );
		$rv->showError( $exception );
	}

	/**
	 * postLoginCheck
	 * @param string $controller
	 */
	private function _postLoginCheck( $controller ) {
		if( $_SERVER['REQUEST_METHOD'] === 'POST' && $controller === 'login' ) {
			if( ! $this->_message->getMessage() ) {
				$this->_redirect();
			}
		}
	}
	/**
	 * postCheck
	 * @param string $controller
	 * @param string $action
	 */
	private function _postCheck( $controller, $action=null ) {
		if( $action === 'logout' ) $this->_redirect();

		if( $_SERVER['REQUEST_METHOD'] === 'POST' && $controller === 'research' && $action !== 'download' ) {
			if( $action != 'log' ) {
				$get = '';
				foreach( $_GET as $k => $v ) { $get .= '&' . $k . '=' . $v; }
				$post = $_POST;
				if( $post['yyyy_from'] && $post['mm_from'] && $post['dd_from'] ) {
					$post['f'] = $post['yyyy_from'] . $post['mm_from'] . $post['dd_from'];
					unset( $post['yyyy_from'] );
					unset( $post['mm_from'] );
					unset( $post['dd_from'] );
				}
				if( $post['yyyy_to'] && $post['mm_to'] && $post['dd_to'] ) {
					$post['t'] = $post['yyyy_to'] . $post['mm_to'] . $post['dd_to'];
					unset( $post['yyyy_to'] );
					unset( $post['mm_to'] );
					unset( $post['dd_to'] );
				}
				foreach( $post as $k => $v ) {
					if( isset( MenuConfig::$getAbbrev[$k] ) ) $k = MenuConfig::$getAbbrev[$k];
					$get .= '&' . $k . '=' . $v;
				}
				$get = ltrim( $get, '&' );

				$this->_redirect( $get );
			}
		}
	}

	/**
	 * redirect
	 * @param string $get
	 */
	private function _redirect( $get=null ) {
		if( ThkUtil::isWindows() ) {
			$redirect = Config::INDEX_FILENAME;
		}
		else {
			$redirect = Config::INDEX;
		}
		if( $get ) $redirect .= '?' . $get;
		header( 'location: '. $redirect );
	}
/* ------------------------------------------------------------------------ */
}
?>
