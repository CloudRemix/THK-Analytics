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
class Log extends ThkModel {
	private $_max_memory = 0;
	private $_memLimitFlag = 0;
	private $_mydomain = '';

	private $_options = array();

	private $_uniqueCount = 0;
	private $_totalCount = 0;
	private $_allCount = 0;
	private $_clickLinkCount = 0;
	private $_clickBtnCount = 0;
	private $_clickAdsenseCount = 0;

	private $_summaryData = array();
	private $_uniqueData = array();
	private $_totalData = array();
	private $_textData = '';

	private $_yymmdd = array();
	private $_remoteHost = array();
	private $_domain = array();
	private $_pref = array();
	private $_country = array();
	private $_os = array();
	private $_os_ver = array();
	private $_browser = array();
	private $_brow_ver = array();
	private $_clickLink = array();
	private $_clickBtn = array();
	private $_clickAdsense = array();

	private $_compare = null;

	private $_forExtractData1 = array();
	private $_forExtractData2 = array();
	private $_workData = array();
	private $_backupData = array();

	private $_ddCheckData = array();

	public function __construct( $noCreate=false, $yyyymm=null ) {
		if( $yyyymm === null ) {
			$now = $_SERVER['REQUEST_TIME'];
			$yyyymm = date( 'Y', $now ). date( 'm', $now );
		}
		parent::__construct( strtolower(__CLASS__) . '_' . $yyyymm, $noCreate, strtolower(__CLASS__) );
	}

	public function __destruct() {
		parent::__destruct();
	}

	public function setKeys() {
		$this->setKey('yyyy');
		$this->setKey('mm');
		$this->setKey('newflg');
	}

	public function findSummary( $options, $method, $yyyyMmOptions, $hhmissOptions, $compare=null, $action=null ) {
		$this->_set_sql_mode();
		$this->_max_memory = SystemUtil::getUtilizableMemory();
		$results = array();
		$this->_options = $options;
		$this->_compare = $compare;
		$terms = $this->_getTerms(
			$yyyyMmOptions['yyyyFrom'],
			$yyyyMmOptions['mmFrom'],
			$yyyyMmOptions['ddFrom'],
			$yyyyMmOptions['yyyyTo'],
			$yyyyMmOptions['mmTo'],
			$yyyyMmOptions['ddTo']
		);
		$hhFrom = ( isset( $hhmissOptions['hhFrom'] ) ) ? $hhmissOptions['hhFrom'] : '00';
		$miFrom = ( isset( $hhmissOptions['miFrom'] ) ) ? $hhmissOptions['miFrom'] : '00';
		$ssFrom = ( isset( $hhmissOptions['ssFrom'] ) ) ? $hhmissOptions['ssFrom'] : '00';
		$hhTo = ( isset( $hhmissOptions['hhTo'] ) ) ? $hhmissOptions['hhTo'] : '23';
		$miTo = ( isset( $hhmissOptions['miTo'] ) ) ? $hhmissOptions['miTo'] : '59';
		$ssTo = ( isset( $hhmissOptions['ssTo'] ) ) ? $hhmissOptions['ssTo'] : '59';

		if( $method === 'visit_actual' || $method === 'time_detail_actual' ) $this->_mydomain = QueryConfig::getLikelyMydomain();

		foreach( $terms as $term ) {
			$this->setTable( strtolower(__CLASS__) . '_' . $term['yyyy'] . $this->_getFormatMmDd( $term['mm'] ) );
			if( $this->showTablesCount() === 0 ) continue;
			try {
				$options = $this->_options;
				$conditions = array();
				if( $method === 'online' ) {
					$conditions[] = 'CONCAT(dd,hh,mi,ss) BETWEEN ? AND ?' . $options['condition'][0];
					$conditions[] = $term['ddFrom'] . $hhFrom . $miFrom . $ssFrom;
					$conditions[] = $term['ddTo'] . $hhTo . $miTo . $ssTo;
				}
				else {
					$conditions[] = 'dd BETWEEN ? AND ?' . $options['condition'][0];
					$conditions[] = $term['ddFrom'];
					$conditions[] = $term['ddTo'];
				}
				foreach( $options['condition'] as $key => $value ) {
					if( $key != 0 ) $conditions[] = $value;
				}
				$options['condition'] = $conditions;

				$make_query = QueryConfig::makeQuery();
				$create_tmp_query = QueryConfig::createTmpTableQuery();

				if( isset( QueryConfig::$subquery[$method] ) ) {
					$query = $this->createSubquery( QueryConfig::$subquery[$method], $options );
					unset( $options['group'] );
					unset( $options['condition'] );
					$rs = $this->findSubquery( QueryConfig::$query[$method], $query, $options );
				}
				elseif( isset( $make_query[$method] ) ) {
					$rs = $this->findQuery( $make_query[$method], $options );
				}
				elseif( isset( $create_tmp_query[$method] ) ) {
					$opt1 = $options;
					unset( $opt1['group'] );

					$this->createTmpTable( $create_tmp_query[$method], $opt1 );

					$opt2 = $options;
					unset( $opt2['condition'] );

					$rs = $this->findTmpTable( QueryConfig::$findTmpTableQuery[$method], $opt2 );
				}
				elseif( isset( QueryConfig::$query[$method] ) ) {
					$rs = $this->findQuery( QueryConfig::$query[$method], $options );
				}
				else {
					$rs = $this->findQuery( "*", $options );
				}

				while( $row = $this->fetchRow( $rs ) ) {
					$this->clearData();
					$data = $this->getData();
					foreach( $data as $column => $value ) {
						if( $column === 'keyword' && isset( $row[$column] ) ) $row[$column] = trim( str_replace( '　', ' ', $row[$column] ) );
						if( isset( $row[$column] ) ) $this->setValue( $column, $row[$column] );
					}
					$this->setValue( 'yyyy', $term['yyyy'] );
					$this->setValue( 'mm', $term['mm'] );
					$function = '_' . $method;
					if( method_exists( $this, $function ) ) {
						$this->$function( $action );
					}
					if( memory_get_usage( true ) > $this->_max_memory ) {
						$this->_memLimitFlag = 1;
						break;
					}
				}
				$this->freeResult( $rs );
				if( $method === 'ddCheck' ) {
					$results['ddCheckData'] = $this->_ddCheckData;
					return $results;
				}
			}
			catch( Exception $ex ) {
				if( $ex->getCode() == ThkConfig::CONTINUE_ERR_CODE || $ex->getCode() == ThkModel::TABLE_NOTFOUND_ERR_CODE ) {
					continue;
				}
				else {
					throw $ex;
				}
			}
		}

		$function = '_' . $method . '_after';
		if( method_exists( $this, $function ) ) {
			$this->$function();
		}

		$results['uniqueCount'] = $this->_uniqueCount;
		$results['totalCount'] = $this->_totalCount;
		$results['allCount'] = $this->_allCount;
		$results['clickLinkCount'] = $this->_clickLinkCount;
		$results['clickBtnCount'] = $this->_clickBtnCount;
		$results['clickAdsenseCount'] = $this->_clickAdsenseCount;

		$results['memLimitFlag'] = $this->_memLimitFlag;

		$results['forExtractData1'] = $this->_forExtractData1;
		$results['forExtractData2'] = $this->_forExtractData2;

		$results['summaryData'] = $this->_summaryData;
		$results['uniqueData'] = $this->_uniqueData;
		$results['totalData'] = $this->_totalData;
		$results['textData'] = $this->_textData;

		$results['yymmdd'] = $this->_yymmdd;
		$results['remoteHost'] = $this->_remoteHost;
		$results['domain'] = $this->_domain;
		$results['pref'] = $this->_pref;
		$results['country'] = $this->_country;
		$results['os'] = $this->_os;
		$results['os_ver'] = $this->_os_ver;
		$results['browser'] = $this->_browser;
		$results['brow_ver'] = $this->_brow_ver;
		$results['clickLink'] = $this->_clickLink;
		$results['clickBtn'] = $this->_clickBtn;
		$results['clickAdsense'] = $this->_clickAdsense;

		return $results;
	}

