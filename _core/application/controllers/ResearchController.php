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
class ResearchController extends BaseController {

	private $_yyyyFrom = null;
	private $_mmFrom = null;
	private $_ddFrom = null;
	private $_yyyyTo = null;
	private $_mmTo = null;
	private $_ddTo = null;

	private $_uniqueCount = 0;
	private $_totalCount = 0;
	private $_allCount = 0;

	private $_summaryData = array();

	public function __construct( ThkRequest $request, ThkSession $session, ThkMessage $message, ThkResult $result, $controller, $action ) {
		parent::__construct( $request, $session, $message, $result, $controller, $action );
		if( $this->session->get('yyyyFrom') !== null ) $this->_yyyyFrom = $this->session->get('yyyyFrom');
		if( $this->session->get('mmFrom') !== null ) $this->_mmFrom = $this->session->get('mmFrom');
		if( $this->session->get('ddFrom') !== null ) $this->_ddFrom = $this->session->get('ddFrom');
		if( $this->session->get('yyyyTo') !== null ) $this->_yyyyTo = $this->session->get('yyyyTo');
		if( $this->session->get('mmTo') !== null ) $this->_mmTo = $this->session->get('mmTo');
		if( $this->session->get('ddTo') !== null ) $this->_ddTo = $this->session->get('ddTo');
	}

	public function btnrank() {
		$conditions = $this->_initConditions( Config::CLICK_BTN );
		$conditions[0] .= ' AND url <> ?';
		$conditions[] = '';
		if( $this->request->get('search') !== null ) {
			$conditions[0] .= ' AND url LIKE ?';
			$conditions[] = '%' . $this->request->get('search') . '%';
		}
		$findOptions = array( 'condition' => $conditions, 'group' => 'url,dd,uid', 'subgroup' => 'url' );
		return $this->_doResearch( $findOptions );
	}

	public function btnrank_user() {
		$this->checkUrl('select');
		$init = Config::CLICK_BTN;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND url = ?';
		$conditions[] = $this->request->get('select');
		return $this->_userResearch( $conditions, $init );
	}

	public function clickrank() {
		$conditions = $this->_initConditions( Config::CLICK_LINK );
		$conditions[0] .= ' AND url <> ?';
		$conditions[] = '';
		if( $this->request->get('search') !== null ) {
			$conditions[0] .= ' AND url LIKE ?';
			$conditions[] = '%' . $this->request->get('search') . '%';
		}
		$findOptions = array( 'condition' => $conditions, 'group' => 'url,dd,uid', 'subgroup' => 'url' );
		return $this->_doResearch( $findOptions );
	}

	public function clickrank_user() {
		$this->checkUrl('select');
		$init = Config::CLICK_LINK;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND url = ?';
		$conditions[] = $this->request->get('select');
		return $this->_userResearch( $conditions, $init, null, __FUNCTION__, false );
	}

	public function adip() {
		$conditions = $this->_initConditions( Config::CLICK_ADSENSE );
		$conditions[0] .= ' AND remote_addr <> ?';
		$conditions[] = '';
		if( $this->request->get('search') !== null ) {
			$conditions[0] .= ' AND remote_addr LIKE ?';
			$conditions[] = '%' . $this->request->get('search') . '%';
		}
		$findOptions = array( 'condition' => $conditions, 'group' => 'remote_addr,dd,uid', 'subgroup' => 'remote_addr' );
		return $this->_doResearch( $findOptions );
	}

	public function adip_user() {
		$this->checkUrl('select');
		$init = Config::CLICK_ADSENSE;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND remote_addr = ?';
		$conditions[] = $this->request->get('select');
		if( $this->request->get('search') !== null ) {
			$conditions[0] .= ' AND uid LIKE ?';
			$conditions[] = '%' . $this->request->get('search') . '%';
		}
		return $this->_userResearch( $conditions, $init, null, __FUNCTION__, false );
	}

	public function adrank() {
		$conditions = $this->_initConditions(Config::CLICK_ADSENSE);
		$conditions[0] .= ' AND title <> ?';
		$conditions[] = '';
		$findOptions = array( 'condition' => $conditions, 'group' => 'title,dd,uid', 'subgroup' => 'title' );
		return $this->_doResearch( $findOptions );
	}

	public function adrank_user() {
		$this->checkUrl('select');
		$init = Config::CLICK_ADSENSE;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND title = ?';
		$conditions[] = $this->request->get('select');
		return $this->_userResearch( $conditions, $init, null, __FUNCTION__, false );
	}

	public function adprank() {
		$conditions = $this->_initConditions(Config::CLICK_ADSENSE);
		$conditions[0] .= ' AND referer_title <> ?';
		$conditions[] = '';
		if( $this->request->get('search') !== null ) {
			$conditions[0] .= ' AND referer_title LIKE ?';
			$conditions[] = '%' . $this->request->get('search') . '%';
		}
		$findOptions = array( 'condition' => $conditions, 'group' => 'referer_title,dd,uid', 'subgroup' => 'referer_title' );
		return $this->_doResearch( $findOptions );
	}

	public function adprank_user() {
		$this->checkUrl('select');
		$init = Config::CLICK_ADSENSE;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND referer_title = ?';
		$conditions[] = $this->request->get('select');
		return $this->_userResearch( $conditions, $init, null, __FUNCTION__, false );
	}

