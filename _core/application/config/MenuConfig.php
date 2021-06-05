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
class MenuConfig {
	public static $titles = array(
		'ダイジェスト' => array(
			'digest1'	=> 'ページダイジェスト',
			'digest2'	=> 'リンク元ダイジェスト',
			'visit'		=> 'ビジターログ'
		),
		'時間軸' => array(
			'time'		=> '時間別',
			'timestack'	=> '時間別アクセス推移',
			'term'		=> '日別',
			'termstack'	=> '日別アクセス推移',
			'week'		=> '曜日別'
		),
		'ページ集計' => array(
			'rank'		=> 'ページ',
			'pagein'	=> '入口ページ',
			'pageout'	=> '出口ページ (離脱率)',
			'bounce'	=> '直帰率',
			'rate'		=> '回遊率'
		),
		'クリック集計' => array(
			'clickrank'	=> 'リンククリック',
			'btnrank'	=> 'ボタンクリック',
			'adrank'	=> 'AdSense クリック',
			'adprank'	=> 'AdSense ページ',
			'adip'		=> 'AdSense IP'
		),
		'リンク元' => array(
			'referer'	=> 'リンク元',
			'host'		=> 'リンク元ホスト',
			'key'		=> '検索フレーズ',
			'word'		=> '検索ワード',
			'engine'	=> '検索エンジン',
			'crawler'	=> 'クローラー'
		),
		'ビジター' => array(
			'uid'		=> 'ビジターランキング',
			'ip'		=> 'IPアドレス',
			'remotehost'	=> 'リモートホスト',
			'domain'	=> 'ドメイン',
			'jpdomain'	=> '企業・学校等の法人',
			'country'	=> '国',
			'pref'		=> '都道府県'
		),
		'デバイス' => array(
			'brow'		=> 'ブラウザ',
			'os'		=> 'OS',
			'screenwh'	=> '画面解像度',
			'screencol'	=> '画面色数',
			'jsck'		=> 'Javascript / Cookie'
		),
		'ログ関連設定' => array(
			'download'	=> 'ログのダウンロード',
			'deletelog'	=> 'ログ容量確認 / ログ削除'
		),
		'名称設定' => array(
			'domainlist'	=> 'ドメイン名称設定',
			'pagelist'	=> 'ページ名称設定',
			'linklist'	=> 'リンク名称設定',
			'aliaslist'	=> 'ビジター名称設定'
		),
		'アクセス解析設定' => array(
			'setting'	=> 'アクセス解析設定',
			'tag'		=> '解析用タグ (Javascript)',
			'phpcode'	=> '解析用タグ (PHP)',
			'syssetting'	=> 'システム設定'
		),
		'ログアウト' => array(
			'logout'	=> 'ログアウト'
		)
	);

	public static $otherTitles = array(
		'install' => array(
			'step0'		=> 'インストール',
			'step1'		=> 'インストール - データベース接続設定',
			'step2'		=> 'インストール - サイト設定',
			'step3'		=> 'インストール - 完了',
			'step4'		=> 'インストール - PHPコード確認'
		 ),
		'upgrade' => array(
			'disp'		=> 'アップグレード',
			'upgrade'	=> 'アップグレード - 結果'
		 ),
		'login'	=> array(
			'login'		=> 'ログイン'
		 )
	);

	public static $actionControllers = array(
		'digest1'	=> 'research',
		'digest2'	=> 'research',
		'visit'		=> 'research',
		'time'		=> 'research',
		'timestack'	=> 'research',
		'term'		=> 'research',
		'termstack'	=> 'research',
		'week'		=> 'research',
		'uid'		=> 'research',
		'ip'		=> 'research',
		'remotehost'	=> 'research',
		'domain'	=> 'research',
		'jpdomain'	=> 'research',
		'country'	=> 'research',
		'pref'		=> 'research',
		'rank'		=> 'research',
		'pagein'	=> 'research',
		'pageout'	=> 'research',
		'clickrank'	=> 'research',
		'btnrank'	=> 'research',
		'bounce'	=> 'research',
		'rate'		=> 'research',
		'key'		=> 'research',
		'word'		=> 'research',
		'engine'	=> 'research',
		'host'		=> 'research',
		'referer'	=> 'research',
		'crawler'	=> 'research',
		'brow'		=> 'research',
		'os'		=> 'research',
		'screenwh'	=> 'research',
		'screencol'	=> 'research',
		'jsck'		=> 'research',
		'adrank'	=> 'research',
		'adprank'	=> 'research',
		'adip'		=> 'research',
		'download'	=> 'research',
		'domainlist'	=> 'admin',
		'pagelist'	=> 'admin',
		'linklist'	=> 'admin',
		'aliaslist'	=> 'admin',
		'tag'		=> 'admin',
		'phpcode'	=> 'admin',
		'jsdownload'	=> 'admin',
		'setting'	=> 'admin',
		'deletelog'	=> 'admin',
		'syssetting'	=> 'admin',
		'logout'	=> 'login'
	);

	public static $actionStyleSheets = array(
		'domainlist'	=> 'sadmin',
		'pagelist'	=> 'sadmin',
		'linklist'	=> 'sadmin',
		'aliaslist'	=> 'sadmin',
		'tag'		=> 'sadmin',
		'phpcode'	=> 'sadmin',
		'jsdownload'	=> 'sadmin',
		'setting'	=> 'sadmin',
		'download'	=> 'sadmin',
		'deletelog'	=> 'sadmin',
		'syssetting'	=> 'sadmin',
		'logout'	=> 'sback'
	);

