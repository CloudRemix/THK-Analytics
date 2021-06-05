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
abstract class BaseHelper extends ThkHelper {
	const RESEARCH_DAY = '1';
	const RESEARCH_MONTH = '2';
	const RESEARCH_TERM = '3';
	const PAGE_COUNT_ALL = 'all';
	const PAGE_COUNT_UNIQUE = 'unq';

	private $_research = null;

	public function __construct( ThkRequest $request, ThkSession $session, ThkMessage $message, ThkResult $result=null, $controller, $action ) {
		parent::__construct( $request, $session, $message, $result, $controller, $action );
		$this->_research = $this->_getResearch();
	}

	public function isMenuWrite() {
		return $this->controller === 'research' || $this->controller === 'admin' ? true : false;
	}

	public function substrMax( $str, $max=null ) {
		if( $max === null ) {
			if( strlen( $str ) !== mb_strlen( $str, ThkConfig::CHARSET ) ) {
				if( stripos( $str, '://' ) !== false ) {
					$max = Config::MAX_DISP_LENGTH;
				}
				else {
					$max = Config::MAX_DISP_MULTIBYTE_LENGTH;
				}
			}
			else {
				$max = Config::MAX_DISP_LENGTH;
			}
		}
		$lastWidth = 10;
		$rtn = $str;
		$len = ThkUtil::strWidth( $str );
		if( $len > $max ) {
			if( $lastWidth < $max ) {
				$rtn = ThkUtil::strimWidth( $str, 0, $max - $lastWidth ) . '...' . ThkUtil::strimWidth( $str, ThkUtil::strWidth( $str ) - $lastWidth, $lastWidth );
			}
			else {
				$rtn = ThkUtil::strimWidth( $str, 0, $max ) . '...';
			}
		}
		return $rtn;
	}

	public function getFormatNumber( $value ) {
		//return number_format($value);
		return $value;
	}

	public function getFormatDataSize( $value ) {
		$len = strlen( $value );
		switch( $len ) {
			case 4:
			case 5:
			case 6:
				$rtn = sprintf( '%.1f', round( $value / 1000, 1 ) ) . ' KB';
				break;
			case 7:
			case 8:
			case 9:
				$rtn = sprintf( '%.1f', round( $value / ( 1000 * 1000 ), 1 ) ) . ' MB';
				break;
			case 10:
			case 11:
			case 12:
				$rtn = sprintf( '%.1f', round( $value / ( 1000 * 1000 * 1000 ), 1 ) ) . ' GB';
				break;
			default:
				$rtn = $this->getFormatNumber( $value ) . ' bytes';
				break;
		}
		return $rtn;
	}

	public function escapeHtml( $value ) {
		return htmlentities( $value, ENT_QUOTES, ThkConfig::CHARSET );
	}

	public function urlEncode( $url ) {
		return rawurlencode( $url );
	}

	public function urlDecode( $url ) {
		while( $url !== ThkUtil::convertEncoding( rawurldecode( $url ), 'auto' ) ) {
			$url = ThkUtil::convertEncoding( rawurldecode( $url ), 'auto' );
		}
		return $url;
	}

	public function getZeroPadding( $value, $cnt ) {
		return str_pad( $value, $cnt + 1, '0', STR_PAD_LEFT );
	}

	public function getZeroSuppress( $value ) {
		return is_numeric( $value ) ? (int)$value : $value;
	}

	public function getVersion() {
		$version = $this->session->get('version') !== null ? $this->session->get('version') : '';
		if( $version === '' ) $version = File::replaceCrlf( File::readFile( THK_CORE_DIR . 'thk_version.txt' ), '' );
		return $version !== '' ? 'ver.' . $version : '';
	}

	public function ralite_getVersion() {
		$version = Config::RA_LITE_VERSION;
		return $version != '' ? '<a href="' . $this->getJumpUrl( ThkConfig::RA_URL ) . 'version/?ver=' . $version . '" title="バージョンチェック" target="_blank">' . 'ver.' . $version . '</a>' : '';
	}

	public function getIndexUrl( $controller, $action, $querystring=null ) {
		$url = ThkUtil::getBaseUrl();

		$act = $action;
		if( isset( MenuConfig::$getAbbrev[$action] ) ) $act = MenuConfig::$getAbbrev[$action];
		$con = $controller;
		if( isset( MenuConfig::$getAbbrev[$controller] ) ) $con = MenuConfig::$getAbbrev[$controller];

		if( trim( $con ) !== '' ) $url .= '?c=' . $con;
		if( trim( $act ) !== '' ) $url .= '&amp;a=' . $act;

		if( $querystring !== null ) {
			foreach( MenuConfig::$getAbbrev as $key => $value ) {
				$querystring = str_replace( '&amp;' . $key . '=', '&amp;' . $value . '=', $querystring );
			}
			$querystring = str_replace( 'yyyy_from', 'f', $querystring );
			$querystring = str_replace( '&amp;mm_from=', '', $querystring );
			$querystring = str_replace( '&amp;dd_from=', '', $querystring );
			$querystring = str_replace( 'yyyy_to', 't', $querystring );
			$querystring = str_replace( '&amp;mm_to=', '', $querystring );
			$querystring = str_replace( '&amp;dd_to=', '', $querystring );

			$url .= $querystring;
		}
		return $url;
	}

	public function getJumpUrl( $link ) {
		return Track::checkUrl( $link ) ? ThkUtil::getBaseUrl() . '?c=m&amp;a=jp&amp;link=' . $this->urlEncode( $link ) : ThkUtil::getBaseUrl() . '?c=m&amp;a=jp';
	}

	public function getJumpLink( $link ) {
		$rtn = '';
		if( Track::checkUrl( $link ) ) {
			$rtn = $link;
		}
		return $rtn;
	}

	public function getToken() {
		$token = $this->result->get('token');
		return $this->escapeHtml( $token );
	}

	public function getEvenClass( $i ) {
		if( ( $i + 1 ) % 2 === 0 ) {
			return 'even';
		}
		return 'white';
	}

	public function getMessage() {
		$controller = $this->controller;
		$action = $this->action;
		$code = $this->message->getCode();
		$message = $this->message->getMessage();
		$crlf = ThkUtil::isHttp() ? '<br />' : ThkLog::NEWLINE;
		switch( $code ) {
			case ThkConfig::CONTROLLER_NOTFOUND_ERR_CODE:
			case ThkConfig::ACTION_NOTFOUND_ERR_CODE:
				if( ( $controller === 'login' && $action === 'login' ) || ( $controller === 'install' && $action === 'step1' ) ) {
					$rtn = '';
				}
				else {
					$rtn = '指定したURLに誤りがあります。';
				}
				break;
			case ThkConfig::DATABASE_CONFIG_UNDEFINED_ERR_CODE:
				$rtn = 'データベース接続設定の読込みに失敗しました。' . $crlf;
				if( ThkUtil::isHttp() ) $rtn .= '<a href="' . ThkUtil::getBaseUrl() . '">データベース接続の再設定</a> を行なってください。';
				break;
			default:
				$rtn = $message;
				if( $code != '' && $code != ThkConfig::DEFAULT_ERR_CODE && $code != Config::NOTICE_ERR_CODE ) $rtn .= ' (' . $code . ')';
				break;
		}
		return !ThkUtil::isHttp() && ThkUtil::isWindows() ? ThkUtil::convertEncoding( $rtn, ThkConfig::CHARSET, 'SJIS' ) : $rtn;
	}

	public function getActionTitle( $char=false ) {
		$rtn = '';
		$controller = $this->controller;
		$action = $this->action;
		if( $action !== null ) {
			$action = isset( MenuConfig::$convertActions[$action] ) ? MenuConfig::$convertActions[$action] : $action;
			if( trim( $rtn ) === '' ) {
				foreach( MenuConfig::$titles as $parent_title => $title ) {
					if( isset( $title[$action] ) ) {
						$rtn = $title[$action];
						break;
					}
				}
			}
			if( trim( $rtn ) === '') {
				if( isset( MenuConfig::$otherTitles[$controller] ) ) {
					if( isset( MenuConfig::$otherTitles[$controller][$action] ) ) {
						$rtn = MenuConfig::$otherTitles[$controller][$action];
					}
				}
			}
		}
		if( trim( $rtn ) !== '' && $char ) $rtn .= ' - ';
		return $rtn;
	}