	private function _totalAll( $action=null ) {
		$summary =& $this->_summaryData[$this->getValue('uid') . $this->getValue('dd')];
		if( !isset( $summary ) ) {
			++$this->_uniqueCount;
		}
		++$summary;
		++$this->_totalCount;
	}

	private function _totalAllGroupBy( $action=null ) {
		$summary =& $this->_summaryData[$this->getValue('uid') . $this->getValue('dd')];
		$uidkey =& $this->_workData[$this->getValue('uid')];
		$cnt = $this->getValue('id');
		if( !isset( $summary ) ) {
			++$this->_uniqueCount;
		}
		$summary += $cnt;
		$this->_totalCount += $cnt;
	}

	private function _uniqueAll( $action=null ) {
		$uid = $this->getValue('uid') . $this->getValue('dd');
		$summary =& $this->_summaryData[$uid];
		switch( $action ) {
			case 'referer_user':
				$uidkey =& $this->_workData[$uid];
				if( $this->getValue('http_referer') === $this->_compare ) {
					if( !isset( $uidkey ) ) {
						$uidkey = true;
						if( !isset( $summary ) ) {
							++$this->_uniqueCount;
						}
						++$summary;
					}
					++$this->_totalCount;
				}
				break;
			case 'pagein_user':
			case 'pageout_user':
				$uidkey =& $this->_workData[$uid];
				if( !isset( $uidkey ) ) {
					$uidkey = true;
					if( $this->getValue('url') === $this->_compare ) {
						++$summary;
						++$this->_uniqueCount;
						++$this->_totalCount;
					}
				}
				break;
			case 'bounce_user':
				$url = $this->getValue('url');
				$uidkey =& $this->_workData[$uid];
				$bounce =& $this->_backupData[$uid];
				if( !isset( $bounce ) ) {
					$bounce = true;
					if( !isset( $summary ) ) {
						if( $url === $this->_compare ) {
							++$summary;
							++$this->_uniqueCount;
							++$this->_totalCount;
						}
					}
				}
				else {
					if( !isset( $uidkey ) ) {
						$uidkey = true;
						if( isset( $summary ) ) {
							--$summary;
							--$this->_uniqueCount;
							--$this->_totalCount;
						}
					}
				}
				break;
			case 'rate_user':
				++$summary;
				break;
			case 'ip_user':
			case 'remotehost_user':
				if( $this->getValue('logtype') !== Config::NORMAL_ACCESS ) {
					break;
				}
			default:
				$uidkey =& $this->_workData[$uid];
				if( !isset( $uidkey ) ) {
					$uidkey = true;
					++$this->_uniqueCount;
				}
				++$summary;
				++$this->_totalCount;
		}
	}
	private function _uniqueAllRef( $action=null ) {
		$this->_uniqueAll( $action );
	}

