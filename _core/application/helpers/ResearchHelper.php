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
 * Graphed by the ccchart - http://ccchart.com/
 * The MIT License Copyright (c) 2013 Toshiro Takahashi
 */

class ResearchHelper extends BaseHelper {
	public function __construct( ThkRequest $request, ThkSession $session, ThkMessage $message, ThkResult $result=null, $controller, $action ) {
		parent::__construct( $request, $session, $message, $result, $controller, $action );
	}

	public function getRemoteAddrResult() {
		$rtn = '';
		$remoteAddr = $this->result->get('remoteAddr');
		if( is_array( $remoteAddr ) ) {
			foreach( $remoteAddr as $v ) {
				$rtn = $v;
				break;
			}
		}
		return $rtn;
	}

	public function getRemoteHostResult() {
		$rtn = '';
		$remoteHost = $this->result->get('remoteHost');
		if( is_array( $remoteHost ) ) {
			foreach( $remoteHost as $v ) {
				$rtn = $v;
				break;
			}
		}
		return $rtn;
	}

	public function getDomainResult() {
		$rtn = '';
		$domain = $this->result->get('domain');
		if( is_array( $domain ) ) {
			foreach( $domain as $v ) {
				$rtn = $v;
				break;
			}
		}
		return $rtn;
	}

	public function getEngine() {
		return $this->request->get('engine');
	}

	public function getKeyword() {
		return $this->request->get('keyword');
	}

	public function getOs() {
		return $this->request->get('os');
	}

	public function getBrowser() {
		return $this->request->get('browser');
	}

	public function getOsImage( $os ) {
		foreach( ImageList::$osImages as $k => $img ) {
			if( stripos( str_replace( ' ', '', $os ), $k ) !== false ) {
				return $img;
			}
		}
		return ImageList::$generalImages['empty'];
	}

	public function getBrowserImage( $browser ) {
		foreach( ImageList::$browserImages as $k => $img ) {
			if( stripos( str_replace( ' ', '', $browser ), $k ) !== false ) {
				return $img;
			}
		}
		return ImageList::$generalImages['empty'];
	}

	public function getHostImage( $host ) {
		if( Track::checkUrl( $host ) ) {
			if( parse_url( $host ) ) {
				$urls = parse_url( $host );
				$host = $urls['host'];
			}
		}
		$domain = Track::getDomain($host);

		foreach( ImageList::$hostImages as $k => $img ) {
			if( $host === $k || $domain === $k ) {
				return $img;
			}
		}

		foreach( ImageList::$hostImages as $k => $img ) {
			if( stripos( $domain, '.' . $k . '.' ) !== false || stripos( $domain, $k . '.' ) === 0 ) {
				return $img;
			}
			elseif( $this->action === 'engine' || $this->action === 'engine_key' || $this->action === 'key_engine' ) {
				if( $host === substr( $k, 0, stripos( $k, '.' ) ) ) {
					return $img;
				}
				$_2dom = explode( '.', $k );
				if( count( $_2dom ) > 2 && isset( $_2dom[1] ) ) {
					if( $host === $_2dom[1] ) {
						return $img;
					}
				}
			}
		}
		return ImageList::$generalImages['empty'];
	}

	public function getCountryImage( $country ) {
		if( isset( $country, CountryAndPrefImages::$countryImages[$country] ) ) {
			$ret = CountryAndPrefImages::$countryImages[$country];
		}
		elseif( isset( CountryAndPrefImages::$countryImages[$country] ) ) {
			$ret = CountryAndPrefImages::$countryImages[$country];
		}
		else {
			$ret = ImageList::$generalImages['empty'];
		}
		return $ret;
	}

	public function getPrefImage( $pref ) {
		if( isset( $pref, CountryAndPrefImages::$prefImages[$pref] ) ) {
			$ret = CountryAndPrefImages::$prefImages[$pref];
		}
		elseif( isset( CountryAndPrefImages::$countryImages[$pref] ) ) {
			$ret = CountryAndPrefImages::$countryImages[$pref];
		}
		else {
			$ret = ImageList::$generalImages['empty'];
		}
		return $ret;
	}

	public function dispView() {
		$siteData = $this->session->get('siteData');
		echo $siteData['dispview'];
	}

	public function allCount() {
		echo $this->getFormatNumber( $this->result->get('allCount') );
	}

	public function uniqueCount() {
		echo $this->getFormatNumber( $this->result->get('uniqueCount') );
	}

	public function totalCount() {
		echo $this->getFormatNumber( $this->result->get('totalCount') );
	}

	public function uniqueAverage() {
		echo $this->getFormatNumber( round( $this->result->get('uniqueCount') / $this->_getTermDay() ) );
	}

	public function totalAverage() {
		echo $this->getFormatNumber( round( $this->result->get('totalCount') / $this->_getTermDay() ) );
	}

	public function bounceAverage() {
		$total = ( $this->result->get('totalCount') > 0 ) ? $this->result->get('totalCount') : 1;
		return sprintf( '%.2f', round( $this->result->get('uniqueCount') * 100 / $total, 2 ) );
	}

	public function clickLinkCount() {
		echo $this->getFormatNumber( $this->result->get('clickLinkCount') );
	}

	public function clickBtnCount() {
		echo $this->getFormatNumber( $this->result->get('clickBtnCount') );
	}

	public function clickAdsenseCount() {
		echo $this->getFormatNumber( $this->result->get('clickAdsenseCount') );
	}

	public function engine() {
		echo $this->escapeHtml( $this->getEngine() );
	}

	public function keyword() {
		echo $this->escapeHtml( $this->getKeyword() );
	}

	public function os() {
		echo $this->escapeHtml( $this->getOs() );
	}

	public function browser() {
		echo $this->escapeHtml( $this->getBrowser() );
	}

	public function remoteAddr() {
		echo $this->escapeHtml( $this->getRemoteAddrResult() );
	}

	public function remoteHost() {
		echo $this->escapeHtml( $this->getRemoteHostResult() );
	}

	public function pageTag( $pageCount ) {
		$count = null;
		if( $pageCount === self::PAGE_COUNT_UNIQUE ) $count = $this->result->get('uniqueCount');
		if( $pageCount === self::PAGE_COUNT_ALL ) {
			$count = $this->result->get('allCount')
			+ $this->result->get('clickLinkCount')
			+ $this->result->get('clickBtnCount')
			+ $this->result->get('clickAdsenseCount');
		}
		return parent::pageTag( $count );
	}

	public function timePageTag() {
		$html = '<div class="page">';
		if( (int)$this->request->get('select') === 0 ) {
			$html .= '<a href="'. $this->getIndexUrl('research', 'time_detail', '&amp;select=01') . '">1時のアクセス詳細&#8811;</a>';
		}
		elseif( (int)$this->request->get('select') === 23 ) {
			$html .= '<a href="' . $this->getIndexUrl('research', 'time_detail', '&amp;select=22') . '">&#8810;22時のアクセス詳細</a>';
		}
		else {
			$html .= '<a href="' . $this->getIndexUrl('research', 'time_detail', '&amp;select=' . sprintf( '%02d', ( $this->urlEncode( $this->request->get('select') ) ) - 1 ) ) . '">&#8810;' . ( $this->escapeHtml( $this->request->get('select') ) - 1 ) . '時のアクセス詳細</a>';
			$html .= '<a href="' . $this->getIndexUrl('research', 'time_detail', '&amp;select=' . sprintf( '%02d', ( $this->urlEncode( $this->request->get('select') ) ) + 1 ) ) . '">' . ( $this->escapeHtml( $this->request->get('select') ) + 1 ) . '時のアクセス詳細&#8811;</a>';
		}
		$html .= '</div>';
		echo $html;
	}

	public function geoipFileCheck() {
		if( !file_exists( THK_GEOIP_DAT ) ) {
			$msg  = '<div class="footnote">';
			$msg .= '<p class="red">';
			$msg .= 'GeoLiteCity.dat ファイルが見つかりません。<br />';
			$msg .= 'GeoLiteCity.dat を _data ディレクトリにアップロードしてください。<br />';
			$msg .= 'GeoLiteCity(Binary) は、';
			$msg .= 'Maxmind 社の <a href="' . $this->getJumpUrl( 'http://dev.maxmind.com/geoip/legacy/geolite/' ) . '" target="_blank">GeoLite Legacy Downloadable Databases</a> からダウンロードできます。';
			$msg .= '</p>';
			$msg .= '<ul class="eos">';
			$msg .= '<li class="duck">ドメイン名で判別できなかった国や都道府県を GeoLiteCity で判別できることがあります。</li>';
			$msg .= '<li class="duck">GeoLiteCity がなくとも、' . ThkConfig::THK_NAME . ' の動作に支障はありません。</li>';
			$msg .= '<li class="duck">GeoLiteCity が _data ディレクトリに見つかった場合、このメッセージは表示されなくなります。</li>';
			$msg .= '</ul>';
			$msg .= '</div>';
		}
		else {
			$msg  = '<p class="powered">';
			$msg .= 'This product includes GeoLite data created by MaxMind, available from <a href="' . $this->getJumpUrl( 'http://www.maxmind.com/' ) . '" target="_blank">http://www.maxmind.com/</a>';
			$msg .= '</p>';
		}
		echo $msg;
	}

	public function resultTag() {
		$action = $this->action;
		$siteData = $this->session->get('siteData');
		$uniqueCount = $this->result->get('uniqueCount');
		$totalCount = $this->result->get('totalCount');
		$this->result->delete('siteData');
		$this->result->delete('uniqueCount');
		$summaryData = $this->_pageCut( $this->result->get('summaryData') );
		$html = '';
		$i = 0;

		switch( $action ) {
			case 'domain':
			case 'jpdomain':
				$domainArray = array();
				foreach( $summaryData as $key => $value ) {
					$domainArray[] = $key;
				}
				$this->setDomainLabel( $domainArray );
				break;
			case 'rank':
			case 'adrank':
			case 'adprank':
			case 'pagein':
			case 'pageout':
			case 'clickrank':
			case 'btnrank':
				$urlArray = array();
				foreach( $summaryData as $key => $value ) {
					$urlArray[] = $key;
				}
				$this->setTitleLabel( $urlArray );
				break;
			default:
				break;
		}

		foreach( $summaryData as $key => $value ) {
			$html .= '<tr>';
			switch( $action ) {
				case 'brow':
					$dispBrowImg = $this->getBrowserImage( $key );
					$html .= '<td class="' . $this->getEvenClass( $i ) . '"><span class="favi"><img class="img" alt="' . $this->escapeHtml( $key ) . '" src="' . $dispBrowImg . '" title="' . $this->escapeHtml( $key ) . '" /></span><a href="' . $this->getIndexUrl( 'research', $action . '_ver', '&amp;select=' . $this->urlEncode( $key ) ) . '">' . $this->substrMax( $this->escapeHtml( $key ) ) . '</a></td>';
					break;
				case 'brow_ver':
					$html .= '<td class="' . $this->getEvenClass( $i ) . '"><a href="' . $this->getIndexUrl( 'research', 'brow_user', '&amp;select=' . $this->urlEncode( $key ) . '&amp;browser=' . $this->getSelect() ) . '" title="' . $this->escapeHtml( $key ) . '">' . $this->substrMax( $this->escapeHtml( $key) ) . '</a></td>';
					break;
				case 'os':
					$dispOsImg = $this->getOsImage( $key );
					$html .= '<td class="' . $this->getEvenClass( $i ) . '"><span class="favi"><img class="img" alt="' . $this->escapeHtml( $key ) . '" src="' . $dispOsImg . '" title="' . $this->escapeHtml( $key ) . '" /></span><a href="' . $this->getIndexUrl( 'research', $action . '_ver', '&amp;select=' . $this->urlEncode( $key ) ) . '">' . $this->substrMax( $this->escapeHtml( $key ) ) . '</a></td>';
					break;
				case 'os_ver':
					$html .= '<td class="' . $this->getEvenClass( $i ) . '"><a href="' . $this->getIndexUrl( 'research', 'os_user', '&amp;select=' . $this->urlEncode( $key ) . '&amp;os='. $this->getSelect() ) . '" title="' . $this->escapeHtml( $key ) . '">' . $this->substrMax( $this->escapeHtml( $key ) ) . '</a></td>';
					break;
				case 'host':
				case 'referer':
					if( $key === Config::DIRECT_ACCESS ) {
						$dispHostImg = ImageList::$generalImages['bookmark'];
					}
					else {
						$dispHostImg = $this->getHostImage( $key );
					}
					$html .= '<td class="' . $this->getEvenClass( $i ) . '"><span class="favi"><img class="img" alt="' . $this->escapeHtml( $key ) . '" src="' . $dispHostImg . '" title="' . $this->escapeHtml( $key ) . '" /></span><a href="' . $this->getIndexUrl( 'research', $action . '_user', '&amp;select=' . $this->urlEncode( $key ) ) . '">' . $this->substrMax( $this->urlDecode( $this->escapeHtml( $key ) ) ) . '</a></td>';
					break;
				case 'key':
					$html .= '<td class="' . $this->getEvenClass( $i ) . '"><a href="' . $this->getIndexUrl( 'research', $action . '_engine', '&amp;select=' . $this->urlEncode( $key ) ) . '" title="' . $key . '">' . $this->substrMax( $this->escapeHtml( $key ) ) . '</a></td>';
					break;
				case 'key_engine':
					$dispHostImg = $this->getHostImage( $key );
					$html .= '<td class="' . $this->getEvenClass( $i ) . '"><span class="favi"><img class="img" alt="' . $this->escapeHtml( $key ) . '" src="' . $dispHostImg . '" title="' . $this->escapeHtml( $key ) . '" /></span><a href="' . $this->getIndexUrl( 'research', 'key_user', '&amp;select=' . $this->urlEncode( $key ) . '&amp;keyword=' . $this->urlEncode( $this->getSelect() ) ) . '">' . $this->substrMax( $this->escapeHtml( $key ) ) . '</a></td>';
					break;
				case 'engine':
					$dispHostImg = $this->getHostImage( $key );
					$html .= '<td class="' . $this->getEvenClass( $i ) . '"><span class="favi"><img class="img" alt="' . $this->escapeHtml( $key ) . '" src="' . $dispHostImg . '" title="' . $this->escapeHtml( $key ) . '" /></span><a href="' . $this->getIndexUrl( 'research', $action . '_key', '&amp;select=' . $this->urlEncode( $key ) ) . '">' . $this->substrMax( $this->escapeHtml( $key ) ) . '</a></td>';
					break;
				case 'engine_key':
					$html .= '<td class="' . $this->getEvenClass( $i ) . '"><a href="' . $this->getIndexUrl( 'research', 'engine_user', '&amp;select=' . $this->urlEncode( $key ) . '&amp;engine=' . $this->getSelect() ) . '" title="' . $this->escapeHtml( $key ) . '">' . $this->substrMax( $this->escapeHtml( $key ) ) . '</a></td>';
					break;
				case 'domain':
				case 'jpdomain':
					$html .= '<td class="' . $this->getEvenClass( $i ) . '"><a href="' . $this->getIndexUrl( 'research', $action . '_user', '&amp;select=' . $this->urlEncode( $key ) ) . '" title="' . $this->escapeHtml( $key ) . '">' . $this->substrMax( $this->getDomain( $key ) ) . '</a>'. $this->getDomainEdit( $key, true, true, '名称設定' ) . '</td>';
					break;
				case 'word':
					$html .= '<td class="' . $this->getEvenClass( $i ) . '"><a href="' . $this->getJumpUrl( Config::SEARCH_URL . $key ) . '" title="' . $this->escapeHtml( $key ) . '" target="_blank">' . $this->substrMax( $this->escapeHtml( $key ) ) . '</a></td>';
					break;
				case 'pagein':
				case 'pageout':
				case 'rank':
				case 'adrank':
				case 'adprank':
				case 'btnrank':
				case 'clickrank':
					$html .= '<td class="' . $this->getEvenClass( $i ) . '"><a href="' . $this->getIndexUrl( 'research', $action . '_user', '&amp;select=' . $this->urlEncode( $key ) ) . '" title="' . $this->urlDecode( $this->escapeHtml( $key ) ) . '">' . $this->substrMax( $this->urlDecode( $this->escapeHtml( $this->getTitle( $key ) ) ) ) . '</a></td>';
					break;
				case 'country':
					$dispCountryImg = $this->getCountryImage( $key );
					$html .= '<td class="' . $this->getEvenClass( $i ) . '"><span class="favi"><img class="img" alt="' . $this->escapeHtml( $key ) . '" src="' . $dispCountryImg . '" title="' . $this->escapeHtml( $key ) . '" width="16" height="16" /></span><a href="' . $this->getIndexUrl( 'research', $action . '_user', '&amp;select=' . $this->urlEncode( $key ) ) . '">' . $this->substrMax( $this->escapeHtml( $key ) ) . '</a></td>';
					break;
				case 'pref':
					$dispPrefImg = $this->getPrefImage( $key );
					$html .= '<td class="' . $this->getEvenClass( $i ) . '"><span class="favi"><img class="img" alt="' . $this->escapeHtml( $key ) . '" src="' . $dispPrefImg . '" title="' . $this->escapeHtml( $key ) . '" width="16" height="16" /></span><a href="' . $this->getIndexUrl( 'research', $action . '_user', '&amp;select=' . $this->urlEncode( $key ) ) . '">' . $this->substrMax( $this->escapeHtml( $key ) );
					if( $key === '日本' ) $html .= ' (地域不明)';
					$html .= '</a></td>';
					break;
				case 'rate':
					$cnt = ( $key === 1 ) ? $cnt = '1回 (サイト全体の直帰率に相当)' : $this->escapeHtml( $key ) . '回';
					$html .= '<td class="' . $this->getEvenClass( $i ) . '"><a href="' . $this->getIndexUrl( 'research', $action . '_user', '&amp;select=' . $this->urlEncode( $key ) ) . '" title="' . $cnt . '">' . $this->substrMax( $this->escapeHtml( $cnt ) ) . '</a></td>';
					break;
				default:
					$html .= '<td class="' . $this->getEvenClass( $i ) . '"><a href="' . $this->getIndexUrl( 'research', $action . '_user', '&amp;select=' . $this->urlEncode( $key ) ) . '" title="' . $this->escapeHtml( $key ) . '">' . $this->substrMax( $this->escapeHtml( $key ) ) . '</a></td>';
					break;
			}
			if( is_array( $value ) ) {
				$t = ( $value['total'] ) ? $value['total'] : 1;
				$totalCount = ( $totalCount > 0 ) ? $totalCount : 1;
				$uniqueCount = ( $uniqueCount > 0 ) ? $uniqueCount : 1;
				$html .= '<td class="numeric ' . $this->getEvenClass( $i ) . '"><span class="blue">' . $this->getFormatNumber( $value['unique'] ) . '</span></td>';
				$html .= '<td class="numeric ' . $this->getEvenClass( $i ) . '">' . sprintf( '%.2f', round( $value['unique'] * 100 / $uniqueCount, 2 ) ) . '%</td>';
				$html .= '<td class="numeric ' . $this->getEvenClass( $i ) . '"><span class="red">' . $this->getFormatNumber( $t ) . '</span></td>';
				$html .= '<td class="numeric ' . $this->getEvenClass( $i ) . '">' . sprintf( '%.2f', round( $t * 100 / $totalCount, 2 ) ) . '%</td>';
				$html .= '<td class="rowgraph ' . $this->getEvenClass( $i ) . '">';
				$html .= '<div class="rowbar">';
				// グラフを重ねるので、グラフだけトータルで割った数字にする
				$html .= '<img class="unique" alt="' . $value['unique'] . '" title="' . $value['unique'] . '" width="' . round( $value['unique'] * 100 / $totalCount, 0 ) . '%" height="15" src="' . ImageList::$generalImages['unique'] . '" />';
				$html .= '<img class="total" alt="' . $t . '" title="' . $t . '" width="' . round( $t * 100 / $totalCount, 0 ) . '%" height="15" src="' . ImageList::$generalImages['total'] . '" />';
			}
			else {
				$uqCount = ( $action === 'rate' ) ? $totalCount : $uniqueCount;
				if( $value <= 0 ) $value = 0;
				$html .= '<td class="numeric ' . $this->getEvenClass( $i ) . '"><span class="green">' . $this->getFormatNumber( $value ) . '</span></td>';
				$html .= '<td class="numeric ' . $this->getEvenClass( $i ) . '">' . sprintf( '%.2f', round( $value * 100 / $uqCount, 2 ) ) . '%</td>';
				$html .= '<td class="rowgraph ' . $this->getEvenClass( $i ) . '">';
				$html .= '<div class="rowbar">';
				$html .= '<img class="green" alt="' . $value . '" title="' . $value . '" width="' . round( $value * 100 / $uqCount, 0 ) . '%" height="15" src="' . ImageList::$generalImages['green'] . '" />';
			}
			$html .= '</div>';
			$html .= '</td>';
			$html .= '</tr>';
			++$i;
		}
		echo $html;
	}