	public function setDomainLabel( $domainArray ) {
		$conditions = array( '' );
		$domainData = array();
		foreach( $domainArray as $value ) {
			$conditions[0] .= ' OR domain = ?';
			$conditions[] = $value;
		}
		$conditions[0] = ltrim( $conditions[0], ' OR' );
		$domain = new Domain();
		$domainData = $domain->findAll( '*', array( 'condition' => $conditions, 'order' => 'name ASC' ) );
		array_walk( $domainData, function( &$v ) { unset( $v['id'] ); } );
		array_walk( $domainData, function( &$v ) { unset( $v['created_on'] ); } );
		array_walk( $domainData, function( &$v ) { unset( $v['updated_on'] ); } );
		$this->session->delete('domainData');
		$this->session->set( 'domainData', $domainData );
	}

	public function getSiteData( $column, $char=false ) {
		$rtn = '';
		$siteData = $this->session->get('siteData');
		if( $siteData !== null && isset( $siteData[$column] ) ) $rtn = $siteData[$column];
		if( trim( $rtn ) !== '' && $char ) $rtn .= ' - ';
		return $rtn;
	}

	public function domainSelect() {
		$select = $this->request->get('select');
		$html = '';
		$html .= $this->getDomain( $select );
		$html .= '&nbsp;'. $this->getDomainEdit( $select, false );
		echo $html;
	}

	public function getSelect() {
		$action = $this->action;
		$select = $this->request->get('select');
		$html = '';
		switch( $action ) {
			case 'rank_user':
			case 'pagein_user':
			case 'pageout_user':
			case 'bounce_user':
			case 'referer_user':
			case 'adprank_user':
			case 'btnrank_user':
			case 'clickrank_user':
				if( Track::checkUrl( $select ) ) {
					$html .= '<a href="' . $this->getJumpUrl( $select ) . '" title="' . $this->urlDecode( $this->escapeHtml( $select ) ) . '" target="_blank">' . $this->substrMax( $this->urlDecode( $this->escapeHtml( $this->getTitle( $select ) ) ) ) . '</a>';
				}
				else {
					$html .= $this->escapeHtml( $select );
				}
				break;
			case 'time_detail':
				if( is_numeric( $select ) ) {
					$html .= $this->escapeHtml( $this->getZeroSuppress( $select, false ) );
				}
				break;
			default:
				$html .= $this->escapeHtml( $select );
				break;
		}
		return $html;
	}

	public function getTitle( $url ) {
		$titleData = $this->session->get('titleData');
		$rtn = $url;
		if( is_array( $titleData ) ) {
			foreach($titleData as $value) {
				if( $this->urlDecode( $value['url'] ) === $this->urlDecode( $url ) ) {
					$rtn = $value['title'];
					break;
				}
			}
		}
		return $rtn;
	}

	public function setTitleLabel( $url ) {
		$rtn = $url;
		$conditions = array('');
		$titleData = array();
		if( is_array( $url ) ) {
			foreach( $url as $value ) {
				$conditions[0] .= ' OR url = ?';
				$conditions[] = $value;
			}
			$conditions[0] = ltrim( $conditions[0], ' OR' );
		}
		else {
			$conditions = array( 'condition' => array( ' url = ?', $url ) );
		}
		$title = new Title();
		$titleData = $title->findAll( '*', array( 'condition' => $conditions ) );
		array_walk( $titleData, function( &$v ) { unset( $v['id'] ); } );
		array_walk( $titleData, function( &$v ) { unset( $v['created_on'] ); } );
		array_walk( $titleData, function( &$v ) { unset( $v['updated_on'] ); } );
		if( $this->action === 'clickrank' ) {
			$titleData = array_merge( $titleData, $this->session->get( 'titleData' ) );
		}
		$this->session->delete('titleData');
		$this->session->set( 'titleData', $titleData );
	}

	public function getAlias( $uid ) {
		$aliasData = $this->session->get('aliasData');

		$rtn = $uid;
		foreach( $aliasData as $value ) {
			if( $value['uid'] === $uid ) $rtn = $value['name'];
		}
		return $this->escapeHtml( $rtn );
	}

	public function getDomain( $domain=null ) {
		$rtn = '';
		if( $domain === null ) $domain = $this->result->get('domain');
		if( is_array( $domain ) ) {
			if( count( $domain ) === 0 ) return $rtn;
			foreach( $domain as $v ) {
				$domain = $v;
				break;
			}
		}
		$rtn = $domain;
		$domainData = $this->session->get('domainData');
		if( is_array( $domainData ) ) {
			foreach( $domainData as $value ) {
				if( $value['domain'] === $domain ) $rtn = $value['name'];
			}
		}
		return $this->escapeHtml( $rtn );
	}

	public function getMenuTermSelect( $fromto ) {
		$yyyyFrom = $this->request->get('yyyy_from') !== null ? $this->request->get('yyyy_from') : $this->session->get('yyyyFrom');
		$mmFrom = $this->request->get('mm_from') !== null ? $this->request->get('mm_from') : $this->session->get('mmFrom');
		$ddFrom = $this->request->get('dd_from') !== null ? $this->request->get('dd_from') : $this->session->get('ddFrom');
		$yyyyTo = $this->request->get('yyyy_to') !== null ? $this->request->get('yyyy_to') : $this->session->get('yyyyTo');
		$mmTo = $this->request->get('mm_to') !== null ? $this->request->get('mm_to') : $this->session->get('mmTo');
		$ddTo = $this->request->get('dd_to') !== null ? $this->request->get('dd_to') : $this->session->get('ddTo');
		$research = $this->_research;

		$now = $_SERVER['REQUEST_TIME'];
		$yyyy = date( 'Y', $now );
		$selectYyyy = array(
			$yyyy - 4 => $yyyy -4,
			$yyyy - 3 => $yyyy -3,
			$yyyy - 2 => $yyyy -2,
			$yyyy - 1 => $yyyy -1,
			$yyyy     => $yyyy
		);
		$selectMm = array(
			'01' => '1',
			'02' => '2',
			'03' => '3',
			'04' => '4',
			'05' => '5',
			'06' => '6',
			'07' => '7',
			'08' => '8',
			'09' => '9',
			'10' => '10',
			'11' => '11',
			'12' => '12'
		);
		$selectDd = array(
			'01' => '1',
			'02' => '2',
			'03' => '3',
			'04' => '4',
			'05' => '5',
			'06' => '6',
			'07' => '7',
			'08' => '8',
			'09' => '9',
			'10' => '10',
			'11' => '11',
			'12' => '12',
			'13' => '13',
			'14' => '14',
			'15' => '15',
			'16' => '16',
			'17' => '17',
			'18' => '18',
			'19' => '19',
			'20' => '20',
			'21' => '21',
			'22' => '22',
			'23' => '23',
			'24' => '24',
			'25' => '25',
			'26' => '26',
			'27' => '27',
			'28' => '28',
			'29' => '29',
			'30' => '30',
			'31' => '31'
		);
		$html = '<select name="yyyy_' . $fromto . '">';
		foreach( $selectYyyy as $key => $value ) {
			if( ( $fromto === 'from' && $key == (int)$yyyyFrom ) || ( $fromto === 'to' && $key == (int)$yyyyTo ) ) {
				$html .= '<option value="' . $this->escapeHtml( $key ) . '" selected="selected">' . $this->escapeHtml( $value ) . '</option>';
			}
			else {
				$html .= '<option value="' . $this->escapeHtml( $key ) . '">' . $this->escapeHtml( $value ) . '</option>';
			}
		}
		$html .= '</select>';
		$html .= '<select name="mm_' . $fromto . '">';
		foreach( $selectMm as $key => $value ) {
			if( ( $fromto === 'from' && $key == (int)$mmFrom ) || ( $fromto === 'to' && $key == (int)$mmTo ) ) {
				$html .= '<option value="' . $this->escapeHtml( $key ) . '" selected="selected">' . $this->escapeHtml( $value ) . '</option>';
			}
			else {
				$html .= '<option value="' . $this->escapeHtml( $key ) . '">' . $this->escapeHtml( $value ) . '</option>';
			}
		}
		$html .= '</select>';
		$html .= '<select name="dd_' . $fromto . '">';
		foreach( $selectDd as $key => $value ) {
			if( (int)$key > 31 ) break;
			if( ( $fromto === 'from' && $key == (int)$ddFrom ) || ( $fromto === 'to' && $key == (int)$ddTo ) ) {
				$html .= '<option value="' . $this->escapeHtml( $key ) . '" selected="selected">' . $this->escapeHtml( $value ) . '</option>';
			}
			else {
				$html .= '<option value="' . $this->escapeHtml( $key ) . '">' . $this->escapeHtml( $value ) . '</option>';
			}
		}
		$html .= '</select>';
		return $html;
	}

