<?php
/* Woothee でクローラーと判別できなかったもののみ記述 */

class CrawlerList {
	/* UserAgent */
	public static $crawler = array(
		'Y!J-'				=> 'Yahoo!',
		'wing/2'			=> 'Yahoo!',
		'hatenascreenshot'		=> 'Hatena Screenshot',
		'sbooksnet'			=> 'SBooksNet',
		'archive.org_bot'		=> 'Internet Archive',
		'WeSEE_Bot'			=> 'WeSEE',
		'YandexBot'			=> 'Yandex',
		'ICC-Crawler'			=> 'NICT ICC-Crawler',
		'SMTBot/'			=> 'SMTBot',
		'TinEye-bot/'			=> 'TinEye-bot',
		'webscraper'			=> 'web scraper',
		'Scrapy/'			=> 'Scrapy',
		'MJ12bot/'			=> 'MJ12bot',
		'yacybot'			=> 'yacybot',
		'MsnBot-Media'			=> 'MsnBot',
		'archive.org_bot'		=> 'archive.org',
		'Nutch Crawler'			=> 'Nutch Crawler',
		'googlepagespeedinsights'	=> 'Google Page Speed Insights',
		'Crawler'			=> 'Crawler',
	);

	/* IP Address */
	public static $crawlerIP = array(
		'114.179.9.16/29'	=> 'TrendMicro',
		'118.22.0.192/29'	=> 'TrendMicro',
		'118.22.4.16/29'	=> 'TrendMicro',
		'128.241.0.0/16'	=> 'TrendMicro',
		'150.70.0.0/16'		=> 'TrendMicro',
		'180.43.188.240/29'	=> 'TrendMicro',
		'210.225.198.0/24'	=> 'TrendMicro',
		'216.104.15.0/24'	=> 'TrendMicro',
		'60.32.133.240/28'	=> 'TrendMicro',
		'66.180.80.0/20'	=> 'TrendMicro',
		'66.35.255.0/24'	=> 'TrendMicro'
	);

	/* HOST */
	public static $crawlerHOST = array(
		//'.amazonaws.com$'	=> 'Amazonaws',
		'.asianetcom.net$'	=> 'Baiduspider',
		'.nmsrv.com$'		=> 'GTmetrix',
		'.fc2.com$'		=> 'FC2',
		'.blogmura.com$'	=> 'にほんブログ村',
		'.doramix.com$'		=> 'ブログ王'
	);
}