	private function _totalAccess( $action=null ) {
		$k = null;
		if( $action !== null ) {
			if( isset( QueryConfig::$ActionToColumn[$action] ) ) {
				$k = QueryConfig::$ActionToColumn[$action];
			}
			else {
				$k = $action;
			}
		}
		$v = ( $k !== null ) ? $this->getValue( $k ) : '';

		$hashky = null;
		$uidkey =& $this->_summaryData[$this->getValue('uid') . $this->getValue('dd')];
		switch( $action ) {
			case 'engine':
			case 'engine_key':
			case 'key':
			case 'key_engine':
				if( $action === 'engine_key' ) {
					$hashky = $this->getValue('engine');
				}
				elseif( $action === 'key_engine' ) {
					$hashky = $this->getValue('keyword');
				}

				$cnt = $this->getValue('id');

				if( isset( $uidkey ) ) {
					$uidkey[0] += $cnt;
				}
				else {
					$uidkey[] = $cnt;
				}
				if( $v !== '' ) $uidkey[][$hashky] = $v;
				break;
			case 'referer':
				$cnt = $this->getValue('id');
				if( isset( $uidkey ) ) {
					$uidkey[0] += $cnt;
				}
				else {
					$uidkey[] = $cnt;
				}
				if( $v !== '' && $v !== null && !in_array( $v, $uidkey ) ) {
					$uidkey[] = $v;
				}
				break;
			default:
				$cnt = $this->getValue('id');
				if( isset( $uidkey ) ) {
					$uidkey[0] += $cnt;
				}
				else {
					$uidkey[] = $cnt;
				}
				if( $v !== '' ) {
					if( !in_array( $v, $uidkey ) ) {
						if( $action === 'host' && $v !== Config::DIRECT_ACCESS ) {
							$uidkey[] = $v;
						}
						else {
							$uidkey[] = $v;
						}
					}
				}
				break;
		}
	}
	private function _referer_total( $action )	{ $this->_totalAccess( $action ); }
	private function _host_total( $action )		{ $this->_totalAccess( $action ); }
	private function _engine_total( $action )	{ $this->_totalAccess( $action ); }
	private function _engine_key_total( $action )	{ $this->_totalAccess( $action ); }
	private function _key_total( $action )		{ $this->_totalAccess( $action ); }
	private function _key_engine_total( $action )	{ $this->_totalAccess( $action ); }

	private function _ddCheck() {
		$this->_ddCheckData[intval( $this->getValue('dd') )] = true;
	}

	private function _online() {
		++$this->_uniqueCount;
	}

	private function _btnrank() {
		$this->_extractUqTotalData( $this->getValue('url') );
	}

	private function _btnrank_user() {
		$this->_extractUidData();
	}

	private function _clickrank() {
		$this->_extractUqTotalData( $this->getValue('url') );
	}

	private function _clickrank_user() {
		$this->_extractUidData();
	}

	private function _adip() {
		$t = $this->getValue('uid');
		$u = $this->getValue('id');
		$key = $this->getValue('remote_addr');

		$cntkey =& $this->_workData[$key];
		$unique =& $this->_summaryData[$key];
		$total =& $this->_summaryData[$key];
		if( !isset( $cntkey ) ) {
			$cntkey = true;
			++$this->_allCount;
		}
		$total += $t;
		$this->_totalCount += $t;
		$this->_uniqueCount += $u;
	}

	private function _adip_user() {
		$this->_extractUidData();
	}

	private function _adrank() {
		$this->_extractUqTotalData( $this->getValue('title') );
	}

	private function _adrank_user() {
		$this->_extractUidData();
	}

	private function _adprank() {
		$this->_extractUqTotalData( $this->getValue('referer_title') );
	}

	private function _adprank_user() {
		$this->_extractUidData();
	}

	private function _host() {
		$this->_extractUqData( $this->getValue('referer_host') );
	}

	private function _host_user() {
		$this->_extractUidData();
	}

	private function _referer() {
		$this->_extractUqData( $this->getValue('http_referer') );
	}

	private function _referer_user() {
		$uid = $this->getValue('uid');
		$uiskey =& $this->_workData[$uid];
		if( $this->getValue('http_referer') === $this->_compare ) {
			if( !isset( $uiskey ) ) {
				$uiskey = true;
				$this->_extractUidData();
			}
		}
	}