	public function getDomainEdit( $domain, $div=true, $button=false , $value='ドメイン名設定' ) {
		$rtn = '';
		if( trim( $domain ) != '' && $domain != Config::NO_DATA ) {
			$domainName = $this->getDomain( $domain );
			if( $div ) {
				$rtn .= '<div class="alias fright">';
			}
			else {
				$rtn .= '<span class="alias">';
			}
			if( $button ) {
				$rtn .= '<input type="button" class="button" value="' . $this->escapeHtml( $value ) . '" onclick="location.href=\'' . $this->getIndexUrl( 'admin', 'domainedit', '&amp;select=' . $this->urlEncode( $domain ) ) . '\'">';
			}
			else {
				$rtn .= '<a title="' . $this->escapeHtml( $domainName ) . '" href="' . $this->getIndexUrl( 'admin', 'domainedit', '&amp;select=' . $this->urlEncode( $domain ) ) . '">' . $this->escapeHtml( $value ) . '</a>';
			}
			if( $div ) {
				$rtn .= '</div>';
			}
			else {
				$rtn .= '</span>';
			}
		}
		return $rtn;
	}

	public function getAliasEdit( $alias, $div=true, $button=false , $value='訪問者名設定' ) {
		$rtn = '';
		if( trim( $alias ) != '' && $alias != Config::NO_DATA ) {
			if( $div ) {
				$rtn .= '<div class="alias fright">';
			}
			else {
				$rtn .= '<span class="alias">';
			}
			if( $button ) {
				$rtn .= '<input type="button" class="button" value="' . $this->escapeHtml( $value ) . '" onclick="location.href=\'' . $this->getIndexUrl( 'admin', 'aliasedit', '&amp;select=' . $this->urlEncode( $alias ) ) . '\'">';
			}
			else {
				$rtn .= '<a href="' . $this->getIndexUrl( 'admin', 'aliasedit', '&amp;select=' . $this->urlEncode( $alias ) ) . '">' . $this->escapeHtml( $value ) . '</a>';
			}
			if( $div ) {
				$rtn .= '</div>';
			}
			else {
				$rtn .= '</span>';
			}
		}
		return $rtn;
	}

	public function versionTag() {
		echo '<a href="' . ThkConfig::THK_URL . '" target="_blank">' . ThkConfig::THK_NAME . ' ' . $this->getVersion() . '</a>';
	}

	public function ralite_versionTag() {
		echo ThkConfig::RA_NAME . ' ' . $this->ralite_getVersion();
	}

	public function indexUrl( $controller, $action, $querystring=null ) {
		echo $this->getIndexUrl( $controller, $action, $querystring );
	}

	public function jumpUrl( $link ) {
		echo $this->getJumpUrl( $link );
	}

	public function copyrightTag() {
		$thisYear = date( 'Y' );
		$yearString = $thisYear <= Config::RELEASE_YEAR ? Config::RELEASE_YEAR : Config::RELEASE_YEAR . '-' . $thisYear;
		echo 'Copyright &copy; ' . $yearString . ' ' . THKConfig::THK_PROJECT . ' All Rights Reserved.';
	}

	public function ralite_copyrightTag() {
		$thisYear = date( 'Y' );
		//$yearString = $thisYear <= Config::RELEASE_YEAR ? Config::RELEASE_YEAR : Config::RELEASE_YEAR . '-' . $thisYear;
		$yearString = Config::RALITE_RELEASE_YEAR . '-' . Config::RALITE_LAST_RELEASE_YEAR;
		echo 'Copyright &copy; ' . $yearString . ' ' . THKConfig::RA_PROJECT . ' All Rights Reserved.';
	}

	public function systemDataSize() {
		/*
		$site = new Site();
		$conditions = array( 'condition' => array( 'id = ?', $this->session->get('siteData')['id'] ) );
		$siteData = $site->find( '*', $conditions );
		*/
		echo $this->getFormatDataSize( SystemUtil::getSystemDataSize() );
	}

	public function messageTag() {
		$message = $this->getMessage();
		echo trim( $message ) != '' ? $message : '';
	}

	public function errorMessageTag() {
		$code = $this->message->getCode();
		$message = $this->getMessage();
		echo trim( $message ) != '' ? ( $code == Config::NOTICE_ERR_CODE ? '<div class="notice">' . $message . '</div>' : '<div class="fatal">' . $message . '</div>' ) : '';
	}

	public function confirmMessageTag() {
		$code = $this->message->getCode();
		$message = $this->getMessage();
		echo trim( $message ) != '' ? '<div class="notice">' . $message . '</div>' : '';
	}

	public function memoryError() {
		if( $this->result->get('memLimitFlag') > 0 ) {
			$per = sprintf( '%.1f', Config::UTILIZABLE_MEMORY * 100 );
			$msg = '使用可能なメモリサイズの ' . $per . '% に達したため処理を中断しました。';
			if( $this->result->get('memLogCount') > 1 ) {
				$msg .= '<br />' . $this->result->get('memLogCount') . '件のデータが取得できました。期間指定を短くしてください。';
			}
			$this->message->setMessage( $msg );
			$this->errorMessageTag();
		}
	}

	public function memoryLimit() {
		if( memory_get_usage( true ) > SystemUtil::getUtilizableMemory() ) {
			$this->result->set( 'memLimitFlag', 1 );
			return true;
		}
		return false;
	}

	public function actionTitle( $char=false ) {
		echo $this->getActionTitle( $char );
	}

	public function siteData( $column, $char=false ) {
		echo $this->escapeHtml( $this->getSiteData( $column, $char ) );
	}

	public function select() {
		echo $this->getSelect();
	}

	public function alias() {
		//echo $this->escapeHtml( $this->getAlias( $this->getSelect() ) );
		echo $this->getAlias( $this->getSelect() );
	}

	public function pageName( $id=null ) {
		if( $id === null ) $id = $this->getSelect();
		$pageName = new Title();
		$pageName->find( '*', array('condition' => array( 'id = ?', $id ) ) );
		$ret = array(
			'url' => $this->escapeHtml( $this->urlDecode( $pageName->getValue( 'url' ) ) ),
			'title' => $this->escapeHtml( $this->urlDecode( $pageName->getValue( 'title' ) ) )
		);
		return $ret;
	}

	public function domain( $domain=null ) {
		if( $domain === null ) $domain = $this->getSelect();
		$domainName = new Domain();
		$domainName->find( '*', array('condition' => array( 'domain = ?', $domain ) ) );
		echo $domainName->getValue( 'name' );
	}

	public function domainEdit( $domain, $divTag=true ) {
		echo $this->getDomainEdit( $domain, $divTag );
	}

	public function htmlChecked( $value, $compvalue ) {
		echo $value == $compvalue ? 'checked=\'checked\'' : '';
	}

	public function targetBlank( $targetflg ) {
		echo $targetflg === ThkConfig::ON ? ' onclick=\'window.open(this.href);return false;\'' : '';
	}

