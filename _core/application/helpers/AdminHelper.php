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
class AdminHelper extends BaseHelper {
	public function __construct( ThkRequest $request, ThkSession $session, ThkMessage $message, ThkResult $result=null, $controller, $action ) {
		parent::__construct( $request, $session, $message, $result, $controller, $action );
	}

	public function titleCount() {
		$titleData = $this->result->get('titleData');
		echo $this->getFormatNumber( count( $titleData ) );
	}

	public function aliasCount() {
		$aliasData = $this->result->get('aliasData');
		echo $this->getFormatNumber( count( $aliasData ) );
	}

	public function domainCount() {
		$domainData = $this->result->get('domainData');
		echo $this->getFormatNumber( count( $domainData ) );
	}

	public function existLogCount() {
		$existLog = $this->result->get('existLog');
		echo $this->getFormatNumber( count( $existLog ) );
	}

	public function selectExistLogCount() {
		$existLog = $this->session->get('existLog');
		echo $this->getFormatNumber( count( $existLog ) );
	}

	public function siteName() {
		$siteData = $this->session->get('siteData');
		echo $this->request->get('sitename') !== null ? $this->request->get('sitename') : $siteData['sitename'];
	}

	public function url() {
		$siteData = $this->session->get('siteData');
		$url = $this->request->get('url') !== null ? $this->request->get('url') : $siteData['url'];
		if( is_writable( SETTING_DIR ) ) {
			echo '<input id="url" maxlength="100" name="url" type="text" value="'. $this->escapeHtml( $url ). '" placeholder="URL" />';
		}
		else {
			echo '<input id="url" class="readonly" maxlength="100" name="url" type="text" value="'. $this->escapeHtml( $url ). '" readonly="readonly" placeholder="URL" />';
		}
	}

	public function loginID() {
		$siteData = $this->session->get('siteData');
		echo $this->request->get('loginid') !== null ? $this->request->get('loginid') : $siteData['loginid'];
	}

	public function settingDirWritable() {
		$writable = ( is_writable(SETTING_DIR) ) ? true : false;
		echo $writable == false ? 'checked="checked"' : '';
	}

	public function filter() {
		$siteData = $this->session->get('siteData');
		echo $this->request->get('filter') !== null ? $this->request->get('filter') : $siteData['filter'];
	}

	public function noCount() {
		if( $this->checkGet() ) $nocount = isset($_COOKIE[Config::getCookieKeyAdminNocount()]) ? Config::ON : Config::OFF;
		if( $this->checkPost() ) $nocount = $this->request->get('nocount') == Config::ON ? Config::ON : Config::OFF;
		echo $nocount === Config::ON ? 'checked="checked"' : '';
	}

	public function sortKey( $key = Config::ON ) {
		$siteData = $this->session->get('siteData');
		if( $this->checkGet() ) $sortkey = $siteData['sortkey'] === Config::ON ? Config::ON : Config::OFF;
		if( $this->checkPost() ) $sortkey = $this->request->get('sortkey') === Config::ON ? Config::ON : Config::OFF;
		if( $key === Config::ON ) {
			echo $sortkey === Config::ON ? 'checked="checked"' : '';
		}
		else {
			echo $sortkey === Config::OFF ? 'checked="checked"' : '';
		}
	}

	public function noCrawler() {
		$siteData = $this->session->get('siteData');
		if( $this->checkGet() ) $nocrawler = $siteData['nocrawler'] === Config::ON ? Config::ON : Config::OFF;
		if( $this->checkPost() ) $nocrawler = $this->request->get('nocrawler') === Config::ON ? Config::ON : Config::OFF;
		echo $nocrawler === Config::ON ? 'checked="checked"' : '';
	}

	public function version() {
		$version = $this->session->get('version');
		echo $version;
	}

