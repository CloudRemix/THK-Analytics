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
class SettingConfig {
	public static $okSeconds = array(
		'10' => '10秒',
		'20' => '20秒',
		'30' => '30秒',
		'60' => '1分',
		'120' => '2分',
		'300' => '5分',
		'600' => '10分',
		'900' => '15分',
		'1800' => '30分',
		'3600' => '1時間',
		'7200' => '2時間'
	);

	public static $againSeconds = array(
		'10' => '10秒',
		'20' => '20秒',
		'30' => '30秒',
		'60' => '1分',
		'120' => '2分',
		'300' => '5分',
		'600' => '10分',
		'900' => '15分',
		'1800' => '30分',
		'3600' => '1時間',
		'7200' => '2時間'
	);

	public static $dispViews = array(
		'10' => '10件',
		'20' => '20件',
		'30' => '30件',
		'40' => '40件',
		'50' => '50件',
		'60' => '60件',
		'70' => '70件',
		'80' => '80件',
		'90' => '90件',
		'100' => '100件'
	);

	public static $appLogLevels = array(
		'NONE' => '出力しない',
		'FATAL' => '致命的エラー',
		'ERROR' => 'エラー',
		'WARNING' => '警告',
		'INFO' => 'インフォメーション',
		'DEBUG' => 'デバッグ',
		'ALL' => '全て'
	);

