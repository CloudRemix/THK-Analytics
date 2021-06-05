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
 * THK Util
 */
class ThkUtil {
/* ------------------------------------------------------------------------ */

/* -- Public Method -- */
	/**
	 * checkEncoding
	 * @param string $value Value
	 * @param string $encoding Encoding
	 * @return boolean $check Result
	 */
	public static function checkEncoding( $value, $encoding=null ) {
		$check = true;
		if( !self::checkDoubleStr( $value ) ) return $check;
		if( $encoding === null ) $encoding = ThkConfig::CHARSET;
		if( function_exists( 'mb_check_encoding' ) ) $check = mb_check_encoding( $value, $encoding );
		if( $check ) {
			mb_language( 'Japanese' );
			$checkEncoding = mb_detect_encoding( $value, 'auto' );
			$convValue = mb_convert_encoding( $value, $encoding, $checkEncoding );
			if( $convValue !== $value ) $check = false;
		}
		return $check;
	}

	/**
	 * checkCharcode
	 * @param string $str Value
	 * @return string $charcode Result
	 */
	public static function checkCharcode( $str ) {
		$codes = array( 'UTF-8','SJIS','EUC-JP','ASCII','JIS' );
		foreach( $codes as $charcode ){
			if( mb_convert_encoding( $str, $charcode, $charcode ) === $str ){
				return $charcode;
			}
		}
		return null;
	}

	/**
	 * convertEncoding
	 * @param string $value Value
	 * @param string $fromEncoding From Encoding
	 * @param string $toEncoding To Encoding
	 * @return string $convValue Result
	 */
	public static function convertEncoding( $value, $fromEncoding, $toEncoding=null ) {
		$convValue = $value;
		if( $toEncoding === null ) $toEncoding = ThkConfig::CHARSET;
		if( !self::checkEncoding( $value, $toEncoding ) ) {
			if( $fromEncoding === 'auto' ) {
				$fromCharcode = self::checkCharcode( $value );
				if( $fromCharcode !== null ) $fromEncoding = $fromCharcode;
			}
			mb_language( 'Japanese' );
			$convValue = mb_convert_encoding( $value, $toEncoding, $fromEncoding );
		}
		return $convValue;
	}

	/**
	 * convertKana
	 * @param string $value Value
	 * @param string $option Option
	 * @param string $encoding Encoding
	 * @return string $result Result
	 */
	public static function convertKana( $value, $option, $encoding=null ) {
		if( $encoding === null ) $encoding = ThkConfig::CHARSET;
		return mb_convert_kana( $value, $option, $encoding );
	}

	/**
	 * strWidth
	 * @param string $value Value
	 * @param string $encoding Encoding
	 * @return int $result Result
	 */
	public static function strWidth( $value, $encoding=null ) {
		if( $encoding === null ) $encoding = ThkConfig::CHARSET;
		return mb_strwidth( $value, $encoding );
	}

	/**
	 * strimWidth
	 * @param string $value Value
	 * @param int $start Start
	 * @param int $width Width
	 * @param string $encoding Encoding
	 * @return string $result Result
	 */
	public static function strimWidth( $value, $start, $width, $encoding=null ) {
		if( $encoding === null ) $encoding = ThkConfig::CHARSET;
		$trimmarker = '';
		return mb_strimwidth( $value, $start, $width, $trimmarker, $encoding );
	}

	/**
	 * strLen
	 * @param string $value Value
	 * @param string $encoding Encoding
	 * @return int $result Result
	 */
	public static function strLen( $value, $encoding=null ) {
		if( $encoding === null ) $encoding = ThkConfig::CHARSET;
		return mb_strlen( $value, $encoding );
	}

	/**
	 * subStr
	 * @param string $value Value
	 * @param int $start Start
	 * @param int $length Length
	 * @param string $encoding Encoding
	 * @return string $result Result
	 */
	public static function subStr( $value, $start, $length, $encoding=null ) {
		if( $encoding === null ) $encoding = ThkConfig::CHARSET;
		return mb_substr( $value, $start, $length, $encoding );
	}

	/**
	 * strToLower
	 * @param string $value Value
	 * @param string $encoding Encoding
	 * @return string $result Result
	 */
	public static function strToLower( $value, $encoding=null ) {
		if( $encoding === null ) $encoding = ThkConfig::CHARSET;
		return mb_strtolower( $value, $encoding );
	}