	private function _crawler() {
		$this->_extractUqTotalData( $this->getValue('crawler') );
	}

	private function _crawler_user() {
		$this->_extractUidData();
	}

	private function _os() {
		$this->_extractUqTotalData( $this->getValue('os') );
	}

	private function _os_ver() {
		$this->_extractUqTotalData( $this->getValue('os_ver') );
	}

	private function _os_user() {
		$this->_extractUidData();
	}

	private function _brow() {
		$this->_extractUqTotalData( $this->getValue('browser') );
	}

	private function _brow_ver() {
		$this->_extractUqTotalData( $this->getValue('brow_ver') );
	}

	private function _brow_user() {
		$this->_extractUidData();
	}

	private function _pagein() {
		$urlkey =& $this->_summaryData[$this->getValue('url')];
		$srtkey =& $this->_forExtractData1[$this->getValue('uid') . $this->getValue('dd')];
		if( !isset( $srtkey ) ) {
			$srtkey = true;
			if( !isset( $urlkey ) ) {
				++$this->_allCount;
			}
			++$urlkey;
			++$this->_uniqueCount;
		}
	}

	private function _pagein_user() {
		$uid = $this->getValue('uid') . $this->getValue('dd');
		$uiskey =& $this->_workData[$uid];
		if( !isset( $uiskey ) ) {
			$uiskey = true;
			if( $this->getValue('url') === $this->_compare ) {
				$this->_extractUidData();
			}
		}
	}

	private function _pageout() {
		$this->_pagein();
	}

	private function _pageout_user() {
		$this->_pagein_user();
	}

	private function _ip_to_domain() {
		$this->_domain[$this->getValue('remote_addr')] = $this->getValue('domain');
	}

	private function _host_to_domain() {
		$this->_domain[$this->getValue('remote_host')] = $this->getValue('domain');
	}

	private function _ip() {
		$this->_extractUqTotalData( $this->getValue('remote_addr') );
	}

	private function _ip_user() {
		$this->_extractUidData();
		$uid = $this->getValue('uid');
		$log = $this->getValue('logtype');
		$rmt = $this->getValue('remote_host');

		$domain =& $this->_domain[$uid];
		$access =& $this->_summaryData[$uid];
		$clicks =& $this->_clickLink[$uid];
		$button =& $this->_clickBtn[$uid];
		$adsence =& $this->_clickAdsense[$uid];

		if( !isset( $this->_remoteHost[$uid.$rmt] ) ) $this->_remoteHost[$uid.$rmt] = $rmt;
		if( !isset( $clicks ) ) $clicks = 0;
		if( !isset( $button ) ) $button = 0;
		if( !isset( $adsence ) ) $adsence = 0;
		switch( $log ) {
			case Config::CLICK_LINK:
				++$clicks;
				break;
			case Config::CLICK_BTN:
				++$button;
				break;
			case Config::CLICK_ADSENSE:
				++$adsence;
				break;
		}
		if( $log != Config::NORMAL_ACCESS ) {
			--$access;
		}
	}

	private function _remotehost() {
		$this->_extractUqTotalData( $this->getValue('remote_host') );
	}

	private function _remotehost_user() {
		$this->_ip_user();
	}

	private function _domain() {
		$this->_extractUqTotalData( $this->getValue('domain') );
	}

	private function _domain_user() {
		$this->_extractUidData();
	}

	private function _jpdomain() {
		$this->_extractUqTotalData( $this->getValue('jpdomain') );
	}

	private function _jpdomain_user() {
		$this->_extractUidData();
	}

	private function _pref() {
		$this->_extractUqTotalData( $this->getValue('pref') );
	}

	private function _pref_user() {
		$this->_extractUidData();
	}

	private function _country() {
		$this->_extractUqTotalData( $this->getValue('country') );
	}

	private function _country_user() {
		$this->_extractUidData();
	}

	private function _rank() {
		$this->_extractUqTotalData( $this->getValue('url') );
	}

	private function _rank_user() {
		$this->_extractUidData();
	}

	private function _bounce() {
		$uid = $this->getValue('uid') . $this->getValue('dd');
		$url = $this->getValue('url');
		$uidkey =& $this->_workData[$uid];
		$backup =& $this->_backupData[$uid];
		$bounce =& $this->_summaryData[$url]['bounce'];
		$unique =& $this->_summaryData[$url]['unique'];
		$srtkey1 =& $this->_forExtractData1[$url];
		$srtkey2 =& $this->_forExtractData2[$url];
		if( !isset( $backup ) ) {
			$backup = $url;
			if( !isset( $bounce ) ) {
				++$this->_allCount;
			}
			++$bounce;
			++$unique;
			++$srtkey1;
			++$srtkey2;
			++$this->_uniqueCount;
			++$this->_totalCount;
		}
		else {
			if( !isset( $uidkey ) ) {
				$uidkey = true;
				--$this->_summaryData[$backup]['bounce'];
				--$this->_forExtractData1[$backup];
				--$this->_uniqueCount;
			}
		}
	}
	private function _bounce_user() {
		$uid = $this->getValue('uid') . $this->getValue('dd');
		$url = $this->getValue('url');
		$uidkey =& $this->_workData[$uid];
		$backup =& $this->_backupData[$uid];
		if( !isset( $backup ) ) {
			$backup = true;
			if( !isset( $this->_summaryData[$uid] ) ) {
				if( $url === $this->_compare ) {
					$this->_extractUidData();
				}
			}
		}
		else {
			if( !isset( $uidkey ) ) {
				$uidkey = true;
				$this->_unsetextractUidData();
			}
		}
	}