	public function host() {
		$init = Config::NORMAL_ACCESS;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND referer_host <> ?';
		$conditions[] = '';
		$conditions[0] .= ' AND referer_host <> ?';
		$conditions[] = Config::DIRECT_ACCESS;
		if( $this->request->get('search') !== null ) {
			$conditions[0] .= ' AND referer_host LIKE ?';
			$conditions[] = '%' . $this->request->get('search') . '%';
		}
		$group = 'referer_host,dd';
		$this->_firstQuery( $conditions, $init, null, $group );
		return $this->_setTotalAccess();
	}

	public function host_user() {
		$this->checkUrl('select');
		$init = Config::NORMAL_ACCESS;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND referer_host = ?';
		$conditions[] = $this->request->get('select');
		return $this->_userResearch( $conditions, $init );
	}

	public function referer() {
		$init = Config::NORMAL_ACCESS;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND http_referer <> ?';
		$conditions[] = '';
		$conditions[0] .= ' AND referer_host <> ?';
		$conditions[] = '';
		$conditions[0] .= ' AND referer_host <> ?';
		$conditions[] = Config::DIRECT_ACCESS;
		$conditions[0] .= ' AND referer_host <> ?';
		$conditions[] = Config::FROM_NO_SCRIPT;
		if( $this->request->get('search') !== null ) {
			$conditions[0] .= ' AND http_referer LIKE ?';
			$conditions[] = '%' . $this->request->get('search') . '%';
		}
		$when = '';
		$shape = 'CONCAT( SUBSTRING( http_referer,1,INSTR( http_referer,"/" ) +1 ),CASE WHEN referer_host <> "" THEN referer_host ELSE NULL END,"/" )';
		foreach( QueryConfig::$shapeReferer as $str ) {
			$when .= ' WHEN http_referer LIKE "%'. $str .'%" THEN ' . $shape;
		}
		$group = 'CASE WHEN referer_title NOT LIKE "%://%" THEN ' . $shape . $when . ' ELSE http_referer END';
		$this->_firstQuery( $conditions, $init, null, $group );
		return $this->_setTotalAccess();
	}

	public function referer_user() {
		$this->checkUrl('select');
		$init = Config::NORMAL_ACCESS;
		$compare = $this->request->get('select');
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND http_referer <> ?';
		$conditions[] = '';
		$conditions[0] .= ' AND referer_host <> ?';
		$conditions[] = '';
		$conditions[0] .= ' AND referer_host <> ?';
		$conditions[] = Config::DIRECT_ACCESS;
		$conditions[0] .= ' AND referer_host <> ?';
		$conditions[] = Config::FROM_NO_SCRIPT;
		return $this->_userResearch( $conditions, $init, null, __FUNCTION__, true, $compare );
	}

	public function crawler() {
		$init = Config::NORMAL_ACCESS;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND crawler <> ?';
		$conditions[] = '';
		if( $this->request->get('search') !== null ) {
			$conditions[0] .= ' AND crawler LIKE ?';
			$conditions[] = '%' . $this->request->get('search') . '%';
		}
		$findOptions = array( 'condition' => $conditions, 'group' => 'dd,uid', 'subgroup' => 'crawler' );
		return $this->_doResearch( $findOptions );
	}

	public function crawler_user() {
		$this->checkUrl('select');
		$init = Config::NORMAL_ACCESS;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND crawler = ?';
		$conditions[] = $this->request->get('select');
		return $this->_userResearch( $conditions, $init );
	}

	public function os() {
		$init = Config::NORMAL_ACCESS;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND os <> ?';
		$conditions[] = '';
		if( $this->request->get('search') !== null ) {
			$conditions[0] .= ' AND os LIKE ?';
			$conditions[] = '%' . $this->request->get('search') . '%';
		}
		$findOptions = array( 'condition' => $conditions, 'group' => 'dd,uid', 'subgroup' => 'os' );
		return $this->_doResearch( $findOptions );
	}

	public function os_ver() {
		$this->checkUrl('select');
		$init = Config::NORMAL_ACCESS;
		$conditions = $this->_initConditions( $init );
		if( $this->request->get('search') !== null ) {
			$conditions[0] .= ' AND os_ver LIKE ?';
			$conditions[] = '%' . $this->request->get('search') . '%';
		}
		$conditions[0] .= ' AND os = ?';
		$conditions[] = $this->request->get('select');
		$findOptions = array( 'condition' => $conditions, 'group' => 'dd,uid', 'subgroup' => 'os_ver' );
		return $this->_doResearch( $findOptions );
	}

	public function os_user() {
		$this->checkUrl( array('select', 'os') );
		$init = Config::NORMAL_ACCESS;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND os_ver = ?';
		$conditions[] = $this->request->get('select');
		$conditions[0] .= ' AND os = ?';
		$conditions[] = $this->request->get('os');
		return $this->_userResearch( $conditions, $init );
	}

	public function brow() {
		$init = Config::NORMAL_ACCESS;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND browser <> ?';
		$conditions[] = '';
		if( $this->request->get('search') !== null ) {
			$conditions[0] .= ' AND browser LIKE ?';
			$conditions[] = '%' . $this->request->get('search') . '%';
		}
		$findOptions = array( 'condition' => $conditions, 'group' => 'dd,uid', 'subgroup' => 'browser' );
		return $this->_doResearch( $findOptions );
	}

	public function brow_ver() {
		$this->checkUrl('select');
		$init = Config::NORMAL_ACCESS;
		$conditions = $this->_initConditions( $init );
		if( $this->request->get('search') !== null ) {
			$conditions[0] .= ' AND brow_ver LIKE ?';
			$conditions[] = '%' . $this->request->get('search') . '%';
		}
		$conditions[0] .= ' AND browser = ?';
		$conditions[] = $this->request->get('select');
		$findOptions = array( 'condition' => $conditions, 'group' => 'dd,uid', 'subgroup' => 'brow_ver' );
		return $this->_doResearch( $findOptions );
	}

