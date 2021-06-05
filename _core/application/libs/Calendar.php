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
class Calendar {
	public static function getWeekday( $weekday ) {
		$rtn = '';
		switch( $weekday ) {
			case 0:
				$rtn = '日';
				break;
			case 1:
				$rtn = '月';
				break;
			case 2:
				$rtn = '火';
				break;
			case 3:
				$rtn = '水';
				break;
			case 4:
				$rtn = '木';
				break;
			case 5:
				$rtn = '金';
				break;
			case 6:
				$rtn = '土';
				break;
		}
		return $rtn;
	}

	public static function getLastDay( $yyyy, $mm ) {
		return date( 'd', mktime(0, 0, 0, $mm + 1, 0, $yyyy ) );
	}

	public static function getNextMonth( $next ) {
		$time = $_SERVER['REQUEST_TIME'];
		if( $next <> 0 ) {
			$yyyy = date( 'y', $time );
			$mm = date( 'm', $time );
			$dd = $next > 0 ? self::getLastDay( $yyyy, $mm ) : 1;
			$onedayTime = $next > 0 ? 86400 : -86400;
			$addCount = $next > 0 ? -1 : 1;
			while( true ) {
				$time = mktime( 0, 0, 0, $mm, $dd, $yyyy ) + $onedayTime;
				$next = $next + $addCount;
				if( $next == 0 ) break;
				$yyyy = date( 'y', $time );
				$mm = date( 'm', $time );
				$dd = $next > 0 ? self::getLastDay( $yyyy, $mm ) : 1;
			}
		}
		return date( 'Y', $time ) . date( 'm', $time );
	}

	public static function getNextDate( $next ) {
		$now = $_SERVER['REQUEST_TIME'];
		$yyyy = date( 'y', $now );
		$mm = date( 'm', $now );
		$dd = date( 'd', $now );
		$nextDate = mktime( 0, 0, 0, $mm, $dd, $yyyy ) + 86400 * $next;
		return date( 'Y', $nextDate ) . date( 'm', $nextDate ) . date( 'd', $nextDate );
	}

	public static function getTermDay( $yyyyFrom, $mmFrom, $ddFrom, $yyyyTo, $mmTo, $ddTo ) {
		$fromTime = mktime( 0, 0, 0, $mmFrom, $ddFrom, $yyyyFrom );
		$toTime = mktime( 0, 0, 0, $mmTo, $ddTo, $yyyyTo );
		$termDay = ( ( $toTime - $fromTime ) / 86400 );
		return $fromTime != $toTime ? $termDay + 1: 1;
	}
}
?>