	public function token() {
		echo $this->getToken();
	}

	public function jsTag( $ssl=false ) {
		$host = $_SERVER['SERVER_NAME'] . str_replace( ADMIN_DIR_NAME . '/' . Config::INDEX_FILENAME, '', $_SERVER['PHP_SELF'] );
		$html = '<script type="text/javascript" src="//' . $host . 'script.php" defer></script>';
		$html .= '<noscript><img src="//' . $host . 'track.php" alt="" width="1" height="1" /></noscript>';
		echo $this->escapeHtml( $html );
	}

	public function jsAdvancedTag( $ssl=false ) {
		$host = $_SERVER['SERVER_NAME'] . str_replace( ADMIN_DIR_NAME . '/' . Config::INDEX_FILENAME, '', $_SERVER['PHP_SELF'] );
		$html = '<script type="text/javascript" src="（script.js までのパス）script.js" defer></script>';
		$html .= '<noscript><img src="//' . $host . 'track.php" alt="" width="1" height="1" /></noscript>';
		echo $this->escapeHtml( $html );
	}

	public function imgTag( $ssl=false ) {
		$host = $_SERVER['SERVER_NAME'] . str_replace( ADMIN_DIR_NAME . '/' . Config::INDEX_FILENAME, '', $_SERVER['PHP_SELF'] );
		$html = '<img src="//' . $host . 'track.php" alt="" width="1" height="1" />';
		echo $this->escapeHtml( $html );
	}

	public function imgTagPage( $ssl=false ) {
		$host = $_SERVER['SERVER_NAME'] . str_replace( ADMIN_DIR_NAME . '/' . Config::INDEX_FILENAME, '', $_SERVER['PHP_SELF'] );
		$html = '<img src="//' . $host . 'track.php?page=ページ1" alt="" width="1" height="1" />';
		echo $this->escapeHtml( $html );
	}

	public function phpCode( $title=false ) {
		$phpTrack = realpath( dirname( SETTING_PATH_FILE ) . DIRECTORY_SEPARATOR . '..' ) . DIRECTORY_SEPARATOR . 'phptrack.php';
		$code = '<?php' . "\n";
		$code .= 'include \'' . $phpTrack . '\';' . "\n";
		$code .= $title ? "_thkTrack('ページのタイトル');" : '_thkTrack();';
		$code .= "\n";
		$code .= '?>';
		echo $code;
	}

	public function searchFormTag( $label ) {
		$controller = $this->controller;
		$action = $this->action;

		$yyyyFrom = $this->session->get('yyyyFrom');
		$mmFrom = $this->session->get('mmFrom');
		$ddFrom = $this->session->get('ddFrom');
		$yyyyTo = $this->session->get('yyyyTo');
		$mmTo = $this->session->get('mmTo');
		$ddTo = $this->session->get('ddTo');

		$html = '';
		$formValue = $this->escapeHtml( $this->request->get('search') );
		if( ThkUtil::strLen( trim( $formValue ) ) > 0 ) $html = '<h3>&quot;' . $formValue . '&quot;で検索した結果</h3>';
		$querystring = '';
		//if( $this->request->get('search') !== null ) $querystring .= '&amp;search=' . $this->urlEncode( $this->request->get('search') );
		if( $this->request->get('select') !== null ) $querystring .= '&amp;select=' . $this->urlEncode( $this->request->get('select') );
		$html .= '<form id="search" action="' . $this->getIndexUrl( $controller, $action, $querystring ) . '" method="post">';
		$html .= '<div class="sbox">';
		$html .= '<input type="text" class="search" name="search" maxlength="100" value="' . $formValue . '" placeholder="' . $this->escapeHtml( $label ) . '" />'."\n";
		$html .= '<input class="button" type="submit" value=" 検索 " />';
		$html .= '</div>';
		$html .= '<input type="hidden" name="yyyy_from" value="' . $yyyyFrom . '" />';
		$html .= '<input type="hidden" name="mm_from" value="' . $mmFrom . '" />';
		$html .= '<input type="hidden" name="dd_from" value="' . $ddFrom . '" />';
		$html .= '<input type="hidden" name="yyyy_to" value="' . $yyyyTo . '" />';
		$html .= '<input type="hidden" name="mm_to" value="' . $mmTo . '" />';
		$html .= '<input type="hidden" name="dd_to" value="' . $ddTo . '" />';
		$html .= '</form>';
		echo $html;
	}

	public function radioChecked( $name, $value ) {
		$checked = '';
		if( $this->request->get($name) !== null && $this->request->get($name) == $value ) $checked = 'checked="checked"';
		echo $checked;
	}

	public function pageTag( $pageCount ) {
		$controller = $this->controller;
		$action = $this->action;
		$siteData = $this->session->get('siteData');

		if( substr_count( $pageCount, ',' ) > 0 ) $pageCount = str_replace( ',', '', $pageCount );

		$querystring = '';
		if( $this->request->get('search') !== null ) $querystring .= '&amp;search=' . $this->urlEncode( $this->request->get('search') );
		if( $this->request->get('select') !== null ) $querystring .= '&amp;select=' . $this->urlEncode( $this->request->get('select') );
		if( $this->request->get('os') !== null ) $querystring .= '&amp;os=' . $this->urlEncode( $this->request->get('os') );
		if( $this->request->get('browser') !== null ) $querystring .= '&amp;browser=' . $this->urlEncode( $this->request->get('browser') );
		if( $this->request->get('engine') !== null ) $querystring .= '&amp;engine=' . $this->urlEncode( $this->request->get('engine') );
		if( $this->request->get('keyword') !== null ) $querystring .= '&amp;keyword=' . $this->urlEncode( $this->request->get('keyword') );
		$querystring .= '&amp;page=';
		$href = $this->getIndexUrl( $controller, $action, $querystring );

		$html = '<div class="page">';
		$page = $this->request->get('page') !== null ? $this->request->get('page') : 1;
		$lastpage = ceil( $pageCount / $siteData['dispview'] );
		if( $lastpage < Config::MAX_PAGE ) {
			$maxpage = $lastpage;
			$cnt = 1;
		}
		else {
			$base = ceil( Config::MAX_PAGE / 2 );
			if( $page > $base ) {
				$maxpage = Config::MAX_PAGE + ( $page - $base );
				if( $maxpage > $lastpage ) {
					$maxpage = $lastpage;
				}
				$cnt = 1 + ( $page - $base );
				if( ( $maxpage - $cnt ) < Config::MAX_PAGE ) {
					while( ( $maxpage - $cnt+1 ) < Config::MAX_PAGE ) {
						$cnt--;
					}
				}
			}
			else {
				$maxpage = Config::MAX_PAGE;
				$cnt = 1;
			}
		}
		if( $page > 1 && $page <= $maxpage ) {
			$html .= '<a href="' . $href . '1' . '">&#8810;最初へ</a>';
			$html .= '<a href="' . $href . ( $page - 1 ) . '">&#8810;前の' . $siteData['dispview'] . '件</a>';
		}
		for( $i = $cnt; $i <= $maxpage; $i++ ) {
			if( $i == $page ) {
				if( $lastpage > 1 ) {
					$html .= '<span class="thispage">' . $i . '</span>';
				}
			}
			else {
				$html .= '<a href="' . $href . $i . '">'. $i . '</a>';
			}
		}
		if( $maxpage > $page ) {
			$html .= '<a href="' . $href . ( $page + 1 ) . '">次の' . $siteData['dispview'] . '件&#8811;</a>';
			$html .= '<a href="' . $href . $lastpage . '">最後へ&#8811;</a>';
		}
		$html .= '</div>';
		return $html;
	}

