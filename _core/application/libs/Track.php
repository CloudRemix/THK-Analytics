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

use UAParser\Parser;
use \Woothee\Classifier;

class Track {
	public static function numberReplace( $replace, $subject ) {
		$nums = array( '1','2','3','4','5','6','7','8','9','0' );
		return str_ireplace( $nums, $replace, $subject );
	}

	public static function checkUrl( $url ) {
		//return preg_match('/^(http|https)(:\/\/)[^\/].*$/', $url) ? true : false;
		return parse_url( $url, PHP_URL_SCHEME ) ? true : false;
	}

	public static function checkIpAddressFormat( $value ) {
		if(
			preg_match( '/\A([0-9])+\.([0-9])+\.([0-9])+\.([0-9])+\z/', $value ) ||
			preg_match( '/\A([0-9])+\.([0-9])+\.([0-9])+\.([0-9])+\:([0-9])+\z/', $value )
		) {
			return true;
		}
		return false;
	}

	public static function getRemoteHost( $remoteAddr ) {
		return gethostbyaddr( $remoteAddr );
	}

	public static function getDomain( $remoteHost, $remoteAddr=null ) {
		$domain = Config::NO_DATA;
		if( $remoteAddr !== null && $remoteHost === $remoteAddr ) return $domain;
		if( self::checkIpaddressFormat( $remoteHost ) ) return $domain;
		$pos = substr_count( $remoteHost, '.' );
		switch( $pos ) {
			case 0:
				break;
			case 1:
				$domain = $remoteHost;
				break;
			default:
				$remoteHost = strtolower( strrev( $remoteHost ) );
				list( $d1, $d2, $d3 ) = explode( '.', $remoteHost );
				$d1 = strrev( $d1 );
				$d2 = strrev( $d2 );
				$d3 = strrev( $d3 );

				$_2nddom = array_merge(
					DomainList::$TopLevelDomains,
					DomainList::$jpSecondLevelDomains,
					DomainList::$orgJpSecondLevelDomains
				);
				if( in_array( $d2, $_2nddom ) ) {
					$domain = $d3 . '.' . $d2 . '.' . $d1;
				}
				else {
					$domain = $d2 . '.' . $d1;
				}
				break;
		}
		return $domain;
	}

	public static function getJpDomain( $domain ) {
		$jpDomain = '';
		$revDomain = strtolower( strrev( $domain ) );
		$pos = substr_count( $revDomain, '.' );
		$d1 = $d2 = $d3 = $d4 = $d5 = '';
		switch( $pos ) {
			case 0:
				break;
			case 1:
				list( $d1, $d2 ) = explode( '.', $revDomain, 2 );
				$d1 = strrev( $d1 );
				$d2 = strrev( $d2 );
				if( $d1 === 'jp' ) {
					$prefcity = array_merge( PrefCityList::$prefsAlpha, PrefCityList::$cities );
					if( in_array( $d2, $prefcity ) ) $jpDomain = $domain;
				}
				break;
			default:
				if( $pos === 2 ) { list( $d1, $d2, $d3 ) = explode( '.', $revDomain, 3 ); }
				elseif( $pos === 3 ) { list( $d1, $d2, $d3, $d4 ) = explode( '.', $revDomain, 4 ); }
				else { list( $d1, $d2, $d3, $d4, $d5 ) = explode( '.', $revDomain, 5 ); }
				$d1 = strrev( $d1 );
				$d2 = strrev( $d2 );
				$d3 = strrev( $d3 );
				$d4 = strrev( $d4 );
				$d5 = strrev( $d5 );
				if( $d1 === 'jp' ) {
					if(
						$d3 === 'pref' || $d4 === 'pref' || $d5 === 'pref' ||
						$d3 === 'city' || $d4 === 'city' || $d5 === 'city'
					) {
						$jpDomain = $domain;
					}
					elseif( in_array( $d2, DomainList::$orgJpSecondLevelDomains ) ) {
						if( !in_array( $d3, DomainList::$otherJpThirdLevelDomains ) ) $jpDomain = $domain;
					}
					else {
						if( !in_array( $d2, DomainList::$otherJpSecondLevelDomains ) ) $jpDomain = $domain;
					}
				}
				break;
		}
		return $jpDomain;
	}

	public static function getCountry( $domain, $remoteAddr ) {
		$country = '';

		if( file_exists( THK_GEOIP_DAT ) ) {
			$gip = geoip_open( THK_GEOIP_DAT, GEOIP_STANDARD );
			$dat = GeoIP_record_by_addr( $gip, $remoteAddr );
			geoip_close( $gip );

			$country_code = null;
			if( !empty( $dat->country_code ) ) $country_code = strtolower( $dat->country_code );
			if( $country_code !== null ) {
				if( isset( DomainList::$countryTopLevelDomains[$country_code] ) ) $country = DomainList::$countryTopLevelDomains[$country_code];
			}
		}
		if( !$country ) {
			$revDomain = strtolower( strrev( $domain ) );
			$pos = substr_count( $revDomain, '.' );
			switch( $pos ) {
				case 0:
					break;
				default:
					list( $d1, $d2 ) = explode( '.', $revDomain );
					$d1 = strrev( $d1 );
					$d2 = strrev( $d2 );
					if( isset( DomainList::$countryTopLevelDomains[$d1] ) ) $country = DomainList::$countryTopLevelDomains[$d1];
					break;
			}
			if( !$country ) {
				$country = Config::NO_DATA;
			}
		}
		return $country;
	}