	private function _rate() {
		$uidkey =& $this->_workData[$this->getValue('uid') . $this->getValue('dd')];
		$uidkey += $this->getValue('id');
	}

	private function _rate_after() {
		foreach( $this->_workData as $key => $value ) {
			$this->_extractData( $value );
		}
	}

	private function _rate_user() {
		$uidkey =& $this->_workData[$this->getValue('uid')];
		if( !isset( $uidkey ) ) {
			$this->_extractUidData();
		}
		++$uidkey;
	}

	private function _rate_user_after() {
		$this->_forExtractData1 = array();
		$this->_summaryData = array();
		$this->_uniqueCount = 0;
		$this->_totalCount = 0;

		foreach( $this->_workData as $key => $value ) {
			if( $value == $this->_compare ) {
				$this->_extractData( $key );
			}
		}
	}

	private function _screencol() {
		$this->_extractUqTotalData( $this->getValue('screencol') );
	}

	private function _screencol_user() {
		$this->_extractUidData();
	}

	private function _screenwh() {
		$this->_extractUqTotalData( $this->getValue('screenwh') );
	}

	private function _screenwh_user() {
		$this->_extractUidData();
	}

	private function _jsck() {
		$this->_extractUqTotalData( $this->getValue('jsck') );
	}

	private function _jsck_user() {
		$this->_extractUidData();
	}

	private function _digest1() {
		$this->_extractTimeData( (int)$this->getValue('hh') );
	}

	private function _digest2() {
		$this->_referer();
	}

	private function _visit() {
		$uid = $this->getValue('uid');
		$yy = $this->getValue('yyyy');
		$mm = $this->getValue('mm');
		$dd = $this->getValue('dd');
		$hh = $this->getValue('hh');

		$key = $uid . $yy . $mm . $dd;
		$keyext =& $this->_workData[$key];

		if( !isset( $keyext ) && $this->getValue('logtype') === Config::NORMAL_ACCESS ) {
			$keyext = true;
			++$this->_uniqueCount;
			$this->_summaryData[$yy . $mm . $dd . $hh . $this->getValue('mi') . $this->getValue('ss') . microtime() . rand( 0, 100 )] = $uid . $dd;
		}
		$this->_extractDetailCount();
	}

	private function _visit_actual() {
		$yy = $this->getValue('yyyy');
		$mm = $this->getValue('mm');
		$dd = $this->getValue('dd');
		$hh = $this->getValue('hh');
		$mi = $this->getValue('mi');
		$ss = $this->getValue('ss');

		$this->_extractVisitData(
			$this->getValue('uid'),
			$key = $yy . $mm . $dd . $hh . $mi . $ss . microtime() . rand( 0, 100 ),
			array(
				'yyyy'	=> $yy,
				'mm'	=> $mm,
				'dd'	=> $dd,
				'hh'	=> $hh,
				'mi'	=> $mi,
				'ss'	=> $ss,
				'logtype'	=> $this->getValue('logtype'),
				'url'		=> $this->getValue('url'),
				'title'		=> $this->getValue('title'),
				'title'		=> $this->getValue('title'),
				'domain'	=> $this->getValue('domain'),
				'referer_host'	=> $this->getValue('referer_host'),
				'http_referer'	=> $this->getValue('http_referer'),
				'referer_title'	=> $this->getValue('referer_title'),
				'country'	=> $this->getValue('country'),
				'pref'		=> $this->getValue('pref'),
				'os'		=> $this->getValue('os'),
				'os_ver'	=> $this->getValue('os_ver'),
				'browser'	=> $this->getValue('browser'),
				'brow_ver'	=> $this->getValue('brow_ver')
			)
		);
	}

	private function _time_detail() {
		$this->_visit();
	}

	private function _time_detail_actual() {
		$this->_visit_actual();
	}

	private function _time() {
		$this->_extractTimeData( (int)$this->getValue('hh') );
	}

	private function _term() {
		$this->_extractTimeData( (int)$this->getValue('dd') );
	}

	private function _timestack() {
		$this->_extractTimeData( (int)$this->getValue('hh') );
	}

	private function _termstack() {
		$this->_extractTimeData( (int)$this->getValue('dd') );
	}

	private function _week() {
		$this->_extractTimeData( (int)$this->getValue('weekday') );
	}