	public static $convertActions = array(
		'time_detail'		=> 'visit',
		'uid_detail'		=> 'visit',
		'ip_user'		=> 'ip',
		'remotehost_user'	=> 'remotehost',
		'domain_user'		=> 'domain',
		'jpdomain_user'		=> 'jpdomain',
		'country_user'		=> 'country',
		'pref_user'		=> 'pref',
		'rank_user'		=> 'rank',
		'key_engine'		=> 'key',
		'key_user'		=> 'key',
		'engine_key'		=> 'engine',
		'engine_user'		=> 'engine',
		'host_user'		=> 'host',
		'crawler_user'		=> 'crawler',
		'referer_user'		=> 'referer',
		'bounce_user'		=> 'bounce',
		'rate_user'		=> 'rate',
		'clickrank_user'	=> 'clickrank',
		'btnrank_user'		=> 'btnrank',
		'pagein_user'		=> 'pagein',
		'pageout_user'		=> 'pageout',
		'os_user'		=> 'os',
		'os_ver'		=> 'os',
		'brow_ver'		=> 'brow',
		'brow_user'		=> 'brow',
		'screenwh_user'		=> 'screenwh',
		'screencol_user'	=> 'screencol',
		'jsck_user'		=> 'jsck',
		'adrank_user'		=> 'adrank',
		'adprank_user'		=> 'adprank',
		'adip_user'		=> 'adip',
		'domainedit'		=> 'domainlist',
		'pageedit'		=> 'pagelist',
		'linkedit'		=> 'linklist',
		'aliasedit'		=> 'aliaslist',
		'deletelog_confirm'	=> 'deletelog',
	);

	public static $getAbbrev = array(
		'action'	=> 'a',
		'controller'	=> 'c',
		'research'	=> 'r',
		'admin'		=> 'm',

		'login'		=> 'li',
		'logout'	=> 'lo',
		'digest1'	=> 'd1',
		'digest2'	=> 'd2',
		'visit'		=> 'vi',
		'time'		=> 'ti',
		'timestack'	=> 'ts',
		'term'		=> 'tm',
		'termstack'	=> 'tk',
		'week'		=> 'w',
		'uid'		=> 'u',
		'ip'		=> 'ip',
		'remotehost'	=> 'rh',
		'domain'	=> 'dm',
		'jpdomain'	=> 'jd',
		'country'	=> 'ct',
		'pref'		=> 'pf',
		'rank'		=> 'rk',
		'pagein'	=> 'pi',
		'pageout'	=> 'po',
		'clickrank'	=> 'ck',
		'btnrank'	=> 'bk',
		'rate'		=> 'rt',
		'bounce'	=> 'b',
		'key'		=> 'ky',
		'word'		=> 'wd',
		'engine'	=> 'eg',
		'host'		=> 'ht',
		'referer'	=> 'rf',
		'crawler'	=> 'cr',
		'brow'		=> 'bw',
		'os'		=> 'os',
		'screenwh'	=> 'sw',
		'screencol'	=> 'sc',
		'jsck'		=> 'jc',
		'adrank'	=> 'ak',
		'adprank'	=> 'ap',
		'adip'		=> 'ai',
		'download'	=> 'dl',
		'domainlist'	=> 'dt',
		'pagelist'	=> 'pt',
		'linklist'	=> 'lt',
		'aliaslist'	=> 'al',
		'tag'		=> 'tag',
		'jump'		=> 'jp',
		'phpcode'	=> 'php',
		'jsdownload'	=> 'js',
		'setting'	=> 'set',
		'deletelog'	=> 'del',
		'syssetting'	=> 'sys',

		'time_detail'		=> 'td',
		'uid_detail'		=> 'ud',
		'ip_user'		=> 'iu',
		'remotehost_user'	=> 'rhu',
		'domain_user'		=> 'du',
		'jpdomain_user'		=> 'jdu',
		'country_user'		=> 'cu',
		'pref_user'		=> 'pu',
		'rank_user'		=> 'rku',
		'key_engine'		=> 'ke',
		'key_user'		=> 'ku',
		'engine_key'		=> 'ek',
		'engine_user'		=> 'eu',
		'host_user'		=> 'hu',
		'crawler_user'		=> 'cru',
		'referer_user'		=> 'rfu',
		'rate_user'		=> 'rtu',
		'clickrank_user'	=> 'crku',
		'btnrank_user'		=> 'bu',
		'pagein_user'		=> 'piu',
		'pageout_user'		=> 'pou',
		'os_user'		=> 'osu',
		'os_ver'		=> 'osv',
		'brow_ver'		=> 'bwv',
		'brow_user'		=> 'bwu',
		'screenwh_user'		=> 'swu',
		'screencol_user'	=> 'scu',
		'jsck_user'		=> 'ju',
		'adrank_user'		=> 'adru',
		'adprank_user'		=> 'adpu',
		'adip_user'		=> 'adiu',
		'domainedit'		=> 'de',
		'pageedit'		=> 'pe',
		'linkedit'		=> 'le',
		'aliasedit'		=> 'ae',
		'deletelog_confirm'	=> 'delc',

		"select" => "se",
		"search" => "q"
	);

	public static $useCcchart = array(
		'time'		=> true,
		'timestack'	=> true,
		'termstack'	=> true,
		'week'		=> true
	);

	public static $usePiechart = array(
		'brow'		=> true,
		'os'		=> true,
		'rank'		=> true,
		'pagein'	=> true,
		'pageout'	=> true,
		'host'		=> true,
		'country'	=> true,
		'pref'		=> true
	);
}
?>