	public static function getPref( $remoteHost, $remoteAddr, $domain ) {
		$pref = Config::NO_DATA;
		$prefKeyword = self::getPrefKeywordByHost( $remoteHost, $domain );

		if( $prefKeyword !== null ) {
			if( isset( PrefCityList::$prefKeywords[$prefKeyword] ) ) $pref = PrefCityList::$prefs[PrefCityList::$prefKeywords[$prefKeyword]];
		}
		if( $pref === Config::NO_DATA ) {
			$prefDomain = self::getPrefByDomain( $domain );
			if( $prefDomain !== null ) $pref = PrefCityList::$prefs[$prefDomain];
		}
		if( $pref === Config::NO_DATA ) {
			if( isset( DomainList::$prefOrgDomains[$domain] ) ) $pref = PrefCityList::$prefs[DomainList::$prefOrgDomains[$domain]];
		}
		if( $pref === Config::NO_DATA ) {
			if( file_exists( THK_GEOIP_DAT ) ) {
				$country = self::getCountry( $domain, $remoteAddr );
				if( $country === '日本' ) {
					$gip = geoip_open( THK_GEOIP_DAT, GEOIP_STANDARD );
					$dat = GeoIP_record_by_addr( $gip, $remoteAddr );
					geoip_close( $gip );

					if( $dat->region !== null ) {
						if( isset( PrefCityList::$prefsGeoIP[$dat->region] ) ) {
							$pref = PrefCityList::$prefsGeoIP[$dat->region];
						}
					}
					if( $pref === Config::NO_DATA ) {
						$pref = $country;
					}
				}
				else {
					if( $country !== null ) {
						$pref = $country;
					}
				}
			}
		}
		return $pref;
	}

	public static function getPrefKeywordByHost( $remoteHost, $domain ) {
		$prefKeyword = null;
		$hostArray = explode( '.', $remoteHost );
		switch( $domain ) {
			case 'odn.ne.jp':
			case 'odn.ad.jp':
				$prefKeyword = substr( $hostArray[0], 0, 3 );
				break;
			case 'ocn.ne.jp':
				$prefKeyword = $hostArray[1];
				break;
			case 'infoweb.ne.jp':
				switch( $hostArray[1] ) {
					case 'adsl':
						$prefKeyword = substr( $hostArray[0], 2, 4 );
						break;
					case 'catv':
						$prefKeyword = substr( $hostArray[0], 0, 4 );
						break;
					case 'ppp':
					case 'mobile':
					case 'em':
						break;
					default:
						if( strlen( $hostArray[1] ) === 4 ) $prefKeyword = $hostArray[1];
						break;
				}
				break;
			case 'mesh.ad.jp':
				$prefKeyword = $hostArray[1];
				break;
			case 'plala.or.jp':
				if( count( $hostArray ) === 7 ) $prefKeyword = $hostArray[2];
				break;
			case 'dion.ne.jp':
				break;
			case 'hi-ho.ne.jp':
				$prefKeyword = substr( $hostArray[0], 0, 3 );
				break;
			case 'so-net.ne.jp':
				$prefKeyword = substr( $hostArray[1], 0, 4 );
				break;
			case 'dti.ne.jp':
				$prefKeyword = substr( $hostArray[1], 0, 4 );
				break;
			case 'alpha-net.ne.jp':
				$prefKeyword = $hostArray[1];
				break;
			case 'vectant.ne.jp':
				$rtn = preg_match( '/[^a-z]*([a-z]+)[^a-z]*\z/', $hostArray[1], $mathces );
				if( is_array( $mathces ) && count( $mathces ) > 1 ) $prefKeyword = $mathces[1];
				break;
			case 'att.ne.jp':
				$tmp = str_ireplace( array( 'ipc','dsl','ftth','newfamily' ), '', $hostArray[1] );
				$tmp = self::numberReplace( '', $tmp );
				$prefKeyword = substr( $tmp, 1 );
				break;
			case 'bbiq.jp':
				$prefKeyword = self::numberReplace( '', $hostArray[1] );
				break;
			case 'commufa.jp':
				$prefKeyword = self::numberReplace( '', $hostArray[1] );
				break;
			case 'coara.or.jp':
				$tmpArray = explode( '-', $hostArray[0] );
				$tmpArray[0] = self::numberReplace( '', $tmpArray[0] );
				$prefKeyword = str_ireplace( 'ap', '', $tmpArray[0] );
				break;
			case 'highway.ne.jp':
				$tmp = str_ireplace( 'ip-', '', $hostArray[1] );
				$tmp = str_ireplace( 'e-', '', $tmp );
				$prefKeyword = $tmp;
				break;
			case 'interq.or.jp':
				$prefKeyword = substr( $hostArray[0], 0, stripos( $hostArray[0], '-' ) + 1 );
				break;
			case 'mbn.or.jp':
				$prefKeyword = $hostArray[1];
				break;
			case 'psinet.ne.jp':
				$prefKeyword = str_ireplace( 'fli-', '', $hostArray[1] );
				break;
			case 'sannet.ne.jp':
				$prefKeyword = $hostArray[1];
				break;
			case 'eonet.ne.jp':
				$prefKeyword = self::numberReplace( '', $hostArray[1] );
				break;
			case 'uu.net':
				$prefKeyword = self::numberReplace( '', $hostArray[2] );
				break;
			case 'zero.ad.jp':
				$tmpArray = explode( '-', $hostArray[0] );
				if( $tmpArray[0] === '-' ) {
					$tmp = $tmpArray[1];
				}
				else {
					$tmp = $tmpArray[0];
				}
				$prefKeyword = self::numberReplace( '', $tmp );
				break;
			case 'pias.ne.jp':
				$tmp = $hostArray[0];
				$tmp = self::numberReplace( '', $tmp );
				$prefKeyword = str_ireplace( '-', '', $tmp );
				break;
			case 'nttpc.ne.jp':
				$prefKeyword = substr( $hostArray[2], stripos( $hostArray[2], '-' ) + 1 );
				break;
			case 'interlink.or.jp':
				$prefKeyword = self::numberReplace( '', $hostArray[1] );
				break;
			case 'kcom.ne.jp':
				$tmp = $hostArray[0];
				$tmp = self::numberReplace( '', $tmp );
				$prefKeyword = str_ireplace( '-', '', $tmp );
				break;
			case 'isao.net':
				$prefKeyword = self::numberReplace( '', $hostArray[1] );
				break;
		}
		return $prefKeyword;
	}

	public static function getPrefByDomain( $domain ) {
		$pref = null;
		$revDomain = strtolower( strrev( $domain ) );
		$pos = substr_count( $revDomain, '.' );
		switch( $pos ) {
			case 0:
				$d1 = '';
				$tmp = '';
				break;
			case 1:
				list( $d1, $tmp ) = explode( '.', $revDomain );
				break;
			default:
				list( $d1, $d2, $tmp ) = explode( '.', $revDomain );
				break;
		}
		$d1 = strrev( $d1 );
		$tmp = strrev( $tmp );
		if( $d1 === 'jp' ) {
			$tmp = self::numberReplace( '', $tmp );
			if( substr_count( $tmp, '-' ) > 0 ) {
				$words = explode( '-', $tmp );
				foreach( $words as $value ) {
					if( isset( PrefCityList::$prefWords[$value] ) ) {
						$pref = PrefCityList::$prefWords[$value];
						break;
					}
				}
			}
			else {
				if( isset( PrefCityList::$prefWords[$tmp] ) ) $pref = PrefCityList::$prefWords[$tmp];
			}
		}
		return $pref;
	}