	public function resultBounceTag() {
		$action = $this->action;
		$siteData = $this->session->get('siteData');
		$totalCount = $this->result->get('totalCount');
		$uniqueCount = $this->result->get('uniqueCount');
		$summaryData = $this->_pageCut( $this->result->get('summaryData') );

		$this->result->delete('siteData');
		$this->result->delete('summaryData');

		$urlArray = array();
		foreach( $summaryData as $key => $value ) {
			$urlArray[] = $key;
		}
		$this->setTitleLabel( $urlArray );

		$html = '';
		$i = 0;
		$pageCount = 1;
		$page = $this->request->get('page') !== null ? $this->request->get('page') : 1;

		$html .= '<tr style="border-bottom:double 4px #999">';
		$html .= '<td class="' . $this->getEvenClass( $i ) . '">サイト全体の直帰率</td>';
		$html .= '<td class="numeric ' . $this->getEvenClass( $i ) . '"><span class="blue">' . $this->getFormatNumber( $totalCount ) . '</span></td>';
		$html .= '<td class="numeric ' . $this->getEvenClass( $i ) . '"><span class="green">' . $this->getFormatNumber( $uniqueCount ) . '</span></td>';
		$html .= '<td class="numeric ' . $this->getEvenClass( $i ) . '">' . $this->bounceAverage() . '%</td>';
		$html .= '<td class="rowgraph ' . $this->getEvenClass( $i ) . '">';
		$html .= '<div class="rowbar">';
		$html .= '<img class="green" alt="' . $uniqueCount . '" title="' . $uniqueCount . '" width="' . round( $this->bounceAverage(), 0 ) . '%" height="15" src="' . ImageList::$generalImages['green'] . '" />';
		$html .= '</div>';
		$html .= '</td>';
		$html .= '</tr>';
		++$i;

		foreach( $summaryData as $key => $value ) {
			if( $value['unique'] === null ) continue;
			$rate = 0;
			if( $value['bounce'] > 0 ) $rate = round( $value['bounce'] * 100 / $value['unique'], 2 );
			$html .= '<tr>';
			$html .= '<td class="' . $this->getEvenClass( $i ) . '"><a href="'. $this->getIndexUrl( 'research', $action . '_user', '&amp;select=' . $this->urlEncode( $key ) ) . '" title="' . $key . '">' . $this->substrMax( $this->urlDecode( $this->escapeHtml( $this->getTitle( $key ) ) ) ) . '</a></td>';
			$html .= '<td class="numeric ' . $this->getEvenClass( $i ) . '"><span class="blue">' . $this->getFormatNumber( $value['unique'] ) . '</span></td>';
			$html .= '<td class="numeric ' . $this->getEvenClass( $i ) . '"><span class="green">' . $this->getFormatNumber( $value['bounce'] ) . '</span></td>';
			$html .= '<td class="numeric ' . $this->getEvenClass( $i ) . '">' . sprintf( '%.2f', $rate ) . '%</td>';
			$html .= '<td class="rowgraph ' . $this->getEvenClass( $i ) . '">';
			$html .= '<div class="rowbar">';
			$html .= '<img class="green" alt="' . $value['bounce'] . '" title="' . $value['bounce'] . '" width="' . round( $rate, 0 ) . '%" height="15" src="' . ImageList::$generalImages['green'] . '" />';
			$html .= '</div>';
			$html .= '</td>';
			$html .= '</tr>';
			++$i;
		}
		echo $html;
	}

	public function resultUserTag() {
		$action = $this->action;
		$yymmdd = $this->result->get('yymmdd');
		$domain = $this->result->get('domain');
		$pref = $this->result->get('pref');
		$os = $this->result->get('os');
		$os_ver = $this->result->get('os_ver');
		$browser = $this->result->get('browser');
		$brow_ver = $this->result->get('brow_ver');
		$siteData = $this->session->get('siteData');
		$summaryData = $this->result->get('summaryData');
		$totalCount = $this->result->get('totalCount');

		$i = 0;

		$this->result->delete('summaryData');
		$this->result->delete('totalCount');
		$this->setDomainLabel( $domain );

		$html = '';
		$rate_value = 0;
		if( $action === 'rate_user' ) {
			$rate_value = (int)$this->request->get('select');
			$totalCount = $totalCount * $rate_value;
		}

		foreach( $summaryData as $key => $value ) {
			$uid = substr( $key, 0, strlen( $key ) - 2 );

			if( $action === 'rate_user' ) $value = $rate_value;

			$os_ver[$key] = ( $os_ver[$key] !== 'unknown' ) ? ' ' . $os_ver[$key] : '';
			$dispOs = $os[$key] . $os_ver[$key];
			$dispOsImg = $this->getOsImage( $dispOs );

			$brow_ver[$key] = ( $brow_ver[$key] !== 'unknown' ) ? ' ' . $brow_ver[$key] : '';
			$dispBrow = $browser[$key] . $brow_ver[$key];
			$dispBrowImg = $this->getBrowserImage( $dispBrow );

			$dispPref = ( $pref[$key] === '日本' ) ? '日本 (地域不明)' : $pref[$key];
			$dispPrefImg = $this->getPrefImage( $pref[$key] );

			$html .= '<tr>';
			$html .= '<td class="nobreak ' . $this->getEvenClass( $i ) . '">';
			$html .= '<div class="visitinfo nowrap">';
			$html .= '<img src="' . ImageList::$generalImages['offline'] . '" width="12" height="12" alt="offline" class="vmiddle">&nbsp;' . $yymmdd[$key];
			$html .= '</div>';
			$html .= '<div class="visitinfo nowrap">';
			$html .= 'ID: <a href="' . $this->getIndexUrl( 'research', 'uid_detail', '&amp;select=' . $this->urlEncode( $uid ) ) . '" title="'. $uid . '">' . $this->getAlias( $uid ) . '</a>';
			$html .= $this->getAliasEdit( $uid, true, true, '名称設定' );
			$html .= '</div>';
			$html .= '</td>';
			$html .= '<td class="' . $this->getEvenClass( $i ) . '">';
			$html .= '<div class="osbrow">';
			$html .= '<span class="favi"><img src="' . $dispOsImg . '" alt="' . $dispOs . '" title="' . $dispOs . '" /></span>';
			$html .= '<span class="favi"><img src="' . $dispBrowImg . '" alt="' . $dispBrow . '" title="'. $dispBrow . '" /></span>';
			$html .= '<span class="favi" ><img src="' . $dispPrefImg. '" alt="' . $dispPref . '" title="' . $dispPref . '" width="16" height="16" /></span>';
			$html .= '<span class="property nowrap"><a href="' . $this->getIndexUrl( 'research', 'pref_user', '&amp;select=' . $this->urlEncode( $pref[$key] ) ) . '" title="' . $dispPref . '">' . $dispPref . '</a></span>';
			$html .= '</div>';
			$html .= '<div class="ipinfo">';
			$html .= '<a href="' . $this->getIndexUrl( 'research', 'domain_user', '&amp;select=' . $this->urlEncode( $domain[$key] ) ) . '" title="' . $this->escapeHtml( $domain[$key] ) . '">' . $this->getDomain( $this->escapeHtml( $domain[$key] ) ) . '</a><br />';
			$html .= '</div>';
			$html .= '</td>';
			$html .= '<td class="numeric ' . $this->getEvenClass( $i ) . '"><span class="green">' . $this->getFormatNumber( $value ) . '</span></td>';
			$html .= '<td class="numeric ' . $this->getEvenClass( $i ) . '">' . sprintf( '%.2f', round( $value * 100 / $totalCount, 2 ) ) . '%</td>';
			$html .= '<td class="rowgraph ' . $this->getEvenClass( $i ) . '">';
			$html .= '<div class="rowbar">';
			$html .= '<img class="green" alt="' . $value . '" title="' . $value . '" width="' . round( $value * 100 / $totalCount, 0 ) . '%" height="15" src="' . ImageList::$generalImages['green'] . '" />';
			$html .= '</div>';
			$html .= '</td>';
			$html .= '</tr>';

			++$i;
		}
		echo $html;
	}

	public function digestTime() {
		$this->resultTimeTag( true );
	}
	public function digestRank() {
		$this->action = 'rank';
		$rcon = new ResearchController( $this->request, $this->session, $this->message, $this->result, $this->controller, $this->action );
		$rcon->rank();
		$this->resultTag();
		$this->action = 'digest1';
	}
	public function digestClick() {
		$this->action = 'clickrank';
		$rcon = new ResearchController( $this->request, $this->session, $this->message, $this->result, $this->controller, $this->action );
		$rcon->clickrank();
		$this->resultTag();
		$this->action = 'digest1';
	}

	public function digestReferer() {
		$this->action = 'referer';
		$this->resultTag();
		$this->action = 'digest2';
	}

	public function digestKey() {
		$this->action = 'key';
		$rcon = new ResearchController( $this->request, $this->session, $this->message, $this->result, $this->controller, $this->action );
		$rcon->key();
		$this->resultTag();
		$this->action = 'digest2';
	}