	public function systemData( $column ) {
		$systemData = $this->result->get('systemData');
		$setValue = $this->request->get($column) !== null ? $this->request->get($column) : $systemData[$column];
		if( $column === 'apploglevel' || $column === 'timezone' ) {
			$html = '';
			$select = $column === 'apploglevel' ? SettingConfig::$appLogLevels : SettingConfig::$timeZone;
			foreach( $select as $key => $value ) {
				if( $key === $setValue ) {
					$html .= '<option value="' . $this->escapeHtml( $key ) . '" selected="selected">' . $this->escapeHtml( $value ) . '</option>';
				}
				else {
					$html .= '<option value="' . $this->escapeHtml( $key ) . '">' . $this->escapeHtml( $value ) . '</option>';
				}
			}
			echo $html;
		}
		else {
			echo $setValue;
		}
	}

	public function pageTag( $pageCount=null ) {
		$action = $this->action;
		switch( $action ) {
			case 'aliaslist':
				$pageCount = count( $this->result->get('aliasData') );
				break;
			case 'domainlist':
				$pageCount = count( $this->result->get('domainData') );
				break;
			case 'pagelist':
			case 'linklist':
				$pageCount = count( $this->result->get('titleData') );
				break;
		}
		return parent::pageTag( $pageCount );
	}

	public function resultAliasListTag() {
		$action = $this->action;
		$siteData = $this->session->get('siteData');
		$aliasData = $this->result->get('aliasData');
		$page = $this->request->get('page') !== null ? $this->request->get('page') : 1;

		$html = '';

		$i = 0;
		$pageCount = 1;
		foreach( $aliasData as $data ) {
			if( $pageCount >= $siteData['dispview'] * ( $page - 1 ) + 1 && $pageCount <= $siteData['dispview'] * $page ) {
				$html .= '<tr>';
				$html .= '<td class="' . $this->getEvenClass($i) . '"><input type="checkbox" class="checkbox" name="delete[]" value="' . $this->escapeHtml($data['uid']) . '" /></td>';
				$html .= '<td class="' . $this->getEvenClass($i) . '"><a href="' . $this->getIndexUrl('research', 'uid_detail', '&amp;select=' . $this->urlEncode($data['uid'])). '">' . $this->escapeHtml($data['uid']) . '</a></td>';
				$html .= '<td class="' . $this->getEvenClass($i) . '"><a href="' . $this->getIndexUrl('research', 'uid_detail', '&amp;select=' . $this->urlEncode($data['uid'])). '">' . $this->escapeHtml($data['name']) . '</a></td>';
				$html .= '<td class="' . $this->getEvenClass($i) . ' acenter"><input type="button" value="設定" onclick="location.href=\'' . $this->getIndexUrl('admin', 'aliasedit', '&amp;select=' . $this->urlEncode($data['uid'])) . '\'" /></td>';
				$html .= '</tr>';
				if( $i == $siteData['dispview'] ) {
					break;
				}
				++$i;
			}
			++$pageCount;
		}
		echo $html;
	}

	public function resultDomainListTag() {
		$action = $this->action;
		$siteData = $this->session->get('siteData');
		$domainData = $this->result->get('domainData');
		$page = $this->request->get('page') !== null ? $this->request->get('page') : 1;

		$html = '';

		$i = 0;
		$pageCount = 1;
		foreach( $domainData as $data ) {
			if( $pageCount >= $siteData['dispview'] * ( $page - 1 ) + 1 && $pageCount <= $siteData['dispview'] * $page ) {
				$html .= '<tr>';
				$html .= '<td class="' . $this->getEvenClass($i) . '"><input type="checkbox" class="checkbox" name="delete[]" value="' . $this->escapeHtml( $data['domain'] ) . '" /></td>';
				$html .= '<td class="' . $this->getEvenClass($i) . '">' . $this->escapeHtml( $data['domain'] ) . '</td>';
				$html .= '<td class="' . $this->getEvenClass($i) . '">' . $this->escapeHtml( $data['name'] ) . '</td>';
				$html .= '<td class="' . $this->getEvenClass($i) . ' acenter"><input type="button" value="設定" onclick="location.href=\'' . $this->getIndexUrl('admin', 'domainedit', '&amp;select=' . $this->urlEncode( $data['domain'] ) ) . '\'" /></td>';
				$html .= '</tr>';
				if( $i == $siteData['dispview'] ) {
					break;
				}
				++$i;
			}
			++$pageCount;
		}
		echo $html;
	}