	public static function getRefererInfo( $httpReferer, $url ) {

		$refererInfo = array( 'keyword' => '', 'engine' => '', 'referer_title' => '', 'referer_host' => '' );

		$referer = strtolower( $httpReferer );
		if( trim( $referer ) === '' ) {
			$refererInfo['referer_title'] = Config::DIRECT_ACCESS;
			$refererInfo['referer_host'] = Config::DIRECT_ACCESS;
			return $refererInfo;
		}
		if( !self::checkUrl($referer) ) {
			$refererInfo['referer_title'] = $httpReferer;
			return $refererInfo;
		}
		$referers = parse_url( $referer );
		if( !$referers ) {
			$refererInfo['referer_title'] = Config::DIRECT_ACCESS;
			$refererInfo['referer_host'] = Config::DIRECT_ACCESS;
			return $refererInfo;
		}
		$urls = parse_url( $url );
		if( !$urls ) {
			$refererInfo['referer_title'] = Config::DIRECT_ACCESS;
			$refererInfo['referer_host'] = Config::DIRECT_ACCESS;
			return $refererInfo;
		}

		$extractReferer = true;

		$refererHost = strtolower( $referers['host'] );
		$urlHost = strtolower( isset($urls['host'] ) ? $urls['host'] : null );
		$query = isset( $referers['query'] ) ? $referers['query'] : '';
		$domain = self::getDomain( $refererHost );
		if( substr_count($domain, '.') > 0 ) {
			list( $hostName, $trash ) = explode( '.', $domain );
		}
		else {
			$hostName = $refererHost;
		}

		$refererUrl  = isset( $referers['scheme'] ) ? $referers['scheme'] . '://' . $refererHost : '';
		$refererUrl .= isset( $referers['path'] ) ? $referers['path'] : '';
		$refererUrl = strtolower( $refererUrl );
		if( $urlHost !== null && strpos( $refererUrl, constant( Config::DEFINE_SITEURL ) ) !== false ) {
			$extractReferer = false;
		}
		if( self::checkIpaddressFormat( $refererHost ) ) {
			if( strpos( $query , 'q=cache:' ) !== false ) {
				$extractReferer = false;
			}
		}
		if( $hostName === 'yahoofs' ) {
			$extractReferer = false;
		}

		$refererInfo['referer_title'] = $httpReferer;

		if( $extractReferer ) {
			$refererInfo['referer_host'] = $refererHost;
			$keyValue = '';

			if( strlen( trim( $query ) ) > 0 ) {
				if( isset( SearchEngineList::$engines[$hostName] ) ) {
					$extractStrings = SearchEngineList::$engines[$hostName];
				}
				else {
					$extractStrings = SearchEngineList::$engines['default'];
				}
				$queryPregString = '';
				foreach( $extractStrings as $queryString ) {
					$queryPregString .= preg_quote( $queryString ) . '|';
				}
				$queryPregString = substr( $queryPregString, 0, strlen( $queryPregString ) - 1 );
				if(
					( !in_array( $hostName, DomainList::$searchEnginesContainMail ) ) ||
					( in_array( $hostName, DomainList::$searchEnginesContainMail ) && substr_count( $refererHost, 'mail' ) === 0 )
				) {
					preg_match( '/(' . $queryPregString . ')[^&]+?(&|$)/', $referer, $matches );
					$keyValue = isset($matches[0]) ? preg_replace( '/(' . $queryPregString . '|&)/', '', $matches[0] ) : '';
				}

				$keyValue_backup = $keyValue;
				$keyValue = urldecode( $keyValue );

				switch( $hostName ) {
					case 'yahoo':
						if( strpos( $query, 'ei=utf-8' ) !== false || strpos( $query, 'fr=ieas' ) !== false ) {
							$keyValue = ThkUtil::convertEncoding( $keyValue, 'UTF-8, JIS, SJIS, ASCII, EUC-JP' );
						}
						elseif( strpos( $query, 'ei=sjis' ) !== false ) {
							$keyValue = ThkUtil::convertEncoding( $keyValue, 'SJIS, JIS, ASCII, EUC-JP, UTF-8' );
						}
						else {
							$keyValue = ThkUtil::convertEncoding( $keyValue, 'EUC-JP, UTF-8, JIS, SJIS, ASCII' );
						}
						break;
					case 'google':
						if( strpos( $query, 'ie=sjis' ) !== false || strpos( $query, 'ie=shift_jis' ) !== false ) {
							if( !ThkUtil::checkDoubleStr( $keyValue_backup ) ) {
								$keyValue = ThkUtil::convertEncoding( $keyValue, 'UTF-8, JIS, SJIS, ASCII, EUC-JP' );
							}
							else {
								$keyValue = ThkUtil::convertEncoding( $keyValue, 'SJIS, JIS, ASCII, UTF-8, EUC-JP' );
							}
						}
						elseif( strpos( $query, 'ie=utf-8' ) !== false ) {
							$keyValue = ThkUtil::convertEncoding( $keyValue, 'UTF-8, JIS, SJIS, ASCII, EUC-JP' );
						}
						elseif( strpos( $query, 'ie=euc-jp' ) !== false ) {
							$keyValue = ThkUtil::convertEncoding( $keyValue, 'EUC-JP, UTF-8, JIS, SJIS, ASCII' );
						}
						else {
							$keyValue = ThkUtil::convertEncoding( $keyValue, 'UTF-8, JIS, SJIS, ASCII, EUC-JP' );
						}
						$keyValue = ThkUtil::convertEncoding( urldecode( $keyValue ), 'auto' );
						break;
					case 'msn':
						if( strpos( $query, 'cp=932' ) !== false ) {
							$keyValue = ThkUtil::convertEncoding( $keyValue, 'SJIS, JIS, ASCII, UTF-8, EUC-JP' );
						}
						else {
							$keyValue = ThkUtil::convertEncoding( $keyValue, 'auto' );
						}
						break;
					case 'goo':
						if( strpos( $query, 'ie=sjis' ) !== false ) {
							$keyValue = ThkUtil::convertEncoding( $keyValue, 'SJIS, JIS, ASCII, EUC-JP, UTF-8' );
						}
						else {
							$keyValue = ThkUtil::convertEncoding( $keyValue, 'EUC-JP, UTF-8, JIS, SJIS, ASCII' );
						}
						break;
					default:
						$keyValue = ThkUtil::convertEncoding( $keyValue, 'EUC-JP, UTF-8, JIS, SJIS, ASCII' );
						break;
				}

				$keyValue = str_replace( array( "\r\n", "\n", "\r", "\t", '　' ), ' ', $keyValue );
				$keyValue = preg_replace( '/\s+/', ' ', $keyValue );
				$refererInfo['keyword'] = trim( $keyValue );
				if( strlen( $keyValue ) > 0 ) {
					$refererInfo['referer_title'] = '@' . $hostName . ':[' . $keyValue . ']';
					$refererInfo['engine'] = $hostName;
				}

			}

		}
		return $refererInfo;
	}