	public function brow_user() {
		$this->checkUrl( array('select', 'browser') );
		$init = Config::NORMAL_ACCESS;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND brow_ver = ?';
		$conditions[] = $this->request->get('select');
		$conditions[0] .= ' AND browser = ?';
		$conditions[] = $this->request->get('browser');
		return $this->_userResearch( $conditions, $init );
	}

	public function pagein() {
		$conditions = $this->_initConditions( Config::NORMAL_ACCESS );
		$conditions[0] .= ' AND url <> ?';
		$conditions[] = '';
		$order = 'dd ASC, hh ASC, mi ASC, ss ASC, id ASC';
		$findOptions = array( 'condition' => $conditions, 'order' => $order );
		return $this->_doResearch( $findOptions, $this->request->get('search') );
	}

	public function pagein_user() {
		$this->checkUrl('select');
		$init = Config::NORMAL_ACCESS;
		$compare = $this->request->get('select');
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND url <> ?';
		$conditions[] = '';
		$order = 'dd ASC, hh ASC, mi ASC, ss ASC, id ASC';
		return $this->_userResearch( $conditions, $init, $order, __FUNCTION__, false, $compare );
	}

	public function pageout() {
		$conditions = $this->_initConditions( Config::NORMAL_ACCESS );
		$conditions[0] .= ' AND url <> ?';
		$conditions[] = '';
		$order = 'dd DESC, hh DESC, mi DESC, ss DESC, id DESC';
		$findOptions = array( 'condition' => $conditions, 'order' => $order);
		return $this->_doResearch( $findOptions, $this->request->get('search') );
	}

	public function pageout_user() {
		$this->checkUrl('select');
		$init = Config::NORMAL_ACCESS;
		$compare = $this->request->get('select');
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND url <> ?';
		$conditions[] = '';
		$order = 'dd DESC, hh DESC, mi DESC, ss DESC, id DESC';
		return $this->_userResearch( $conditions, $init, $order, __FUNCTION__, false, $compare );
	}

	public function ip() {
		$init = Config::NORMAL_ACCESS;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND remote_addr <> ?';
		$conditions[] = '';
		if( $this->request->get('search') !== null ) {
			$conditions[0] .= ' AND remote_addr LIKE ?';
			$conditions[] = '%' . $this->request->get('search') . '%';
		}
		$findOptions = array( 'condition' => $conditions, 'group' => 'dd,uid', 'subgroup' => 'remote_addr' );
		return $this->_doResearch( $findOptions );
	}

	public function ip_user() {
		$this->checkUrl('select');
		$conditions = $this->_initConditions();
		$conditions[0] .= ' AND remote_addr = ?';
		$conditions[] = $this->request->get('select');
		if( $this->request->get('search') !== null ) {
			$conditions[0] .= ' AND uid LIKE ?';
			$conditions[] = '%' . $this->request->get('search') . '%';
		}
		return $this->_userResearch( $conditions, null, null, __FUNCTION__, false );
	}

	public function remotehost() {
		$init = Config::NORMAL_ACCESS;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND remote_host <> ?';
		$conditions[] = '';
		$conditions[0] .= ' AND remote_host <> ?';
		$conditions[] = Config::NO_DATA;
		if( $this->request->get('search') !== null ) {
			$conditions[0] .= ' AND remote_host LIKE ?';
			$conditions[] = '%' . $this->request->get('search') . '%';
		}
		$findOptions = array( 'condition' => $conditions, 'group' => 'dd,uid', 'subgroup' => 'remote_host' );
		return $this->_doResearch( $findOptions );
	}

	public function remotehost_user() {
		$this->checkUrl('select');
		$conditions = $this->_initConditions();
		$conditions[0] .= ' AND remote_host = ?';
		$conditions[] = $this->request->get('select');
		if( $this->request->get('search') !== null ) {
			$conditions[0] .= ' AND uid LIKE ?';
			$conditions[] = '%' . $this->request->get('search') . '%';
		}
		return $this->_userResearch( $conditions, null, null, __FUNCTION__, false );
	}

	public function domain() {
		$init = Config::NORMAL_ACCESS;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND domain <> ?';
		$conditions[] = '';
		$conditions[0] .= ' AND domain <> ?';
		$conditions[] = Config::NO_DATA;
		if( $this->request->get('search') !== null ) {
			$conditions[0] .= ' AND domain LIKE ?';
			$conditions[] = '%' . $this->request->get('search') . '%';
		}
		$findOptions = array( 'condition' => $conditions, 'group' => 'dd,uid', 'subgroup' => 'domain' );
		return $this->_doResearch( $findOptions );
	}

	public function domain_user() {
		$this->checkUrl('select');
		$init = Config::NORMAL_ACCESS;
		if( $this->request->get('logtype') !== null ) {
			$init = Config::CLICK_ADSENSE;
		}
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND domain = ?';
		$conditions[] = $this->request->get('select');
		return $this->_userResearch( $conditions, $init );
	}

	public function jpdomain() {
		$init = Config::NORMAL_ACCESS;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND jpdomain <> ?';
		$conditions[] = '';
		if( $this->request->get('search') !== null ) {
			$conditions[0] .= ' AND jpdomain LIKE ?';
			$conditions[] = '%' . $this->request->get('search') . '%';
		}
		$findOptions = array( 'condition' => $conditions, 'group' => 'dd,uid', 'subgroup' => 'jpdomain' );
		return $this->_doResearch( $findOptions );
	}