	public function menuSelectMonthTag() {
		$yyyyFrom = $this->session->get('yyyyFrom');
		$mmFrom = $this->session->get('mmFrom');
		$ddFrom = $this->session->get('ddFrom');
		$yyyyTo = $this->session->get('yyyyTo');
		$mmTo = $this->session->get('mmTo');
		$ddTo = $this->session->get('ddTo');
		$research = $this->_research;
		$controller = $this->controller;
		$action = $this->action;

		$convertAction = $this->_getConvertAction( $action );

		$lastMonths = $this->_getLastNextMonths( 'last', $yyyyFrom, $mmFrom );
		$nextMonths = $this->_getLastNextMonths( 'next', $yyyyFrom, $mmFrom );

		$lastDdFrom = $lastDdTo = $nextDdFrom = $nextDdTo = date( 'd' );

		if( $research === self::RESEARCH_DAY ) {
			if( $lastMonths['yyyy'] == date( 'Y' ) && $lastMonths['mm'] == date( 'm' ) ) {
				$nextDdFrom = Calendar::getLastDay( $nextMonths['yyyy'], $nextMonths['mm'] );
				$nextDdTo = $nextDdFrom;
			}
			elseif( $nextMonths['yyyy'] == date( 'Y' ) && $nextMonths['mm'] == date( 'm' ) ) {
				$lastDdFrom = Calendar::getLastDay( $lastMonths['yyyy'], $lastMonths['mm'] );
				$lastDdTo = $lastDdFrom;
			}
			else {
				$lastDdFrom = Calendar::getLastDay( $lastMonths['yyyy'], $lastMonths['mm'] );
				$lastDdTo = $lastDdFrom;
				$nextDdFrom = Calendar::getLastDay( $nextMonths['yyyy'], $nextMonths['mm'] );
				$nextDdTo = $nextDdFrom;
			}
		}
		else {
			$lastDdFrom = '01';
			$lastDdTo = Calendar::getLastDay( $lastMonths['yyyy'], $lastMonths['mm'] );
			$nextDdFrom = '01';
			$nextDdTo = Calendar::getLastDay( $nextMonths['yyyy'], $nextMonths['mm'] );
		}

		$clsName = '';
		if(
			$yyyyFrom === $yyyyTo &&
			$mmFrom === $mmTo &&
			$ddFrom === '01' &&
			$ddTo === Calendar::getLastDay( $yyyyFrom, $mmFrom )
		) {
			$clsName = 'set';
		}

		$html = '<a class="lmonth smonth" href="' . $this->getIndexUrl( $controller, $convertAction,
			'&amp;yyyy_from=' . $lastMonths['yyyy'] .
			'&amp;mm_from=' . $lastMonths['mm'] .
			'&amp;dd_from=' . $lastDdFrom .
			'&amp;yyyy_to=' . $lastMonths['yyyy'] .
			'&amp;mm_to=' . $lastMonths['mm'] .
			'&amp;dd_to=' . $lastDdTo ) .
			'" title="前月へ">&#8810;</a>';
		$html .= '<a class="tmonth smonth' . $clsName . '" href="' . $this->getIndexUrl( $controller, $convertAction,
			'&amp;yyyy_from=' . $yyyyFrom .
			'&amp;mm_from=' . $mmFrom .
			'&amp;dd_from=' . '01' .
			'&amp;yyyy_to=' . $yyyyFrom .
			'&amp;mm_to=' . $mmFrom .
			'&amp;dd_to=' . Calendar::getLastDay( $yyyyFrom, $mmFrom ) ) .
			'" title="' .
				$this->getZeroSuppress($yyyyFrom). '年'.
				$this->getZeroSuppress($mmFrom) . '月">' .
				$this->getZeroSuppress($yyyyFrom). '年'.
				$this->getZeroSuppress($mmFrom) . '月</a>';
		$html .= '<a class="nmonth smonth" href="' . $this->getIndexUrl( $controller, $convertAction,
			'&amp;yyyy_from=' . $nextMonths['yyyy'] .
			'&amp;mm_from=' . $nextMonths['mm'] .
			'&amp;dd_from=' . $nextDdFrom .
			'&amp;yyyy_to=' . $nextMonths['yyyy'] .
			'&amp;mm_to=' . $nextMonths['mm'] .
			'&amp;dd_to=' . $nextDdTo ) .
			'" title="次月へ">&#8811;</a>';

		return $html;
	}

