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
class QueryConfig {
	// where 句の前の select 文
	public static $query = array(
		'ddCheck'		=> 'dd',
		'online'		=> 'DISTINCT uid',
		'totalAll'		=> 'uid,dd',
		'totalAllGroupBy'	=> 'COUNT(uid) AS id,uid,dd',
		'uniqueAll'		=> 'uid,dd,logtype,url,referer_host',
		'visit'			=> 'uid,dd,hh,mi,ss,logtype',
		'visit_actual'		=> 'uid,dd,hh,mi,ss,logtype,domain,country,pref,url,title,os,os_ver,browser,brow_ver,referer_host,referer_title,http_referer',
		'time_detail'		=> 'uid,dd,hh,mi,ss,logtype',
		'time_detail_actual'	=> 'uid,dd,hh,mi,ss,logtype,domain,country,pref,url,title,os,os_ver,browser,brow_ver,referer_host,referer_title,http_referer',
		'digest1' 		=> 'COUNT(sub.cnt) AS id,SUM(sub.cnt) AS uid,hh',
		//'digest2'		=> '不要',
		'time' 			=> 'COUNT(sub.cnt) AS id,SUM(sub.cnt) AS uid,hh',
		'timestack' 		=> 'COUNT(sub.cnt) AS id,SUM(sub.cnt) AS uid,hh',
		'term' 			=> 'COUNT(sub.cnt) AS id,SUM(sub.cnt) AS uid,dd',
		'termstack' 		=> 'COUNT(sub.cnt) AS id,SUM(sub.cnt) AS uid,dd',
		'week' 			=> 'COUNT(sub.cnt) AS id,SUM(sub.cnt) AS uid,weekday',
		'rank' 			=> 'COUNT(sub.cnt) AS id,SUM(sub.cnt) AS uid,url',
		'pagein' 		=> 'uid,dd,url',
		'pageout'		=> 'uid,dd,url',
		'bounce'		=> 'uid,dd,url',
		'rate'			=> 'COUNT(uid) AS id,uid,dd',
		'clickrank' 		=> 'COUNT(sub.cnt) AS id,SUM(sub.cnt) AS uid,url',
		'btnrank' 		=> 'COUNT(sub.cnt) AS id,SUM(sub.cnt) AS uid,url',
		'adip' 			=> 'COUNT(sub.cnt) AS id,SUM(sub.cnt) AS uid,remote_addr',
		'adrank' 		=> 'COUNT(sub.cnt) AS id,SUM(sub.cnt) AS uid,title',
		'adprank' 		=> 'COUNT(sub.cnt) AS id,SUM(sub.cnt) AS uid,referer_title',
		'crawler' 		=> 'COUNT(sub.cnt) AS id,SUM(sub.cnt) AS uid,crawler',
		'host'			=> 'COUNT(DISTINCT uid) AS id,referer_host',
		'key'			=> 'COUNT(DISTINCT uid) AS id,keyword,engine',
		'key_engine'		=> 'COUNT(DISTINCT uid) AS id,engine',
		'engine'		=> 'COUNT(DISTINCT uid) AS id,engine,keyword',
		'engine_key'		=> 'COUNT(DISTINCT uid) AS id,keyword',
		'word'			=> 'uid,keyword',
		'uid'			=> 'uid,dd,logtype',
		'uid_access'		=> 'uid,dd,logtype,pref,os,os_ver,browser,brow_ver',
		'uid_clicks'		=> 'uid,dd,logtype,pref,os,os_ver,browser,brow_ver',
		'ip' 			=> 'COUNT(sub.cnt) AS id,SUM(sub.cnt) AS uid,remote_addr',
		'remotehost' 		=> 'COUNT(sub.cnt) AS id,SUM(sub.cnt) AS uid,remote_host',
		'ip_to_domain'		=> 'DISTINCT domain,remote_addr',
		'host_to_domain'	=> 'DISTINCT domain,remote_host',
		'domain' 		=> 'COUNT(sub.cnt) AS id,SUM(sub.cnt) AS uid,domain',
		'jpdomain' 		=> 'COUNT(sub.cnt) AS id,SUM(sub.cnt) AS uid,jpdomain',
		'country' 		=> 'COUNT(sub.cnt) AS id,SUM(sub.cnt) AS uid,country',
		'pref' 			=> 'COUNT(sub.cnt) AS id,SUM(sub.cnt) AS uid,pref',
		'os' 			=> 'COUNT(sub.cnt) AS id,SUM(sub.cnt) AS uid,os',
		'os_ver' 		=> 'COUNT(sub.cnt) AS id,SUM(sub.cnt) AS uid,os_ver',
		'brow' 			=> 'COUNT(sub.cnt) AS id,SUM(sub.cnt) AS uid,browser',
		'brow_ver' 		=> 'COUNT(sub.cnt) AS id,SUM(sub.cnt) AS uid,brow_ver',
		'screenwh' 		=> 'COUNT(sub.cnt) AS id,SUM(sub.cnt) AS uid,screenwh',
		'screencol' 		=> 'COUNT(sub.cnt) AS id,SUM(sub.cnt) AS uid,screencol',
		'jsck' 			=> 'COUNT(sub.cnt) AS id,SUM(sub.cnt) AS uid,jsck',

		'host_total'		=> 'COUNT(uid) AS id,uid,dd,referer_host',
		'engine_total'		=> 'COUNT(uid) AS id,uid,dd,engine',
		'engine_key_total'	=> 'COUNT(uid) AS id,uid,dd,engine,keyword',
		'key_total'		=> 'COUNT(uid) AS id,uid,dd,keyword',
		'key_engine_total'	=> 'COUNT(uid) AS id,uid,dd,keyword,engine',

		// 各ユーザー一覧
		'rank_user' 		=> 'CONCAT(uid,dd) as uid,dd,os,os_ver,browser,brow_ver,pref,domain',
		'pagein_user' 		=> 'CONCAT(uid,dd) as uid,dd,url,os,os_ver,browser,brow_ver,pref,domain',
		'pageout_user'		=> 'CONCAT(uid,dd) as uid,dd,url,os,os_ver,browser,brow_ver,pref,domain',
		'bounce_user'		=> 'CONCAT(uid,dd) as uid,dd,url,os,os_ver,browser,brow_ver,pref,domain',
		'rate_user'		=> 'CONCAT(uid,dd) as uid,dd,os,os_ver,browser,brow_ver,pref,domain',
		'clickrank_user' 	=> 'CONCAT(uid,dd) as uid,dd,os,os_ver,browser,brow_ver,pref,domain',
		'btnrank_user' 		=> 'CONCAT(uid,dd) as uid,dd,os,os_ver,browser,brow_ver,pref,domain',
		'adip_user'		=> 'CONCAT(uid,dd) as uid,dd,url,os,os_ver,browser,brow_ver,pref,domain',
		'adrank_user' 		=> 'CONCAT(uid,dd) as uid,dd,url,os,os_ver,browser,brow_ver,pref,domain',
		'adprank_user'		=> 'CONCAT(uid,dd) as uid,dd,url,os,os_ver,browser,brow_ver,pref,domain',
		'host_user'		=> 'CONCAT(uid,dd) as uid,dd,os,os_ver,browser,brow_ver,pref,domain',
		'word_user'		=> 'CONCAT(uid,dd) as uid,dd,os,os_ver,browser,brow_ver,pref,domain',
		'key_user'		=> 'CONCAT(uid,dd) as uid,dd,os,os_ver,browser,brow_ver,pref,domain',
		'engine_user'		=> 'CONCAT(uid,dd) as uid,dd,os,os_ver,browser,brow_ver,pref,domain',
		'crawler_user'		=> 'CONCAT(uid,dd) as uid,dd,os,os_ver,browser,brow_ver,pref,domain',
		'ip_user'		=> 'CONCAT(uid,dd) as uid,dd,logtype,os,os_ver,browser,brow_ver,pref,remote_host,domain',
		'remotehost_user'	=> 'CONCAT(uid,dd) as uid,dd,logtype,os,os_ver,browser,brow_ver,pref,domain',
		'domain_user'		=> 'CONCAT(uid,dd) as uid,dd,os,os_ver,browser,brow_ver,pref,domain',
		'jpdomain_user'		=> 'CONCAT(uid,dd) as uid,dd,os,os_ver,browser,brow_ver,pref,domain',
		'country_user'		=> 'CONCAT(uid,dd) as uid,dd,os,os_ver,browser,brow_ver,pref,domain',
		'pref_user'		=> 'CONCAT(uid,dd) as uid,dd,os,os_ver,browser,brow_ver,pref,domain',
		'os_user'		=> 'CONCAT(uid,dd) as uid,dd,os,os_ver,browser,brow_ver,pref,domain',
		'brow_user'		=> 'CONCAT(uid,dd) as uid,dd,os,os_ver,browser,brow_ver,pref,domain',
		'screenwh_user'		=> 'CONCAT(uid,dd) as uid,dd,os,os_ver,browser,brow_ver,pref,domain',
		'screencol_user'	=> 'CONCAT(uid,dd) as uid,dd,os,os_ver,browser,brow_ver,pref,domain',
		'jsck_user'		=> 'CONCAT(uid,dd) as uid,dd,os,os_ver,browser,brow_ver,pref,domain'
	);