	/**
	 * setCookie
	 * @param string $key Key
	 * @param string $value Value
	 * @param int $enableDays Cookie Enable Days
	 * @param string $path Path
	 * @param string $domain Domain
	 */
	public static function setCookie( $key, $value, $enableDays, $path=null, $domain=null ) {
		if( $enableDays == 0 ) {
			$enableDays = mktime( 0, 0, 0, 1, 1, 1970 );
		}
		else {
			$enableDays = $_SERVER['REQUEST_TIME'] + 86400 * $enableDays;
		}
		if( $path === null ) {
			$scriptName = $_SERVER['SCRIPT_NAME'];
			$path = substr( $scriptName, 0, strrpos( $scriptName, '/' ) + 1 );
		}
		if( $domain === null ) $domain = $_SERVER['SERVER_NAME'];
		if( strpos( $domain, '.' ) > 0 ) {
			setcookie( $key, $value, $enableDays, $path, $domain );
		}
		else {
			setcookie( $key, $value, $enableDays, $path );
		}
	}

	/**
	 * versionComparePhp
	 * @param string $version1 Version1
	 * @param string $version2 Version2
	 * @param string $operator Operator
	 * @result boolean CompareResult
	 */
	public static function versionComparePhp( $version1, $version2, $operator ) {
		if( $version1 !== null && $version2 !== null ) {
			return version_compare( $version1, $version2, $operator );
		}
		return false;
	}

	/**
	 * versionCompareMysql
	 * @param string $version1 Version1
	 * @param string $version2 Version2
	 * @param string $operator Operator
	 * @result boolean CompareResult
	 */
	public static function versionCompareMysql( $version1, $version2, $operator ) {
		if( $version1 !== null && $version2 !== null ) {
			return version_compare( $version1, $version2, $operator );
		}
		return false;
	}

	/**
	 * isHttp
	 * @result boolean CheckResult
	 */
	public static function isHttp() {
		if( isset( $_SERVER['SERVER_PROTOCOL'] ) && strpos( strtolower( $_SERVER['SERVER_PROTOCOL'] ), 'http' ) !== false ) {
			return true;
		}
		return false;
	}

	/**
	 * isWindows
	 * @result boolean CheckResult
	 */
	public static function isWindows() {
		if( PHP_OS === 'WINNT' || PHP_OS === 'WIN32' || PHP_OS === 'Windows' || DIRECTORY_SEPARATOR === '\\' ) {
			return true;
		}
		return false;
	}

	/**
	 * checkDoubleStr
	 * @param string $value Value
	 * @result boolean CheckResult
	 */
	public static function checkDoubleStr( $value ) {
		if( preg_match( '/\A[ -~｡-ﾟ]*\z/', $value ) ) {
			return false;
		}
		return true;
	}

	/**
	 * isHttps
	 * @result boolean CheckResult
	 */
	public static function isHttps() {
		$rtn = false;
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') $rtn = true;
		return $rtn;
	}

	/**
	 * getBaseUrl
	 * @result string $url Base Url
	 */
	public static function getBaseUrl() {
		$httpHeader = 'http://';
		$sslHeader = 'https://';
		$header = self::isHttps() ? $sslHeader : $httpHeader;
		$url = $header . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
		if( !ThkUtil::isWindows() ) {
			$url = str_replace( 'index.php', '', $url );
		}
		return $url;
	}

	/**
	 * getToken
	 * @result string Token Value
	 */
	public static function getToken() {
		return sha1( uniqid( mt_rand(), true ) );
	}

	/**
	 * chmod
	 * @result boolean CheckResult
	 */
	public static function chMod( $filename=null, $mode=0755 ) {
		if( is_callable( 'posix_getpwuid' ) ){
			$owner = posix_getpwuid( fileowner( $filename ) );
			$processUser = posix_getpwuid( posix_geteuid() );
			if( $owner === $processUser ) {
				if( @chmod( $filename, $mode ) === false ) {
					return false;
				}
			}
			else {
				return false;
			}
		}
		return true;
	}
/* ------------------------------------------------------------------------ */
}
?>