	public function menuCalendarTag() {
		$ddCheckData = $this->result->get('ddCheckData');
		$yyyyFrom = $this->session->get('yyyyFrom');
		$mmFrom = $this->session->get('mmFrom');
		$ddFrom = $this->session->get('ddFrom');
		$yyyyTo = $this->session->get('yyyyTo');
		$mmTo = $this->session->get('mmTo');
		$ddTo = $this->session->get('ddTo');
		$research = $this->_research;
		$controller = $this->controller;
		$action = $this->action;

		$convertAction = $this->_getConvertAction( $action );

		$lastMonths = $this->_getLastNextMonths( 'last', $yyyyFrom, $mmFrom );
		$nextMonths = $this->_getLastNextMonths( 'next', $yyyyFrom, $mmFrom );

		$lastDdFrom = $lastDdTo = $nextDdFrom = $nextDdTo = date('d');

		if( $research === self::RESEARCH_DAY ) {
			if( $lastMonths['yyyy'] == date( 'Y' ) && $lastMonths['mm'] == date( 'm' ) ) {
				$nextDdFrom = Calendar::getLastDay( $nextMonths['yyyy'], $nextMonths['mm'] );
				$nextDdTo = $nextDdFrom;
			}
			elseif( $nextMonths['yyyy'] == date( 'Y' ) && $nextMonths['mm'] == date( 'm' ) ) {
				$lastDdFrom = Calendar::getLastDay( $lastMonths['yyyy'], $lastMonths['mm'] );
				$lastDdTo = $lastDdFrom;
			}
			else {
				$lastDdFrom = Calendar::getLastDay( $lastMonths['yyyy'], $lastMonths['mm'] );
				$lastDdTo = $lastDdFrom;
				$nextDdFrom = Calendar::getLastDay( $nextMonths['yyyy'], $nextMonths['mm'] );
				$nextDdTo = $nextDdFrom;
			}
		}
		else {
			$lastDdFrom = '01';
			$lastDdTo = Calendar::getLastDay( $lastMonths['yyyy'], $lastMonths['mm'] );
			$nextDdFrom = '01';
			$nextDdTo = Calendar::getLastDay( $nextMonths['yyyy'], $nextMonths['mm'] );
		}

		$now = $_SERVER['REQUEST_TIME'];
		$wday = 0;
		$mday = 1;
		$firstDay = getdate( mktime( 0, 0, 0, $mmFrom, 1, $yyyyFrom ) );

		$html  = '<table class="calen">';
		$html .= '<tr class="calentitle">';
		$html .= '<th class="aright" colspan="1">';
		$html .= '<a class="lmonth smonth" href="' . $this->getIndexUrl( $controller, $convertAction,
			'&amp;yyyy_from=' . $lastMonths['yyyy'] .
			'&amp;mm_from=' . $lastMonths['mm'] .
			'&amp;dd_from=' . $lastDdFrom .
			'&amp;yyyy_to=' . $lastMonths['yyyy'] .
			'&amp;mm_to=' . $lastMonths['mm'] .
			'&amp;dd_to=' . $lastDdTo ) .
			'" title="前月へ">&#8810;</a>';
		$html .= '</th>';
		$html .= '<th class="acenter" colspan="5">';
		$html .= '<a class="tmonth smonth" href="' . $this->getIndexUrl( $controller, $convertAction,
			'&amp;yyyy_from=' . $yyyyFrom .
			'&amp;mm_from=' . $mmFrom .
			'&amp;dd_from=' . '01' .
			'&amp;yyyy_to=' . $yyyyFrom .
			'&amp;mm_to=' . $mmFrom .
			'&amp;dd_to=' . Calendar::getLastDay( $yyyyFrom, $mmFrom ) ) .
			'" title="' .
			$this->getZeroSuppress( $yyyyFrom ) . '年'.
			$this->getZeroSuppress( $mmFrom ) . '月">' .
			$this->getZeroSuppress( $yyyyFrom ) . '年'.
			$this->getZeroSuppress( $mmFrom ) . '月</a>';
		$html .= '</th>';
		$html .= '<th class="aleft" colspan="1">';
		$html .= '<a class="nmonth smonth" href="'. $this->getIndexUrl( $controller, $convertAction,
			'&amp;yyyy_from=' . $nextMonths['yyyy'] .
			'&amp;mm_from=' . $nextMonths['mm'] .
			'&amp;dd_from=' . $nextDdFrom .
			'&amp;yyyy_to=' . $nextMonths['yyyy'] .
			'&amp;mm_to=' . $nextMonths['mm'] .
			'&amp;dd_to=' . $nextDdTo ) .
			'" title="次月へ">&#8811;</a>';
		$html .= '</th>';
		$html .= '</tr>';
		$html .= '<tr class="calenhead"><th>日</th><th>月</th><th>火</th><th>水</th><th>木</th><th>金</th><th>土</th></tr>';
		$html .= '<tr>';

		while( $wday < $firstDay['wday'] ) {
			$wday++;
			$html .= '<td>&nbsp;</td>';
		}
		while( checkdate( $mmFrom, $mday, $yyyyFrom ) ) {
			switch( $research ) {
				case self::RESEARCH_DAY:
					if( $mday == $ddFrom ) {
						$html .= '<td class="today">';
					}
					else {
						$html .= '<td class="' . $this->_getMenuWeekdayCssClass( $yyyyFrom, $mmFrom, $mday ) . '">';
					}
					break;
				case self::RESEARCH_MONTH:
					$html .= '<td class="today">';
					break;
				case self::RESEARCH_TERM:
					$termday = $this->getZeroPadding( $mday, 1 );
					if( $yyyyFrom . $mmFrom . $termday >= $yyyyFrom . $mmFrom . $ddFrom && $yyyyFrom . $mmFrom . $termday <= $yyyyTo . $mmTo . $ddTo ) {
						$html .= '<td class="today">';
					}
					else {
						$html .= '<td class="' . $this->_getMenuWeekdayCssClass( $yyyyFrom, $mmFrom, $mday ) . '">';
					}
					break;
			}
			$dispday = $mday;
			$mday = $this->getZeroPadding( $mday, 1 );
			if( $controller === 'admin' || ( !isset( $ddCheckData[$dispday] ) ) ) {
				$html .= '<div class="days">' . $dispday . '</div></td>';
			}
			elseif( $yyyyFrom . $mmFrom == date( 'Y', $now ) . date( 'm', $now ) ) {
				if( $mday > date( 'd', $now ) ) {
					$html .= '<div class="days">' . $dispday . '</div></td>';
				}
				else {
					$html .= '<div class="days">' . '<a href="' . $this->getIndexUrl( $controller, $convertAction,
						'&amp;yyyy_from=' . $yyyyFrom .
						'&amp;mm_from=' . $mmFrom .
						'&amp;dd_from=' . $mday .
						'&amp;yyyy_to=' . $yyyyFrom .
						'&amp;mm_to=' . $mmFrom .
						'&amp;dd_to=' . $mday) .
						'" title="' . $dispday . '日">' . $dispday . '</a></div></td>';
				}
			}
			elseif( $yyyyFrom . $mmFrom < date( 'Y', $now ) . date( 'm', $now ) ) {
				$html .= '<div class="days">' . '<a href="' . $this->getIndexUrl( $controller, $convertAction,
					'&amp;yyyy_from=' . $yyyyFrom .
					'&amp;mm_from=' . $mmFrom .
					'&amp;dd_from=' . $mday .
					'&amp;yyyy_to=' . $yyyyFrom .
					'&amp;mm_to=' . $mmFrom .
					'&amp;dd_to=' . $mday) .
					'" title="' . $dispday . '日">' . $dispday . '</a></div></td>';
			}
			else {
				$html .= '<div class="days">' . $dispday . '</div></td>';
			}
			$mday++; $wday++;
			if( ( $wday > 6 ) && ( checkdate( $mmFrom, $mday, $yyyyFrom ) ) ) {
				$html .= '</tr><tr>';
				$wday = 0;
			}
		}
		while( $wday++ < 7 ) $html .= '<td>&nbsp;</td>';
		$html .= '</tr>';
		$html .= '</table>';
		return $html;
	}

	public function menuResearchDayMonthTag( $dispResearch ) {
		$yyyyFrom = $this->session->get('yyyyFrom');
		$mmFrom = $this->session->get('mmFrom');
		$ddFrom = $this->session->get('ddFrom');
		$yyyyTo = $this->session->get('yyyyTo');
		$mmTo = $this->session->get('mmTo');
		$ddTo = $this->session->get('ddTo');
		$controller = $this->controller;
		$action = $this->action;

		$convertAction = $this->_getConvertAction( $action );

		$clsName = '';

		switch( $dispResearch ) {
			case self::RESEARCH_DAY:
				$title = '本日';
				if( $yyyyFrom.$mmFrom.$ddFrom == date( 'Ymd' ) && $yyyyTo.$mmTo.$ddTo == date( 'Ymd' ) ) {
					$clsName = 'set';
				}
				$yyyyFrom = date( 'Y' );
				$mmFrom = date( 'm' );
				$ddFrom = date( 'd' );
				$yyyyTo = date( 'Y' );
				$mmTo = date( 'm' );
				$ddTo = date( 'd' );
				break;
			case self::RESEARCH_MONTH:
				$title = '今月';
				if( $yyyyFrom.$mmFrom . $ddFrom == date( 'Ym' ) . '01' && $yyyyTo . $mmTo . $ddTo == date( 'Ym' ) . Calendar::getLastDay( date( 'Y' ), date( 'm' ) ) ) {
					$clsName = 'set';
				}
				$yyyyFrom = $yyyyTo = date( 'Y' );
				$mmFrom   = $mmTo   = date( 'm' );
				$ddFrom = '01';
				$ddTo = Calendar::getLastDay( $yyyyFrom, $mmFrom );
				break;
		}
		$html = '<a class="sterm' . $clsName . '" href="' .
			$this->getIndexUrl( $controller, $convertAction,
			'&amp;yyyy_from=' . $yyyyFrom .
			'&amp;mm_from=' . $mmFrom .
			'&amp;dd_from=' . $ddFrom .
			'&amp;yyyy_to=' . $yyyyTo .
			'&amp;mm_to=' . $mmTo .
			'&amp;dd_to=' . $ddTo ) .
			'" title="' . $title . '">' . $title . '</a>';
		return $html;
	}


	public function menuTermFormTag() {
		$yyyyFrom = $this->session->get('yyyyFrom');
		$mmFrom = $this->session->get('mmFrom');
		$ddFrom = $this->session->get('ddFrom');
		$yyyyTo = $this->session->get('yyyyTo');
		$mmTo = $this->session->get('mmTo');
		$ddTo = $this->session->get('ddTo');
		$research = $this->_research;
		$action = $this->action;
		$controller = $this->controller;

		$convertAction = $this->_getConvertAction( $action );

		$from_day = $this->session->get('yyyyFrom') . $this->session->get('mmFrom') . $this->session->get('ddFrom');
		$to_day = $this->session->get('yyyyTo') . $this->session->get('mmTo') . $this->session->get('ddTo');

		$html  = '<div class="queryterm">';
		$html .= '<form id="searchterm" action="' . $this->getIndexUrl( $controller, $convertAction ) . '" method="post">';
		$html .= '<div class="period">';
		$html .= '<div class="periodtxt">From:</div><div class="periodbox">' . $this->getMenuTermSelect('from') . '</div>';
		$html .= '</div>';
		$html .= '<div class="period">';
		$html .= '<div class="periodtxt">To:</div><div class="periodbox">' . $this->getMenuTermSelect('to') . '</div>';
		$html .= '</div>';
		$html .= '<div class="monthbtn">';
		$html .= $this->menuSelectMonthTag();
		$html .= '</div>';
		$html .= '<div class="termbtn cleft">';
		$html .= $this->menuResearchDayMonthTag( BaseHelper::RESEARCH_DAY );
		$html .= '</div>';
		$html .= '<div class="termbtn">';
		if(
			( $from_day == date( 'Ymd' ) && $to_day == date( 'Ymd' ) ) or
			( $yyyyFrom === $yyyyTo && $mmFrom === $mmTo && $ddFrom == '01' && $ddTo == Calendar::getLastDay( $yyyyFrom, $mmFrom ) )
		) {
			$html .= '<input type="submit" value="期間選択" class="termsub" />';
		}
		else {
			$html .= '<input type="submit" value="期間選択" class="termsubset" />';
		}
		$html .= '</div>';
		$html .= '</form>';
		$html .= '</div>';
		return $html;
	}