	public function jpdomain_user() {
		$this->checkUrl('select');
		$init = Config::NORMAL_ACCESS;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND jpdomain = ?';
		$conditions[] = $this->request->get('select');
		return $this->_userResearch( $conditions, $init );
	}

	public function pref() {
		$init = Config::NORMAL_ACCESS;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND pref <> ?';
		$conditions[] = '';
		$conditions[0] .= ' AND pref <> ?';
		$conditions[] = Config::NO_DATA;
		if( $this->request->get('search') !== null ) {
			$conditions[0] .= ' AND pref LIKE ?';
			$conditions[] = '%' . $this->request->get('search') . '%';
		}
		$findOptions = array( 'condition' => $conditions, 'group' => 'dd,uid', 'subgroup' => 'pref' );
		return $this->_doResearch( $findOptions );
	}

	public function pref_user() {
		$this->checkUrl('select');
		$init = Config::NORMAL_ACCESS;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND pref = ?';
		$conditions[] = $this->request->get('select');
		return $this->_userResearch( $conditions, $init );
	}

	public function country() {
		$init = Config::NORMAL_ACCESS;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND country <> ?';
		$conditions[] = '';
		$conditions[0] .= ' AND country <> ?';
		$conditions[] = Config::NO_DATA;
		if( $this->request->get('search') !== null ) {
			$conditions[0] .= ' AND country LIKE ?';
			$conditions[] = '%' . $this->request->get('search') . '%';
		}
		$findOptions = array( 'condition' => $conditions, 'group' => 'dd,uid', 'subgroup' => 'country' );
		return $this->_doResearch( $findOptions );
	}

	public function country_user() {
		$this->checkUrl('select');
		$init = Config::NORMAL_ACCESS;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND country = ?';
		$conditions[] = $this->request->get('select');
		return $this->_userResearch( $conditions, $init );
	}

	public function rank() {
		$conditions = $this->_initConditions( Config::NORMAL_ACCESS );
		$conditions[0] .= ' AND url <> ?';
		$conditions[] = '';
		if( $this->request->get('search') !== null ) {
			$conditions[0] .= ' AND url LIKE ?';
			$conditions[] = '%' . $this->request->get('search') . '%';
		}
		$findOptions = array( 'condition' => $conditions, 'group' => 'url,dd,uid', 'subgroup' => 'url' );
		return $this->_doResearch( $findOptions );
	}

	public function rank_user() {
		$this->checkUrl('select');
		$init = Config::NORMAL_ACCESS;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND url = ?';
		$conditions[] = $this->request->get('select');
		return $this->_userResearch( $conditions, $init, null, __FUNCTION__, false );
	}

	public function bounce() {
		$conditions = $this->_initConditions( Config::NORMAL_ACCESS );
		$conditions[0] .= ' AND url <> ?';
		$conditions[] = '';
		if( $this->request->get('search') !== null ) {
			$conditions[0] .= ' AND url LIKE ?';
			$conditions[] = '%' . $this->request->get('search') . '%';
		}
		$order = 'dd ASC, hh ASC, mi ASC, ss ASC, id ASC';
		$findOptions = array( 'condition' => $conditions, 'order' => $order );
		return $this->_doResearch( $findOptions );
	}

	public function bounce_user() {
		$this->checkUrl('select');
		$init = Config::NORMAL_ACCESS;
		$compare = $this->request->get('select');
		$conditions = $this->_initConditions( $init );
		$order = 'dd ASC, hh ASC, mi ASC, ss ASC, id ASC';
		return $this->_userResearch( $conditions, $init, $order, __FUNCTION__, false, $compare );
	}

	public function rate() {
		$conditions = $this->_initConditions( Config::NORMAL_ACCESS );
		$findOptions = array( 'condition' => $conditions);
		$findOptions = array( 'condition' => $conditions, 'group' => 'dd,uid' );
		return $this->_doResearch( $findOptions );
	}

	public function rate_user() {
		$this->checkUrl('select');
		$init = Config::NORMAL_ACCESS;
		$compare = $this->request->get('select');
		$conditions = $this->_initConditions( $init );
		return $this->_userResearch( $conditions, $init, null, __FUNCTION__, false, $compare );
	}

	public function screencol() {
		$conditions = $this->_initConditions( Config::NORMAL_ACCESS );
		$conditions[0] .= ' AND screencol <> ?';
		$conditions[] = '';
		$findOptions = array( 'condition' => $conditions, 'group' => 'dd,uid', 'subgroup' => 'screencol' );
		return $this->_doResearch( $findOptions );
	}

	public function screencol_user() {
		$this->checkUrl('select');
		$init = Config::NORMAL_ACCESS;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND screencol = ?';
		$conditions[] = $this->request->get('select');
		return $this->_userResearch( $conditions, $init );
	}

	public function screenwh() {
		$conditions = $this->_initConditions( Config::NORMAL_ACCESS );
		$conditions[0] .= ' AND screenwh <> ?';
		$conditions[] = '';
		$findOptions = array( 'condition' => $conditions, 'group' => 'dd,uid', 'subgroup' => 'screenwh' );
		return $this->_doResearch( $findOptions );
	}

	public function screenwh_user() {
		$this->checkUrl('select');
		$init = Config::NORMAL_ACCESS;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND screenwh = ?';
		$conditions[] = $this->request->get('select');
		return $this->_userResearch( $conditions, $init );
	}

