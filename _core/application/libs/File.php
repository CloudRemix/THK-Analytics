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
class File {
	public static function writeFile( $fileName, $writeString ) {
		$rtn = true;
		try {
			$fp = @fopen( $fileName, 'wb+' );
			if( $fp === false ) return false;
			$length = @fwrite( $fp, $writeString );
			if( $length === false ) return false;
			if( @fclose( $fp ) === false ) return false;
		}
		catch( Exception $ex ) {
			$rtn = false;
		}
		return $rtn;
	}

	public static function readFile( $fileName ) {
		$rtn = '';
		if( file_exists( $fileName ) ) $rtn = @file_get_contents( $fileName );
		return $rtn;
	}

	public static function deleteFile( $fileName ) {
		$rtn = true;
		try {
			if( file_exists( $fileName ) ) @unlink( $fileName );
		}
		catch( Exception $ex ) {
			$rtn = false;
		}
		return $rtn;
	}

	public static function replaceCrlf( $value, $replace ) {
		return preg_replace( '/\\r|\\n|\\r\\n/', $replace, $value );
	}
}
?>
