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
 * THK Logging
 */
class ThkLog {
/* ------------------------------------------------------------------------ */

/* -- Define -- */
	/**
	 * Log Name(http)
	 * @var string
	 */
	const HTTP_NAME = 'http';
	/**
	 * Log Name(cron)
	 * @var string
	 */
	const CRON_NAME = 'cron';
	/**
	 * Extension
	 * @var string
	 */
	const EXT = '.log';
	/**
	 * Level All
	 * @var string
	 */
	const LEVEL_ALL = 'ALL';
	/**
	 * Level Debug
	 * @var string
	 */
	const LEVEL_DEBUG = 'DEBUG';
	/**
	 * Level Info
	 * @var string
	 */
	const LEVEL_INFO = 'INFO';
	/**
	 * Level Warning
	 * @var string
	 */
	const LEVEL_WARN = 'WARN';
	/**
	 * Level Error
	 * @var string
	 */
	const LEVEL_ERROR = 'ERROR';
	/**
	 * Level Fatal
	 * @var string
	 */
	const LEVEL_FATAL = 'FATAL';
	/**
	 * Level None
	 * @var string
	 */
	const LEVEL_NONE = 'NONE';
	/**
	 * Max File Size
	 * @var int
	 */
	const MAX_FILESIZE = 1000000; //1MB
	/**
	 * Max File Count
	 * @var int
	 */
	const MAX_FILECOUNT = 5;
	/**
	 * New Line
	 * @var string	 */
	const NEWLINE = "\r\n";
/* ------------------------------------------------------------------------ */

/* -- Private Property -- */
	/**
	 * singleton
	 * @var ThkLog
	 */
	private static $_singleton = null;
	/**
	 * Log Level Number
	 * @var array
	 */
	private static $_levels = array(
		self::LEVEL_ALL		=> 9,
		self::LEVEL_DEBUG	=> 5,
		self::LEVEL_INFO	=> 4,
		self::LEVEL_WARN	=> 3,
		self::LEVEL_ERROR	=> 2,
		self::LEVEL_FATAL	=> 1,
		self::LEVEL_NONE	=> 0
	);
	/**
	 * limitLevel
	 * @var int
	 */
	private $_limitLevel = self::LEVEL_ALL;
	/**
	 * FilePath * @var string
	 */
	private $_filePath = null;
	/**
	 * Charset
	 * @var string
	 */
	private $_charset = ThkConfig::CHARSET;
/* ------------------------------------------------------------------------ */

/* -- Public Method -- */
	/**
	 * getFilePath
	 */
	public static function getFilePath() {
		return ThkLog::_getInstance()->_filePath;
	}

	/**
	 * setLevel
	 * @param int $limitLevel limitLevel
	 */
	public static function setLevel( $limitLevel ) {
		ThkLog::_getInstance()->_limitLevel = $limitLevel;
	}

	/**
	 * setCharset
	 * @param int $charset charset
	 */
	public static function setCharset($charset) {
		ThkLog::_getInstance()->_charset = $charset;
	}

	/**
	 * write
	 * @param string $msg msg
	 * @param int $level level
	 */
	public static function write( $msg, $level=ThkLog::LEVEL_DEBUG ) {
		$log = ThkLog::_getInstance();
		$writelevel = ThkLog::$_levels[$level];
		$limitLevel = ThkLog::$_levels[$log->_limitLevel];
		if( $limitLevel >= $writelevel ) {
			$log->_rotate();
			$log->_write($msg, $level);
		}
	}
/* ------------------------------------------------------------------------ */

/* -- Private Method -- */
	/**
	 * Constructer
	 */
	private function __construct() {
		if( ThkUtil::isHttp() ) {
			$fileName = self::HTTP_NAME;
		}
		else {
			$fileName = self::CRON_NAME;
		}
		if( defined( ThkConfig::DATABASE_DEFINE_TABLE_PREFIX ) ) $fileName = constant( ThkConfig::DATABASE_DEFINE_TABLE_PREFIX ) . $fileName;
		$this->_filePath = THK_CORE_DIR . THK_LOG_DIR . $fileName . self::EXT;
	}

	/**
	 * getInstance
	 */
	private static function _getInstance() {
		if( ThkLog::$_singleton == null ) {
			ThkLog::$_singleton = new ThkLog();
		}
		return ThkLog::$_singleton;
	}

	/**
	 * write
	 * @param string $msg msg
	 * @param string $level level
	 */
	private function _write( $msg, $level ) {
		$filePath = $this->_filePath;
		$charset = $this->_charset;
		try {
			$msg = ThkUtil::convertEncoding( $msg, $charset );
			$msg = strftime( '%Y-%m-%d %H:%M:%S ' ) . sprintf('[%s] %s', $level, $msg) . self::NEWLINE;
			$fp = @fopen($filePath, 'ab');
			if( $fp !== false ) {
				flock( $fp, LOCK_EX );
				fwrite( $fp, $msg );
				flock( $fp, LOCK_UN );
				fclose( $fp );
			}
		}
		catch( Exception $exception ) {
		}
		return;
	}

	/**
	 * rotate
	 */
	private function _rotate() {
		$filePath = $this->_filePath;
		$fileSize = is_writable( $filePath ) ? filesize( $filePath ): 0;
		if( $fileSize < self::MAX_FILESIZE ) return;
		$file = $filePath . '.' . self::MAX_FILECOUNT;
		try {
			if( is_writable( $file ) ) unlink( $file );
			for( $i = self::MAX_FILECOUNT - 1; $i >= 1; $i-- ) {
				$file = $filePath . '.' . $i;
				if( is_writable( $file ) ) rename( $file, $filePath . '.' . ( $i + 1 ) );
			}
			if( is_writable( $filePath ) ) rename( $filePath, $filePath . '.1' );
			clearstatcache();
		}
		catch( Exception $exception ) {
		}
		return;
	}
/* ------------------------------------------------------------------------ */
}
?>