	private function _uid() {
		$uid = $this->getValue('uid') . $this->getValue('dd');
		$log = $this->getValue('logtype');
		$uidkey =& $this->_workData[$uid];
		$access =& $this->_summaryData[$uid];
		$clicks =& $this->_clickLink[$uid];
		if( !isset( $clicks ) ) $clicks = 0;
		if( !isset( $access ) ) $access = 0;
		switch( $log ) {
			case Config::CLICK_LINK:
				++$clicks;
				++$this->_clickLinkCount;
				break;
			case Config::CLICK_BTN:
				++$this->_clickBtnCount;
				break;
			case Config::CLICK_ADSENSE:
				++$this->_clickAdsenseCount;
				break;
			default:
				if( !isset( $uidkey ) ) {
					$uidkey = true;
					++$this->_uniqueCount;
				}
				++$access;
				++$this->_totalCount;
				break;
		}
	}

	private function _uid_access() {
		$this->_extractUidData();
		$uid = $this->getValue('uid');
		$log = $this->getValue('logtype');
		$access =& $this->_summaryData[$uid];
		$clicks =& $this->_clickLink[$uid];
		$button =& $this->_clickBtn[$uid];
		$adsence =& $this->_clickAdsense[$uid];
		if( !isset( $clicks ) ) $clicks = 0;
		if( !isset( $button ) ) $button = 0;
		if( !isset( $adsence ) ) $adsence = 0;
		switch( $log ) {
			case Config::CLICK_LINK:
				++$clicks;
				break;
			case Config::CLICK_BTN:
				++$button;
				break;
			case Config::CLICK_ADSENSE:
				++$adsence;
				break;
		}
		if( $log !== Config::NORMAL_ACCESS ) {
			--$access;
		}
	}

	private function _uid_clicks() {
		$this->_uid_access();
	}

	private function _uid_access_after() {
		array_multisort( $this->_summaryData, SORT_DESC, $this->_clickLink, SORT_DESC, $this->_clickBtn, SORT_DESC, $this->_clickAdsense, SORT_DESC );
	}
	private function _uid_clicks_after() {
		array_multisort( $this->_clickLink, SORT_DESC, $this->_summaryData, SORT_DESC, $this->_clickBtn, SORT_DESC, $this->_clickAdsense, SORT_DESC );
	}

	private function _uid_detail() {
		$this->_summaryData[$this->getValue('yyyy') . $this->getValue('mm') . $this->getValue('dd') . $this->getValue('hh') . $this->getValue('mi') . $this->getValue('ss') . microtime() . rand( 0, 100 )] = $this->getData();
		$this->_extractDetailCount();
	}
	private function _uid_detail_after() {
		$this->_allCount = $this->_totalCount;
	}

	private function _word() {
		$keyword = ThkUtil::convertKana( ThkUtil::strToLower( $this->getValue('keyword') ), 'KVas' );
		$words = preg_split( '/[\s]/', $keyword );
		foreach( $words as $word ) {
			if( trim( $word ) !== '' ) {
				if( trim( $this->_compare ) !== '' ) {
					if( strpos( ThkUtil::strToLower( $word ), ThkUtil::strToLower( $this->_compare ) ) === false ) continue;
				}
				if( is_numeric( $word ) ) $word .= ' ';
				$this->_extractData( $this->getValue('uid'), $word );
			}
		}
	}

	private function _engine() {
		$this->_extractUqData( $this->getValue('engine') );
	}

	private function _engine_key() {
		$key = $this->getValue('keyword');
		if( is_numeric( $key ) ) $key .= ' ';
		$this->_extractUqData( $key );
	}

	private function _engine_user() {
		$this->_extractUidData();
	}

	private function _key() {
		$key = $this->getValue('keyword');
		if( is_numeric( $key ) ) $key .= ' ';
		$this->_extractUqData( $key );
	}

	private function _key_engine() {
		$this->_extractUqData( $this->getValue('engine') );
	}

	private function _key_user() {
		$this->_extractUidData();
	}

	private function _log() {
		$this->_textData .= '"' . $this->getValue('yyyy') . '/' . $this->getValue('mm') . '/' . $this->getValue('dd') . '",';
		$this->_textData .= '"' . $this->getValue('hh') . ':' . $this->getValue('mi') . ':' . $this->getValue('ss') . '",';
		$this->_textData .= '"' . Calendar::getWeekday( $this->getValue('weekday') ) . '",';
		$this->_textData .= '"' . $this->getValue('logtype') . '",';
		$this->_textData .= '"' . $this->getValue('uid') . '",';
		$this->_textData .= '"' . $this->getValue('remote_addr') . '",';
		$this->_textData .= '"' . $this->getValue('remote_host') . '",';
		$this->_textData .= '"' . $this->getValue('domain') . '",';
		$this->_textData .= '"' . $this->getValue('country') . '",';
		$this->_textData .= '"' . $this->getValue('pref') . '",';
		$this->_textData .= '"' . $this->getValue('title') . '",';
		$this->_textData .= '"' . $this->getValue('url') . '",';
		$this->_textData .= '"' . $this->getValue('screenwh') . '",';
		$this->_textData .= '"' . $this->getValue('screencol') . '",';
		$this->_textData .= '"' . $this->getValue('jsck') . '",';
		$this->_textData .= '"' . $this->getValue('os') . '",';
		$this->_textData .= '"' . $this->getValue('os_ver') . '",';
		$this->_textData .= '"' . $this->getValue('browser') . '",';
		$this->_textData .= '"' . $this->getValue('brow_ver') . '",';
		$this->_textData .= '"' . $this->getValue('crawler') . '",';
		$this->_textData .= '"' . $this->getValue('keyword') . '",';
		$this->_textData .= '"' . $this->getValue('engine') . '",';
		$this->_textData .= '"' . $this->getValue('referer_title') . '",';
		$this->_textData .= '"' . $this->getValue('referer_host') . '",';
		$this->_textData .= '"' . $this->getValue('http_user_agent') . '",';
		$this->_textData .= '"' . $this->getValue('http_referer') . '"';
		$this->_textData .= "\r\n";
	}