	public static function getOs( $httpUserAgent ) {
		$os = Config::NO_DATA."\t".Config::NO_DATA;

		$agent = strtolower( str_replace( ' ', '', $httpUserAgent ) );
		foreach( OsList::$os as $k => $v ) {
			if( strpos( $agent, $k ) !== false ) {
				return $v . "\t" . Config::NO_DATA;
			}
		}

		$parser = Parser::create();
		$ret = $parser->parse( $httpUserAgent );
		if( $ret->os->family !== null ) {
			if( $ret->os->family === 'Other' ) {
				$classifier = new \Woothee\Classifier;
				$r = $classifier->parse( $httpUserAgent );
				if( $r['os'] !== 'UNKNOWN' ) {
					$os = $r['os'];
				}
				if( $r['os_version'] !== 'UNKNOWN' ) {
					if( strpos( $r['os_version'], '.' ) ) {
						list( $major, $minor ) = explode( '.', $r['os_version'] );
						$os .= "\t" . $major . '.x';
					}
					else {
						$os .= "\t" . $r['os_version'];
					}
				}
				elseif( $r['category'] === 'mobilephone' ) {
					$os .= "\t" . $r['version'];
				}
			}
			else {
				if( stripos( $ret->os->family, 'Windows' ) !== false && $ret->os->major === null ) {
					$os = str_replace( 'Windows ', 'Windows' . "\t", $ret->os->family );
				}
				elseif( $ret->os->family === 'iOS' ) {
					if( stripos( $ret->device->family, 'iPhone' ) ) {
						$os = 'iPhone';
					}
					elseif( stripos( $ret->device->family, 'iPad' ) ) {
						$os = 'iPad';
					}
					elseif( stripos( $ret->device->family, 'iPod' ) ) {
						$os = 'iPod';
					}
					else {
						$os = $ret->device->family;
					}
				}
				else {
					$os = $ret->os->family;
				}
				if( $ret->os->major !== null ) {
					if( substr( $ret->os->family, 0, 4 ) === 'Mac ' && $ret->os->minor ) {
						$os .= "\t" . $ret->os->major . '.' . $ret->os->minor;
					}
					else {
						$os .= "\t" . $ret->os->major . '.x';
					}
				}
			}
		}
		return $os;
	}

	public static function getBrowser($httpUserAgent) {
		$browser = Config::NO_DATA . "\t" . Config::NO_DATA;

		$agent = strtolower( str_replace( ' ', '', $httpUserAgent ) );
		foreach( BrowserList::$browser as $k => $v ) {
			if( strpos( $agent, $k ) !== false ) {
				return $v . "\t" . Config::NO_DATA;
			}
		}

		$parser = Parser::create();
		$ret = $parser->parse( $httpUserAgent );
		if( $ret->ua->family !== null ) {
			if( $ret->ua->family === 'Other' ) {
				$classifier = new \Woothee\Classifier;
				$r = $classifier->parse( $httpUserAgent );
				if( $r['name'] !== 'UNKNOWN' ) {
					$browser = $r['name'];
				}
				if( $r['version'] !== 'UNKNOWN' ) {
					if( strpos( $r['version'], '.' ) !== false ) {
						list( $major, $minor ) = explode( '.', $r['version'] );
						$browser .= "\t" . $major . '.x';
					}
					else {
						$browser .= "\t" . $r['version'];
					}
				}
			}
			else {
				$browser = $ret->ua->family;
				if( $browser === 'Android' ) {
					$browser = 'Android Browser';
				}
				elseif( $browser === 'IE' ) {
					$browser = 'Internet Explorer';
				}

				if( $ret->ua->major !== null ) {
					$browser .= "\t" . $ret->ua->major . '.x';
				}
			}
		}
		return $browser;
	}

	public static function getCrawler( $httpUserAgent ) {
		$classifier = new \Woothee\Classifier;
		if( $classifier->isCrawler( $httpUserAgent ) ) {
			$r = $classifier->parse( $httpUserAgent );
			return $r['name'];
		}
		else {
			$httpUserAgent = strtolower( str_replace( ' ', '', $httpUserAgent ) );
			foreach( CrawlerList::$crawler as $k => $v ) {
				if( strpos( $httpUserAgent, $k ) !== false ) return $v;
			}
		}
		return '';
	}

	public static function getCrawlerIP( $ip ) {
		$netmask = 32;
		foreach( CrawlerList::$crawlerIP as $k => $v ) {
			$sep = substr_count( $k, '/' );
			if( $sep ) {
				if( strpos( $k, '/' ) !== false ) list( $k, $netmask ) = explode( '/', $k );
				$check = ip2long( $ip ) >> ( 32 - $netmask );
				$long  = ip2long( $k ) >> ( 32 - $netmask );
				if( $check == $long )  return $v;
			}
			else {
				if( preg_match( "/${k}/", $ip ) ) return $v;
			}
		}
		return '';
	}

	public static function getCrawlerHOST( $host ) {
		foreach( CrawlerList::$crawlerHOST as $k => $v ) {
			if( preg_match( "/${k}/", $host ) ) return $v;
		}
		return '';
	}