	public function menuTitlesTag() {
		$html = '';
		$html .= $this->_getMenuTitles( MenuConfig::$titles );
		echo $html;
	}

	public function headerTag() {
		$yyyyFrom = $this->session->get('yyyyFrom');
		$mmFrom = $this->session->get('mmFrom');
		$ddFrom = $this->session->get('ddFrom');
		$yyyyTo = $this->session->get('yyyyTo');
		$mmTo = $this->session->get('mmTo');
		$ddTo = $this->session->get('ddTo');
		$research = $this->_research;
		$siteData = $this->session->get('siteData');
		$controller = $this->controller;
		$action = $this->action;

		$html = '';

		$html .= '<table class="hval">';
		$html .= '<tr>';
		$html .= '<td class="label">サイト名</td>';
		$html .= '<td class="label">&nbsp;</td>';
		$html .= '<td class="value"><span class="hval"><a href="' . $this->getJumpUrl( $siteData['url'] ) . '" title="' . $this->escapeHtml( $siteData['url'] ) . '" target="_blank">' . $this->escapeHtml( $siteData['sitename'] ) . '</a></span></td>';
		$html .= '</tr>';
		if( $controller === 'research' && ( $action !== 'download' && $action !== 'log' ) ) {
			if( $research === self::RESEARCH_MONTH || $action === 'term' || $action === 'termstack' || $action === 'week' ) {
				$html .= '<tr>';
				$html .= '<td class="label">解析対象月</td>';
				$html .= '<td class="label">&nbsp;</td>';
				$html .= '<td class="value"><span class="hval">' . $yyyyFrom . '年' . $this->getZeroSuppress( $mmFrom ) . '月' . '</span></td>';
				$html .= '</tr>';
			}
			elseif( $research === self::RESEARCH_DAY ) {
				$html .= '<tr>';
				$html .= '<td class="label">解析対象日</td>';
				$html .= '<td class="label">&nbsp;</td>';
				$html .= '<td class="value"><span class="hval">' . $yyyyFrom . '年' . $this->getZeroSuppress( $mmFrom ) . '月' . $this->getZeroSuppress( $ddFrom ) . '日' . '</span></td>';
				$html .= '</tr>';
			}
			elseif( $research === self::RESEARCH_TERM ) {
				$html .= '<tr>';
				$html .= '<td class="label">解析対象期間</td>';
				$html .= '<td class="label">&nbsp;</td>';
				$html .= '<td class="value"><span class="hval">' .
					$yyyyFrom . '年' . $this->getZeroSuppress( $mmFrom ) . '月' . $this->getZeroSuppress( $ddFrom ) . '日～' .
					$yyyyTo . '年' . $this->getZeroSuppress( $mmTo ) . '月' . $this->getZeroSuppress( $ddTo ) . '日' . '</span></td>';
				$html .= '</tr>';
			}
		}
		$html .= '</table>';
		echo $html;
	}

	public function menuSelectHeaderTag() {
		$selectTitle = $this->session->get('selectTitle');
		$selectName = $this->session->get('selectName');
		$controller = $this->controller;
		$action = $this->action;

		$html = '';

		$systemMessage = SystemUtil::getSystemMessage();
		if( is_array( $systemMessage ) ) {
			$html .= '<div class="system_message">';
			foreach( $systemMessage as $message ) {
				$html .= '<p>' . $message . '</p>';
			}
			$html .= '</div>';
		}

		$actionTitle = $this->getActionTitle();

		$parent_title = $this->array_search_recursive( $actionTitle, MenuConfig::$titles );
		$i = 1;
		foreach( MenuConfig::$titles as $key => $value ) {
			if( $parent_title === $key ) break;
			$i++;
		}

		$html .= trim( $actionTitle ) !== '' ? '<h1 class="h1-title-' . $i . '">' . $actionTitle . '</h1>' : '';
		if( $controller === 'research' && $action !== 'download' ) {
			$selected = "";
			$convertAction = $this->_getConvertAction( $action );
			$html .= '<div class="cbuttons">';
			//$html .= '<img src="' . ImageList::$generalImages['back'] . '" width="16" height="16" alt="戻る" title="戻る" class="button" onclick="pageback()" />';
			$html .= '<img src="' . ImageList::$generalImages['reload'] . '" width="16" height="16" alt="更新" title="更新" class="button" onclick="pagereload()" />';
			$html .= '<span id="autoele"><img src="' . ImageList::$generalImages['auto'] . '" width="16" height="16" alt="自動更新" title="自動更新" class="button" onclick="autoStart();" /></span>';
			$html .= '<select id="autotime">';
			$html .= '<option value="30">30秒</option>';
			$html .= '<option value="60" selected>60秒</option>';
			$html .= '<option value="300">5分</option>';
			$html .= '<option value="600">10分</option>';
			$html .= '<option value="900">15分</option>';
			$html .= '<option value="1200">20分</option>';
			$html .= '<option value="1800">30分</option>';
			$html .= '</select>';
			$html .= '</div>';
		}
		$html .= '<div class="cleft"></div>';

		echo $html;
	}

	public function menuTermSelectTag( $fromto ) {
		echo $this->getMenuTermSelect( $fromto );
	}

	public function selectOptionTag( $optionList, $column ) {
		$siteData = $this->session->get('siteData');
		$inputValue = $this->request->get( $column ) !== null ? $this->request->get( $column ) : $siteData[$column];
		$html = '';
		foreach( $optionList as $key => $value ) {
			if( $key == $inputValue ) {
				$html .= '<option value="' . $this->escapeHtml( $key ) . '" selected="selected">' . $this->escapeHtml( $value ) . '</option>';
			}
			else {
				$html .= '<option value="' . $this->escapeHtml( $key ) . '">' . $this->escapeHtml( $value ) . '</option>';
			}
		}
		echo $html;
	}

	public function headNaviCalender() {
		$html  = '';
		$html .= '<div id="modal">';
		$html .= '<div class="navmonth">';
		$html .= $this->menuCalendarTag();
		$html .= '</div>';
		$html .= $this->menuTermFormTag();
		$html .= '<a id="mclose" class="button-link">閉じる</a>';
		$html .= '</div>';
		return $html;
	}