	private function _getTerms( $yyyyFrom, $mmFrom, $ddFrom, $yyyyTo, $mmTo, $ddTo ) {
		$terms = array();
		$params = array();
		$count = 1;

		$yyyy = $yyyyFrom;
		$mm = $mmFrom;
		while( $yyyy . $mm <= $yyyyTo . $mmTo ) {
			$params['yyyy'] = $yyyy;
			$params['mm'] = $mm;
			if( $count === 1 ) {
				$params['ddFrom'] = $ddFrom;
				if( $yyyy . $mm === $yyyyTo . $mmTo ) {
					$params['ddTo'] = $ddTo;
				}
				else {
					$params['ddTo'] = Calendar::getLastDay( $yyyy, $mm );
				}
			}
			if( $count > 1 && $yyyy . $mm < $yyyyTo . $mmTo ) {
				$params['ddFrom'] = '01';
				$params['ddTo'] = Calendar::getLastDay( $yyyy, $mm );
			}
			if( $count > 1 && $yyyy . $mm === $yyyyTo . $mmTo ) {
				$params['ddFrom'] = '01';
				$params['ddTo'] = $ddTo;
			}
			$mm = $this->_getZeroSuppress( $mm );
			$terms[] = $params;
			if( $mm == 12 ) {
				$yyyy = $yyyy + 1;
				$mm = 1;
			}
			else {
				$yyyy = $yyyy;
				$mm = $mm + 1;
			}
			$mm = $this->_getZeroPadding( $mm, 1 );
			$count++;
		}
		return array_reverse( $terms );
	}

	private function _getZeroPadding( $value, $cnt ) {
		return str_pad( $value, $cnt+1, '0', STR_PAD_LEFT );
	}

	private function _getZeroSuppress( $value ) {
		return is_numeric( $value ) ? (int)$value : $value;
	}

	private function _getFormatMmDd( $value ) {
		return strlen( $value ) == 1 ? '0' . $value : $value;
	}

	private function _extractVisitData( $uid, $key, $value ) {
		$uidkey =& $this->_summaryData[$uid];
		$refkey =& $this->_workData[$value['http_referer']];
		if( !isset( $uidkey ) ) {
			$uidkey = $value;
			$uidkey['type'][] = $value['logtype'];
			$uidkey['action'][] = $value['url'];
			$uidkey['action_title'][] = $value['title'];
			$uidkey['referer_host'] = $value['referer_host'];
			$uidkey['http_referer'] = $value['http_referer'];
			$uidkey['referer_title'] = $value['referer_title'];
			$refkey = true;
		}
		else {
			$uidkey['yyyy'] = $value['yyyy'];
			$uidkey['mm'] = $value['mm'];
			$uidkey['dd'] = $value['dd'];
			$uidkey['hh'] = $value['hh'];
			$uidkey['mi'] = $value['mi'];
			$uidkey['ss'] = $value['ss'];
			if( $uidkey['referer_host'] === '' ) {
				$uidkey['referer_host'] = $value['referer_host'];
				$uidkey['http_referer'] = $value['http_referer'];
				$uidkey['referer_title'] = $value['referer_title'];
			}
			elseif(
				$value['referer_host'] !== '' &&
				$value['referer_host'] !== Config::DIRECT_ACCESS &&
				$value['referer_host'] !== Config::FROM_NO_SCRIPT
			) {
				if( !$this->_checkMydomain( $value['http_referer'] ) && !isset( $refkey, $value['http_referer'] ) ) {
					$uidkey['type'][] = 'ref';
					$uidkey['action'][] = $value['http_referer'];
					$uidkey['action_title'][] = $value['referer_title'];
					$refkey = true;
				}
			}
			$uidkey['type'][] = $value['logtype'];
			$uidkey['action'][] = $value['url'];
			$uidkey['action_title'][] = $value['title'];
		}
		$this->_forExtractData1[$uid] = $key;
		$uidkey['key'] = $key;
	}