	public function jsck() {
		$conditions = $this->_initConditions( Config::NORMAL_ACCESS );
		$conditions[0] .= ' AND jsck <> ?';
		$conditions[] = '';
		$findOptions = array( 'condition' => $conditions, 'group' => 'dd,uid', 'subgroup' => 'jsck' );
		return $this->_doResearch( $findOptions );
	}

	public function jsck_user() {
		$this->checkUrl('select');
		$init = Config::NORMAL_ACCESS;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND jsck = ?';
		$conditions[] = $this->request->get('select');
		return $this->_userResearch( $conditions, $init );
	}

	public function times( $method ) {
		$conditions = $this->_initConditions( Config::NORMAL_ACCESS );
		$findOptions = array( 'condition' => $conditions, 'group' => 'dd,hh,uid', 'subgroup' => 'hh' );
		return $this->_doResearch( $findOptions, null, $method );
	}
	public function digest1() {
		return $this->times( 'digest1' );
	}
	public function digest2() {
		return $this->referer();
	}
	public function time() {
		return $this->times( 'time' );
	}
	public function timestack() {
		return $this->times( 'timestack' );
	}
	public function terms( $method ) {
		$conditions = $this->_initConditions( Config::NORMAL_ACCESS );
		$findOptions = array( 'condition' => $conditions, 'group' => 'dd,uid', 'subgroup' => 'dd' );
		return $this->_doResearch( $findOptions, null, $method );
	}
	public function term() {
		return $this->terms( 'term' );
	}
	public function termstack() {
		return $this->terms( 'termstack' );
	}

	public function week() {
		return $this->terms( 'week' );
	}

	public function time_detail() {
		$this->checkUrl('select');
		$conditions = $this->_initConditions();
		//$conditions[0] .= ' AND hh = ?';
		//$conditions[] = $this->request->get('select');
		$order = 'dd DESC, hh DESC, mi DESC, ss DESC, id DESC';
		$findOptions = array( 'condition' => $conditions, 'order' => $order );
		return $this->_doResearch( $findOptions, $this->request->get('select') );
	}

	public function visit() {
		$conditions = $this->_initConditions();
		if( $this->request->get('search') !== null ) {
			$conditions[0] .= ' AND uid LIKE ?';
			$conditions[] = '%' . $this->request->get('search') . '%';
		}
		$order = 'dd DESC, hh DESC, mi DESC, ss DESC, id DESC';
		$findOptions = array( 'condition' => $conditions, 'order' => $order );
		return $this->_doResearch( $findOptions );
	}

	public function uid() {
		$conditions = $this->_initConditions();
		if( $this->request->get('search') !== null ) {
			$conditions[0] .= ' AND uid LIKE ?';
			$conditions[] = '%' . $this->request->get('search') . '%';
		}
		$findOptions = array( 'condition' => $conditions );
		return $this->_doResearch( $findOptions );
	}

	public function uid_detail() {
		$this->checkUrl('select');
		$conditions = $this->_initConditions();
		$conditions[0] .= ' AND uid = ?';
		$conditions[] = $this->request->get('select');
		$order = 'dd ASC, hh ASC, mi ASC, ss ASC, id ASC';
		$findOptions = array( 'condition' => $conditions, 'order' => $order );
		return $this->_doResearch($findOptions, $this->request->get('select'));
	}

	public function word() {
		$conditions = $this->_initConditions( Config::NORMAL_ACCESS );
		$conditions[0] .= ' AND keyword <> ?';
		$conditions[] = '';
		if( $this->request->get('search') !== null ) {
			$conditions[0] .= ' AND keyword LIKE ?';
			$conditions[] = '%' . $this->request->get('search') . '%';
		}
		$findOptions = array( 'condition' => $conditions);
		return $this->_doResearch($findOptions, $this->request->get('search'));
	}

	public function engine() {
		$init = Config::NORMAL_ACCESS;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND engine <> ?';
		$conditions[] = '';
		$conditions[0] .= ' AND keyword <> ?';
		$conditions[] = '';
		if( $this->request->get('search') !== null ) {
			$conditions[0] .= ' AND engine LIKE ?';
			$conditions[] = '%' . $this->request->get('search') . '%';
		}
		$group = 'engine,keyword,dd';
		$this->_firstQuery( $conditions, $init, null, $group );
		return $this->_setTotalAccess();
	}

	public function engine_key() {
		$this->checkUrl('select');
		$init = Config::NORMAL_ACCESS;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND engine <> ?';
		$conditions[] = '';
		$conditions[0] .= ' AND keyword <> ?';
		$conditions[] = '';
		$conditions[0] .= ' AND engine = ?';
		$conditions[] = $this->request->get('select');
		$group = 'keyword,dd';
		$this->_firstQuery( $conditions, $init, null, $group );
		return $this->_setTotalAccess();
	}

	public function engine_user() {
		$this->checkUrl( array( 'select', 'engine' ) );
		$init = Config::NORMAL_ACCESS;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND trim(replace(keyword,"　"," " )) = ?';
		$conditions[] = trim( $this->request->get('select') );
		$conditions[0] .= ' AND engine = ?';
		$conditions[] = $this->request->get('engine');
		return $this->_userResearch( $conditions, $init );
	}

	public function key() {
		$init = Config::NORMAL_ACCESS;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND keyword <> ?';
		$conditions[] = '';
		$conditions[0] .= ' AND engine <> ?';
		$conditions[] = '';
		if( $this->request->get('search') !== null ) {
			$conditions[0] .= ' AND keyword LIKE ?';
			$conditions[] = '%' . $this->request->get('search') . '%';
		}
		$group = 'keyword,engine,dd';
		$this->_firstQuery( $conditions, $init, null, $group );
		return $this->_setTotalAccess();
	}