	// サブクエリ
	public static $subquery = array(
		'digest1'		=> 'COUNT(uid) AS cnt,hh',
		'time'			=> 'COUNT(uid) AS cnt,hh',
		'timestack'		=> 'COUNT(uid) AS cnt,hh',
		'term'			=> 'COUNT(uid) AS cnt,dd',
		'termstack'		=> 'COUNT(uid) AS cnt,dd',
		'week'			=> 'COUNT(uid) AS cnt,dd,weekday',
		'rank' 			=> 'COUNT(uid) AS cnt,dd,url',
		'clickrank' 		=> 'COUNT(uid) AS cnt,dd,url',
		'btnrank' 		=> 'COUNT(uid) AS cnt,dd,url',
		'adrank' 		=> 'COUNT(uid) AS cnt,dd,title',
		'adprank'		=> 'COUNT(uid) AS cnt,dd,referer_title',
		'adip'			=> 'COUNT(uid) AS cnt,dd,remote_addr',
		'crawler'		=> 'COUNT(uid) AS cnt,dd,crawler',
		'ip'			=> 'COUNT(uid) AS cnt,dd,remote_addr',
		'remotehost'		=> 'COUNT(uid) AS cnt,dd,remote_host',
		'domain'		=> 'COUNT(uid) AS cnt,dd,domain',
		'jpdomain'		=> 'COUNT(uid) AS cnt,dd,jpdomain',
		'country'		=> 'COUNT(uid) AS cnt,dd,country',
		'pref'			=> 'COUNT(uid) AS cnt,dd,pref',
		'os'			=> 'COUNT(uid) AS cnt,dd,os',
		'os_ver'		=> 'COUNT(uid) AS cnt,dd,os_ver',
		'brow'			=> 'COUNT(uid) AS cnt,dd,browser',
		'brow_ver'		=> 'COUNT(uid) AS cnt,dd,brow_ver',
		'screenwh'		=> 'COUNT(uid) AS cnt,dd,screenwh',
		'screencol'		=> 'COUNT(uid) AS cnt,dd,screencol',
		'jsck'			=> 'COUNT(uid) AS cnt,dd,jsck'
	);