	public function timeStackGraph() {
		$before = '';
		if(
			$this->session->get('yyyyFrom') . $this->session->get('mmFrom') . $this->session->get('ddFrom')
			=== $this->session->get('yyyyTo') . $this->session->get('mmTo') . $this->session->get('ddTo')
		) {
			$before = 'day';
		}

		$uniqueData = $this->result->get('uniqueData');
		$totalData  = $this->result->get('totalData');

		if( count( $totalData ) > 0 ) {
			$max = $totalData;
			rsort( $max );
			$maxValue = $max[0];
		}
		else {
			$maxValue = 0;
		}

		$data_now_total = '';
		$data_now_uniq  = '';
		$tmp_total = '';
		$tmp_uniq  = '';
		$totalStack  = 0;
		$uniqueStack = 0;
		$tmp_totalStack  = 0;
		$tmp_uniqueStack = 0;

		for( $i = 0; $i <= 23; $i++ ) {
			if( isset( $totalData[$i] ) ) $totalStack += (int)$totalData[$i];
			if( isset( $uniqueData[$i] ) ) $uniqueStack += (int)$uniqueData[$i];

			$tmp_total .= $totalStack . ',';
			$tmp_uniq  .= $uniqueStack . ',';

			if( $tmp_totalStack != $totalStack ) $data_now_total = $tmp_total;
			if( $tmp_uniqueStack != $uniqueStack ) $data_now_uniq = $tmp_uniq;

			$tmp_totalStack  = $totalStack;
			$tmp_uniqueStack = $uniqueStack;
		}
		$data_now_total = rtrim( $data_now_total, ',' );
		$data_now_uniq  = rtrim( $data_now_uniq, ',' );

		// 前日のデータ取得
		$this->_getBeforeTimeData( $this->action, $before, 'hh' );
		$uniqueData = $this->result->get('uniqueData');
		$totalData = $this->result->get('totalData');
		// 前日のデータ取得 - ここまで

		if( count( $totalData ) > 0 ) {
			$max = $totalData;
			rsort( $max );
			$maxValue = $max[0];
		}
		else {
			$maxValue = 0;
		}

		$data_before_total = '';
		$data_before_uniq  = '';
		$totalStack  = 0;
		$uniqueStack = 0;

		for( $i = 0; $i <= 23; $i++ ) {
			if( isset( $totalData[$i] ) ) $totalStack += (int)$totalData[$i];
			if( isset( $uniqueData[$i] ) ) $uniqueStack += (int)$uniqueData[$i];
			$data_before_total .= $totalStack . ',';
			$data_before_uniq  .= $uniqueStack . ',';
		}
		$data_before_total = rtrim( $data_before_total, ',' );
		$data_before_uniq  = rtrim( $data_before_uniq, ',' );

		$currentDay = $this->session->get('ddFrom');
		$currentMonth = $this->session->get('mmFrom');
		if( $this->session->get('yyyyFrom') . $this->session->get('mmFrom') . $this->session->get('ddFrom') == date( 'Ymd' ) ) {
			$currentDay = $currentMonth = '今';
		}

		$html = '';
		$title = '';
		$type = 'area';
		$colorset = array();
		$xscale = '';
		$xdata = array();
		$x1 = $x2 = '';
		for( $i = 0; $i <= 1; $i++ ) {
			if( $i <= 0 ) {
				$title = '時間別アクセス推移 - ユニークアクセス数';
				$colorset = array( 'rgba(0,160,221,0.4)', 'rgba(0,0,255,0.3)' );
				if( $before === 'day' ) {
					$h1 = $currentDay . '日';
					$h2 = '前日';
					$x1 = '"",' . $data_now_uniq;
					$x2 = '"",' . $data_before_uniq;
				}
				else {
					$h1 = $currentMonth . '月';
					$h2 = '前月';
					$x1 = '"",' . $data_now_uniq;
					$x2 = '"",' . $data_before_uniq;
				}
			}
			else {
				$title = '時間別アクセス推移 - ページビュー';
				$colorset = array( 'rgba(255,0,0,0.4)', 'rgba(128,0,25,0.3)' );
				if( $before === 'day' ) {
					$h1 = $currentDay . '日';
					$h2 = '前日';
					$x1 = '"",' . $data_now_total;
					$x2 = '"",' . $data_before_total;
				}
				else {
					$h1 = $currentMonth . '月';
					$h2 = '前月';
					$x1 = '"",' . $data_now_total;
					$x2 = '"",' . $data_before_total;
				}
			}
			$hanrei = array( $h1, $h2 );
			$xdata = array( $x1, $x2 );
			$xscale = '"","0",1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23';

			$html .= $this->_drawGraph( $title, $type, $colorset, $xscale, $xdata, 10, $hanrei, 350 );
		}
		echo $html;
	}

	public function termStackGraph() {
		$uniqueData = $this->result->get('uniqueData');
		$totalData = $this->result->get('totalData');
		$data_now_total = '';
		$data_now_uniq  = '';
		$tmp_total = '';
		$tmp_uniq  = '';
		$totalStack  = 0;
		$uniqueStack = 0;
		$tmp_totalStack  = 0;
		$tmp_uniqueStack = 0;

		for( $i = 1; $i <= 31; $i++ ) {
			if( isset( $totalData[$i] ) ) $totalStack += (int)$totalData[$i];
			if( isset( $uniqueData[$i] ) ) $uniqueStack += (int)$uniqueData[$i];

			$tmp_total .= $totalStack . ',';
			$tmp_uniq  .= $uniqueStack . ',';

			if( $tmp_totalStack != $totalStack ) $data_now_total = $tmp_total;
			if( $tmp_uniqueStack != $uniqueStack ) $data_now_uniq = $tmp_uniq;

			$tmp_totalStack = $totalStack;
			$tmp_uniqueStack = $uniqueStack;
		}
		if( $data_now_total === '' ) {
			for( $i = 0; $i <= 31; $i++ ) {
				$data_now_total .= '0,';
				$data_now_uniq  .= '0,';
			}
		}
		$data_now_total = rtrim( $data_now_total, ',' );
		$data_now_uniq  = rtrim( $data_now_uniq, ',' );

		if( $data_now_total === null ) $data_now_total = '0';
		if( $data_now_uniq === null ) $data_now_uniq = '0';

		// 前月のデータ取得
		$this->_getBeforeTimeData( $this->action, 'month' );
		$uniqueData = $this->result->get('uniqueData');
		$totalData = $this->result->get('totalData');
		// 前月のデータ取得 - ここまで

		$data_before_total = '';
		$data_before_uniq  = '';
		$tmp_total = '';
		$tmp_uniq  = '';
		$totalStack  = 0;
		$uniqueStack = 0;
		$tmp_totalStack  = 0;
		$tmp_uniqueStack = 0;

		for( $i = 1; $i <= 31; $i++ ) {
			if( isset( $totalData[$i] ) ) $totalStack += (int)$totalData[$i];
			if( isset( $uniqueData[$i] ) ) $uniqueStack += (int)$uniqueData[$i];

			$tmp_total .= $totalStack . ',';
			$tmp_uniq  .= $uniqueStack . ',';

			if( $tmp_totalStack != $totalStack ) $data_before_total = $tmp_total;
			if( $tmp_uniqueStack != $uniqueStack ) $data_before_uniq = $tmp_uniq;

			$tmp_totalStack = $totalStack;
			$tmp_uniqueStack = $uniqueStack;
		}
		if( $data_before_total === '' ) {
			for( $i = 0; $i <= 31; $i++ ) {
				$data_before_total .= '0,';
				$data_before_uniq  .= '0,';
			}
		}
		$data_before_total = rtrim( $data_before_total, ',' );
		$data_before_uniq  = rtrim( $data_before_uniq, ',' );

		$currentDay = $this->session->get('ddFrom');
		$currentMonth = $this->session->get('mmFrom');
		if( $this->session->get('yyyyFrom') . $this->session->get('mmFrom') == date( 'Ym' ) ) {
			$currentDay = $currentMonth = '今';
		}

		$html = '';
		$title = '';
		$type = 'area';
		$colorset = array();
		$xscale = '';
		$xdata = array();
		$x1 = $x2 = '';
		$h1 = $currentMonth . '月';
		$h2 = '前月';
		for( $i = 0; $i <= 1; $i++ ) {
			if( $i <= 0 ) {
				$title = '日別アクセス推移 - ユニークアクセス数';
				$colorset = array( 'rgba(0,160,221,0.4)', 'rgba(0,0,255,0.3)' );
				$x1 = '"",' . $data_now_uniq;
				$x2 = '"",' . $data_before_uniq;
			}
			else {
				$title = '日別アクセス推移 - ページビュー';
				$colorset = array( 'rgba(255,0,0,0.4)', 'rgba(128,0,25,0.3)' );
				$x1 = '"",' . $data_now_total;
				$x2 = '"",' . $data_before_total;
			}
			$hanrei = array( $h1, $h2 );
			$xdata = array( $x1, $x2 );
			$xscale = '"",1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31';

			$html .= $this->_drawGraph( $title, $type, $colorset, $xscale, $xdata, 10, $hanrei );
		}
		echo $html;
	}

	public function timeStack() {
		$before = '';
		$today = false;
		$xlen = 0;

		if(
			$this->session->get('yyyyFrom') . $this->session->get('mmFrom') . $this->session->get('ddFrom')
			 === $this->session->get('yyyyTo') . $this->session->get('mmTo') . $this->session->get('ddTo')
		) {
			$before = 'day';
		}

		if( $before === 'day' ) {
			$now = $_SERVER['REQUEST_TIME'];
			if(
				$this->session->get('yyyyTo') . $this->session->get('mmTo') . $this->session->get('ddTo')
				=== date( 'Y', $now ) . date( 'm', $now ) . date( 'd', $now )
			) {
				$today = true;
			}
		}

		$uniqueData = $this->result->get('uniqueData');
		$totalData = $this->result->get('totalData');

		if( count( $totalData ) > 0 ) {
			$xlen = ( max( $totalData ) > $xlen ) ? max( $totalData ) : $xlen;
		}


		$data_now_total = '';
		$data_now_uniq  = '';
		$tmp_total = '';
		$tmp_uniq  = '';
		$totalStack  = 0;
		$uniqueStack = 0;
		$tmp_totalStack  = 0;
		$tmp_uniqueStack = 0;

		for( $i = 0; $i <= 23; $i++ ) {
			$totalStack  = isset( $totalData[$i] )  ? (int)$totalData[$i]  : 0;
			$uniqueStack = isset( $uniqueData[$i] ) ? (int)$uniqueData[$i] : 0;

			if( $today && $i > date('G', $now) ) {
				if( $totalStack != 0 )  $data_now_total .= $totalStack . ',';
				if( $uniqueStack != 0 ) $data_now_uniq .= $uniqueStack . ',';
			}
			else {
				$data_now_total .= $totalStack . ',';
				$data_now_uniq  .= $uniqueStack . ',';
			}
		}
		$data_now_total = rtrim( $data_now_total, ',' );
		$data_now_uniq  = rtrim( $data_now_uniq, ',' );

		// 前日のデータ取得
		$this->_getBeforeTimeData( $this->action, $before, 'hh' );
		$uniqueData = $this->result->get('uniqueData');
		$totalData = $this->result->get('totalData');
		// 前日のデータ取得 - ここまで

		if( count( $totalData ) > 0 ) {
			$xlen = ( max( $totalData ) > $xlen ) ? max( $totalData ) : $xlen;
		}

		$data_before_total = '';
		$data_before_uniq  = '';
		$tmp_total = '';
		$tmp_uniq  = '';
		$totalStack  = 0;
		$uniqueStack = 0;
		$tmp_totalStack  = 0;
		$tmp_uniqueStack = 0;

		for( $i = 0; $i <= 23; $i++ ) {
			$totalStack  = isset( $totalData[$i] )  ? (int)$totalData[$i]  : 0;
			$uniqueStack = isset( $uniqueData[$i] ) ? (int)$uniqueData[$i] : 0;

			$data_before_total .= $totalStack . ',';
			$data_before_uniq  .= $uniqueStack . ',';
		}
		$data_before_total = rtrim( $data_before_total, ',' );
		$data_before_uniq  = rtrim( $data_before_uniq, ',' );

		$currentDay = $this->session->get('ddFrom');
		$currentMonth = $this->session->get('mmFrom');
		if( $this->session->get('yyyyFrom') . $this->session->get('mmFrom') . $this->session->get('ddFrom') == date( 'Ymd' ) ) {
			$currentDay   = '今';
			$currentMonth = '今';
		}

		$html = '';
		$title = '';
		$type = 'line';
		$colorset = array();
		$xscale = '';
		$xdata = array();
		$x1 = $x2 = '';

		if( $before === 'day' ) {
			$title = '時間別アクセス（前日比）';
			$h1 = $currentDay . '日';
			$h2 = '前日';
		}
		else {
			$title = '時間別アクセス（前月比）';
			$h1 = $currentMonth . '月';
			$h2 = '前月';
		}
		$colorset = array( 'rgba(212,41,31,1.0)', 'rgba(245,178,178,1.0)', 'rgba(31,120,180,1.0)', 'rgba(160,216,239,1.0)' );
		$xlen = ( $xlen < 20 ) ? $xlen : 20;
		$hanrei = array( $h1.' PV', $h2.' PV', $h1.' UQ', $h2.' UQ' );
		$x1 = '"",' . $data_now_total;
		$x2 = '"",' . $data_before_total;
		$x3 = '"",' . $data_now_uniq;
		$x4 = '"",' . $data_before_uniq;
		$xdata = array( $x1, $x2, $x3, $x4 );
		$xscale = '"","0",1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23';

		echo $this->_drawGraph( $title, $type, $colorset, $xscale, $xdata, $xlen, $hanrei, 640 );
	}