	private function _extractData( $key1, $key2 = false ) {
		if( !$key2 ) {
			$summary1 =& $this->_summaryData[$key1];
			$srtkey1 =& $this->_forExtractData1[$key1];
			if( !isset( $srtkey1 ) ) {
				$srtkey1 = $key1;
				++$this->_uniqueCount;
			}
			++$summary1;
		}
		else {
			$keyext1 =& $this->_workData[$key1];
			$srtkey2 =& $this->_forExtractData1[$key2];
			$summary2 =& $this->_summaryData[$key2];
			if( !isset( $keyext1 ) ) {
				$keyext1 = true;
				if( !isset( $srtkey2 ) ) {
					++$this->_allCount;
				}
				++$srtkey2;
				++$summary2;
				++$this->_uniqueCount;
			}
		}
		++$this->_totalCount;
	}

	private function _extractUqTotalData( $key ) {
		$t = $this->getValue('uid');
		$u = $this->getValue('id');
		$srtkey1 =& $this->_forExtractData1[$key];
		$srtkey2 =& $this->_forExtractData2[$key];
		$unique =& $this->_summaryData[$key]['unique'];
		$total =& $this->_summaryData[$key]['total'];
		if( !isset( $srtkey2 ) ) {
			++$this->_allCount;
		}
		$srtkey1 += $t;
		$srtkey2 += $u;
		$total += $t;
		$unique += $u;
		$this->_totalCount += $t;
		$this->_uniqueCount += $u;
	}

	private function _extractUqData( $key ) {
		if( $key !== null && $key != '' ) {
			$srtkey =& $this->_forExtractData1[$key];
			$unique =& $this->_summaryData[$key]['unique'];
			$total =& $this->_summaryData[$key]['total'];
			$cnt = $this->getValue('id');
			if( !isset( $srtkey ) ) {
				++$this->_allCount;
			}
			$total = 0;
			$srtkey += $cnt;
			$unique += $cnt;
			$this->_uniqueCount += $cnt;
		}
	}

	private function _extractUidData() {
		$uid = $this->getValue('uid');
		$uidkey =& $this->_summaryData[$uid];
		$srtkey =& $this->_forExtractData1[$uid];
		if( !isset( $srtkey ) ) {
			$srtkey = true;
			++$this->_uniqueCount;
		}
		++$uidkey;
		++$this->_totalCount;
		$os = $this->getValue('os');
		$os_ver = $this->getValue('os_ver');
		$browser = $this->getValue('browser');
		$brow_ver = $this->getValue('brow_ver');
		$pref = $this->getValue('pref');
		$domain = $this->getValue('domain');
		$yymmdd = $this->getValue('yyyy') . '/' . $this->getValue('mm') . '/' . $this->getValue('dd');

		if( !isset( $this->_os[$uid] ) ) $this->_os[$uid] = $os;
		if( !isset( $this->_os_ver[$uid] ) ) $this->_os_ver[$uid] = $os_ver;
		if( !isset( $this->_browser[$uid] ) ) $this->_browser[$uid] = $browser;
		if( !isset( $this->_brow_ver[$uid] ) ) $this->_brow_ver[$uid] = $brow_ver;
		if( !isset( $this->_pref[$uid] ) ) $this->_pref[$uid] = $pref;
		if( !isset( $this->_domain[$uid] ) ) $this->_domain[$uid] = $domain;
		if( !isset( $this->_yymmdd[$uid] ) ) $this->_yymmdd[$uid] = $yymmdd;
	}

	private function _extractTimeData( $tm ) {
		$t = $this->getValue('uid');
		$u = $this->getValue('id');
		$unique =& $this->_uniqueData[$tm];
		$total =& $this->_totalData[$tm];
		$total += $t;
		$unique += $u;
		$this->_totalCount += $t;
		$this->_uniqueCount += $u;
	}

	private function _extractDetailCount() {
		switch( $this->getValue('logtype') ) {
			case Config::NORMAL_ACCESS:
				++$this->_totalCount;
				break;
			case Config::CLICK_LINK:
				++$this->_clickLinkCount;
				break;
			case Config::CLICK_BTN:
				++$this->_clickBtnCount;
				break;
			case Config::CLICK_ADSENSE:
				++$this->_clickAdsenseCount;
				break;
		}
	}

	private function _unsetExtractUidData() {
		$uid = $this->getValue('uid');
		if( isset($this->_forExtractData1[$uid] ) ) {
			--$this->_summaryData[$uid];
			if( $this->_summaryData[$uid] == 0 ) {
				unset( $this->_summaryData[$uid] );
				unset( $this->_forExtractData1[$uid] );
				unset( $this->_os[$uid] );
				unset( $this->_os_ver[$uid] );
				unset( $this->_browser[$uid] );
				unset( $this->_brow_ver[$uid] );
				unset( $this->_domain[$uid] );
				unset( $this->_yymmdd[$uid] );
				--$this->_uniqueCount;
			}
			--$this->_totalCount;
		}
	}

	private function _checkMydomain( $referer ) {
		$ref = explode( '?', $referer );
		foreach( $this->_mydomain as $value ) {
			if( !empty( $ref[0] ) && !empty( $value )  ) {
				if( strpos( $ref[0], $value ) !== false ) {
					return true;
				}
			}
		}
		return false;
	}
}
?>
