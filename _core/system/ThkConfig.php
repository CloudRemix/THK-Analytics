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
 * THK Config
 */
class ThkConfig {
/* ------------------------------------------------------------------------ */

/* -- Define -- */
	/**
	 * _NAME
	 * @var string
	 */
	const THK_NAME = 'THK Analytics';
	const RA_NAME = 'Research Artisan Lite';
	/**
	 * _URL
	 * @var string
	 */
	const THK_URL = 'http://thk.kanzae.net/analytics/';
	const RA_URL = 'http://lite.research-artisan.net/';
	/**
	 * _PROJECT
	 * @var string
	 */
	const THK_PROJECT = 'Thought is free';
	const RA_PROJECT = 'Research Artisan Project';
	/**
	 * SUPPORT_PHPVERSION
	 * @var string
	 */
	const SUPPORT_PHPVERSION = '5.3';
	/**
	 * SUPPORT_MYSQLVERSION
	 * @var float
	 */
	const SUPPORT_MYSQLVERSION = 4.2;
	/**
	 * CHARSET
	 * @var string
	 */
	const CHARSET = 'UTF-8';
	/**
	 * MYSQL_CHARSET
	 * @var string
	 */
	const MYSQL_CHARSET = 'utf8';
	/**
	 * FIRST_RELEASE
	 * @var int
	 */
	const FIRST_RELEASE = 200901;
	/**
	 * CONTINUE_ERR_CODE
	 * @var int
	 */
	const CONTINUE_ERR_CODE = 9999;
	/**
	 * DEFAULT_ERR_CODE
	 * @var int
	 */
	const DEFAULT_ERR_CODE = 9900;
	/**
	 * PHP_NOTSUPPORT_VERSION_ERR_CODE
	 * @var int
	 */
	const PHP_NOTSUPPORT_VERSION_ERR_CODE = 9902;
	/**
	 * PHP_EXTENSION_NOTFOUND_ERR_CODE
	 * @var int
	 */
	const PHP_EXTENSION_NOTFOUND_ERR_CODE = 9903;
	/**
	 * MYSQL_NOTSUPPORT_VERSION_ERR_CODE
	 * @var int
	 */
	const MYSQL_NOTSUPPORT_VERSION_ERR_CODE = 9904;
	/**
	 * HELPER_FILE_NOTFOUND_ERR_CODE
	 * @var int
	 */
	const HELPER_FILE_NOTFOUND_ERR_CODE = 9905;
	/**
	 * VIEW_FILE_NOTFOUND_ERR_CODE
	 * @var int
	 */
	const VIEW_FILE_NOTFOUND_ERR_CODE = 9906;
	/**
	 * DATABASE_CONFIG_UNDEFINED_ERR_CODE
	 * @var int
	 */
	const DATABASE_CONFIG_UNDEFINED_ERR_CODE = 9907;
	/**
	 * ENCODING_INVALID_ERR_CODE
	 * @var int
	 */
	const ENCODING_INVALID_ERR_CODE = 9908;
	/**
	 * CTRL_RTN_NONE_ERR_CODE
	 * @var int
	 */
	const CTRL_RTN_NONE_ERR_CODE = 9909;
	/**
	 * DATABASE_SET_CHARSET_FAILED_ERR_CODE
	 * @var int
	 */
	const DATABASE_SET_CHARSET_FAILED_ERR_CODE = 9910;
	/**
	 * CONTROLLER_NOTFOUND_ERR_CODE
	 * @var int
	 */
	const CONTROLLER_NOTFOUND_ERR_CODE = 9911;
	/**
	 * ACTION_NOTFOUND_ERR_CODE
	 * @var int
	 */
	const ACTION_NOTFOUND_ERR_CODE = 9912;
	/**
	 * PHP_NOTSUPPORT_VERSION_ERR_MSG
	 * @var string
	 */
	const PHP_NOTSUPPORT_VERSION_ERR_MSG = 'Not Support PHP Version';
	/**
	 * PHP_EXTENSION_NOTFOUND_ERR_MSG
	 * @var string
	 */
	const PHP_EXTENSION_NOTFOUND_ERR_MSG = 'PHP Extensions Not Found';
	/**
	 * MYSQL_NOTSUPPORT_VERSION_ERR_MSG
	 * @var string
	 */
	const MYSQL_NOTSUPPORT_VERSION_ERR_MSG = 'Not Support MySQL Version';
	/**
	 * HELPER_FILE_NOTFOUND_ERR_MSG
	 * @var string
	 */
	const HELPER_FILE_NOTFOUND_ERR_MSG = 'Helper File Not Found';
	/**
	 * VIEW_FILE_NOTFOUND_ERR_MSG
	 * @var string
	 */
	const VIEW_FILE_NOTFOUND_ERR_MSG = 'View File Not Found';
	/**
	 * DATABASE_CONFIG_UNDEFINED_ERR_MSG
	 * @var string
	 */
	const DATABASE_CONFIG_UNDEFINED_ERR_MSG = 'Undefined Database Configuration';
	/**
	 * ENCODING_INVALID_ERR_MSG
	 * @var string
	 */
	const ENCODING_INVALID_ERR_MSG = 'Encoding Invalid';
	/**
	 * CTRL_RTN_NONE_ERR_MSG
	 * @var string
	 */
	const CTRL_RTN_NONE_ERR_MSG = 'Controller Return Non-Object';
	/**
	 * DATABASE_SET_CHARSET_FAILED_ERR_MSG
	 * @var string
	 */
	const DATABASE_SET_CHARSET_FAILED_ERR_MSG = 'Database Set Charset Failed';
	/**
	 * CONTROLLER_NOTFOUND_ERR_MSG
	 * @var string
	 */
	const CONTROLLER_NOTFOUND_ERR_MSG = 'Request Controller Not Found';
	/**
	 * ACTION_NOTFOUND_ERR_MSG
	 * @var string
	 */
	const ACTION_NOTFOUND_ERR_MSG = 'Request Action Not Found';
	/**
	 * ERR_CTRL_NAME
	 * @var string
	 */
	const ERR_CTRL_NAME = 'error';
	/**
	 * ERR_ACTION_NAME
	 * @var string
	 */
	const ERR_ACTION_NAME = 'error';
	/**
	 * DATABASE_DEFINE_HOST
	 * @var string
	 */
	const DATABASE_DEFINE_HOST = 'DATABASE_CONNECT_HOST';
	/**
	 * DATABASE_DEFINE_USER
	 * @var string
	 */
	const DATABASE_DEFINE_USER = 'DATABASE_CONNECT_USER';
	/**
	 * DATABASE_DEFINE_PASS
	 * @var string
	 */
	const DATABASE_DEFINE_PASS = 'DATABASE_CONNECT_PASS';
	/**
	 * DATABASE_DEFINE_DB_NAME
	 * @var string
	 */
	const DATABASE_DEFINE_DB_NAME = 'DATABASE_DB_NAME';
	/**
	 * DATABASE_DEFINE_TABLE_PREFIX
	 * @var string
	 */
	const DATABASE_DEFINE_TABLE_PREFIX = 'DATABASE_TABLE_PREFIX';
	/**
	 * SESSION_NAME
	 * @var string
	 */
	const SESSION_NAME = '_thk_session_id';
	/**
	 * SESSION_SECONDS
	 * @var int
	 */
	const SESSION_DAYS = 1;
/* ------------------------------------------------------------------------ */

/* -- Public Property -- */
	/**
	 * phpExtensions
	 * @var array
	 */
	public static $phpExtensions = array(
		'mbstring',
		'mysqli'
		//'mysql'
	);
/* ------------------------------------------------------------------------ */
}
?>