	public function resultTitleListTag() {
		$action = $this->action;
		$siteData = $this->session->get('siteData');
		$titleData = $this->result->get('titleData');
		$page = $this->request->get('page') !== null ? $this->request->get('page') : 1;

		$nextact = ( $action === 'pagelist' ) ? 'pageedit' : 'linkedit';

		$html = '';

		$i = 0;
		$pageCount = 1;
		foreach( $titleData as $data ) {
			if( $pageCount >= $siteData['dispview'] * ( $page - 1 ) + 1 && $pageCount <= $siteData['dispview'] * $page ) {
				$html .= '<tr>';
				$html .= '<td class="' . $this->getEvenClass($i) . '"><input type="checkbox" class="checkbox" name="delete[]" value="' . $data['id'] . '" /></td>';
				$html .= '<td class="' . $this->getEvenClass($i) . '">' . $this->escapeHtml( $this->urlDecode( $data['title'] ) ) . '</td>';
				$html .= '<td class="' . $this->getEvenClass($i) . '"><input type="text" class="nbinput ' . $this->getEvenClass($i) . '" value="' . $this->escapeHtml( $this->urlDecode( $data['url'] ) ) . '"></td>';
				$html .= '<td class="' . $this->getEvenClass($i) . ' acenter"><input type="button" value="設定" onclick="location.href=\'' . $this->getIndexUrl('admin', $nextact, '&amp;select=' . $data['id'] ) . '\'" /></td>';
				$html .= '</tr>';
				if( $i == $siteData['dispview'] ) {
					break;
				}
				++$i;
			}
			++$pageCount;
		}
		echo $html;
	}

	public function resultExistLogListTag( $confirm=false ) {
		$existLog = $confirm ? $this->session->get('existLog') : $this->result->get('existLog');
		$existLogDataSize = $confirm ? $this->session->get('existLogDataSize') : $this->result->get('existLogDataSize');

		$html = '';

		foreach( $existLog as $key => $log ) {
			$html .= '<tr>';
			$html .= '<td>';
			if( !$confirm ) {
				$html .= '<input type="checkbox" class="checkbox" name="delete[]" value="'. $log. '" />';
			}
			else {
				$html .= '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQBAMAAADt3eJSAAAAA3NCSVQICAjb4U/gAAAAMFBMVEVQjgpdmBdnpBxvrxd2tSB+vCaDxxuQx0CO1Byb3ims5kfJ3bXP8ZXo89bz+ez///9B+UUdAAAAEHRSTlP///////////////////8A4CNdGQAAAGFJREFUCJlj+A8FDKiMf2ugjFed58GMfzM65zL82/f//8uMjnUMT1nv/ytN63zPsEFQ+7lp2rz/DM8ZhZxdw98DdRUIKbvUgsz5zqRsfh9sYKFSLMTk76L3oVbsxWIpEAAAK9RmwDbbuvkAAAAASUVORK5CYII=" width="16" height="16" alt="○" />';
			}
			$html .= '</td>';
			$html .= '<td>' . $this->escapeHtml( substr( $log, 0, 4 ) ) . '年' . $this->escapeHtml( $this->getZeroSuppress( substr( $log, 4 ) ) ) . '月分' . '</td>';
			$html .= '<td>' . $this->escapeHtml( $this->getFormatDataSize( $existLogDataSize[$key] ) ) . '</td>';
			$html .= '</tr>';
		}
		echo $html;
	}

	public function jumpLink() {
		return $this->escapeHtml( $this->getJumpLink( $this->result->get('link') ) );
	}
}
?>