	public function resultVisitorLog() {
		$summaryData = $this->result->get('summaryData');
		$siteData = $this->session->get('siteData');

		$this->result->delete('summaryData');
		$this->result->delete('siteData');

		$i = 0;
		$pageCount = 1;
		$page = $this->request->get('page') !== null ? $this->request->get('page') : 1;

		$uid_array = array();

		//while( list( $key, $value ) = each( $summaryData ) ) {
		foreach( $summaryData as $key => $value ) {
			if( $pageCount >= $siteData['dispview'] * ( $page - 1 ) + 1 && $pageCount <= $siteData['dispview'] * $page ) {
				$uid_array[$value] = true;
				if( $i >= $siteData['dispview'] ) break;
				++$i;
			}
			++$pageCount;
		}
		reset( $uid_array );

		$rcon = new ResearchController( $this->request, $this->session, $this->message, $this->result, $this->controller, $this->action );
		$conditions = $rcon->_initConditions();
		$conditions[0] .= ' AND ( CONCAT( uid,dd ) = ?';
		$conditions[] = key( $uid_array );
		array_shift( $uid_array );
		foreach( $uid_array as $key => $value ) {
			$conditions[0] .= ' OR CONCAT( uid,dd ) = ?';
			$conditions[] = $key;
		}
		$conditions[0] .= ' )';
		$order = 'dd ASC, hh ASC, mi ASC, ss ASC, id ASC';
		$findOptions = array( 'condition' => $conditions, 'order' => $order );

		$method = $this->action . '_actual';
		$compare = $this->request->get('select');
		$rcon->_doResearch( $findOptions, $compare, $method );

		unset( $uid_array );
		unset( $uid_unique );

		$summaryData = $this->result->get('summaryData');
		$this->result->delete('summaryData');

		$i = 0;
		$html = '';
		$hh = '';
		$hhid = null;
		$online_timestamp = strtotime( date( 'Y-m-d H:i:s' ) . ' -' . Config::ONLINE_TIME . ' second' );

		$domainArray = array();
		foreach( $summaryData as $value ) {
			$domainArray[] = $value['domain'];
		}
		$this->setDomainLabel( $domainArray );
		unset( $domainArray );

		$mydomain = QueryConfig::getLikelyMydomain();

		foreach( $summaryData as $key => $value ) {
			if(
				$value['referer_host'] !== Config::DIRECT_ACCESS &&
				$value['referer_host'] !== Config::FROM_NO_SCRIPT &&
				$value['referer_host'] !== Config::NO_DATA
			) {
				if( $this->_checkMydomain( $value['http_referer'], $mydomain ) ) {
					$value['referer_title'] = Config::FROM_CONTINUE;
				}
			}

			if( $hh !== $value['hh'] ) {
				$hh = $value['hh'];
				$hhid = 't' . $value['hh'];
			}
			else {
				$hhid = null;
			}

			$from_timestamp = strtotime( date( $value['yyyy'] . '-' . $value['mm'] . '-' . $value['dd'] . ' ' . $value['hh'] . ':' . $value['mi'] . ':' . $value['ss'] ) );
			$online_flag = ($from_timestamp >= $online_timestamp) ? true : false;

			$accessTime  = '<div class="detail">' . $value['yyyy'] . '/' . $value['mm'] . '/' . $value['dd'] . '</div>';
			$accessTime .= '<div class="detail nowrap">';
			if( $online_flag ) {
				$accessTime .= '<img src="' . ImageList::$generalImages['online'] . '" width="12" height="12" alt="online" class="vmiddle">&nbsp;';
			}
			else {
				$accessTime .= '<img src="' . ImageList::$generalImages['offline'] . '" width="12" height="12" alt="offline" class="vmiddle">&nbsp;';
			}
			$accessTime .= '<span class="vmiddle">' . $value['hh'] . ':' . $value['mi'] . ':' . $value['ss'] . '</span></div>';

			$dispRefererTitle = $this->substrMax( $this->escapeHtml( $this->urlDecode( $this->getTitle( $value['referer_title'] ) ) ) );

			if( $dispRefererTitle === Config::FROM_NO_SCRIPT || $dispRefererTitle === Config::NO_DATA ) {
				$dispReferer = '<span class="favi"><img alt="' . $dispRefererTitle . '" title="' . $dispRefererTitle . '" src="' . ImageList::$generalImages['noscript'] . '" /></span>' . $dispRefererTitle;
			}
			elseif( $dispRefererTitle === Config::DIRECT_ACCESS ) {
				$dispReferer = '<span class="favi"><img alt="' . $dispRefererTitle . '" title="' . $dispRefererTitle . '" src="' . ImageList::$generalImages['bookmark'] . '" /></span>' . $dispRefererTitle;
			}
			elseif( $dispRefererTitle === Config::FROM_CONTINUE ) {
				$dispReferer = '<span class="favi"><img alt="' . $dispRefererTitle . '" title="' . $dispRefererTitle . '" src="' . ImageList::$generalImages['continue'] . '" /></span>' . $dispRefererTitle;
			}
			else {
				$dispHostImg = $this->getHostImage( $value['http_referer'] );
				$dispReferer = '<span class="favi"><img alt="' . $dispHostImg . '" title="' . $dispHostImg . '" src="' . $dispHostImg . '" /></span>' . '<a href="' . '' . $this->getJumpUrl( $value['http_referer'] ) . '" title="' . $this->escapeHtml( $value['http_referer'] ) . '" target="_blank">' . $dispRefererTitle . '</a>';
			}
			$ranking = '';
			if( stripos( $value['http_referer'], '.google.' ) !== false ) {
				$rank = preg_replace( "/.+?\.google\..+?\/.*?cd=([0-9]+).*/", '$1', $this->escapeHtml( $value['http_referer'] ), 1 );
				if( is_numeric($rank) ) $ranking = $rank;
			}
			$arrowTag = '<img alt="&rarr;" title="&rarr;" src="' . ImageList::$generalImages['arrowright'] . '" />';

			$dispTitle = '';
			$click = '';
			$liclass = '';
			$rankAction = 'rank';
			$dispRequest = '<ol style="margin:0 0 10px 5px">';

			foreach( $value['action'] as $k => $v ) {
				if( $value['type'][$k] === 'ref' ) {
					$dispRequest .= '</ol>';
					$dispRequest .= '<div class="detail">';
					$dispHostImg = $this->getHostImage( $v );
					$dispRefererTitle = $this->substrMax( $this->escapeHtml( $this->urlDecode( $this->getTitle( $value['action_title'][$k] ) ) ) );
					$dispRequest .= '<span class="favi"><img alt="' . $dispHostImg . '" title="' . $dispHostImg . '" src="' . $dispHostImg . '" /></span>' . '<a href="' . '' . $this->getJumpUrl( $v ) . '" title="' . $this->escapeHtml( $v ) . '" target="_blank">' . $dispRefererTitle . '</a>';
					$dispRequest .= '</div>';
					$dispRequest .= '<div style="float:left;margin:auto 5px">' . $arrowTag . '</div>';
					$dispRequest .= '<ol style="margin:0 0 10px 5px">';
					continue;
				}
				if( $value['type'][$k] === Config::CLICK_LINK ) {
					$liclass = ' class="click"';
					$click = '<span class="linkclick">Link Click!!</span><br />';
					$rankAction = 'clickrank';
				}
				elseif( $value['type'][$k] === Config::CLICK_BTN ) {
					$liclass = ' class="click"';
					$click = '<span class="btnclick">Button Click!!</span><br />';
					$rankAction = 'btnrank';
				}
				elseif( $value['type'][$k] === Config::CLICK_ADSENSE ) {
					$liclass = ' class="click"';
					$click = '<span class="adclick">AdSense Click!!</span><br />';
					$rankAction = 'adrank';
				}
				$dispTitle = $this->substrMax( $this->escapeHtml( $this->urlDecode( $this->getTitle( $value['action_title'][$k] ) ) ) );
				$dispRequest .= '<li' . $liclass . ' style="padding:4px 0 2px 0">' . $click . '<a href="' . $this->getIndexUrl( 'research', $rankAction . '_user', '&amp;select=' . $this->urlEncode( $v ) ) . '" title="' . $this->escapeHtml( $value['action_title'][$k] ) . '">' . $dispTitle . '</a></li>';
				$click = '';
				$liclass = '';
			}
			$dispRequest .= '</ol>';

			$value['os_ver'] = $value['os'] . ' ' . $value['os_ver'];
			$dispOsImg = $this->getOsImage( $value['os'] . $value['os_ver'] );

			$value['brow_ver'] = ( $value['brow_ver'] != 'unknown' ) ? ' ' . $value['brow_ver'] : '';
			$dispBrowImg = $this->getBrowserImage( $value['browser'] . $value['brow_ver'] );

			$dispPref = ( $value['pref'] === '日本' ) ? '日本 (地域不明)' : $value['pref'];
			$dispPrefImg = $this->getPrefImage( $value['pref'] );

			$html .= '<tr>';
			$html .= '<td class="nobreak ' . $this->getEvenClass( $i ) . '">' . $accessTime . '</td>';
			$html .= '<td class="' . $this->getEvenClass( $i ) . ' hhtd">';
			$html .= '<div class="visitinfo">';
			if( $hhid !== null ) $html .= '<a id="' . $hhid . '" class="hhid"></a>';
			$html .= 'ID: <a href="' . $this->getIndexUrl( 'research', 'uid_detail', '&amp;select=' . $this->urlEncode( $key ) ) . '" title="' . $this->escapeHtml( $key ) . '">' . $this->getAlias( $this->escapeHtml( $key ) ) . '</a>';
			$html .= '</div>';
			$html .= '<div class="visitinfo">';
			$html .= '<span class="favi"><img src="' . $dispOsImg . '" alt="' . $value['os'].$value['os_ver'] . '" title="' . $value['os'].$value['os_ver'] . '" /></span>';
			$html .= '<span class="favi"><img src="' . $dispBrowImg . '" alt="' . $value['browser'] . $value['brow_ver'] . '" title="' . $value['browser'] . $value['brow_ver'] . '" /></span>';
			$html .= '<span class="favi" ><img src="' . $dispPrefImg. '" alt="' . $dispPref . '" title="' . $dispPref . '" width="16" height="16" /></span>';
			$html .= '<span class="property"><a href="'. $this->getIndexUrl( 'research', 'pref_user', '&amp;select=' . $this->urlEncode( $value['pref'] ) ) . '" title="' . $dispPref . '">' . $dispPref . '</a></span>';
			$html .= '</div>';
			$html .= '<div class="visitinfo">';
			$html .= '<a href="'. $this->getIndexUrl( 'research', 'domain_user', '&amp;select=' . $this->urlEncode( $value['domain'] ) ) . '" title="' . $value['domain'] . '">' . $this->getDomain( $value['domain'] ) . '</a>';
			$html .= '</div>';
			if( $ranking ) {
				$html .= '<div class="googlerank">';
				$html .= 'Google検索順位: <span>&nbsp;&nbsp;# ' . $ranking . '&nbsp;&nbsp;</span>';
				$html .= '</div>';
			}
			$html .= '</td>';
			$html .= '<td class="' . $this->getEvenClass( $i ) . '"><div class="detail">' . $dispReferer . '</div><div style="float:left;margin:auto 5px">' . $arrowTag . '</div>' . $dispRequest . '</div></td>';
			$html .= '</tr>';

			if( $i >= $siteData['dispview'] ) break;
			++$i;
		}
		echo $html;
	}

	public function resultTimeTag( $digest = false ) {
		$uniqueData = $this->result->get('uniqueData');
		$totalData = $this->result->get('totalData');

		if( count( $totalData ) > 0 ) {
			$max = $totalData;
			rsort( $max );
			$maxValue = $max[0];
		}
		else {
			$maxValue = 0;
		}

		$uniqueCnt = 0;
		$totalCnt  = 0;

		$html = '';

		$html .= '<tr>';
		$html .= '<td class="topspace" colspan="26"></td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td class="empty-left"></td>';
		for( $i = 0; $i <= 23; $i++ ) {
			if( isset( $uniqueData[$i] ) ) {
				//$uniqueCnt = $this->getFormatNumber( $uniqueData[$i] );
				$uniqueCnt = $uniqueData[$i];
			}
			else {
				$uniqueCnt = 0;
			}
			if( isset( $totalData[$i] ) ) {
				//$totalCnt = $this->getFormatNumber( $totalData[$i] );
				$totalCnt = $totalData[$i];
			}
			else {
				$totalCnt = 0;
			}
			$clsNameUniq  = 'blue';
			$clsNameTotal = 'red';
			if( strlen( $uniqueCnt ) > Config::STANDARD_DIGIT ) $clsNameUniq  .= ' stretch';
			if( strlen( $totalCnt  ) > Config::STANDARD_DIGIT ) $clsNameTotal .= ' stretch';
			$html .= '<td class="num graph-count"><div class="' . $clsNameTotal . '">' . $totalCnt . '</div>';
			$html .= '<div class="' . $clsNameUniq . '">' . $uniqueCnt . '</div></td>';
		}
		$html .= '<td class="empty-right"></td>';
		$html .= '</tr>';

		$html .= '<tr>';
		$html .= '<td class="yscale nobreak">';

		$j = 0;
		$yUnitStack = 0;
		$yUnit_array = array();

		if( $digest ) {
			$graphUnit = 32;
			$j = 5;
		}
		else {
			$graphUnit = 20;
			$j = 10;
		}

		$yUnit = $maxValue >= $j ? ceil( $maxValue / $j ) : 1;

		for( $i = 0; $i < $j; $i++ ) {
			$yUnitStack += $yUnit;
			$yUnit_array[] = $yUnitStack;
		}

		$yUnit_array = array_reverse( $yUnit_array );

		for( $i = 0; $i < $j; $i++ ) {
			$html .= '<div style="height:' . $graphUnit . 'px;">' . $yUnit_array[$i] . '</div>';
		}
		$html .= '</td>';

		for( $i = 0; $i <= 23; $i++ ) {
			if( isset( $uniqueData[$i] ) && $yUnitStack > 0 ) {
				if( $digest ) {
					$uniqueHeight = round( ( $uniqueData[$i] * 32 ) / $yUnitStack * 5 ) - 3; //border と shadow の値分-3
				}
				else {
					$uniqueHeight = round( ( $uniqueData[$i] * 40 ) / $yUnitStack * 5 ) - 3; //border と shadow の値分-3
				}
				if( $uniqueHeight <= 0 ) $uniqueHeight = 1;
				$uniqueHeightAlt = $this->getFormatNumber( $uniqueData[$i] );
				$uniqueClass = 'unique';
				$uniquePng = ImageList::$generalImages['unique'];
			}
			else {
				if( $digest ) {
					$uniqueHeight = 160;
				}
				else {
					$uniqueHeight = 200;
				}
				$uniqueHeightAlt = '';
				$uniqueClass = 'spacer';
				$uniquePng = ImageList::$generalImages['spacer'];
			}
			if( isset( $totalData[$i] ) && $yUnitStack > 0 ) {
				if( $digest ) {
					$totalHeight = round( ( $totalData[$i] * 32 ) / $yUnitStack * 5 ) - 3; //border と shadow の値分-3
				}
				else {
					$totalHeight = round( ( $totalData[$i] * 40 ) / $yUnitStack * 5 ) - 3; //border と shadow の値分-3
				}
				if( $totalHeight <= 0 ) $totalHeight = 1;
				$totalHeightAlt = $this->getFormatNumber( $totalData[$i] );
				$totalClass = 'total';
				$totalPng = ImageList::$generalImages['total'];
			}
			else {
				if( $digest ) {
					$totalHeight = 160;
				}
				else {
					$totalHeight = 200;
				}
				$totalHeightAlt = '';
				$totalClass = 'spacer';
				$totalPng = ImageList::$generalImages['spacer'];
			}
			if( $digest ) {
				$html .= '<td class="gdigest" align="center" valign="bottom">';
			}
			else {
				$html .= '<td class="gtime" align="center" valign="bottom">';
			}
			$html .= '<div class="colbar">';
			$html .= '<img class="' . $uniqueClass . '" width="15" height="' . $uniqueHeight. '" alt="' . $uniqueHeightAlt . '" title="' . $uniqueHeightAlt . '" src="' . $uniquePng . '" />';
			$html .= '<img class="' . $totalClass . '" width="15" height="' . $totalHeight. '" alt="' . $totalHeightAlt . '" title="' . $totalHeightAlt . '" src="' . $totalPng . '" /></div></td>';
		}
		$html .= '<td class="rightspace"></td>';
		$html .= '</tr>';

		$html .= '<tr>';
		$html .= '<td class="yscale-zero"><div>0</div></td>';

		$from = $this->session->get('yyyyFrom') . $this->session->get('mmFrom') . $this->session->get('ddFrom');
		$to = $this->session->get('yyyyTo') . $this->session->get('mmTo') . $this->session->get('ddTo');
		for( $i = 0; $i <= 23; $i++ ) {
			if( isset( $totalData[$i] ) && $from === $to ) {
				$html .= '<td class="times"><a href="' . $this->getIndexUrl('research', 'time_detail', '&amp;select='. sprintf( '%02d', $i ) ) . '#t' . sprintf( '%02d', $i ) . '" title="' . $i . '">' . $i . '</a></td>';
			}
			else {
				$html .= '<td class="times">' . $i . '</td>';
			}
		}
		$html .= '<td class="rightbottomspace"></td>';
		$html .= '</tr>';

		echo $html;
	}