	public function headerMenu() {
		$yyyyFrom = $this->session->get('yyyyFrom');
		$mmFrom = $this->session->get('mmFrom');
		$action = $this->action;
		$convertAction = $this->_getConvertAction( $action );

		$html = '';
		$i = 1;
		$settingFlag = false;

		$html .= '<div class="navi-menu">';
		//$html .= $this->headNaviCalender();
		$html .= '<ul id="main-menu" class="sm sm-thk">';
		$html .= '<li><a id="mopen" class="button-link">カレンダー<span class="sub-arrow">+</span></a></li>';

		foreach( MenuConfig::$titles as $parent_title => $titles ) {
			$k = key( $titles );
			$controller = MenuConfig::$actionControllers[$k];
			$clsName = isset( MenuConfig::$actionStyleSheets[$k] ) ? MenuConfig::$actionStyleSheets[$k] : 'query';
			$clsName .= $convertAction == $k ? 'set' : '';

			if( $settingFlag === false && stripos( $parent_title, '設定' ) !== false ) {
				$html .= '<li>';
				$html .= '<a class="' . $clsName . '" href="' . $this->getIndexUrl( $controller, $k ) . '">設定</a>';
				$html .= '<ul class="sub-menu">';
				$settingFlag = true;
			}
			$html .= '<li class="menu-title-' . $i . '">';
			$html .= '<a class="' . $clsName . '" href="' . $this->getIndexUrl( $controller, $k ) . '">' . $parent_title . '</a>';

			if( stripos( $parent_title, 'ログアウト' ) !== false ) {
				$html .= '</li>';
			}

			if( count( MenuConfig::$titles[$parent_title] ) <= 1 ) continue;

			$html .= '<ul>';
			foreach( $titles as $key => $value ) {
				if( !isset( MenuConfig::$actionControllers[$key] ) ) continue;
				$controller = MenuConfig::$actionControllers[$key];
				$clsName = isset( MenuConfig::$actionStyleSheets[$key] ) ? MenuConfig::$actionStyleSheets[$key] : 'query';
				$clsName .= $convertAction == $key ? 'set' : '';
				$html .= '<li><a class="' . $clsName . '" href="' . $this->getIndexUrl( $controller, $key ) . '">' . $value . '</a></li>';
			}
			$html .= '</ul>';
			$html .= '</li>';
			$i++;
		}
		$html .= '</ul>';
		$html .= '</li>';
		$html .= '</ul>';
		$html .= '</div>';
		echo $html;
	}

	public function _checkMydomain( $referer, $mydomain ) {
		$ref = explode( '?', $referer );
		foreach( (array)$mydomain as $value ) {
			if( !empty( $ref[0] ) && !empty( $value ) ) {
				if( strpos( $ref[0], $value ) !== false ) {
					return true;
				}
			}
		}
		return false;
	}

	public function onlineUser() {
		$rcon = new ResearchController( $this->request, $this->session, $this->message, $this->result, $this->controller, $this->action );
		$conditions = $rcon->_initConditions( Config::NORMAL_ACCESS );
		$group = 'uid';
		$findOptions = array( 'condition' => $conditions, 'group' => $group );

		$from_timestamp = strtotime( date( 'Y-m-d H:i:s' ) . ' -' . Config::ONLINE_TIME . ' second' );
		$yyyyMmOptions = array(
			'yyyyFrom' => date( 'Y', $from_timestamp ),
			'mmFrom'   => date( 'm', $from_timestamp ),
			'ddFrom'   => date( 'd', $from_timestamp ),
			'yyyyTo'   => date( 'Y' ),
			'mmTo'     => date( 'm' ),
			'ddTo'     => date( 'd' )
		);
		$hhmissOptions = array(
			'hhFrom' => date( 'H', $from_timestamp ),
			'miFrom' => date( 'i', $from_timestamp ),
			'ssFrom' => date( 's', $from_timestamp ),
			'hhTo'   => date( 'H' ),
			'miTo'   => date( 'i' ),
			'ssTo'   => date( 's' )
		);

		$method = 'online';
		$rcon->_doResearch( $findOptions, null, $method, null, $yyyyMmOptions, $hhmissOptions );

		$html ='';
		if( $this->result->get('uniqueCount') > 0 ) {
			$html .= '<img src="' . ImageList::$generalImages['online-big'] . '" width="22" height="22" alt="' . $this->result->get('totalCount') . '">&nbsp;&nbsp;';
		}
		else {
			$html .= '<img src="' . ImageList::$generalImages['offline-big'] . '" width="22" height="22" alt="' . $this->result->get('totalCount') . '">&nbsp;&nbsp;';
		}
		$html .= '現在のオンラインユーザー：';
		$html .='<span>' . $this->result->get('uniqueCount') . '</span>';
		echo $html;
	}

	public function _ddCheck() {
		if( $this->controller === 'research' ) {
			$rcon = new ResearchController( $this->request, $this->session, $this->message, $this->result, $this->controller, $this->action );
			$yyyyMmOptions = array(
				'yyyyFrom' => $this->session->get('yyyyFrom'),
				'mmFrom' => $this->session->get('mmFrom'),
				'ddFrom' => '01',
				'yyyyTo' => $this->session->get('yyyyFrom'),
				'mmTo' => $this->session->get('mmFrom'),
				'ddTo' => Calendar::getLastDay( $this->session->get('yyyyFrom'), $this->session->get('mmFrom') )
			);
			$conditions = $rcon->_initConditions();
			$findOptions = array( 'condition' => $conditions, 'group' => 'dd' );
			$method = 'ddCheck';
			$rcon->_doResearch( $findOptions, null, $method, null, $yyyyMmOptions );
		}
	}

	private function _getLastNextMonths( $lastNext, $yyyy, $mm ) {
		$time_string = $yyyy . '-' . $mm . '-' . '01';
		$timestamp = $_SERVER['REQUEST_TIME'];
		switch( $lastNext ) {
			case 'last':
				$timestamp = strtotime( $time_string . '-1 month' );
				break;
			default:
				$timestamp = strtotime( $time_string . '+1 month' );
				break;
		}
		return array( 'yyyy' => date( 'Y', $timestamp ), 'mm' => date( 'm', $timestamp ) );
	}

	private function _getMenuWeekdayCssClass( $yyyy, $mm, $dd ) {
		$clsnm = 'weekday';
		$dates = getdate( mktime( 0, 0, 0, $mm, $dd, $yyyy ) );
		$wday = $dates['wday'];
		if( (int)$wday === 0 ) $clsnm = 'sun';
		if( (int)$wday === 6 ) $clsnm = 'sat';
		return $clsnm;
	}

	private function _getResearch() {
		$yyyyFrom = $this->session->get('yyyyFrom');
		$mmFrom = $this->session->get('mmFrom');
		$ddFrom = $this->session->get('ddFrom');
		$yyyyTo = $this->session->get('yyyyTo');
		$mmTo = $this->session->get('mmTo');
		$ddTo = $this->session->get('ddTo');

		$research = self::RESEARCH_TERM;
		if( $yyyyFrom . $mmFrom . $ddFrom === $yyyyTo . $mmTo . $ddTo ) $research = self::RESEARCH_DAY;
		if(
			( $yyyyFrom . $mmFrom === $yyyyTo . $mmTo ) &&
			( $ddFrom === '01' && $ddTo === Calendar::getLastDay( $yyyyFrom, $mmFrom ) )
		) {
			$research = self::RESEARCH_MONTH;
		}

		return $research;
	}

	private function _getConvertAction( $action ) {
		return isset( MenuConfig::$convertActions[$action] ) ? MenuConfig::$convertActions[$action] : $action;
	}

	private function _getMenuTitles( $menuTitles ) {
		$yyyyFrom = $this->session->get('yyyyFrom');
		$mmFrom = $this->session->get('mmFrom');
		$action = $this->action;
		$convertAction = $this->_getConvertAction( $action );

		$html = '';
		$i = 1;
		foreach( $menuTitles as $parent_title => $titles ) {
			$html .= '<div class="menuline">';
			$html .= '<div class="menu-title-' . $i . '">' . $parent_title . '</div>';
			foreach( $titles as $key => $value ) {
				if( !isset( MenuConfig::$actionControllers[$key] ) ) continue;
				$controller = MenuConfig::$actionControllers[$key];
				$clsName = isset( MenuConfig::$actionStyleSheets[$key] ) ? MenuConfig::$actionStyleSheets[$key] : 'query';
				$clsName .= $convertAction == $key ? 'set' : '';
				$html .= '<a class="' . $clsName . '" href="' . $this->getIndexUrl( $controller, $key ) . '">' . $value . '</a>';
			}
			$html .= '</div>';
			$i++;
		}
		return $html;
	}

	private function array_search_recursive( $search_element, $array ) {
		$recursive_func = function( $search_element, $array ) use ( &$recursive_func ) {
			foreach( $array as $key => $value ) {
				if( is_array( $value ) ) {
					if( $recursive_func( $search_element, $value ) !== false ) return $key;
				}
				if( $search_element == $value ) return $key;
			}
			return false;
		};
		return $recursive_func( $search_element, $array );
	}
}
?>