	// カラムの生成に処理が必要なもの
	public static function makeQuery() {
		$column = array(
			'uniqueAllRef'	=> 'uid,dd,' . self::makeRefColumn(),
			'referer'	=> 'COUNT( DISTINCT CONCAT(uid,dd) ) AS id,' . self::makeRefColumn(),
			'referer_user'	=> 'CONCAT(uid,dd) as uid,os,os_ver,browser,brow_ver,pref,domain,' . self::makeRefColumn()
		);
		return $column;
	}

	// 一時テーブル作成してから select するもの (createTmpTableQuery と findTmpTableQuery は必ずセット)
	public static function createTmpTableQuery() {
		$column = array(
			'referer_total'	=> 'uid,dd,' . self::makeRefColumn()
		);
		return $column;
	}

	public static $findTmpTableQuery = array(
		'referer_total'		=> 'COUNT(uid) AS id,uid,dd,http_referer'
	);

	// ユニークアクセスとは別にクエリを発行してトータルのページビューを取得する必要があるものを SQL のカラムに変換
	public static $ActionToColumn = array(
		'referer'		=> 'http_referer',
		'host'			=> 'referer_host',
		'engine'		=> 'engine',
		'engine_key'		=> 'keyword',
		'key'			=> 'keyword',
		'key_engine'		=> 'engine'
	);