	public function resultTermTag() {
		$uniqueData = $this->result->get('uniqueData');
		$totalData = $this->result->get('totalData');
		$yyyy = $this->session->get('yyyyFrom');
		$mm = $this->session->get('mmFrom');

		if( count( $totalData ) > 0 ) {
			$max = $totalData;
			rsort( $max );
			$maxValue = $max[0];
		}
		else {
			$maxValue = 0;
		}

		$html = '';

		for( $row = 1; $row <= 2; $row++ ) {
			$html .= '<tr>';
			$html .= '<td class="topspace" colspan="18"></td>';
			$html .= '</tr>';
			$html .= '<tr>';
			$html .= '<td class="empty-left"></td>';
			for( $i = 16 * ( $row - 1 ) + 1; $i <= 16 * $row; $i++ ) {
				if( isset( $uniqueData[$i] ) ) {
					//$uniqueCnt = $this->getFormatNumber( $uniqueData[$i] );
					$uniqueCnt = $uniqueData[$i];
				}
				else {
					$uniqueCnt = 0;
				}
				if( isset( $totalData[$i] ) ) {
					//$totalCnt = $this->getFormatNumber( $totalData[$i] );
					$totalCnt = $totalData[$i];
				}
				else {
					$totalCnt = 0;
				}
				if( !checkdate( $mm, $i, $yyyy ) ) {
					$uniqueCnt = '';
					$totalCnt = '';
				}
				$html .= '<td class="num graph-count">';
				$html .= '<div class="red">' . $totalCnt . '</div>';
				$html .= '<div class="blue">' . $uniqueCnt . '</div>';
				$html .= '</td>';
			}
			$html .= '<td class="empty-right"></td>';
			$html .= '</tr>';

			$html .= '<tr>';
			$html .= '<td class="yscale nobreak">';
			$yUnitStack = 0;
			$yUnit = $maxValue >= 5 ? ceil( $maxValue / 5 ) : 1;

			for( $i = 0; $i < 5; $i++ ) {
				$yUnitStack += $yUnit;
				$yUnit_array[] = $yUnitStack;
			}

			$yUnit_array = array_reverse( $yUnit_array );

			for( $i = 0; $i < 5; $i++ ) {
				$html .= '<div style="height:25px;">' . $yUnit_array[$i] . '</div>';
			}
			$html .= '</td>';

			for( $i = 16 * ( $row - 1 ) + 1; $i <= 16 * $row; $i++ ) {
				if( isset( $uniqueData[$i] ) && $yUnitStack > 0 ) {
					$uniqueHeight = round( ( $uniqueData[$i] * 25 ) / $yUnitStack * 5 ) - 3; // border と shadow の値分 -3
					if( $uniqueHeight < 0 ) $uniqueHeight = 1; // -3 した分で 0 以下になった場合の処理
					$uniqueHeightAlt = $this->getFormatNumber( $uniqueData[$i] );
					$uniqueClass = 'unique';
					$uniquePng = ImageList::$generalImages['unique'];
				}
				else {
					$uniqueHeight = 125;
					$uniqueHeightAlt = 0;
					$uniqueClass = 'spacer';
					$uniquePng = ImageList::$generalImages['spacer'];
				}
				if( isset( $totalData[$i] ) && $yUnitStack > 0 ) {
					$totalHeight = round( ( $totalData[$i] * 25 ) / $yUnitStack * 5 ) - 3; //border と shadow の値分 -3
					if( $totalHeight < 0 ) $totalHeight = 1; // -3 した分で 0 以下になった場合の処理
					$totalHeightAlt = $this->getFormatNumber( $totalData[$i] );
					$totalClass = 'total';
					$totalPng = ImageList::$generalImages['total'];
				}
				else {
					$totalHeight = 125;
					$totalHeightAlt = 0;
					$totalClass = 'spacer';
					$totalPng = ImageList::$generalImages['spacer'];
				}
				$html .= '<td class="gterm" align="center" valign="bottom">';
				$html .= '<div class="colbar">';
				$html .= '<img class="' . $uniqueClass . '" width="20" height="' . $uniqueHeight . '" alt="' . $uniqueHeightAlt . '" title="' . $uniqueHeightAlt . '" src="'. $uniquePng . '" />';
				$html .= '<img class="' . $totalClass . '" width="20" height="' . $totalHeight . '" alt="' . $totalHeightAlt . '" title="' . $totalHeightAlt . '" src="'. $totalPng . '" /></div></td>';
			}
			$html .= '<td class="rightspace"></td>';
			$html .= '</tr>';

			$html .= '<tr>';
			$html .= '<td class="yscale-zero"><div>0</div></td>';
			for( $i = 16 * ( $row - 1 ) + 1; $i <= 16 * $row; $i++ ) {
				$clsName = '';
				$dayString = '';
				if( checkdate( $mm, $i, $yyyy ) ) {
					$dayString = $i . '日';
					$weekday = date( 'w', mktime( '0', '0', '0', $mm, $i, $yyyy ) );
					if( (int)$weekday === 0 ) $clsName = ' sun';
					if( (int)$weekday === 6 ) $clsName = ' sat';
				}
				if( isset( $totalData[$i] ) ) {
					$html .= '<td class="terms vtop"><div class="terms' . $clsName . '"><a href="' . $this->getIndexUrl( 'research', 'time',
						'&amp;yyyy_from='. $yyyy.
						'&amp;mm_from=' . $mm.
						'&amp;dd_from=' . $this->getZeroPadding( $i, 1 ) .
						'&amp;yyyy_to=' . $yyyy .
						'&amp;mm_to=' . $mm .
						'&amp;dd_to=' . $this->getZeroPadding( $i, 1 ) ) . '">';
					$html .= $dayString . '</a></div></td>';
				}
				else {
					$html .= '<td class="terms vtop"><div class="terms' . $clsName . '">' . $dayString . '</div></td>';
				}
			}
			$html .= '<td class="rightbottomspace"></td>';
			$html .= '</tr>';
		}

		echo $html;
	}

	public function resultWeekTag() {
		$uniqueData = $this->result->get('uniqueData');
		$totalData  = $this->result->get('totalData');

		if( count( $totalData ) > 0 ) {
			$max = $totalData;
			rsort( $max );
			$maxValue = $max[0];
		}
		else {
			$maxValue = 0;
		}

		$html  = '';
		$html .= '<tr>';
		$html .= '<td class="topspace" colspan="9"></td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td class="empty-left"></td>';

		$uniqueCnt = 0;
		$totalCnt  = 0;

		for( $i = 0; $i <= 6; $i++ ) {
			if( isset( $uniqueData[$i] ) ) {
				$uniqueCnt = $uniqueData[$i];
			}
			else {
				$uniqueCnt = 0;
			}
			if( isset( $totalData[$i] ) ) {
				$totalCnt = $totalData[$i];
			}
			else {
				$totalCnt = 0;
			}
			$clsNameUniq  = 'blue';
			$clsNameTotal = 'red';
			if( strlen( $uniqueCnt ) > Config::STANDARD_DIGIT ) $clsNameUniq  .= ' stretch';
			if( strlen( $totalCnt  ) > Config::STANDARD_DIGIT ) $clsNameTotal .= ' stretch';
			$html .= '<td class="num graph-count"><div class="' . $clsNameTotal . '">' . $totalCnt . '</div>';
			$html .= '<div class="' . $clsNameUniq . '">' . $uniqueCnt . '</div></td>';
		}
		$html .= '<td class="empty-right"></td>';
		$html .= '</tr>';

		$html .= '<tr>';
		$html .= '<td class="yscale nobreak">';

		$yUnitStack = 0;
		$yUnit_array = array();
		$graphUnit = 32;
		$j = 5;
		$yUnit = $maxValue >= $j ? ceil( $maxValue / $j ) : 1;

		for( $i = 0; $i < $j; $i++ ) {
			$yUnitStack += $yUnit;
			$yUnit_array[] = $yUnitStack;
		}

		$yUnit_array = array_reverse( $yUnit_array );

		for( $i = 0; $i < $j; $i++ ) {
			$html .= '<div style="height:' . $graphUnit . 'px;">' . $yUnit_array[$i] . '</div>';
		}
		$html .= '</td>';

		for( $i = 0; $i <= 6; $i++ ) {
			if( isset( $uniqueData[$i] ) && $yUnitStack > 0 ) {
				$uniqueHeight = round( ( $uniqueData[$i] * 32 ) / $yUnitStack * 5 ) - 3; // border と shadow の値分 -3
				if( $uniqueHeight < 0 ) $uniqueHeight = 1; // -3 した分で 0 以下になった場合の処理
				$uniqueHeightAlt = $this->getFormatNumber( $uniqueData[$i] );
				$uniqueClass = 'unique';
				$uniquePng = ImageList::$generalImages['unique'];
			}
			else {
				$uniqueHeight = 160;
				$uniqueHeightAlt = '';
				$uniqueClass = 'spacer';
				$uniquePng = ImageList::$generalImages['spacer'];
			}
			if( isset( $totalData[$i] ) && $yUnitStack > 0 ) {
				$totalHeight = round( ( $totalData[$i] * 32 ) / $yUnitStack * 5 ) - 3; //border と shadow の値分 -3
				if( $totalHeight < 0 ) $totalHeight = 1; // -3 した分で 0 以下になった場合の処理
				$totalHeightAlt = $this->getFormatNumber( $totalData[$i] );
				$totalClass = 'total';
				$totalPng = ImageList::$generalImages['total'];
			}
			else {
				$totalHeight = 160;
				$totalHeightAlt = '';
				$totalClass = 'spacer';
				$totalPng = ImageList::$generalImages['spacer'];
			}
			$html .= '<td class="gweek" align="center" valign="bottom">';
			$html .= '<div class="colbar">';
			$html .= '<img class="' . $uniqueClass . '" width="40" height="' . $uniqueHeight . '" alt="' . $uniqueHeightAlt . '" title="' . $uniqueHeightAlt . '" src="' . $uniquePng . '" />';
			$html .= '<img class="' . $totalClass . '" width="40" height="' . $totalHeight . '" alt="' . $totalHeightAlt . '" title="' . $totalHeightAlt . '" src="' . $totalPng . '" /></div></td>';
		}
		$html .= '<td class="rightspace"></td>';
		$html .= '</tr>';

		$html .= '<tr>';
		$html .= '<td class="yscale-zero"><div>0</div></td>';
		for( $i = 0; $i <= 6; $i++ ) {
			$clsName = '';
			if( $i === 0 ) $clsName = ' sun';
			if( $i === 6 ) $clsName = ' sat';
			$html .= '<td class="weeks vtop"><div class="weeks' . $clsName . '">' . Calendar::getWeekday( $i ) . '</div></td>';
		}
		$html .= '<td class="rightbottomspace"></td>';
		$html .= '</tr>';

		echo $html;
	}

	public function weekPieChart( $kind = 'unique' ) {
		$data = $title = $xscale = '';
		$xdata = array();
		$type = 'pie';

		$colorset = array(
			'rgba(51,102,204,1.0)',
			'rgba(137,98,53,1.0)',
			'rgba(16,150,24,1.0)',
			'rgba(0,153,198,1.0)',
			'rgba(221,68,119,1.0)',
			'rgba(225,153,0,1.0)',
			'rgba(220,57,18,1.0)'
		);

		if( $kind === 'unique' ) {
			$data = $this->result->get('uniqueData');
			$title = '曜日別ユニークアクセス';
			$xscale = '"","UNIQUE"';
		}
		else {
			$data = $this->result->get('totalData');
			$title = '曜日別ページビュー';
			$xscale = '"","PV"';
		}

		for( $i = 0; $i <= 6; $i++ ) {
			if( $i === 0 ) $xdata[] = ( isset( $data[$i] ) ) ? '"日曜日",' . $data[$i] : '"日曜日",' . 0;
			if( $i === 1 ) $xdata[] = ( isset( $data[$i] ) ) ? '"月曜日",' . $data[$i] : '"月曜日",' . 0;
			if( $i === 2 ) $xdata[] = ( isset( $data[$i] ) ) ? '"火曜日",' . $data[$i] : '"火曜日",' . 0;
			if( $i === 3 ) $xdata[] = ( isset( $data[$i] ) ) ? '"水曜日",' . $data[$i] : '"水曜日",' . 0;
			if( $i === 4 ) $xdata[] = ( isset( $data[$i] ) ) ? '"木曜日",' . $data[$i] : '"木曜日",' . 0;
			if( $i === 5 ) $xdata[] = ( isset( $data[$i] ) ) ? '"金曜日",' . $data[$i] : '"金曜日",' . 0;
			if( $i === 6 ) $xdata[] = ( isset( $data[$i] ) ) ? '"土曜日",' . $data[$i] : '"土曜日",' . 0;
		}
		$xdata = array_reverse( $xdata );

		echo $this->_drawGraph( $title, $type, $colorset, $xscale, $xdata, null, true, 230 );
	}

	public function minPieChart( $kind=null ) {
		$action = $this->action;
		$summaryData = $this->result->get('summaryData');
		$title = 'アクセス数';
		$half = 'full';
		$xdata = array();

		switch( $kind ) {
			case 'unique':
				$title = 'ユニークアクセス';
				$kind = 'unique';
				break;
			case 'total':
				$kind = 'total';
				$title = 'ページビュー ';
				break;
			default:
				 break;
		}

		foreach( $summaryData as $key => $value ) {
			if( $action === 'pref' && $key === '日本' ) $key .= ' (地域不明)';
			switch( $action ) {
				case 'rank':
					$xdata[] = '"' . $this->substrMax( $this->urlDecode( $this->escapeHtml( $this->getTitle( $this->escapeHtml( $key ) ) ) ) ) . '",' . $value[$kind];
					break;
				case 'pagein':
				case 'pageout':
					if( $value <= 0 ) $value = 0;
					$xdata[] = '"' . $this->substrMax( $this->urlDecode( $this->escapeHtml( $this->getTitle( $this->escapeHtml( $key ) ) ) ) ) . '",' . $value;
					break;
				case 'host':
				case 'country':
				case 'pref':
					$xdata[] = '"' . $this->urlDecode( $this->escapeHtml( $this->getTitle( $this->escapeHtml( $key ) ) ) ) . '",' . $value[$kind];
					$half = 'half';
					break;
				default:
					$xdata[] = '"' . mb_strimwidth( $this->escapeHtml( $this->escapeHtml( $key ) ), 0, 18, '…' ) . '",' . $value[$kind];
					$half = 'half';
					break;
			}
		}
		echo $this->_drawPiecahrt( $title, $xdata, $half, 9999, 280 );
	}

