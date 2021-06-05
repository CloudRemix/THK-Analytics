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
class SystemUtil {
	private static $_singleton = null;
	private $_messages = array();

	public static function getSystemDataSize() {
		$systemDataSize = 0;
		$month = 0;
		while( true ) {
			try {
				$yyyymm = Calendar::getNextMonth( $month );
				if( $yyyymm < ThkConfig::FIRST_RELEASE ) break;
				$log = new Log( true, $yyyymm );
				$systemDataSize = $systemDataSize + $log->getDataSize();
				$log = null;
				$month--;
			}
			catch( Exception $exception ) {
				if( $exception->getCode() == ThkModel::TABLE_NOTFOUND_ERR_CODE ) {
					$month--;
					continue;
				}
				else {
					throw $exception;
				}
			}
		}
		$alias = new Alias();
		$systemDataSize = $systemDataSize + $alias->getDataSize();
		$domain = new Domain();
		$systemDataSize = $systemDataSize + $domain->getDataSize();
		$site = new Site();
		$systemDataSize = $systemDataSize + $site->getDataSize();
		$title = new Title();
		$systemDataSize = $systemDataSize + $title->getDataSize();
		$system = new System();
		$systemDataSize = $systemDataSize + $system->getDataSize();
		$logs = glob( THK_CORE_DIR. THK_LOG_DIR . constant( ThkConfig::DATABASE_DEFINE_TABLE_PREFIX ) . '*' );
		if( is_array( $logs ) ) {
			foreach( $logs as $logFile ) {
				$systemDataSize += filesize( $logFile );
			}
		}
		return $systemDataSize;
	}

	public static function checkPhpMemoryLimit() {
		$memoryLimit = self::_getMemoryLimit('M');
		if( $memoryLimit >= Config::MINIMUM_MEMORY_LIMIT || $memoryLimit == -1 ) {
			return true;
		}
		return false;
	}

	public static function getUtilizableMemory() {
		return round( self::_getMemoryLimit() * Config::UTILIZABLE_MEMORY );
	}

	public static function getUsageMemory( $peak = false ) {
		if( $peak ) {
			return self::_memConvert( memory_get_peak_usage( true ) );
		}
		return self::_memConvert( memory_get_usage( true ) );
	}

	public static function getProcessTime() {
		list( $usec, $sec ) = explode( ' ', microtime() );
		return sprintf( "%.2f", @round( (float)$sec + (float)$usec - constant('THK_STIME'), 2 ) ) . ' sec';
	}

	public static function versionCompareThk( $version1, $version2, $operator ) {
		return version_compare( $version1, $version2, $operator );
	}

	public static function setSystemMessage( $message ) {
		$messages = SystemUtil::_getInstance()->_messages;
		$messages[] = $message;
		SystemUtil::_getInstance()->_messages = $messages;
	}

	public static function getSystemMessage() {
		return SystemUtil::_getInstance()->_messages;
	}

	public static function clearSystemMessage() {
		SystemUtil::_getInstance()->_messages = array();
	}

	public static function isInstalled( $fileOnly=false ) {
		try {
			if( !$fileOnly ) {
				if( !file_exists( SETTING_DATABASE_FILE ) ) return false;
				if( !file_exists( SETTING_SITEURL_FILE ) ) return false;
				if( !ThkModel::isDatabaseDefined() ) {
					throw new ThkException( ThkConfig::DATABASE_CONFIG_UNDEFINED_ERR_MSG, ThkConfig::DATABASE_CONFIG_UNDEFINED_ERR_CODE );
				}
				$system = new System();
				$systemData = $system->find( '*', array( 'condition' => array( 'id = ' . Config::SYSTEM_DEFAULT_ID ) ) );
				if( $systemData['version'] === null ) return false;
			}
			if( !file_exists( SETTING_INSTALL_COMPLETE_FILE ) ) {
				return false;
			}
		}
		catch( Exception $exception ) {
			File::deleteFile( SETTING_DATABASE_FILE );
			return false;
		}
		return true;
	}

	private function __construct() {
	}

	private static function _getInstance() {
		if( SystemUtil::$_singleton == null ) {
			SystemUtil::$_singleton = new SystemUtil();
		}
		return SystemUtil::$_singleton;
	}

	private static function _getMemoryLimit( $unit=null ) {
		$ret = Config::MINIMUM_MEMORY_LIMIT * 1048576;
		$memory_limit = trim( ini_get('memory_limit') );

		if( preg_match('/^(\d+)(.)$/', $memory_limit, $matches ) ) {
			if( !empty( $matches[1] ) ) $ret = $matches[1];

			if( $unit !== null ) {
				$unit = strtolower( $unit );
			}
			elseif( !empty( $matches[2] ) ) {
				$unit = strtolower( $matches[2] );
			}

			switch( $unit ) {
				case 'y':
					$ret *= 1208925819614629174706176;
					break;
				case 'z':
					$ret *= 1180591620717411303424;
					break;
				case 'e':
					$ret *= 1152921504606846976;
					break;
				case 'p':
					$ret *= 1125899906842624;
					break;
				case 't':
					$ret *= 1099511627776;
					break;
				case 'g':
					$ret *= 1073741824;
					break;
				case 'm':
					$ret *= 1048576;
					break;
				case 'k':
					$ret *= 1024;
					break;
				default:
					break;
			}
		}
		return (int)$ret;
	}

	private static function _memConvert( $size ) {
		$unit = array( 'b','kb','mb','gb','tb','pb' );
		return sprintf( "%.2f", @round( $size / pow( 1024, ( $i = floor( log( $size,1024 ) ) ) ), 2 ) ) . ' ' . $unit[$i];
	}
}
?>