	public static function setCookie( $key, $value, $enableDays=null, $path=null ) {
		if( $enableDays === null ) $enableDays = Config::COOKIE_ENABLE_DAYS;
		ThkUtil::setCookie( $key, $value, $enableDays, $path );
	}

	public static function clearCookie( $key, $path=null ) {
		ThkUtil::setCookie( $key, '', 0, $path );
	}

	public static function generateUid( $trackInfo ) {
		$os = $trackInfo['os'];
		$ov = $trackInfo['os_ver'];
		$bw = $trackInfo['browser'];
		$bv = $trackInfo['brow_ver'];
		$rh = $trackInfo['remote_host'];
		$uid = self::makeHash( $rh . $os . $ov . $bw . $bv ) . $trackInfo['uid'];
		$trackInfo['uid'] = $uid;

		return $trackInfo;
	}

	public static function makeHash( $data ) {
		return strtr( rtrim( base64_encode( pack( 'H*', hash( 'crc32b', $data ) ) ), '=' ), '+/', '01' );
	}

	public static function initializeTrackInfo() {
		$trackInfo = array();
		$trackInfo['dd'] = '';
		$trackInfo['logtype'] = '';
		$trackInfo['uid'] = '';
		$trackInfo['hh'] = '';
		$trackInfo['mi'] = '';
		$trackInfo['ss'] = '';
		$trackInfo['weekday'] = '';
		$trackInfo['remote_addr'] = '';
		$trackInfo['remote_host'] = '';
		$trackInfo['domain'] = '';
		$trackInfo['jpdomain'] = '';
		$trackInfo['country'] = '';
		$trackInfo['pref'] = '';
		$trackInfo['title'] = '';
		$trackInfo['url'] = '';
		$trackInfo['screenwh'] = '';
		$trackInfo['screencol'] = '';
		$trackInfo['jsck'] = '';
		$trackInfo['os'] = '';
		$trackInfo['os_ver'] = '';
		$trackInfo['browser'] = '';
		$trackInfo['brow_ver'] = '';
		$trackInfo['crawler'] = '';
		$trackInfo['keyword'] = '';
		$trackInfo['engine'] = '';
		$trackInfo['referer_title'] = '';
		$trackInfo['referer_host'] = '';
		$trackInfo['http_user_agent'] = '';
		$trackInfo['http_referer'] = '';
		return $trackInfo;
	}

	public static function getTrackInfo() {
		$trackInfo = self::initializeTrackInfo();

		$system = new System();
		$systemData = $system->find( 'timezone' );
		if( !empty( $systemData['timezone'] ) && $systemData['timezone'] !== 'default' ) {
			date_default_timezone_set( $systemData['timezone'] );
		}

		$now = $_SERVER['REQUEST_TIME'];
		$trackInfo['yyyy'] = date( 'Y', $now );
		$trackInfo['mm']   = date( 'm', $now );
		$trackInfo['dd']   = date( 'd', $now );
		$trackInfo['hh']   = date( 'H', $now );
		$trackInfo['mi']   = date( 'i', $now );
		$trackInfo['ss']   = date( 's', $now );
		$trackInfo['weekday'] = date( 'w', mktime( $trackInfo['hh'], $trackInfo['mi'], $trackInfo['ss'], $trackInfo['mm'], $trackInfo['dd'], $trackInfo['yyyy'] ) );

		$trackInfo['http_user_agent'] = $_SERVER['HTTP_USER_AGENT'];
		if( strlen( $trackInfo['http_user_agent'] ) === 0 ) $trackInfo['http_user_agent'] = Config::NO_DATA;

		$_os = self::getOs( $trackInfo['http_user_agent'] );
		list( $os, $os_ver ) = array_pad( explode( "\t", $_os ), 2, '');
		$trackInfo['os'] = $os;
		$trackInfo['os_ver'] = ( $os_ver ) ? $os_ver : Config::NO_DATA;

		$_browser = self::getBrowser( $trackInfo['http_user_agent'] );
		list( $browser, $brow_ver ) = array_pad( explode( "\t", $_browser ), 2, '');
		$trackInfo['browser'] = $browser;
		$trackInfo['brow_ver'] = ( $brow_ver ) ? $brow_ver : Config::NO_DATA;

		$trackInfo['remote_addr'] = $_SERVER['REMOTE_ADDR'];
		$trackInfo['remote_host'] = self::getRemoteHost( $trackInfo['remote_addr'] );
		$trackInfo['remote_host'] = $trackInfo['remote_addr'] !== $trackInfo['remote_host'] ? $trackInfo['remote_host'] : Config::NO_DATA;
		$trackInfo['domain'] = self::getDomain( $trackInfo['remote_host'], $trackInfo['remote_addr'] );
		$trackInfo['jpdomain'] = self::getJpDomain( $trackInfo['domain'] );
		$trackInfo['country'] = self::getCountry( $trackInfo['domain'], $trackInfo['remote_addr'] );
		$trackInfo['pref'] = self::getPref( $trackInfo['remote_host'], $trackInfo['remote_addr'], $trackInfo['domain'] );

		$trackInfo['crawler'] = self::getCrawler( $trackInfo['http_user_agent'] );
		if( !$trackInfo['crawler'] ) {
			$trackInfo['crawler'] = self::getCrawlerIP( $trackInfo['remote_addr'] );
		}
		if( !$trackInfo['crawler'] ) {
			$trackInfo['crawler'] = self::getCrawlerHOST( $trackInfo['remote_host'] );
		}

		return $trackInfo;
	}