	public function resultUidTag() {
		$summaryData = $this->result->get('summaryData');
		$clickLink = $this->result->get('clickLink');
		$totalCount = $this->result->get('totalCount');
		$siteData = $this->session->get('siteData');

		$this->result->delete('summaryData');
		$this->result->delete('clickLink');
		$this->result->delete('totalCount');
		$this->result->delete('siteData');

		$i = 0;
		$pageCount = 1;
		$page = $this->request->get('page') !== null ? $this->request->get('page') : 1;

		$uid_access = array();
		$uid_clicks = array();

		foreach( (array)$summaryData as $key => $value ) {
		//foreach( $uid_unique as $key => $value ) {
			if( $pageCount >= $siteData['dispview'] * ( $page - 1 ) + 1 && $pageCount <= $siteData['dispview'] * $page ) {
				$uid_access[] = $key;
				if( $i >= $siteData['dispview'] ) break;
				++$i;
			}
			++$pageCount;
		}

		$i = 0;
		$pageCount = 1;

		foreach( (array)$clickLink as $key => $value ) {
		//foreach( $uid_unique as $key => $value ) {
			if( $pageCount >= $siteData['dispview'] * ( $page - 1 ) + 1 && $pageCount <= $siteData['dispview'] * $page ) {
				$uid_clicks[] = $key;
				if( $i >= $siteData['dispview'] ) break;
				++$i;
			}
			++$pageCount;
		}

		$rcon = new ResearchController( $this->request, $this->session, $this->message, $this->result, $this->controller, $this->action );
		$conditions = $rcon->_initConditions();
		$conditions[0] .= ' AND ( CONCAT( uid,dd ) = ?';
		$conditions[] = array_shift( $uid_access );
		foreach( $uid_access as $key => $value ) {
			$conditions[0] .= ' OR CONCAT( uid,dd ) = ?';
			$conditions[] = $value;
		}
		$conditions[0] .= ' )';
		$findOptions = array( 'condition' => $conditions );

		$method = 'uid_access';
		$rcon->_doResearch( $findOptions, null, $method );

		$yymmdd1 = $this->result->get('yymmdd');
		$pref1 = $this->result->get('pref');
		$os1 = $this->result->get('os');
		$os_ver1 = $this->result->get('os_ver');
		$browser1 = $this->result->get('browser');
		$brow_ver1 = $this->result->get('brow_ver');
		$summaryData1 = $this->result->get('summaryData');
		$clickLink1 = $this->result->get('clickLink');
		$clickBtn1 = $this->result->get('clickBtn');
		$clickAdsense1 = $this->result->get('clickAdsense');

		$rcon = new ResearchController( $this->request, $this->session, $this->message, $this->result, $this->controller, $this->action );
		$conditions = $rcon->_initConditions();
		$conditions[0] .= ' AND ( CONCAT( uid,dd ) = ?';
		$conditions[] = array_shift( $uid_clicks );
		foreach( $uid_clicks as $key => $value ) {
			$conditions[0] .= ' OR CONCAT( uid,dd ) = ?';
			$conditions[] = $value;
		}
		$conditions[0] .= ' )';
		$findOptions = array( 'condition' => $conditions );

		$method = 'uid_clicks';
		$rcon->_doResearch( $findOptions, null, $method );

		$yymmdd2 = $this->result->get('yymmdd');
		$pref2 = $this->result->get('pref');
		$os2 = $this->result->get('os');
		$os_ver2 = $this->result->get('os_ver');
		$browser2 = $this->result->get('browser');
		$brow_ver2 = $this->result->get('brow_ver');
		$summaryData2 = $this->result->get('summaryData');
		$clickLink2 = $this->result->get('clickLink');
		$clickBtn2 = $this->result->get('clickBtn');
		$clickAdsense2 = $this->result->get('clickAdsense');

		unset( $uid_access );
		unset( $uid_clicks );
		unset( $summaryData );

		$html = '';

		$i = 0;
		$ranking = $siteData['dispview'] * $page - $siteData['dispview'] + 1;

		foreach( $summaryData1 as $key => $value ) {
			$dispOs = $os1[$key] . ' ' . $os_ver1[$key];
			$dispOsImg = $this->getOsImage( $dispOs );

			$dispBrow = $browser1[$key] . ' ' . $brow_ver1[$key];
			$dispBrowImg = $this->getBrowserImage( $dispBrow );

			$dispPref = ( $pref1[$key] === '日本' ) ? '日本 (地域不明)' : $pref1[$key];
			$dispPrefImg = $this->getPrefImage( $pref1[$key] );

			$html .= '<tr>';
			$html .= '<td class="' . $this->getEvenClass( $i ) . ' aright">' . $ranking . '</td>';
			$html .= '<td class="' . $this->getEvenClass( $i ) . '">';
			$html .= '<div class="visitinfo nowrap">';
			$html .= '<img src="' . ImageList::$generalImages['offline'] . '" width="12" height="12" alt="offline" class="vmiddle">&nbsp;' . $yymmdd1[$key];
			$html .= '</div>';
			$html .= 'ID: <a href="' . $this->getIndexUrl( 'research', 'uid_detail', '&amp;select=' . $this->urlEncode( $key ) ) . '" title="' . $this->escapeHtml( $key ) . '">' . $this->getAlias( $this->escapeHtml( $key ) ) . '</a>';
			$html .= '<div class="visitinfo">';
			$html .= '<span class="favi"><img src="' . $dispOsImg . '" alt="' . $dispOs . '" title="' . $dispOs . '" /></span>';
			$html .= '<span class="favi"><img src="' . $dispBrowImg . '" alt="' . $dispBrow . '" title="' . $dispBrow . '" /></span>';
			$html .= '<span class="favi" ><img src="' . $dispPrefImg. '" alt="' . $dispPref . '" title="' . $dispPref . '" width="16" height="16" /></span>';
			$html .= '<span class="property nowrap"><a href="' . $this->getIndexUrl( 'research', 'pref_user', '&amp;select=' . $this->urlEncode( $pref1[$key] ) ) . '" title="' . $dispPref . '">' . $dispPref . '</a></span>';
			$html .= '</div>';
			$html .= '</td>';
			$html .= '<td class="' . $this->getEvenClass( $i ) . ' aright">';
			$html .= '<div class="rankinfo">';
			$html .= '<div class="click">アクセス</div>';
			$html .= '<div>&nbsp;<span class="red bold">' . $this->getFormatNumber( $value ) . '</span></div>';
			$html .= '</div>';
			$html .= '<div class="clickinfo">';
			$html .= '<div class="click"">リンク</div>';
			$html .= '<div>&nbsp;<span class="green">' . $this->getFormatNumber( $clickLink1[$key] ) . '</span></div>';
			$html .= '<div class="click"">ボタン</div>';
			$html .= '<div>&nbsp;<span class="green">' . $this->getFormatNumber( $clickBtn1[$key] ) . '</span></div>';
			$html .= '<div class="click"">Adsense</div>';
			$html .= '<div>&nbsp;<span class="green">' . $this->getFormatNumber( $clickAdsense1[$key] ) . '</span></div>';
			$html .= '</div>';
			$html .= '</td>';

			$html .= '<td class="no-back b0-t b0-b"></td>';

			$key2 = key( $summaryData2 );

			$dispOs = $os2[$key2] . ' ' . $os_ver2[$key2];
			$dispOsImg = $this->getOsImage( $dispOs );

			$dispBrow = $browser2[$key2] . ' ' . $brow_ver2[$key2];
			$dispBrowImg = $this->getBrowserImage( $dispBrow );

			$dispPref = ( $pref2[$key2] === '日本' ) ? '日本 (地域不明)' : $pref2[$key2];
			$dispPrefImg = $this->getPrefImage( $pref2[$key2] );

			$html .= '<td class="' . $this->getEvenClass( $i ) . ' aright">' . $ranking . '</td>';
			$html .= '<td class="' . $this->getEvenClass( $i ) . '">';
			$html .= '<div class="visitinfo nowrap">';
			$html .= '<img src="' . ImageList::$generalImages['offline'] . '" width="12" height="12" alt="offline" class="vmiddle">&nbsp;' . $yymmdd2[$key2];
			$html .= '</div>';
			$html .= 'ID: <a href="' . $this->getIndexUrl( 'research', 'uid_detail', '&amp;select=' . $this->urlEncode( $key2 ) ) . '" title="' . $this->escapeHtml( $key2 ) . '">' . $this->getAlias( $this->escapeHtml( $key2 ) ) . '</a>';
			$html .= '<div class="visitinfo">';
			$html .= '<span class="favi"><img src="' . $dispOsImg . '" alt="' . $dispOs . '" title="' . $dispOs . '" /></span>';
			$html .= '<span class="favi"><img src="' . $dispBrowImg . '" alt="' . $dispBrow . '" title="'. $dispBrow . '" /></span>';
			$html .= '<span class="favi" ><img src="' . $dispPrefImg . '" alt="'. $dispPref . '" title="' . $dispPref . '" width="16" height="16" /></span>';
			$html .= '<span class="property nowrap"><a href="' . $this->getIndexUrl( 'research', 'pref_user', '&amp;select=' . $this->urlEncode( $pref2[$key2] ) ) . '" title="' . $dispPref . '">' . $dispPref . '</a></span>';
			$html .= '</div>';
			$html .= '</td>';
			$html .= '<td class="' . $this->getEvenClass( $i ) . ' aright">';
			$html .= '<div class="rankinfo">';
			$html .= '<div class="click">リンク</div>';
			$html .= '<div>&nbsp;<span class="red bold">' . $this->getFormatNumber( $clickLink2[$key2] ) . '</span></div>';
			$html .= '</div>';
			$html .= '<div class="clickinfo">';
			$html .= '<div class="click"">アクセス</div>';
			$html .= '<div>&nbsp;<span class="green">' . $this->getFormatNumber( $summaryData2[$key2] ) . '</span></div>';
			$html .= '<div class="click"">ボタン</div>';
			$html .= '<div>&nbsp;<span class="green">' . $this->getFormatNumber( $clickBtn2[$key2] ) . '</span></div>';
			$html .= '<div class="click"">Adsense</div>';
			$html .= '<div>&nbsp;<span class="green">' . $this->getFormatNumber( $clickAdsense2[$key2] ) . '</span></div>';
			$html .= '</div>';
			$html .= '</td>';
			$html .= '</tr>';

			next( $summaryData2 );
			++$i;
			++$ranking;
		}
		echo $html;
	}

	public function resultUidDetailHeaderTag() {
		$summaryData = $this->result->get('summaryData');
		asort( $summaryData );

		$html = '';
		$addr = array();
		$host = array();
		$domain = array();
		$pref = array();
		$country = array();
		$http_referer = array();
		$referer_title = array();
		$propertySet = false;
		$count = count( $summaryData );

		if( $count > 0 ) {
			foreach( $summaryData as $key => $value ) {
				if( $value['url'] !== null ) $url = $value['url'];
				if( !isset( $addr[$value['remote_addr']] ) ) {
					$addr[$value['remote_addr']] = $value['remote_addr'];
				}
				if( !isset( $addr[$value['remote_host']] ) ) {
					$host[$value['remote_host']] = $value['remote_host'];
				}
				if( !isset( $domain[$value['domain']] ) ) {
					$domain[$value['domain']] = $value['domain'];
				}
				if( !isset( $pref[$value['pref']] ) ) {
					$pref[$value['pref']] = $value['pref'];
				}
				if( !isset( $country[$value['country']] ) ) {
					$country[$value['country']] = $value['country'];
				}
				if( $value['referer_host'] !== '' && $value['referer_host'] !== null && $value['referer_host'] !== Config::DIRECT_ACCESS ) {
					if( !isset( $http_referer[$value['http_referer']] ) ) {
						$http_referer[$value['http_referer']] = $value['http_referer'];
						$referer_title[$value['referer_title']] = $this->escapeHtml( $this->urlDecode( $this->getTitle( $value['referer_title'] ) ) );
					}
				}
				if( !$propertySet ) {
					$os = $value['os'];
					$os_ver = ($value['os_ver'] != 'unknown') ? ' ' . $value['os_ver'] : '';
					$osImg = $this->getOsImage( $os . $os_ver );
					$brow = $value['browser'];
					$brow_ver = ($value['brow_ver'] != 'unknown') ? ' ' . $value['brow_ver'] : '';
					$browImg = $this->getBrowserImage( $brow . $brow_ver );
					$screenWh = $value['screenwh'];
					$screenCol = $value['screencol'];
					$jsck = $value['jsck'];
					$http_user_agent = $value['http_user_agent'];
					$propertySet = true;
				}
			}
			if( !$http_referer ) {
				$ref = current( $summaryData );
				$http_referer = ( $ref['http_referer'] ) ? $ref['http_referer'] : '';
			}
			if( !$referer_title ) {
				$tit = current( $summaryData );
				$referer_title = ( $tit['referer_title'] ) ? $this->escapeHtml( $this->urlDecode( $this->getTitle( $tit['referer_title'] ) ) ) : '';
				if( !$referer_title ) $referer_title = Config::NO_DATA;
			}
		}
		unset( $summaryData );

		$html .= '<tr>';
		$html .= '<td class="label">ID&nbsp;/&nbsp;訪問者名</td>';
		$html .= '<td class="label">:</td>';
		$html .= '<td class="value"><span class="hval">' . $this->getAlias( $this->request->get('select') ) . '</span>';
		if( $count > 0 ) {
			$html .= '<span class="alias"><a href="' . $this->getIndexUrl( 'admin', 'aliasedit', '&amp;select=' . $this->urlEncode( $this->request->get('select') ) ) . '">訪問者名設定</a></span>';
		}
		$html .= '</td>';
		$html .= '</tr>';
		if( $count > 0 ) {
			$cnt = 0;
			foreach( (array)$country as $key => $value ) {
				$html .= '<tr>';
				if( $cnt === 0 ) {
					$html .= '<td class="label">国</td>';
				}
				else {
					$html .= '<td class="label">&nbsp;</td>';
				}
				$html .= '<td class="label">:</td>';
				$html .= '<td class="value">';
				$html .= '<span class="favi"><img src="' . $this->getCountryImage( $value ) . '" alt="' . $this->escapeHtml( $value ) . '" title="'. $this->escapeHtml( $value ) . '" width="16" height="16" /></span>';
				$html .= '<span class="hval"><a href="' . $this->getIndexUrl( 'research', 'country_user', '&amp;select=' . $this->urlEncode( $value ) ) . '" title="' . $this->escapeHtml( $value ) . '">' . $this->escapeHtml( $value ) . '</a></span>';
				$html .= '</td>';
				$html .= '</tr>';
				++$cnt;
			}
			unset( $country );

			$cnt = 0;
			foreach( (array)$pref as $key => $value ) {
				$unlkown = ( $value === '日本' ) ? ' (地域不明)' : '';
				$html .= '<tr>';
				if( $cnt === 0 ) {
					$html .= '<td class="label">都道府県</td>';
				}
				else {
					$html .= '<td class="label">&nbsp;</td>';
				}
				$html .= '<td class="label">:</td>';
				$html .= '<td class="value">';
				$html .= '<span class="favi"><img src="' . $this->getPrefImage( $value ) . '" alt="' . $this->escapeHtml( $value ) . $unlkown . '" title="' . $this->escapeHtml( $value ) . $unlkown . '" width="16" height="16" /></span>';
				$html .= '<span class="hval"><a href="' . $this->getIndexUrl( 'research', 'pref_user', '&amp;select=' . $this->urlEncode( $value ) ) . '" title="' . $this->escapeHtml( $value ) . $unlkown . '">' . $this->escapeHtml( $value ) . $unlkown . '</a></span>';
				$html .= '</td>';
				$html .= '</tr>';
				++$cnt;
				unset( $pref[$key] );
			}
			unset( $pref );

			$cnt = 0;
			foreach( (array)$addr as $key => $value ) {
				$html .= '<tr>';
				if( $cnt === 0 ) {
					$html .= '<td class="label">IPアドレス</td>';
				}
				else {
					$html .= '<td class="label">&nbsp;</td>';
				}
				$html .= '<td class="label">:</td>';
				$html .= '<td class="value">';
				$html .= '<span class="hval"><a href="' . $this->getIndexUrl( 'research', 'ip_user', '&amp;select=' . $this->urlEncode( $value ) ) . '" title="' . $this->escapeHtml( $value ) . '">' . $this->escapeHtml( $value ) . '</a></span>';
				$html .= '</td>';
				$html .= '</tr>';
				++$cnt;
			}
			unset( $addr );

			$cnt = 0;
			foreach( (array)$host as $key => $value ) {
				$html .= '<tr>';
				if( $cnt === 0 ) {
					$html .= '<td class="label">リモートホスト</td>';
				}
				else {
					$html .= '<td class="label">&nbsp;</td>';
				}
				$html .= '<td class="label">:</td>';
				$html .= '<td class="value">';
				$html .= '<span class="hval"><a href="' . $this->getIndexUrl( 'research', 'remotehost_user', '&amp;select=' . $this->urlEncode( $value ) ) . '" title="' . $this->escapeHtml( $value ) . '">' . $this->escapeHtml( $value ) . '</a></span>';
				$html .= '</td>';
				$html .= '</tr>';
				++$cnt;
			}
			unset( $host );

			$cnt = 0;
			foreach( (array)$domain as $key => $value ) {
				$html .= '<tr>';
				if( $cnt === 0 ) {
					$html .= '<td class="label">ドメイン</td>';
				}
				else {
					$html .= '<td class="label">&nbsp;</td>';
				}
				$html .= '<td class="label">:</td>';
				$html .= '<td class="value">';
				$html .= '<span class="hval"><a href="' . $this->getIndexUrl( 'research', 'domain_user', '&amp;select=' . $this->urlEncode( $value ) ) . '" title="' . $this->escapeHtml( $value ) . '">' . $this->getDomain( $this->escapeHtml( $value ) ) . '</a>' . '</span>' . $this->getDomainEdit( $this->escapeHtml( $value ), false );
				$html .= '</td>';
				$html .= '</tr>';
				++$cnt;
			}
			unset( $domain );

			$html .= '<tr>';
			$html .= '<td class="label">OS</td>';
			$html .= '<td class="label">:</td>';
			$html .= '<td class="value"><span class="favi"><img src="' . $osImg . '" alt="' . $this->escapeHtml( $os ) . '" title="' . $this->escapeHtml( $os ) . '" /></span>';
			$html .= '<span class="hval">' . $this->escapeHtml( $os . $os_ver ) . '</span></td>';
			$html .= '</tr>';
			$html .= '<tr>';
			$html .= '<td class="label">ブラウザ</td>';
			$html .= '<td class="label">:</td>';
			$html .= '<td class="value"><span class="favi"><img src="' . $browImg . '" alt="' . $this->escapeHtml( $brow ) . '" title="' . $this->escapeHtml( $brow ) . '" /></span>';
			$html .= '<span class="hval">' . $this->escapeHtml( $brow . $brow_ver ) . '</span></td>';
			$html .= '</tr>';
			$html .= '<tr>';
			$html .= '<td class="label">画面解像度 / 色数</td>';
			$html .= '<td class="label">:</td>';
			$html .= '<td class="value"><span class="hval">' . $this->escapeHtml( $screenWh ) . '</span>/ <span class="hval">' . $this->escapeHtml( $screenCol ) . '</span></td>';
			$html .= '</tr>';
			$html .= '<tr>';
			$html .= '<td class="label">Javascript/Cookie</td>';
			$html .= '<td class="label">:</td>';
			$html .= '<td class="value"><span class="hval">' . $this->escapeHtml( $jsck ) . '</span></td>';
			$html .= '</tr>';
			$html .= '<tr>';
			$html .= '<td class="label vtop">User Agent</td>';
			$html .= '<td class="label vtop">:</td>';
			$html .= '<td class="value"><div class="agent">' . $this->escapeHtml( $http_user_agent ) . '</div></td>';
			$html .= '</tr>';

			$cnt = 0;
			foreach( (array)$http_referer as $key => $value ) {
				$html .= '<tr>';
				if( $cnt === 0 ) {
					$html .= '<td class="label vtop">リンク元</td>';
				}
				else {
					$html .= '<td class="label vtop">&nbsp;</td>';
				}
				$html .= '<td class="label vtop">:</td>';
				$html .= '<td class="value">';

				if(
					$referer_title === Config::FROM_NO_SCRIPT ||
					$referer_title === Config::DIRECT_ACCESS ||
					$referer_title === Config::FROM_CONTINUE ||
					$referer_title === Config::NO_DATA
				) {
					$html .= '<div class="agent">' . $referer_title . '</div>';
				}
				else {
					$html .= '<div class="agent"><a href="' . $this->getJumpUrl( $this->urlDecode( $value ) ) . '" title="' . $this->escapeHtml( $this->urlDecode( $value ) ) . '" target="_blank">' . $this->escapeHtml( $this->urlDecode( $value ) ) . '</a></div>';
				}
				$html .= '</td>';
				$html .= '</tr>';
				++$cnt;
			}
		}
		echo $html;
	}

