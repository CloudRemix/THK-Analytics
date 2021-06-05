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
@set_time_limit( 300 );
$thkCoreDir = '_core' . DIRECTORY_SEPARATOR;

$thkSystemDir = 'system' . DIRECTORY_SEPARATOR;
$thkIncludeFileName = 'ThkInclude.php';
$settingDir = realpath('..') . DIRECTORY_SEPARATOR . 'setting' . DIRECTORY_SEPARATOR;
$settingPathFile = 'path.php';
$coreDefine = 'CORE_PATH';

if( !checkThkPath( $coreDefine, $settingDir . $settingPathFile ) ) {
	defineThkPath( $coreDefine, $thkCoreDir, $thkSystemDir, $thkIncludeFileName, $settingDir . $settingPathFile );
	require_once( $settingDir . $settingPathFile );
}
$thkIncludeFilePath = constant( $coreDefine ) . DIRECTORY_SEPARATOR . $thkSystemDir . $thkIncludeFileName;
if( !@file_exists( $thkIncludeFilePath ) ) {
	writeFile( $settingDir . $settingPathFile, '' );
	defineThkPath( $coreDefine, $thkCoreDir, $thkSystemDir, $thkIncludeFileName, $settingDir . $settingPathFile );
	outputMessage( '環境設定に失敗しました。', 'ブラウザの更新ボタンやF5キー等でリロード（再読み込み）してください。' );
}

define( 'THK_CORE_DIR', constant( $coreDefine ) );
define( 'SETTING_DIR', $settingDir );
define( 'SETTING_SITEURL_FILE', $settingDir . 'siteurl.php' );
define( 'SETTING_DATABASE_FILE', $settingDir . 'database.php' );
define( 'SETTING_INSTALL_COMPLETE_FILE', $settingDir . 'install_complete' );
define( 'SETTING_PATH_FILE', $settingDir . $settingPathFile );
define( 'ADMIN_DIR_NAME', getAdminDirName() );
define( 'UPGRADE_DIR', realpath( THK_CORE_DIR . '..' ) . DIRECTORY_SEPARATOR . '_upgrade' . DIRECTORY_SEPARATOR );

list( $usec, $sec ) = explode( ' ', microtime() );
define( 'THK_STIME', (float)$sec + (float)$usec );

require_once( $thkIncludeFilePath );

$thk = new Thk();
$thk->execute();

function checkThkPath( $coreDefine, $thkPublicSettingFilePath ) {
	if( !file_exists( $thkPublicSettingFilePath ) ) {
		return false;
	}
	require_once( $thkPublicSettingFilePath );
	if( !defined( $coreDefine ) ) {
		return false;
	}
	return true;
}

function defineThkPath( $coreDefine, $thkCoreDir, $thkSystemDir, $thkIncludeFileName, $thkPublicSettingFilePath ) {
	$relativePath = '..';
	$thkIncludeFilePath = realpath( $relativePath ) . DIRECTORY_SEPARATOR . $thkCoreDir . $thkSystemDir . $thkIncludeFileName;
	while( !file_exists( $thkIncludeFilePath ) ) {
		$relativePath .= DIRECTORY_SEPARATOR . '..';
		$thkIncludeFilePath = realpath( $relativePath ) . DIRECTORY_SEPARATOR . $thkCoreDir . $thkSystemDir . $thkIncludeFileName;
		if( !file_exists( realpath( $relativePath ) ) ) {
			outputMessage( '環境設定に失敗しました。', $thkCoreDir . ' ディレクトリが適切な場所にアップロードされていません。' );
		}
	}
	$define = '';
	$define .= '<?php' . "\n";
	$define .= 'define(\'' . $coreDefine . '\',\'' . addslashes( realpath( dirname( $thkIncludeFilePath ) . DIRECTORY_SEPARATOR . '..' ) . DIRECTORY_SEPARATOR ) . '\');' . "\n";
	$define .= '?>';
	writeFile( $thkPublicSettingFilePath, $define );
}

function writeFile( $fileName, $writeString ) {
	$result = false;
	$fp = @fopen( $fileName, 'wb+' );
	if( $fp !== false ) {
		$length = @fwrite( $fp, $writeString );
		if( $length !== false ) {
			if( @fclose( $fp ) !== false ) $result = true;
		}
	}
	if( !$result ) {
		outputMessage( '環境設定に失敗しました。以下のディレクトリのアクセス権（パーミッション）を書き込み可能にしてください。', dirname( $fileName ) );
	}
}

function getAdminDirName() {
	$rtn = null;
	$dirs = explode( DIRECTORY_SEPARATOR, realpath('.') );
	foreach( $dirs as $k => $v ) {
		if( $k == count( $dirs ) -1 ) $rtn = $v;
	}
	if( $rtn === null ) {
		outputMessage( '環境設定に失敗しました。', realpath('.') . ' ディレクトリが適切な場所にアップロードされていません。' );
	}
	return $rtn;
}

function outputMessage( $message1, $message2=null ) {
	$charset = 'UTF-8';
	if( PHP_OS === 'WINNT' || PHP_OS === 'WIN32' || PHP_OS === 'Windows' || DIRECTORY_SEPARATOR === '\\' ) {
		$message1 = mb_convert_encoding( $message1, 'SJIS', $charset );
		if( $message2 !== null ) {
			$message2 = mb_convert_encoding( $message2, 'SJIS', $charset );
		}
		$charset = 'Shift_JIS';
	}
	header( 'Content-Type: text/plain; charset=' . $charset );
	echo $message1;
	if( $message2 !== null ) {
		echo "\n";
		echo $message2;
	}
	exit;
}
?>