	public static $timeZone = array(
		'default'			=> 'デフォルト',
		'Africa/Cairo'			=> 'Africa/Cairo (カイロ)',
		'Africa/Casablanca'		=> 'Africa/Casablanca (カサブランカ)',
		'Africa/Johannesburg'		=> 'Africa/Johannesburg (ハラーレ、プレトリア)',
		'Africa/Lagos'			=> 'Africa/Lagos (西中央アフリカ)',
		'Africa/Nairobi'		=> 'Africa/Nairobi (ナイロビ)',
		'Africa/Windhoek'		=> 'Africa/Windhoek (ウィントフック)',
		'America/Anchorage'		=> 'America/Anchorage (アラスカ)',
		'America/Argentina/Buenos_Aires'=> 'America/Argentina/Buenos_Aires (ブエノスアイレス)',
		'America/Asuncion'		=> 'America/Asuncion (アスンシオン)',
		'America/Bogota'		=> 'America/Bogota (ボゴタ、リマ、キト)',
		'America/Caracas'		=> 'America/Caracas (カラカス)',
		'America/Cayenne'		=> 'America/Cayenne (カイエンヌ、フォルタレザ)',
		'America/Chicago'		=> 'America/Chicago (中部標準時(米国およびカナダ))',
		'America/Chihuahua'		=> 'America/Chihuahua (チワワ、ラパス、マサトラン)',
		'America/Cuiaba'		=> 'America/Cuiaba (クイアバ)',
		'America/Denver'		=> 'America/Denver (山地標準時(米国およびカナダ))',
		'America/Godthab'		=> 'America/Godthab (グリーンランド)',
		'America/Guatemala'		=> 'America/Guatemala (中央アメリカ)',
		'America/Halifax'		=> 'America/Halifax (大西洋標準時(カナダ))',
		'America/Indiana/Indianapolis'	=> 'America/Indiana/Indianapolis (インディアナ東部)',
		'America/La_Paz'		=> 'America/La_Paz (ジョージタウン、ラパス、マナウス、サンフアン)',
		'America/Los_Angeles'		=> 'America/Los_Angeles (太平洋標準時(米国およびカナダ))',
		'America/Mexico_City'		=> 'America/Mexico_City (グアダラハラ、メキシコシティ、モンテレー)',
		'America/Montevideo'		=> 'America/Montevideo (モンテビデオ)',
		'America/New_York'		=> 'America/New_York (東部標準時(米国およびカナダ))',
		'America/Phoenix'		=> 'America/Phoenix (アリゾナ)',
		'America/Regina'		=> 'America/Regina (サスカチュワン)',
		'America/Santa_Isabel'		=> 'America/Santa_Isabel (バハカリフォルニア)',
		'America/Santiago'		=> 'America/Santiago (サンチアゴ)',
		'America/Sao_Paulo'		=> 'America/Sao_Paulo (ブラジリア)',
		'America/St_Johns'		=> 'America/St_Johns (ニューファンドランド)',
		'Asia/Almaty'			=> 'Asia/Almaty (アスタナ)',
		'Asia/Amman'			=> 'Asia/Amman (アンマン)',
		'Asia/Baghdad'			=> 'Asia/Baghdad (バグダッド)',
		'Asia/Baku'			=> 'Asia/Baku (バクー)',
		'Asia/Bangkok'			=> 'Asia/Bangkok (バンコク、ハノイ、ジャカルタ)',
		'Asia/Beirut'			=> 'Asia/Beirut (ベイルート)',
		'Asia/Colombo'			=> 'Asia/Colombo (スリジャヤワルダナプラコッテ)',
		'Asia/Damascus'			=> 'Asia/Damascus (ダマスカス)',
		'Asia/Dhaka'			=> 'Asia/Dhaka (ダッカ)',
		'Asia/Dubai'			=> 'Asia/Dubai (アブダビ、マスカット)',
		'Asia/Irkutsk'			=> 'Asia/Irkutsk (イルクーツク)',
		'Asia/Jerusalem'		=> 'Asia/Jerusalem (エルサレム)',
		'Asia/Kabul'			=> 'Asia/Kabul (カブール)',
		'Asia/Karachi'			=> 'Asia/Karachi (イスラマバード、カラチ)',
		'Asia/Kathmandu'		=> 'Asia/Kathmandu (カトマンズ)',
		'Asia/Kolkata'			=> 'Asia/Kolkata (チェンナイ、コルカタ、ムンバイ、ニューデリー)',
		'Asia/Krasnoyarsk'		=> 'Asia/Krasnoyarsk (クラスノヤルスク)',
		'Asia/Magadan'			=> 'Asia/Magadan (マガダン)',
		'Asia/Novosibirsk'		=> 'Asia/Novosibirsk (ノヴォシビルスク)',
		'Asia/Rangoon'			=> 'Asia/Rangoon (ヤンゴン(ラングーン))',
		'Asia/Riyadh'			=> 'Asia/Riyadh (クエート、リヤド)',
		'Asia/Seoul'			=> 'Asia/Seoul (ソウル)',
		'Asia/Shanghai'			=> 'Asia/Shanghai (北京、重慶、香港特別行政区、ウルムチ)',
		'Asia/Singapore'		=> 'Asia/Singapore (クアラルンプール、シンガポール)',
		'Asia/Taipei'			=> 'Asia/Taipei (台北)',
		'Asia/Tashkent'			=> 'Asia/Tashkent (タシケント)',
		'Asia/Tbilisi'			=> 'Asia/Tbilisi (トビリシ)',
		'Asia/Tehran'			=> 'Asia/Tehran (テヘラン)',
		'Asia/Tokyo'			=> 'Asia/Tokyo (大阪、札幌、東京)',
		'Asia/Ulaanbaatar'		=> 'Asia/Ulaanbaatar (ウランバートル)',
		'Asia/Vladivostok'		=> 'Asia/Vladivostok (ウラジオストク)',
		'Asia/Yakutsk'			=> 'Asia/Yakutsk (ヤクーツク)',
		'Asia/Yekaterinburg'		=> 'Asia/Yekaterinburg (エカテリンブルグ)',
		'Asia/Yerevan'			=> 'Asia/Yerevan (エレバン)',
		'Atlantic/Azores'		=> 'Atlantic/Azores (アゾレス)',
		'Atlantic/Cape_Verde'		=> 'Atlantic/Cape_Verde (カーボベルデ諸島)',
		'Atlantic/Reykjavik'		=> 'Atlantic/Reykjavik (モンロビア、レイキャビク)',
		'Australia/Adelaide'		=> 'Australia/Adelaide (アデレード)',
		'Australia/Brisbane'		=> 'Australia/Brisbane (ブリスベン)',
		'Australia/Darwin'		=> 'Australia/Darwin (ダーウィン)',
		'Australia/Hobart'		=> 'Australia/Hobart (ホバート)',
		'Australia/Perth'		=> 'Australia/Perth (パース)',
		'Australia/Sydney'		=> 'Australia/Sydney (キャンベラ、メルボルン、シドニー)',
		'Etc/GMT'			=> 'Etc/GMT (協定世界時)',
		'Etc/GMT+11'			=> 'Etc/GMT+11 (協定世界時-11)',
		'Etc/GMT+12'			=> 'Etc/GMT+12 (国際日付変更線(西側))',
		'Etc/GMT+2'			=> 'Etc/GMT+2 (協定世界時-2)',
		'Etc/GMT-12'			=> 'Etc/GMT-12 (協定世界時+12)',
		'Europe/Berlin'			=> 'Europe/Berlin (アムステルダム、ベルリン、ベルン、ローマ、ストックホルム、ウィーン)',
		'Europe/Budapest'		=> 'Europe/Budapest (ベオグラード、ブラチスラバ、ブダペスト、リュブリャナ、プラハ)',
		'Europe/Istanbul'		=> 'Europe/Istanbul (アテネ、ブカレスト、イスタンブール)',
		'Europe/Kiev'			=> 'Europe/Kiev (ヘルシンキ、キエフ、リガ、ソフィア、タリン、ビリニュス)',
		'Europe/London'			=> 'Europe/London (ダブリン、エジンバラ、リスボン、ロンドン)',
		'Europe/Minsk'			=> 'Europe/Minsk (ミンスク)',
		'Europe/Moscow'			=> 'Europe/Moscow (モスクワ、サンクトペテルブルグ、ボルゴグラード)',
		'Europe/Paris'			=> 'Europe/Paris (ブリュッセル、コペンハーゲン、マドリード、パリ)',
		'Europe/Warsaw'			=> 'Europe/Warsaw (サラエボ、スコピエ、ワルシャワ、ザグレブ)',
		'Indian/Mauritius'		=> 'Indian/Mauritius (ポートルイス)',
		'Pacific/Apia'			=> 'Pacific/Apia (サモア)',
		'Pacific/Auckland'		=> 'Pacific/Auckland (オークランド、ウェリントン)',
		'Pacific/Fiji'			=> 'Pacific/Fiji (フィジー、マーシャル諸島)',
		'Pacific/Guadalcanal'		=> 'Pacific/Guadalcanal (ソロモン諸島、ニューカレドニア)',
		'Pacific/Honolulu'		=> 'Pacific/Honolulu (ハワイ)',
		'Pacific/Port_Moresby'		=> 'Pacific/Port_Moresby (グアム、ポートモレスビー)',
		'Pacific/Tongatapu'		=> 'Pacific/Tongatapu (ヌクアロファ)'
	);
}
?>