	public function key_engine() {
		$this->checkUrl('select');
		$init = Config::NORMAL_ACCESS;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND keyword <> ?';
		$conditions[] = '';
		$conditions[0] .= ' AND engine <> ?';
		$conditions[] = '';
		$conditions[0] .= ' AND trim(replace(keyword,"　"," " )) = ?';
		$conditions[] = trim( $this->request->get('select') );
		$group = 'engine,dd';
		$this->_firstQuery( $conditions, $init, null, $group );
		return $this->_setTotalAccess();
	}

	public function key_user() {
		$this->checkUrl( array( 'select', 'keyword' ) );
		$init = Config::NORMAL_ACCESS;
		$conditions = $this->_initConditions( $init );
		$conditions[0] .= ' AND engine = ?';
		$conditions[] = $this->request->get('select');
		$conditions[0] .= ' AND trim(replace(keyword,"　"," " )) = ?';
		$conditions[] = trim( $this->request->get('keyword') );
		return $this->_userResearch( $conditions, $init );
	}

	public function download() {
		$result = $this->result;

		if( !$this->checkPost() ) {
			return $result;
		}

		if( !$this->checkParamYyyyMmDd(
			$this->request->get('yyyy_from'), $this->request->get('mm_from'), $this->request->get('dd_from'),
			$this->request->get('yyyy_to'), $this->request->get('mm_to'), $this->request->get('dd_to'))
		) {
			$this->message->setMessage( '期間を正しく設定してください。' );
			return $result;
		}
		if( $this->request->get('charset') === null ) {
			$this->message->setMessage( '文字コードを選択してください。' );
			return $result;
		}
		$queryString = '&charset='. $this->request->get('charset');
		$this->redirect('research', 'log', $queryString);
	}

	public function log() {
		$result = $this->result;
		if( $this->checkPost() ) {
			$this->redirect('research', 'download');
		}
		$result->setCharset( $this->request->get('charset') );
		$conditions = $this->_initConditions();
		$order = 'dd ASC, hh ASC, mi ASC, ss ASC';
		$findOptions = array( 'condition' => $conditions, 'order' => $order );
		return $this->_doResearch( $findOptions );
	}

	public function _initConditions( $logtype = null ) {
		$conditions = array('');
		if( $logtype !== null ) {
			$conditions[0] .= ' AND logtype = ?';
			$conditions[] = $logtype;
		}
		if( $this->session->get('selectUid') !== null ) {
			$conditions[0] .= ' AND uid = ?';
			$conditions[] = $this->session->get('selectUid');
		}
		if( $this->action != 'crawler' && $this->action != 'crawler_user' && $this->action != 'uid_detail' ) {
			$conditions = $this->_checkNoCrawler($conditions);
		}
		$conditions = $this->_checkfilter( $this->_getFilters(), $conditions );
		return $conditions;
	}

	public function _getFilters() {
		$id = $this->session->get('login') !== null ? $this->session->get('login') : Config::SYSTEM_DEFAULT_ID;
		$site = new Site();
		$site->find( '*', array( 'condition' => array( 'id = ?', $id ) ) );
		$filter = $site->getValue('filter');
		if( strlen( trim( $filter ) ) == 0 ) return null;
		$filter = File::replaceCrlf( $filter, "/r" );
		return explode( "/r" , $filter );
	}

	public function _checkfilter( $filters, $conditions ) {
		if( is_array( $filters ) ) {
			foreach( $filters as $filter ) {
				if( trim( $filter ) <> '' ) {
					$conditions[0] .= ' AND (';
					$conditions[0] .= ' remote_host NOT LIKE ?';
					$conditions[] = '%' . $filter . '%';
					$conditions[0] .= ' AND';
					$conditions[0] .= ' remote_addr NOT LIKE ?';
					$conditions[] = '%' . $filter . '%';
					$conditions[0] .= ' )';
				}
			}
		}
		return $conditions;
	}

	public function _checkNoCrawler( $conditions ) {
		$id = $this->session->get('login') !== null ? $this->session->get('login') : Config::SYSTEM_DEFAULT_ID;
		$site = new Site();
		$site->find( '*', array( 'condition' => array( 'id = ?', $id ) ) );
		$nocrawler = $site->getValue('nocrawler');
		if( $nocrawler === Config::ON ) {
			$conditions[0] .= ' AND crawler = \'\'';
		}
		return $conditions;
	}