	public function resultUidDetailTag() {
		$summaryData = $this->result->get('summaryData');
		$siteData = $this->session->get('siteData');
		$page = $this->request->get('page') !== null ? $this->request->get('page') : 1;

		$html = '';
		$afterday = '';
		$dispReferer = '';
		$html_array = array();
		$http_referer = array();
		$timeData = array();
		$timeTemp = array();
		$pageCount = 1;
		$i = $j = 0;

		$summaryTemp = array_reverse( $summaryData );

		foreach( $summaryTemp as $key => $value ) {
			$nowday = $value['yyyy'] . $value['mm'] . $value['dd'];
			$now = ( $value['hh'] * 3600 ) + ( $value['mi'] * 60 ) + $value['ss'];

			if( $nowday !== $afterday ) {
				$clsName = 'last';
				$altName = 'End&#133;';
				$timeDisp = '';
			}
			else {
				$readtime = $after - $now;
				if( $readtime / 60 < 1 ) {
					$timeDisp = $readtime . '秒';
				}
				else {
					if( $readtime / 3600 < 1 ) {
						if( $readtime % 60 === 0 ) {
							$timeDisp = $readtime / 60 . '分';
						}
						else {
							$timeDisp = floor( $readtime / 60 ) . '分' . $readtime % 60 . '秒';
						}
					}
					else {
						if( $readtime % 3600 === 0 ) {
							$timeDisp = $readtime / 3600 . '時間';
						}
						else {
							if( ($readtime % 3600) % 60 === 0 ) {
								$timeDisp = floor( $readtime / 3600 ) . '時間' . ( $readtime % 3600 ) / 60 . '分';
							}
							else {
								$timeDisp = floor( $readtime / 3600 ) . '時間' . floor( ( $readtime % 3600 ) / 60 ) . '分' . ( $readtime % 3600 ) % 60 . '秒';
							}
						}
					}
				}
				if( $readtime >= $siteData['oksecond'] ) {
					if( $readtime >= $siteData['againsecond'] ) {
						$clsName = 'revisit';
						$altName = 'ReVisit!';
					}
					else {
						$clsName = 'good';
						$altName = 'Good!!';
					}
				}
				else {
					$clsName = 'bad';
					$altName = 'Bad&#133;';
				}
			}

			$afterday = $nowday;
			$after = $now;
			$timeTemp[$j]['timeDisp'] = $timeDisp;
			$timeTemp[$j]['clsName'] = $clsName;
			$timeTemp[$j]['altName'] = $altName;
			++$j;
		}
		unset( $summaryTemp );
		$timeData = array_reverse( $timeTemp );
		unset( $timeTemp );

		$mydomain = QueryConfig::getLikelyMydomain();

		foreach( $summaryData as $key => $value ) {
			$html = '';
			$accessTime = '<div class="detail">' . $value['yyyy'] . '/' . $value['mm'] . '/' . $value['dd'] . ' ' . $value['hh'] . ':' . $value['mi'] . ':' . $value['ss'] . '</div>';
			$dispTitle = $this->substrMax( $this->escapeHtml( $this->urlDecode( $this->getTitle( $value['title'] ) ) ) );

			$arrowTag = '<img alt="&rarr;" title="&darr;" class="arrow-click" src="' . ImageList::$generalImages['arrowdown'] . '" />';

			if( $value['logtype'] === Config::CLICK_LINK ) {
				$action = 'clickrank';
				$arrowTag .= '<span class="linkclick">Link Click!!</span>';
			}
			elseif( $value['logtype'] === Config::CLICK_BTN ) {
				$action = 'btnrank';
				$arrowTag .= '<span class="btnclick">Button Click!!</span>';
			}
			elseif( $value['logtype'] === Config::CLICK_ADSENSE ) {
				$action = 'adrank';
				$arrowTag .= '<span class="adclick">AdSense Click!!</span>';
			}
			else {
				$action = 'rank';
			}

			$dispRequest = '<a href="' . $this->getIndexUrl( 'research', $action . '_user', '&amp;select=' . $this->urlEncode( $value['url'] ) ) . '" title="' . $this->escapeHtml( $this->urlDecode( $value['title'] ) ) . '">' . $dispTitle . '</a>';

			/* リファラー ここから */
			$dispRefererTitle = '';
			if( $this->_checkMydomain( $value['http_referer'], $mydomain ) && $value['referer_title'] !== '' ) {
				$dispRefererTitle = Config::FROM_CONTINUE;
			}
			else {
				$dispRefererTitle = $this->substrMax( $this->escapeHtml( $this->urlDecode( $this->getTitle( $value['referer_title'] ) ) ) );
			}
			if( count( $http_referer ) > 0 && ( $dispRefererTitle === Config::DIRECT_ACCESS || $dispRefererTitle === Config::FROM_CONTINUE ) ) goto needless;

			if( !isset( $http_referer[$value['http_referer']] ) ) {
				if( $dispRefererTitle === Config::FROM_NO_SCRIPT || $dispRefererTitle === Config::NO_DATA ) {
					$dispReferer = '<span class="favi"><img alt="' . $dispRefererTitle . '" title="' . $dispRefererTitle . '" src="' . ImageList::$generalImages['noscript'] . '" /></span>' . $dispRefererTitle;
				}
				elseif( $dispRefererTitle === Config::DIRECT_ACCESS ) {
					$dispReferer = '<span class="favi"><img alt="' . $dispRefererTitle . '" title="' . $dispRefererTitle . '" src="' . ImageList::$generalImages['bookmark'] . '" /></span>' . $dispRefererTitle;
				}
				elseif( $dispRefererTitle === Config::FROM_CONTINUE ) {
					$dispReferer = '<span class="favi"><img alt="' . $dispRefererTitle . '" title="' . $dispRefererTitle . '" src="' . ImageList::$generalImages['continue'] . '" /></span>' . $dispRefererTitle;
				}
				else {
					$dispHostImg = $this->getHostImage( $value['http_referer'] );
					$dispReferer = '<span class="favi"><img alt="' . $dispHostImg . '" title="' . $dispHostImg . '" src="' . $dispHostImg . '" /></span>' . '<a href="' . ''. $this->getJumpUrl( $value['http_referer'] ) . '" title="' . $this->escapeHtml( $value['http_referer'] ) . '" target="_blank">' . $dispRefererTitle . '</a>';
				}
				$html = '<tr>';
				$html .= '<td class="replaceClass b0-t b0-r">&nbsp;</td>';
				if( count( $http_referer ) > 0 ) {
					$html .= '<td class="replaceClass b0-t b0-l"><span class="revisit">ReVisit!</span></td>';
				}
				else {
					$html .= '<td class="replaceClass b0-t b0-l">&nbsp;</td>';
				}
				$html .= '<td class="replaceClass"><div class="detail">' . $dispReferer . '</div></td>';
				$html .= '<tr>';
				$http_referer[$value['http_referer']] = true;
				$html_array[] = $html;
			}
			needless:
			/* リファラー ここまで */

			$html = '<tr>';
			$html .= '<td class="replaceClass p0-b b0-b" colspan="2">' . $accessTime;
			$html .= '<td class="replaceClass" rowspan="2"><div class="detail">' . $arrowTag . '</div><div class="detail">' . $dispRequest . '</div></td>';
			$html .= '</tr>';
			$html .= '<tr>';
			$html .= '<td class="replaceClass p0-t b0-t b0-r"><span class="' . $timeData[$i]['clsName'] . '">' . $timeData[$i]['timeDisp'] . '</span></td>';
			$html .= '<td class="replaceClass p0-t b0-t b0-l"><span class="' . $timeData[$i]['clsName'] . '">' . $timeData[$i]['altName'] . '</span></td>';
			$html .= '</td>';
			$html .= '</tr>';

			$html_array[] = $html;
			++$i;
		}

		$i = 0;
		foreach( $html_array as $html ) {
			if( $pageCount >= $siteData['dispview'] * ( $page - 1 ) + 1 && $pageCount <= $siteData['dispview'] * $page ) {
				$html = str_replace( 'class="replaceClass', 'class="' . $this->getEvenClass( $i ), $html );
				echo $html;
				if( $i >= $siteData['dispview'] ) break;
				++$i;
			}
			++$pageCount;
		}
	}

	public function resultIpRemoteHostTag() {
		$action = $this->action;
		$siteData = $this->session->get('siteData');
		$uniqueCount = $this->result->get('uniqueCount');
		$totalCount = $this->result->get('totalCount');
		$summaryData = $this->_pageCut( $this->result->get('summaryData') );

		$domain_array = array();
		foreach( $summaryData as $key => $value ) {
			$domain_array[] = $key;
		}

		$method = $column = null;
		if( $action === 'remotehost' ) {
			$method = 'host_to_domain';
			$column = 'remote_host';
		}
		else {
			$method = 'ip_to_domain';
			$column = 'remote_addr';
		}

		$rcon = new ResearchController( $this->request, $this->session, $this->message, $this->result, $this->controller, $this->action );
		$conditions = $rcon->_initConditions();
		$conditions[0] .= ' AND ( ' . $column . ' = ?';
		$conditions[] = array_shift( $domain_array );
		foreach( $domain_array as $key => $value ) {
			$conditions[0] .= ' OR ' . $column . ' = ?';
			$conditions[] = $value;
		}
		$conditions[0] .= ' )';
		$findOptions = array( 'condition' => $conditions );
		$rcon->_doResearch( $findOptions, null, $method );
		$domain = $this->result->get('domain');

		$html = '';
		$i = 0;
		$this->setDomainLabel( $domain );

		foreach( $summaryData as $key => $value ) {
			$html .= '<tr>';
			$html .= '<td class="' . $this->getEvenClass( $i ) . '">';
			switch( $action ) {
				case 'ip':
					$html .= '<div class="ipinfo"><a href="' . $this->getIndexUrl( 'research', 'ip_user', '&amp;select=' . $this->urlEncode( $key ) ) . '" title="' . $this->escapeHtml( $key ) . '">' . $this->escapeHtml( $key ) . '</a></div>';
					$html .= '<div class="ipinfo"><a href="' . $this->getIndexUrl( 'research', 'domain_user', '&amp;select=' . $this->urlEncode( $domain[$key] ) ) . '" title="' . $domain[$key] . '">' . $this->getDomain( $domain[$key] ) . '</a></div>';
					break;
				case 'adip':
					$html .= '<div class="ipinfo"><a href="' . $this->getIndexUrl( 'research', 'adip_user', '&amp;select=' . $this->urlEncode( $key ) ) . '" title="' . $this->escapeHtml( $key ) . '">' . $this->escapeHtml( $key ) . '</a></div>';
					$html .= '<div class="ipinfo"><a href="' . $this->getIndexUrl( 'research', 'domain_user', '&amp;logtype=' . Config::CLICK_ADSENSE . '&amp;select=' . $this->urlEncode( $domain[$key] ) ) . '" title="'. $domain[$key] . '">' . $this->getDomain( $domain[$key] ) . '</a></div>';
					break;
				case 'remotehost':
					$html .= '<div class="ipinfo"><a href="' . $this->getIndexUrl( 'research', 'remotehost_user', '&amp;select=' . $this->urlEncode( $key ) ) . '" title="' . $key . '">' . $this->escapeHtml( $key ) . '</a></div>';
					$html .= '<div class="ipinfo"><a href="' . $this->getIndexUrl( 'research', 'domain_user', '&amp;select=' . $this->urlEncode($domain[$key] ) ) . '" title="' . $domain[$key] . '">' . $this->getDomain( $domain[$key] ) . '</a></div>';
					break;
				default:
					break;
			}
			if( is_array( $value ) ) {
				$t = ( $value['total'] ) ? $value['total'] : 1;
				$totalCount = ( $totalCount > 0 ) ? $totalCount : 1;
				$html .= '<td class="numeric ' . $this->getEvenClass( $i ) . '"><span class="blue">' . $this->getFormatNumber( $value['unique'] ) . '</span></td>';
				$html .= '<td class="numeric ' . $this->getEvenClass( $i ) . '">' . sprintf( '%.2f', round( $value['unique'] * 100 / $uniqueCount, 2 ) ) . '%</td>';
				$html .= '<td class="numeric ' . $this->getEvenClass( $i ) . '"><span class="red">' . $this->getFormatNumber( $t ) . '</span></td>';
				$html .= '<td class="numeric ' . $this->getEvenClass( $i ) . '">' . sprintf( '%.2f', round( $t * 100 / $totalCount, 2 ) ) . '%</td>';
				$html .= '<td class="rowgraph ' . $this->getEvenClass( $i ) . '">';
				$html .= '<div class="rowbar">';
				// グラフを重ねるので、グラフだけトータルで割った数字にする
				$html .= '<img class="unique" alt="' . $value['unique'] . '" title="' . $value['unique'] . '" width="' . round( $value['unique'] * 100 / $totalCount, 0 ) . '%" height="15" src="' . ImageList::$generalImages['unique'] . '" />';
				$html .= '<img class="total" alt="' . $t . '" title="' . $t . '" width="' . round( $t * 100 / $totalCount, 0 ) . '%" height="15" src="' . ImageList::$generalImages['total'] . '" />';
			}
			else {
				$html .= '<td class="numeric ' . $this->getEvenClass( $i ) . '"><span class="green">' . $this->getFormatNumber( $value ) . '</span></td>';
				$html .= '<td class="numeric ' . $this->getEvenClass( $i ) . '">' . sprintf( '%.2f', round( $value * 100 / $uniqueCount, 2 ) ) . '%</td>';
				$html .= '<td class="rowgraph ' . $this->getEvenClass( $i ) . '">';
				$html .= '<div class="rowbar">';
				$html .= '<img class="green" alt="' . $value . '" title="' . $value . '" width="' . round( $value * 100 / $uniqueCount, 0 ) . '%" height="15" src="' . ImageList::$generalImages['green'] . '" />';
			}
			$html .= '</div>';
			$html .= '</td>';
			$html .= '</tr>';
			++$i;
		}
		echo $html;
	}

