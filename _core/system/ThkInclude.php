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

/* -- Define -- */
define( 'THK_SYSTEM_DIR', 'system' . DIRECTORY_SEPARATOR );
define( 'THK_APP_DIR', 'application' . DIRECTORY_SEPARATOR );
define( 'THK_PHPLIB_DIR', THK_SYSTEM_DIR . 'phplib' . DIRECTORY_SEPARATOR );
define( 'THK_MINIFY_DIR', THK_PHPLIB_DIR . 'minify' . DIRECTORY_SEPARATOR );
define( 'THK_VENDOR_DIR', THK_PHPLIB_DIR . 'vendor' . DIRECTORY_SEPARATOR );
define( 'THK_GEOIP_DIR', THK_PHPLIB_DIR . 'geoip' . DIRECTORY_SEPARATOR );
define( 'THK_CONFIG_DIR', THK_APP_DIR . 'config' . DIRECTORY_SEPARATOR );
define( 'THK_CTRL_DIR', THK_APP_DIR . 'controllers' . DIRECTORY_SEPARATOR );
define( 'THK_HELPER_DIR', THK_APP_DIR . 'helpers' . DIRECTORY_SEPARATOR );
define( 'THK_LIBS_DIR', THK_APP_DIR . 'libs' . DIRECTORY_SEPARATOR );
define( 'THK_MODEL_DIR', THK_APP_DIR . 'models' . DIRECTORY_SEPARATOR );
define( 'THK_VIEW_DIR', THK_APP_DIR . 'views' . DIRECTORY_SEPARATOR );
define( 'THK_TMP_DIR', THK_APP_DIR . 'tmp' . DIRECTORY_SEPARATOR );
define( 'THK_LOG_DIR', THK_TMP_DIR . 'logs' . DIRECTORY_SEPARATOR );
define( 'THK_SESS_DIR', THK_TMP_DIR . 'sess' . DIRECTORY_SEPARATOR );

define( 'THK_SQL_DIR', THK_CORE_DIR. THK_MODEL_DIR . 'sql' . DIRECTORY_SEPARATOR );
define( 'THK_CREATE_SQL_DIR', THK_SQL_DIR . 'create' . DIRECTORY_SEPARATOR );
define( 'THK_CREATE_SQL_OLDDIR', THK_SQL_DIR . 'create' . DIRECTORY_SEPARATOR . 'old' . DIRECTORY_SEPARATOR );
define( 'THK_CREATE_SQL_55DIR', THK_SQL_DIR . 'create' . DIRECTORY_SEPARATOR . '5.5' . DIRECTORY_SEPARATOR );
define( 'THK_LOAD_SQL_DIR', THK_SQL_DIR . 'load' . DIRECTORY_SEPARATOR);

define( 'THK_DATA_DIR', str_replace( '_core', '_data', constant('CORE_PATH') ) );
define( 'THK_GEOIP_DAT', THK_DATA_DIR . 'GeoLiteCity.dat' );
/* ------------------------------------------------------------------------ */
/* -- Include -- */
require_once( THK_CORE_DIR . THK_SYSTEM_DIR . 'Thk.php' );
require_once( THK_CORE_DIR . THK_SYSTEM_DIR . 'ThkConfig.php' );
require_once( THK_CORE_DIR . THK_SYSTEM_DIR . 'ThkController.php' );
require_once( THK_CORE_DIR . THK_SYSTEM_DIR . 'ThkModel.php' );
require_once( THK_CORE_DIR . THK_SYSTEM_DIR . 'ThkView.php' );
require_once( THK_CORE_DIR . THK_SYSTEM_DIR . 'ThkHelper.php' );
require_once( THK_CORE_DIR . THK_SYSTEM_DIR . 'ThkException.php' );
require_once( THK_CORE_DIR . THK_SYSTEM_DIR . 'ThkRequest.php' );
require_once( THK_CORE_DIR . THK_SYSTEM_DIR . 'ThkSession.php' );
require_once( THK_CORE_DIR . THK_SYSTEM_DIR . 'ThkResult.php' );
require_once( THK_CORE_DIR . THK_SYSTEM_DIR . 'ThkMessage.php' );
require_once( THK_CORE_DIR . THK_SYSTEM_DIR . 'ThkUtil.php' );
require_once( THK_CORE_DIR . THK_SYSTEM_DIR . 'ThkLog.php' );

require_once( THK_CORE_DIR . THK_MINIFY_DIR . 'jsmin.php' );
require_once( THK_CORE_DIR . THK_MINIFY_DIR . 'cssmin.php' );
require_once( THK_CORE_DIR . THK_GEOIP_DIR . 'geoipcity.inc' );
require_once( THK_CORE_DIR . THK_VENDOR_DIR . 'autoload.php' );

include_files( THK_CORE_DIR . THK_LIBS_DIR, ".php" );
include_files( THK_CORE_DIR . THK_MODEL_DIR, ".php" );
include_files( THK_CORE_DIR . THK_CONFIG_DIR, ".php" );
/* ------------------------------------------------------------------------ */

function include_files( $dir, $ext ) {
	$files = glob( rtrim( $dir, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . '*' );
	foreach( $files as $file ) {
		if( is_file( $file ) ) {
			if( strrchr( $file, $ext ) === $ext ) {
				require_once( $file );
			}
		}
		if( is_dir( $file ) ) {
			include_files( $file, $ext );
		}
	}
}
?>