	public function _doResearch( $findOptions, $compareValue=null, $method=null, $action=null, $yyyyMmOptions=null, $hhmissOptions=null ) {
		if( $method === null ) {
			$dbg = debug_backtrace();
			$method = $dbg[1]['function'];
		}

		if( !is_array( $yyyyMmOptions ) ) {
			$yyyyMmOptions = array(
				'yyyyFrom' => $this->_yyyyFrom,
				'mmFrom' => $this->_mmFrom,
				'ddFrom' => $this->_ddFrom,
				'yyyyTo' => $this->_yyyyTo,
				'mmTo' => $this->_mmTo,
				'ddTo' => $this->_ddTo
			);
		}
		if( $method === 'term' or $method === 'termstack' or $method === 'week' ) {
			$yyyyFrom = $yyyyTo = $yyyyMmOptions['yyyyFrom'];
			$mmFrom = $mmTo = $yyyyMmOptions['mmFrom'];
			$ddFrom = '01';
			$ddTo = Calendar::getLastDay( $yyyyFrom, $mmFrom );

			$yyyyMmOptions = array(
				'yyyyFrom' => $yyyyFrom,
				'mmFrom' => $mmFrom,
				'ddFrom' => $ddFrom,
				'yyyyTo' => $yyyyTo,
				'mmTo' => $mmTo,
				'ddTo' => $ddTo
			);
		}

		$now = $_SERVER['REQUEST_TIME'];
		$yyyy = date( 'Y', $now );
		$mm = date( 'm', $now );
		if( $this->_yyyyFrom . $this->_mmFrom == $yyyy . $mm || $this->_yyyyFrom . $this->_mmFrom == Calendar::getNextMonth(1) ) {
			$log = new Log();
		}
		else {
			$log = new Log( true );
		}
		if( !is_array( $hhmissOptions ) ) {
			$hhmissOptions = array(
				'hhFrom' => '00',
				'miFrom' => '00',
				'ssFrom' => '00',
				'hhTo'   => '23',
				'miTo'   => '59',
				'ssTo'   => '59'
			);
		}
		$log->setKeys();
		$results = $this->_resultSort( $log->findSummary( $findOptions, $method, $yyyyMmOptions, $hhmissOptions, $compareValue, $action ), $method );

		unset( $results['forExtractData1'] );
		unset( $results['forExtractData2'] );
		$result = $this->result;
		foreach( $results as $k => $v ) {
			$result->set( $k, $v );
		}
		return $result;
	}

	private function _firstQuery( $conditions=array(), $init=null, $order=null, $group=null, $action=null ) {
		if( $action === null ) {
			$dbg = debug_backtrace();
			$action = $dbg[1]['function'];
		}
		$findOptions = array( 'condition' => $conditions, 'order' => $order, 'group' => $group );
		$this->_doResearch( $findOptions, null, $action );

		$this->_allCount = $this->result->get('allCount');
		$this->_uniqueCount = $this->result->get('uniqueCount');
		$this->_summaryData = $this->result->get('summaryData');
		$this->result->delete('allCount');
		$this->result->delete('uniqueCount');
		$this->result->delete('summaryData');

		return $this->_getTotalAccess( $init, $action, $order=null, $group=null );
	}

	private function _getTotalAccess( $init=null, $action=null, $order=null, $group=null ) {
		$conditions = $this->_initConditions( $init );
		$findOptions = array();
		$method = $action . '_total';

		if( isset( QueryConfig::$ActionToGroupBy[$action] ) ) {
			$group = QueryConfig::$ActionToGroupBy[$action];
			$findOptions = array( 'condition' => $conditions, 'group' => $group );
		}
		else {
			$findOptions = array( 'condition' => $conditions, 'order' => $order );
		}

		$this->_doResearch( $findOptions, null, $method, $action );

		return $this->result;
	}

	private function _setTotalAccess( $action=null ) {
		if( $action === null ) {
			$dbg = debug_backtrace();
			$action = $dbg[1]['function'];
		}
		$totalAccess = $this->result->get('summaryData');
		$summary =& $this->_summaryData;

		if( $action === 'engine' || $action === 'engine_key' || $action === 'key' || $action === 'key_engine' ) {
			foreach( $totalAccess as $key => $value ) {
				foreach( (array)$value as $val ) {
					foreach( (array)$val as $k => $v ) {
						if( isset( $summary[$v] ) && $k === trim( $this->request->get('select') ) ) {
							$summary[$v]['total'] += $value[0];
							$this->_totalCount += $value[0];
						}
					}
				}
			}
		}
		else {
			foreach( $totalAccess as $value ) {
				foreach( (array)$value as $val ) {
					if( isset( $summary[$val] ) ) {
						$summary[$val]['total'] += $value[0];
						$this->_totalCount += $value[0];
					}
				}
			}
		}

		unset( $totalAccess );
		$this->result->delete('summaryData');

		$sort_key_total = array();
		$sort_key_unique = array();
		foreach( $this->_summaryData as $key => $value ) {
			$sort_key_total[] = $value['total'];
			$sort_key_unique[] = $value['unique'];
		}

		$siteData = $this->session->get('siteData');
		$sortkey = isset( $siteData['sortkey'] ) ? $siteData['sortkey'] : 1;

		if( $sortkey !== Config::ON ) {
			array_multisort( $sort_key_unique, SORT_DESC, $sort_key_total, SORT_DESC, $this->_summaryData );
		}
		else {
			array_multisort( $sort_key_total, SORT_DESC, $sort_key_unique, SORT_DESC, $this->_summaryData );
		}

		$this->result->set( 'allCount', $this->_allCount );
		$this->result->set( 'totalCount', $this->_totalCount );
		$this->result->set( 'uniqueCount', $this->_uniqueCount );
		$this->result->set( 'summaryData', $this->_summaryData );

		return $this->result;
	}