	public function resultIpRemoteHostUserTag() {
		$summaryData = $this->result->get('summaryData');
		$totalCount = $this->result->get('totalCount');
		$yymmdd = $this->result->get('yymmdd');
		$pref = $this->result->get('pref');
		$browser = $this->result->get('browser');
		$brow_ver = $this->result->get('brow_ver');
		$os = $this->result->get('os');
		$os_ver = $this->result->get('os_ver');
		$clickLink = $this->result->get('clickLink');
		$clickBtn = $this->result->get('clickBtn');
		$clickAdsense = $this->result->get('clickAdsense');
		$siteData = $this->session->get('siteData');

		$this->result->delete('summaryData');
		$this->result->delete('siteData');

		$html = '';
		$i = 0;

		foreach( $summaryData as $key => $value ) {
			$uid = substr( $key, 0, strlen( $key ) - 2 );

			$os_ver[$key] = ( $os_ver[$key] !== 'unknown' ) ? ' ' . $os_ver[$key] : '';
			$dispOs = $os[$key] . $os_ver[$key];
			$dispOsImg = $this->getOsImage( $dispOs );

			$brow_ver[$key] = ( $brow_ver[$key] !== 'unknown' ) ? ' ' . $brow_ver[$key] : '';
			$dispBrow = $browser[$key] . $brow_ver[$key];
			$dispBrowImg = $this->getBrowserImage( $dispBrow );

			$dispPref = ( $pref[$key] === '日本' ) ? '日本 (地域不明)' : $pref[$key];
			$dispPrefImg = $this->getPrefImage( $pref[$key] );

			$html .= '<tr>';
			$html .= '<td class="nobreak ' . $this->getEvenClass( $i ) . '">';
			$html .= '<div class="visitinfo nowrap">';
			$html .= '<img src="' . ImageList::$generalImages['offline'] . '" width="12" height="12" alt="offline" class="vmiddle">&nbsp;' . $yymmdd[$key];
			$html .= '</div>';
			$html .= '<div class="visitinfo nowrap">';
			$html .= 'ID: <a href="' . $this->getIndexUrl( 'research', 'uid_detail', '&amp;select=' . $this->urlEncode( $uid ) ) . '" title="'. $this->escapeHtml( $uid ) . '">' . $this->getAlias( $uid ) . '</a>';
			$html .= $this->getAliasEdit( $uid, true, true, '名称設定' );
			$html .= '</div>';
			$html .= '</td>';
			$html .= '<td class="' . $this->getEvenClass( $i ) . '">';
			$html .= '<span class="favi"><img src="' . $dispOsImg . '" alt="' . $dispOs . '" title="' . $dispOs . '" /></span>';
			$html .= '<span class="favi"><img src="' . $dispBrowImg . '" alt="' . $dispBrow . '" title="'. $dispBrow . '" /></span>';
			$html .= '<span class="favi" ><img src="' . $dispPrefImg. '" alt="'. $dispPref . '" title="' . $dispPref . '" width="16" height="16" /></span>';
			$html .= '<span class="property nowrap"><a href="'. $this->getIndexUrl( 'research', 'pref_user', '&amp;select=' . $this->urlEncode( $pref[$key] ) ) . '" title="' . $dispPref . '">' . $dispPref . '</a></span>';
			$html .= '</td>';

			$html .= '<td class="' . $this->getEvenClass( $i ) . ' aright">';
			$html .= '<div class="rankinfo">';
			$html .= '<div class="click">アクセス</div>';
			$html .= '<div>&nbsp;<span class="red bold">' . $this->getFormatNumber( $value ) . '</span></div>';
			$html .= '</div>';
			$html .= '<div class="clickinfo">';
			$html .= '<div class="click"">リンク</div>';
			$html .= '<div>&nbsp;<span class="green">' . $this->getFormatNumber( $clickLink[$key] ) . '</span></div>';
			$html .= '<div class="click"">ボタン</div>';
			$html .= '<div>&nbsp;<span class="green">' . $this->getFormatNumber( $clickBtn[$key] ) . '</span></div>';
			$html .= '<div class="click"">Adsense</div>';
			$html .= '<div>&nbsp;<span class="green">' . $this->getFormatNumber( $clickAdsense[$key] ) . '</span></div>';
			$html .= '</div>';
			$html .= '</td>';
			$html .= '<td class="numeric ' . $this->getEvenClass( $i ) . '">' . sprintf( '%.2f', round( $value * 100 / $totalCount, 2 ) ) . '%</td>';
			$html .= '<td class="rowgraph ' . $this->getEvenClass( $i ) . '">';
			$html .= '<div class="rowbar">';
			$html .= '<img class="green" alt="' . $this->escapeHtml( $value ) . '" title="' . $this->escapeHtml( $value ) . '" width="' . round( $value * 100 / $totalCount, 0 ) . '%" height="15" src="' . ImageList::$generalImages['green'] . '" />';
			$html .= '</div>';
			$html .= '</td>';
			$html .= '</tr>';
			++$i;
		}
		echo $html;
	}

	private function _drawGraph( $title, $type, $colorset, $xscale, $xdata, $xlen=10, $hanrei=false, $height=350 ) {
		$html  = '';
		$html .= '<div class="graph-title">' . "\n";
		$html .= $title . "\n";
		$html .= '</div>' . "\n";
		$html .= '<div class="graph">' . "\n";
		$html .= '<div class="graph-ccchart-' . $type . '">' . "\n";
		$html .= '<canvas id="' . md5( $title ) . '"></canvas>' . "\n";
		$html .= '</div>' . "\n";

		if( is_array( $hanrei ) ) {
			$cset = array_reverse( $colorset );
			$html .= '<ul class="hanrei">';
			foreach( array_reverse( $hanrei ) as $k => $v ) {
				$html .= '<li class="text">' . $v . '</li>';
				$html .= '<li class="mark" style="background:' . $cset[$k] . '">&nbsp;</li>';
			}
			$html .= '<li class="clear"></li>';
			$html .= '</ul>';
		}

		$html .= '</div>' . "\n";
		$html .= '<var></var>' . "\n";
		$html .= '<script type="text/javascript">' . "\n";
		$html .= 'var bw = $("var").width();' . "\n";
		$html .= 'var gw = Math.round(bw * 1.300);' . "\n";
		$html .= 'if( bw >= 1100 ) { gw = Math.round(bw * 1.070); }' . "\n";
		$html .= 'else if( bw >= 1000 ) { gw = Math.round(bw * 1.080); }' . "\n";
		$html .= 'else if( bw >= 950 ) { gw = Math.round(bw * 1.085); }' . "\n";
		$html .= 'else if( bw >= 900 ) { gw = Math.round(bw * 1.090); }' . "\n";
		$html .= 'else if( bw >= 850 ) { gw = Math.round(bw * 1.095); }' . "\n";
		$html .= 'else if( bw >= 800 ) { gw = Math.round(bw * 1.105); }' . "\n";
		$html .= 'else if( bw >= 750 ) { gw = Math.round(bw * 1.115); }' . "\n";
		$html .= 'else if( bw >= 700 ) { gw = Math.round(bw * 1.120); }' . "\n";
		$html .= 'else if( bw >= 650 ) { gw = Math.round(bw * 1.135); }' . "\n";
		$html .= 'else if( bw >= 600 ) { gw = Math.round(bw * 1.145); }' . "\n";
		$html .= 'else if( bw >= 550 ) { gw = Math.round(bw * 1.155); }' . "\n";
		$html .= 'else if( bw >= 500 ) { gw = Math.round(bw * 1.170); }' . "\n";
		$html .= 'else if( bw >= 450 ) { gw = Math.round(bw * 1.195); }' . "\n";
		$html .= 'else if( bw >= 400 ) { gw = Math.round(bw * 1.215); }' . "\n";
		$html .= 'else if( bw >= 350 ) { gw = Math.round(bw * 1.240); }' . "\n";
		$html .= 'else if( bw >= 300 ) { gw = Math.round(bw * 1.280); }' . "\n";
		$html .= 'var chartdata_' . md5( $title ) . ' = {' . "\n";
		$html .= '"config": {' . "\n";
		$html .= '"type": "' . $type . '",' . "\n";
		if( $type === 'pie' ) {
			//$html .= '"pieRingWidth": 80,' . "\n";
			$html .= '"pieHoleRadius": 50,' . "\n";
		}
		$html .= '"colorSet": ' . "\n";
		$html .= '["rgba(255,255,255,0.0)",';
		$colors = '';
		foreach( $colorset as $rgba ) {
			$colors .= '"' . $rgba . '",';
		}
		$colors = rtrim( $colors, "," );
		$html .= $colors . '],' . "\n";
		if( $type !== 'pie' ) {
			$html .= '"axisXLen": ' . $xlen . ',' . "\n";
		}
		$html .= '"useMarker": "arc",' . "\n";
		$html .= '"useVal": "yes",' . "\n";
		$html .= '"minX": 0,' . "\n";
		$html .= '"minY": 0,' . "\n";
		$html .= '"width": gw,' . "\n";
		$html .= '"height": ' . $height . ',' . "\n";
		//$html .= '"paddingTop": 30,' . "\n";
		$html .= '"textColors": {"x":"#333","y":"#333","hanrei":"#333","unit":"#333","memo":"#333"},' . "\n";
		$html .= '"shadows": {"all":["undefined",0,0,0]},' . "\n";
		$html .= '"markerWidth": 6,' . "\n";
		if( $hanrei && $type === 'pie' ) {
			$html .= '"hanreiYOffset": -10,' . "\n";
			$html .= '"hanreiMarkerStyle": "rect",' . "\n";
		}
		else {
			//$html .= '"hanreiYOffset": 32767,' . "\n";
			$html .= '"useHanrei": "no",' . "\n";
		}
		$html .= '"bg": "#fff"' . "\n";
		$html .= '},' . "\n";
		$html .= '"data": [' . "\n";
		$html .= '[' . $xscale . '],' . "\n";
		$zerocount = count( explode( ',', $xscale ) ) - 1;
		$html .= '["",';
		$zero = '';
		for( $i = 0; $i < $zerocount; $i++ ) {
			$zero .= '0,';
		}
		$zero = rtrim( $zero, ',' );
		$html .= $zero . '],' . "\n";
		$datas = '';
		foreach( $xdata as $data ) {
			$datas .= '[' . $data . '],' . "\n";
		}
		$datas = rtrim( $datas, "\n" );
		$html .= rtrim( $datas, ',' ) . "\n";
		$html .= ']' . "\n";
		$html .= '};' . "\n";
		$html .= 'ccchart.init("' . md5( $title ) . '", chartdata_' . md5( $title ) . ')' . "\n";
		$html .= '</script>' . "\n";
		$html .= '<p class="powered">graphed by the <a href="' . $this->getJumpUrl('http://ccchart.com/') . '" target="_blank">ccchart</a></p>' . "\n";

		return $html;
	}

	private function _drawPiecahrt( $title, $xdata, $half='full', $w1=9999, $height=300 ) {
		$html = '';
		if( count( $xdata ) > 0 ) {
			$html .= '<script type="text/javascript">' . "\n";
			$html .= 'jQuery(document).ready(' . "\n";
			$html .= 'function() {' . "\n";
			$html .= 'var cg = new html5jp.graph.circle("' . md5( $title ) . '");' . "\n";
			$html .= 'if( ! cg ) { return; }' . "\n";
			$html .= 'var items = [' . "\n";
			$datas = '';
			foreach( $xdata as $data ) {
				$datas .= '[' . $data . '],' . "\n";
			}
			$datas = rtrim( $datas, "\n" );
			$html .= rtrim( $datas, "," ) . "\n";
			$html .= '];' . "\n";
			$html .= 'cg.draw(items);' . "\n";
			$html .= '});' . "\n";
			$html .= '</script>' . "\n";
		}
		$html .= '<div class="graph-title">' . "\n";
		$html .= $title . "\n";
		$html .= '</div>' . "\n";
		$html .= '<div class="graph">' . "\n";
		$html .= '<div class="graph-piechart-' . $half . '">' . "\n";
		$html .= '<div><canvas id="' . md5( $title ) . '" width="' . $w1 . '" height="' . $height . '"></canvas></div>' . "\n";
		$html .= '</div>' . "\n";
		$html .= '</div>' . "\n";
		$html .= '<p class="powered">graphed by the <a href="' . $this->getJumpUrl( 'http://www.html5.jp/library/graph_circle.html' ) . '" target="_blank">JavaScript Library - HTML5.JP</a></p>' . "\n";
		return $html;
	}

	private function _getBeforeTimeData( $method, $before, $group=null ) {
		$rcon = new ResearchController( $this->request, $this->session, $this->message, $this->result, $this->controller, $this->action );
		$conditions = $rcon->_initConditions( Config::NORMAL_ACCESS );
		if( $group === 'hh' ) {
			$findOptions = array( 'condition' => $conditions, 'group' => 'dd,hh,uid', 'subgroup' => 'hh' );
		}
		else {
			$findOptions = array( 'condition' => $conditions, 'group' => 'dd,uid', 'subgroup' => 'dd' );
		}

		$from_string = $this->session->get('yyyyFrom') . '-' . $this->session->get('mmFrom') . '-' . $this->session->get('ddFrom');
		$to_string = $this->session->get('yyyyTo') . '-' . $this->session->get('mmTo') . '-' . $this->session->get('ddTo');

		$from_timestamp = $to_timestamp = '';
		if( $before === 'day' ) {
			// 前日
			$from_timestamp = strtotime( $from_string . '-1 day' );
			$to_timestamp = strtotime( $to_string . '-1 day' );
		}
		elseif( $before === 'month' ) {
			// 前月
			$from_string = $this->session->get('yyyyFrom') . '-' . $this->session->get('mmFrom') . '-01';
			$from_timestamp = strtotime( $from_string . '-1 month' );
			$to_timestamp = strtotime( $from_string = $this->session->get('yyyyFrom') . '-' . $this->session->get('mmFrom') . '-' . Calendar::getLastDay( date( 'Y', $from_timestamp ), date( 'm', $from_timestamp ) ) . '-1 month' );
		}
		else {
			// 前月の期間
			$from_timestamp = strtotime( $from_string . '-1 month' );
			$to_timestamp = strtotime( $to_string . '-1 month' );

			$mmFrom = date( 'm', $from_timestamp );
			while( $mmFrom < date( 'm', $to_timestamp ) ) {
				$to_timestamp -= 86400;
			}

			if( date( 'd', strtotime( $to_string ) ) === Calendar::getLastDay( date( 'Y', strtotime( $to_string ) ), date( 'm', strtotime( $to_string ) ) ) ) {
				$to_timestamp = strtotime( date( 'Y', $to_timestamp ) . '-' . date( 'm', $to_timestamp ) . '-' . Calendar::getLastDay( date( 'Y', $to_timestamp ), date( 'm', $to_timestamp ) ) );
			}
		}

		$yyyyMmOptions = array(
			'yyyyFrom' => date( 'Y', $from_timestamp ),
			'mmFrom' => date( 'm', $from_timestamp ),
			'ddFrom' => date( 'd', $from_timestamp ),
			'yyyyTo' => date( 'Y', $to_timestamp ),
			'mmTo' => date( 'm', $to_timestamp ),
			'ddTo' => date( 'd', $to_timestamp )
		);

		$rcon->_doResearch( $findOptions, null, $this->action, null, $yyyyMmOptions );
	}

	private function _getTermDay() {
		$yyyyFrom = $this->session->get('yyyyFrom');
		$mmFrom = $this->session->get('mmFrom');
		$ddFrom = $this->session->get('ddFrom');
		$yyyyTo = $this->session->get('yyyyTo');
		$mmTo = $this->session->get('mmTo');
		$ddTo = $this->session->get('ddTo');

		if( $yyyyFrom.$mmFrom.$ddFrom != $yyyyTo.$mmTo.$ddTo ) {
			return Calendar::getTermDay( $yyyyFrom, $mmFrom, $ddFrom, $yyyyTo, $mmTo, $ddTo );
		}
		else {
			return Calendar::getTermDay( $yyyyFrom, $mmFrom, 1, $yyyyTo, $mmTo, $ddTo );
		}
	}

	private function _pageCut( $summary ) {
		$ret = array();
		$i = 0;
		$pageCount = 1;
		$siteData = $this->session->get('siteData');
		$page = $this->request->get('page') !== null ? $this->request->get('page') : 1;

		foreach( $summary as $key => $value ) {
			if( $pageCount >= $siteData['dispview'] * ( $page - 1 ) + 1 && $pageCount <= $siteData['dispview'] * $page ) {
				$ret[$key] = $value;
				if( $i >= $siteData['dispview'] ) break;
				++$i;
			}
			++$pageCount;
		}
		return $ret;
	}
}
?>
