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

class Config {
	const INDEX = './';
	const INDEX_FILENAME = 'index.php';

	const DEFINE_SITEURL = 'REGISTER_SITEURL';

	const ON = '1';
	const OFF = '0';

	const SJIS = 'Shift_JIS';
	const EUC = 'EUC-JP';
	const UTF = 'UTF-8';

	const NORMAL_ACCESS = '0';
	const CLICK_LINK = '1';
	const CLICK_BTN = '2';
	const CLICK_ADSENSE = 'g';

	const JC_ENABLED_JC= '有効/有効';
	const JC_ENABLED_J= '有効/無効';
	const JC_ENABLED_C= '無効/有効';
	const JC_ENABLED_N= '無効/不明';

	const CLICK_REL = '_THKclick';

	const NOSELECT_PAGE = '全てのページ';
	const NOSELECT_UID = '全ての訪問者';

	const FROM_CONTINUE = '解析対象期間よりも前からの継続的なアクセス';
	const FROM_NO_SCRIPT = 'FROM NO SCRIPT';
	//const FROM_BOOKMARK = 'ブックマーク等';
	const DIRECT_ACCESS = '直接アクセス';
	const NO_DATA = 'unknown';

	const STANDARD_DIGIT = 4;
	const MAX_PAGE = 10;
	//const MAX_DISP_LENGTH = 45;
	const MAX_DISP_LENGTH = 52;
	const MAX_DISP_MULTIBYTE_LENGTH = 64;

	const SEARCH_URL = 'http://www.google.co.jp/search?q=';

	const ONLINE_TIME = 300;

	const COOKIE_ENABLE_DAYS = 365;
	const AUTOLOGIN_DAYS = 30;

	const COOKIE_LOGINID = '_thk_loginid';
	const COOKIE_LOGINKEY = '_thk_loginkey';
	const COOKIE_MEMORY = '_thk_memory';
	const COOKIE_UID = '_thk_uid';

	const ENABLE_DOWNGRADE_VERSION = '1.02';
	const RA_LITE_VERSION = '1.18';

	const RELEASE_YEAR = 2015;

	const RALITE_RELEASE_YEAR = 2009;
	const RALITE_LAST_RELEASE_YEAR = 2015;

	const MINIMUM_MEMORY_LIMIT = 16;
	const UTILIZABLE_MEMORY = 0.99;	// default 99.0%

	const UPGRADE_FILECHAR = '_';

	const CREATE_LOG_MONTHS = 1;

	const NOTICE_ERR_CODE = -9900;

	const SYSTEM_DEFAULT_ID = 1;

	public static function getCookieKeyAdminNocount() {
		return md5( constant( self::DEFINE_SITEURL ) );
	}
}
?>