	public static function writeLog( $trackInfo ) {
		$trackInfo = self::generateUid( $trackInfo );
		try {
			if( is_array($trackInfo) ) {
				$log = new Log();
				$log->setValue( 'dd', $trackInfo['dd'] );
				$log->setValue( 'logtype', $trackInfo['logtype'] );
				$log->setValue( 'uid', $trackInfo['uid'] );
				$log->setValue( 'hh', $trackInfo['hh'] );
				$log->setValue( 'mi', $trackInfo['mi'] );
				$log->setValue( 'ss', $trackInfo['ss'] );
				$log->setValue( 'weekday', $trackInfo['weekday'] );
				$log->setValue( 'remote_addr', $trackInfo['remote_addr'] );
				$log->setValue( 'remote_host', $trackInfo['remote_host'] );
				$log->setValue( 'domain', $trackInfo['domain'] );
				$log->setValue( 'jpdomain', $trackInfo['jpdomain'] );
				$log->setValue( 'country', $trackInfo['country'] );
				$log->setValue( 'pref', $trackInfo['pref'] );
				$log->setValue( 'title', $trackInfo['title'] );
				$log->setValue( 'url', $trackInfo['url'] );
				$log->setValue( 'screenwh', $trackInfo['screenwh'] );
				$log->setValue( 'screencol', $trackInfo['screencol'] );
				$log->setValue( 'jsck', $trackInfo['jsck'] );
				$log->setValue( 'os', $trackInfo['os'] );
				$log->setValue( 'os_ver', $trackInfo['os_ver'] );
				$log->setValue( 'browser', $trackInfo['browser'] );
				$log->setValue( 'brow_ver', $trackInfo['brow_ver'] );
				$log->setValue( 'crawler', $trackInfo['crawler'] );
				$log->setValue( 'keyword', $trackInfo['keyword'] );
				$log->setValue( 'engine', $trackInfo['engine'] );
				$log->setValue( 'referer_title', $trackInfo['referer_title'] );
				$log->setValue( 'referer_host', $trackInfo['referer_host'] );
				$log->setValue( 'http_user_agent', $trackInfo['http_user_agent'] );
				$log->setValue( 'http_referer', rtrim( $trackInfo['http_referer'], '/' ) );
				$log->save();
				if( $trackInfo['url'] !== $trackInfo['title'] && trim( $trackInfo['title'] ) !== '' ) {
					$title = new Title();
					$title->setIgnore( true );
					$title->setValue( 'url', $trackInfo['url'] );
					$title->setValue( 'title', $trackInfo['title'] );
					$title->setValue( 'logtype', $trackInfo['logtype']);
					$title->save();
				}
			}

		}
		catch( Exception $exception ) {
			echo $exception->getMessage();
		}
	}

	public static function doScript() {
		$script = self::generateScript();
		if( class_exists('JSMin') ) {
			$script = trim( JSMin::minify( $script ) );
		}
		header( 'Content-type: application/x-javascript' );
		echo $script;
	}