	private function _userResearch( $conditions, $init=null, $order=null, $action=null, $total=true, $compare=null ) {
		if( $action === null ) {
			$dbg = debug_backtrace();
			$action = $dbg[1]['function'];
		}
		$i = 0;
		$pageCount = 1;
		$siteData = $this->session->get('siteData');
		$page = $this->request->get('page') !== null ? $this->request->get('page') : 1;

		$total_array_tmp = array();

		if( $total ) {
			$conds = $this->_initConditions( $init );
			$findOptions = array();
			$method = '';

			if( isset( QueryConfig::$isTotalAll[$action] ) ) {
				$method = 'totalAll';
				$findOptions = array( 'condition' => $conds, 'order' => $order );
			}
			else {
				$method = 'totalAllGroupBy';
				$findOptions = array( 'condition' => $conditions, 'group' => 'dd,uid' );
			}

			$this->_doResearch( $findOptions, $compare, $method, $action );
			$total_array_tmp = $this->result->get('summaryData');
		}

		$method = 'uniqueAll';
		if( $action === 'referer_user' ) $method = 'uniqueAllRef';

		$findOptions = array( 'condition' => $conditions, 'order' => $order );
		$this->_doResearch( $findOptions, $compare, $method, $action );

		$totalCount = 0;
		$uniqueCount = 0;
		$total_array = array();

		if( $total ) {
			$unique_array = $this->result->get('summaryData');
			foreach( $total_array_tmp as $key => $value ) {
				if( isset( $unique_array[$key] ) ) {
					$total_array[$key] = $value;
					$totalCount += $value;
				}
			}
			$uniqueCount = $this->result->get('uniqueCount');
		}
		elseif( $action === 'rate_user' ) {
			$unique_array = $this->result->get('summaryData');
			foreach( $unique_array as $key => $value ) {
				if( $value === (int)$compare ) {
					$total_array[$key] = true;
					++$totalCount;
					++$uniqueCount;
				}
			}
		}
		else {
			$total_array = $this->result->get('summaryData');
			$totalCount = $this->result->get('totalCount');
			$uniqueCount = $this->result->get('uniqueCount');
		}

		arsort( $total_array );
		$uid_array = array();

		foreach( $total_array as $key => $value ) {
			if( $pageCount >= $siteData['dispview'] * ( $page - 1 ) + 1 && $pageCount <= $siteData['dispview'] * $page ) {
				$uid_array[] = $key;
				if( $i >= $siteData['dispview'] ) break;
				++$i;
			}
			++$pageCount;
		}

		$conditions[0] .= ' AND ( CONCAT( uid,dd ) = ?';
		$conditions[] = array_shift( $uid_array );
		foreach( $uid_array as $key => $value ) {
			$conditions[0] .= ' OR CONCAT( uid,dd ) = ?';
			$conditions[] = $value;
		}
		$conditions[0] .= ' )';
		$findOptions = array( 'condition' => $conditions, 'order' => $order );
		$this->_doResearch( $findOptions, $compare, $action );

		$summaryData = $this->result->get('summaryData');
		$this->result->delete('summaryData');
		if( $total ) {
			foreach( $summaryData as $key => $value ) {
				$summaryData[$key] = $total_array[$key];
			}
		}
		arsort( $summaryData );

		$this->result->set( 'totalCount', $totalCount );
		$this->result->set( 'uniqueCount', $uniqueCount );
		$this->result->set( 'summaryData', $summaryData );

		return $this->result;
	}

	private function _resultSort( $results, $method ) {
		$siteData = $this->session->get('siteData');
		$sortkey = isset( $siteData['sortkey'] ) ? $siteData['sortkey'] : 1;

		// array_multisort は 数値 ONLY なキーがあると添字が振り直されてしまうので、
		// キーが 数値 ONLY になる可能性があるものは (遅いけど) uasort 使う
		// それ以外は array_multisort
		switch( $method ) {
			case 'digest1':
			case 'rank':
			case 'bounce':
			case 'adrank':
			case 'adprank':
			case 'btnrank':
			case 'clickrank':
			case 'screencol':
			case 'screenwh':
			case 'jsck':
			case 'ip':
			case 'remotehost':
			case 'domain':
			case 'jpdomain':
			case 'pref':
			case 'country':
			case 'crawler':
			case 'os':
			case 'brow':
				if( $sortkey !== Config::ON ) {
					// UNIQUE SORT
					array_multisort( $results['forExtractData2'], SORT_DESC, SORT_NUMERIC, $results['forExtractData1'], SORT_DESC, SORT_NUMERIC, $results['summaryData'] );
				}
				else {
					// TOTAL SORT
					array_multisort( $results['forExtractData1'], SORT_DESC, SORT_NUMERIC, $results['forExtractData2'], SORT_DESC, SORT_NUMERIC, $results['summaryData'] );
				}
				break;
			case 'word':
			case 'visit_actual':
			case 'time_detail_actual':
				array_multisort( $results['forExtractData1'], SORT_DESC, SORT_NUMERIC, $results['summaryData'] );
				break;
			case 'uid':
				arsort( $results['clickLink'], SORT_NUMERIC );
			case 'adip':
			case 'pagein':
			case 'pageout':
				arsort( $results['summaryData'], SORT_NUMERIC );
				break;
			case 'rate':
				ksort( $results['summaryData'], SORT_NUMERIC );
				break;
			case 'os_ver':
			case 'brow_ver':
				uasort( $results['summaryData'],
					function( $a, $b ) {
						if( $a['total'] == $b['total'] ) {
							return ( $a['unique'] < $b['unique'] );
						}
						else {
							return ( $a['total'] < $b['total'] );
						}
					}
				);
				break;
			case 'time_detail':
				if( $this->request->get('page') === null ) {
					$i = 1;
					$siteData = $this->session->get('siteData');
					$selecttime = $this->session->get('yyyyTo').$this->session->get('mmTo').$this->session->get('ddTo').$this->request->get('select');
					foreach( $results['summaryData'] as $key => $value ) {
						if( substr( $key, 0, 10 ) === $selecttime ) {
							$this->request->set('page' , ceil( $i / $siteData['dispview'] ) );
							break;
						}
						++$i;
					}
				}
				break;
			default:
				break;
		}
		return $results;
	}
}
?>