	// ユニークアクセスとは別にクエリを発行してトータルのページビューを取得する必要があるものの group by 句
	public static $ActionToGroupBy = array(
		'referer'		=> 'http_referer,dd,uid',
		'host'			=> 'referer_host,dd,uid',
		'engine'		=> 'engine,keyword,dd,uid',
		'engine_key'		=> 'keyword,engine,dd,uid',
		'key'			=> 'keyword,engine,dd,uid',
		'key_engine'		=> 'engine,keyword,dd,uid'
	);

	// ユーザー一覧のページビューを取得する際、group by 句が使えないもの
	public static $isTotalAll = array(
		'referer_user'		=> true,
		'host_user'		=> true,
		'engine_user'		=> true,
		'key_user'		=> true
	);

	// 以下の文字列を含むリファラはリンク元ページでクエリーストリングをちょん切ってホスト名でまとめる
	public static $shapeReferer = array(
		'://www.google.',
		'://www.bing.',
		'search.'
	);

	// Myドメイン取得
	public static function getLikelyMydomain() {
		$mydomain = array();

		$select = 'TRIM( SUBSTRING( url,1,INSTR( url,"/")+1 ) FROM SUBSTRING_INDEX( url,"/",3 )) AS url,
			"" AS id,
			"" AS sitename,
			"" AS loginid,
			"" AS pswd,
			"" AS dispview,
			"" AS oksecond,
			"" AS againsecond,
			"" AS sortkey,
			"" AS nocrawler,
			"" AS filter,
			"" AS cookiekey,
			"" AS created_on,
			"" AS updated_on';
		$site = new site();
		$data = $site->find( $select );
		$mydomain[] = $data['url'];

		$select = 'DISTINCT( TRIM( SUBSTRING( url,1,INSTR(url,"/")+1 ) FROM SUBSTRING_INDEX( url,"/",3 ))) AS url,
			"" AS id,
			"" AS title,
			"" AS logtype,
			"" AS created_on,
			"" AS updated_on';
		$conditions = array( '
			logtype = 0
			AND url LIKE "%://%"
			AND url NOT LIKE "%.google%"
			AND url NOT LIKE "%.yahoo.%"
			AND url NOT LIKE "%.goo.ne.jp%"
		' );

		$title = new title();
		$data = $title->findAll( $select, array( 'condition' => $conditions ) );
		foreach( $data as $value ) {
			$mydomain[] = $value['url'];
		}
		return array_unique( $mydomain );
	}

	private static function makeRefColumn() {
		$cases = 'CASE ';
		$mydomains = self::getLikelyMydomain();
		foreach( $mydomains as $mydomain ) {
			if( !empty( $mydomain ) ) {
				$cases .= 'WHEN http_referer LIKE "%' . $mydomain . '%" THEN NULL ';
			}
		}

		$cases .= 'WHEN referer_title="' . Config::DIRECT_ACCESS . '" THEN NULL WHEN referer_title NOT LIKE "%://%" THEN ';
		$shape = 'CONCAT( SUBSTRING( http_referer, 1, INSTR( http_referer, "/" ) +1 ),CASE WHEN referer_host <> "" THEN referer_host ELSE NULL END, "/" )';
		$whens = '';
		foreach( self::$shapeReferer as $value ) {
			$whens .= ' WHEN http_referer LIKE "%'. $value .'%" THEN ' . $shape;
		}
		return $cases . $shape . $whens . ' ELSE http_referer END AS http_referer';
	}
}