	public static function doTrack() {
		$nocount = isset( $_COOKIE[Config::getCookieKeyAdminNocount()] ) ? Config::ON : Config::OFF;

		$trackInfo = self::getTrackInfo();
		$cookieCheck = false;
		$uid = self::makeHash( $trackInfo['remote_addr'] . $trackInfo['http_user_agent'] );
		self::setCookie( Config::COOKIE_UID, $uid, 0 );

		if( isset($_REQUEST['LT'] ) ) {
			header( 'Content-type: application/x-javascript' );
			if( $nocount === Config::ON ) return;
			$cookieCheck = isset( $_COOKIE[Config::COOKIE_UID] ) ? true : false;
			if( !$cookieCheck && isset( $_REQUEST['CC'] ) ) $cookieCheck = $_REQUEST['CC'];
			if( $cookieCheck ) {
				$trackInfo['jsck'] = Config::JC_ENABLED_JC;
			}
			else {
				$trackInfo['jsck'] = Config::JC_ENABLED_J;
			}
			$trackInfo['uid'] = $uid;
			$trackInfo['logtype'] = $_REQUEST['LT'];
			$trackInfo['http_referer'] = $_REQUEST['RF'];
			$trackInfo['url'] = $_REQUEST['UR'];
			$trackInfo['title'] = trim($_REQUEST['TI']) !== '' ? ThkUtil::convertEncoding($_REQUEST['TI'], 'auto') : $trackInfo['url'];
			if( $trackInfo['logtype'] === Config::CLICK_ADSENSE ) {
				$trackInfo['url'] = urldecode( $trackInfo['url'] );
				$trackInfo['title'] = urldecode( $trackInfo['title'] );
			}
			$trackInfo['screenwh'] = $_REQUEST['SW'] . 'x' . $_REQUEST['SH'];
			if( strlen( $_REQUEST['SW'] . $_REQUEST['SH'] ) === 0 ) $trackInfo['screenwh'] = Config::NO_DATA;
			$trackInfo['screencol'] = $_REQUEST['SC'] . 'bit';
			if( strlen( $_REQUEST['SC'] ) === 0 ) $trackInfo['screencol'] = Config::NO_DATA;
			$refererInfo = self::getRefererInfo($trackInfo['http_referer'], $trackInfo['url']);
			$trackInfo['keyword'] = $refererInfo['keyword'];
			$trackInfo['engine'] = $refererInfo['engine'];
			$trackInfo['referer_title'] = $refererInfo['referer_title'];
			$trackInfo['referer_host'] = $refererInfo['referer_host'];
		}
		else {
			if(
				strpos( $trackInfo['http_user_agent'], 'J-PHONE' ) !== false ||
				strpos( $trackInfo['http_user_agent'], 'Vodafone' ) !== false ||
				strpos( $trackInfo['http_user_agent'], 'Softbank' ) !== false
			) {
				header( 'Content-type: image/png' );
				echo base64_decode( 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVQI12NgYAAAAAMAASDVlMcAAAAASUVORK5CYII' );
			}
			else {
				header( 'Content-type: image/gif' );
				echo base64_decode( 'R0lGODlhAQABAGAAACH5BAEKAP8ALAAAAAABAAEAAAgEAP8FBAA7' );
			}
			if( $nocount === Config::ON ) return;
			$cookieCheck = isset( $_COOKIE[Config::COOKIE_UID] ) ? true : false;
			if( $cookieCheck ) {
				$trackInfo['jsck'] = Config::JC_ENABLED_C;
			}
			else {
				$trackInfo['jsck'] = Config::JC_ENABLED_N;
			}
			$trackInfo['uid'] = $uid;
			$trackInfo['logtype'] = Config::NORMAL_ACCESS;
			$page = isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : null;
			$page = ThkUtil::convertEncoding($page, 'SJIS, JIS, ASCII, EUC-JP, UTF-8');
			$trackInfo['http_referer'] = Config::FROM_NO_SCRIPT;
			$trackInfo['url'] = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : null;
			if( strlen( trim( $trackInfo['url'] ) ) === 0 ) {
				$trackInfo['url'] = ( strlen ( trim( $page ) ) === 0 ) ? Config::NO_DATA : $page;
			}
			$trackInfo['title'] = $trackInfo['url'];
			if( strlen( trim( $page ) ) !== 0 ) $trackInfo['title'] = $page;
			$trackInfo['screenwh'] = Config::NO_DATA;
			$trackInfo['screencol'] = Config::NO_DATA;
			$trackInfo['keyword'] = '';
			$trackInfo['engine'] = '';
			$trackInfo['referer_title'] = Config::FROM_NO_SCRIPT;
			$trackInfo['referer_host'] = '';

		}

		self::writeLog( $trackInfo );
	}

	public static function doPhpTrack( $title=null ) {
		$nocount = isset( $_COOKIE[Config::getCookieKeyAdminNocount()] ) ? Config::ON : Config::OFF;
		if( $nocount === Config::ON ) return;

		$trackInfo = self::getTrackInfo();
		$trackInfo['uid'] = self::makeHash( $trackInfo['remote_addr'] . $trackInfo['http_user_agent'] );

		$trackInfo['logtype'] = Config::NORMAL_ACCESS;
		$trackInfo['http_referer'] = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER']: '';
		$host = $_SERVER['SERVER_NAME'];
		$protocol = ( isset($_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== '' && $_SERVER['HTTPS'] != 'off' ) ? 'https://' : 'http://';
		$trackInfo['url'] = $protocol . $host . $_SERVER['REQUEST_URI'];
		$trackInfo['title'] = $title !== null && trim( $title ) !== '' ? ThkUtil::convertEncoding( $title, 'auto' ) : $trackInfo['url'];
		$trackInfo['screenwh'] = Config::NO_DATA;
		$trackInfo['screencol'] = Config::NO_DATA;
		$trackInfo['jsck'] = Config::NO_DATA;
		$refererInfo = self::getRefererInfo( $trackInfo['http_referer'], $trackInfo['url'] );
		$trackInfo['keyword'] = $refererInfo['keyword'];
		$trackInfo['engine'] = $refererInfo['engine'];
		$trackInfo['referer_title'] = $refererInfo['referer_title'];
		$trackInfo['referer_host'] = $refererInfo['referer_host'];
		self::writeLog( $trackInfo );
	}

	public static function generateScript() {
		$host = $_SERVER['SERVER_NAME'];
		$httpUserAgent = $_SERVER['HTTP_USER_AGENT'];
		$remoteAddr = $_SERVER['REMOTE_ADDR'];
		$protocol = '//';
		$scriptname = $_SERVER['SCRIPT_NAME'];
		$scriptbase = basename( $_SERVER['SCRIPT_NAME'] );
		if( stripos( $scriptname, 'view/index.php' ) !== false ) $scriptname = str_replace( 'view/index.php', $scriptbase, $scriptname );
		$trackUrl = $protocol. $host. str_replace( $scriptbase, 'track.php', $scriptname );
		$clickRel = Config::CLICK_REL;
		$clickNormal = Config::NORMAL_ACCESS;
		$clickLink = Config::CLICK_LINK;
		$clickBtn = Config::CLICK_BTN;
		$clickAdsense = Config::CLICK_ADSENSE;

		$script = <<<END
(function() {
	var win = window,
	doc = document,
	nav = navigator,
	loc = win.location,
	pnt = win.parent,
	pdoc = pnt.document,
	ploc = pnt.location,
	referer = (loc.href != ploc.href) ? pdoc.referrer : doc.referrer,
	requrl = loc.protocol + '//' + loc.host + loc.pathname + loc.search,
	cok = (nav.cookieEnabled) ? true : false,
	/*
	ec = function( o ) {
		var r = false;
		if( o === null ) r = true;
		return r;
	},
	gt = function() {
		return (new Date).getTime();
	},
	*/
	ua = nav.userAgent.toLowerCase(),
	ver = (ua.match(/.+(?:on|me|rv|it|ra|ie)[\/: ]([\d.]+)/) || [])[1],
	saf = /^(?!.*chrome)(?=.*safari)(?!.*edge).*/.test(ua),
	opr = /opera/.test(ua),
	mie = /(msie|trident)/.test(ua) && !opr,
	oie = mie && parseInt(ver, 10) < 9 ? true : false,
	moz = /mozilla/.test(ua) && !/(compatible|webkit)/.test(ua),
	ios = /(iphone|ipod|ipad)/.test(ua),
	aid = /android/.test(ua),
	mob = /mobile/.test(ua),
	trk = function() {
		var t = (new Date()).getTime();
		this.atime = t;
		return this;
	};
	trk.prototype = {
		atime: null,
		type: {
			load: '$clickNormal',
			clik: '$clickLink',
			bclk: '$clickBtn',
			adse: '$clickAdsense'
		},
		ckr: '$clickRel',
		urt: '$trackUrl',
		gtl: function() {
			if( doc.getElementById('title') ) {
				var elmT = doc.getElementsByTagName('title')[0];
				return (mie) ? elmT.innerHTML : elmT.firstChild.nodeValue;
			}
			else {
				return doc.title;
			}
		},
		urg: function( url, ttl, referer, type ) {
			return this.urt + '?LT=' + type + '&RF=' + encodeURIComponent(referer) + '&UR=' + encodeURIComponent(url) + '&TI=' + encodeURIComponent(ttl) + '&SW=' + screen.width + '&SH=' + screen.height + '&SC=' + screen.colorDepth + '&CC=' + cok + '&s=' + Math.floor(Math.random() * 100);
		},
		snd: function( url, dom ) {
			if( dom ) {
				var ite = doc.getElementById('item');
				var bde = (ite) ? ite : doc.getElementsByTagName('body').item(0);
				var nn = doc.createElement('script');
				nn.async = false;
				nn.defer = true;
				nn.src = url;
				bde.appendChild(nn);
				this.wat(0.4);
			}
			else {
				if( oie ) {
					var nn = doc.createElement('div');
					nn.innerHTML = '&nbsp;<script defer="defer" type="text/javascript" src="' + url + '" ></script>';
					doc.body.appendChild(nn);
				}
				else {
					//doc.write('<script type="text/javascript" src="' + url + '" async defer></script>');
					var nn = doc.createElement('script');
					nn.async = false;
					nn.defer = true;
					nn.type = 'text/javascript';
					nn.src = url;
					var s = doc.getElementsByTagName('script');
					var cc = s[s.length-1];
					cc.parentNode.insertBefore(nn, cc);
				}
			}
			return this;
		},
		cct: function( e ) {
			var url = ttl = rel = '';
			var tge = this.gge(e);
			var tgn = tge.nodeName.toLowerCase();
			var cck = function(url, rel, ckr) {
				return (url && url.match("^(https?:\/\/|ftp:\/\/|\/\/)") && (!url.match(loc.host) || rel == ckr)) ? true : false;
			}
			switch( tgn ) {
				case 'a':
					url = tge.href;
					ttl = (mie) ? tge.innerText : tge.textContent;
					rel = (typeof tge.rel !== "undefined") ? tge.rel : '';
					if( cck(url, rel, this.ckr) ) this.snd(this.urg(url, ttl, requrl, this.type.clik), true);
					break;
				case 'input':
				case 'button':
					if( tge.type.toLowerCase() == 'button' || tge.type.toLowerCase() == 'submit' || tge.type.toLowerCase() == 'image' ) {
						if( tge.value ) {
							url = requrl + '#' + tge.value;
							ttl = '[' + tge.value + '] (' + this.gtl() + ')';
						}
						else if( tge.id ) {
							url = requrl + '#' + tge.id;
							ttl = '[' + tge.id.toLowerCase() + '] (' + this.gtl() + ')';
						}
						else {
							url = requrl + '#' + tge.type;
							ttl = '[' + tge.type.toLowerCase() + '] (' + this.gtl() + ')';
						}
						this.snd(this.urg(url, ttl, requrl, this.type.bclk), true);
					}
					break;
				default:
					if( typeof tge.parentNode.href !== "undefined" ) {
						url = tge.parentNode.href;
						ttl = (typeof tge.alt !== "undefined") ? tge.alt : ((oie) ? tge.innerText : tge.firstChild.nodeValue);
						rel = (typeof tge.parentNode.rel !== "undefined") ? tge.parentNode.rel : '';
						if( cck(url, rel, this.ckr) ) this.snd(this.urg(url, ttl, requrl, this.type.clik), true);
					}
					break;
			}
		},
		ada: [],
		adi: [],
		ado: false,
		ade: null,
		igt: null,
		adt: function() {
			if( this.ado ) {
				for( var i = 0; i < this.ada.length; i++ ) {
					if( this.ada[i] == this.ade ) {
						var url = encodeURIComponent('Unit=' + (i + 1) + ',Size=' + this.ada[i].width + 'x' + this.ada[i].height);
						this.snd(this.urg(url, url, requrl, this.type.adse), true);
						this.ado = false;
						break;
					}
				}
			}
		},
		ads: function( e ) {
			var ife = doc.getElementsByTagName('iframe');
			var findAd = false;
			for( var i = 0; i < ife.length; i++ ) {
				findAd = false;
				if( ife[i].src.indexOf('googlesyndication.com') > -1 || ife[i].src.indexOf('googleads.g.doubleclick.net') > -1 ) findAd = true;
				if( ife[i].id && ife[i].id.indexOf('aswift_') > -1 && ife[i].parentNode.tagName.toLowerCase() == 'ins' && typeof ife[i].parentNode.id !== "undefined" && ife[i].parentNode.id.indexOf('aswift_') > -1 ) findAd = true;
				if( findAd ) {
					this.ada[this.ada.length] = ife[i];
					if( oie ) {
						this.aev('focus', trk.tfr.adf, ife[i]);
						this.aev('blur', trk.tfr.adb, ife[i]);
						this.aev('beforeunload', trk.tfr.adt, win);
					}
					else {
						this.aev('mouseover', trk.tfr.adf, ife[i]);
						//setTimeout(this.aev('mouseout', trk.tfr.adb, ife[i]),1e3);
						this.aev('mouseout', trk.tfr.adb, ife[i]);
						if( ios || aid || mob ) {
							this.aev('click', trk.tfr.adt, win);
						}
						else if( opr ) {
							this.aev('unload', trk.tfr.adt, win);
						}
						//else if( saf ) {
						//	this.aev('pagehide', trk.tfr.adt, win);
						//}
						else {
							this.aev('beforeunload', trk.tfr.adt, win);
						}
						this.aev('DOMContentLoaded', trk.tfr.adt, ife[i]);
					}
				}
			}
		},
		adf: function( e ) {
			this.ado = true;
			this.ade = this.gge(e);
			//event.preventDefault();
		},
		adb: function() {
			this.ado = false;
			this.ade = null;
		},
		drd: function( c ) {
			if( oie ) {
				(function() {
					try {
						doc.documentElement.doScroll('left');
					}
					catch( error ) {
						setTimeout(arguments.callee, 0);
						return;
					}
					c.apply(doc);
				})();
			}
			else {
				if(doc.addEventListener) {
					doc.addEventListener('DOMContentLoaded', c, false);
				}
				else {
					win.attachEvent ? win.attachEvent('onload', c) : win.addEventListener('load', c, false);
				}
			}
			//return doc;
		},
		aev: function( e, c, o ) {
			if( (typeof o.nodeType !== "undefined" && (o.nodeType === 1 || o.nodeType === 9)) || o === win ) {
				o.attachEvent ? o.attachEvent('on' + e, c) : o.addEventListener(e, c, false);
			}
			return o;
		},
		gge: function( e ) {
			return win.event ? win.event.srcElement : e.target;
		},
		wat: function( sec ) {
			var w = (new Date()).getTime() + (sec * 1000);
			while( true ) {
				if( (new Date()).getTime() > w ) return;
			}
		},
		dtk: function() {
			this.snd(this.urg(requrl, this.gtl(), referer, this.type.load), false);
			if( mie ) {
				this.aev("click", trk.tfr.cct, doc);
				this.aev("contextmenu", trk.tfr.cct, doc);
			}
			else {
				this.aev("click", trk.tfr.cct, doc);
			}
			this.drd(trk.tfr.ads);
			return this;
		}
	}
	trk.tfr = {
		cct: function( e ) {
			__trk.cct( e );
		},
		adt: function( e ) {
			__trk.adt( e );
		},
		ads: function( e ) {
			__trk.ads( e );
		},
		adf: function( e ) {
			__trk.adf( e );
		},
		adb: function( e ) {
			__trk.adb( e );
		}
	}
	win.__trk = new trk();
	__trk.dtk();
})()
END;
		return $script;
	}
}
?>
