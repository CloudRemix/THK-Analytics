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

class SearchEngineList {
	public static $engines = array(
		'yahoo'		=> array( '?p=', '?w=', '?va=', '&p=', '&w=', '&va=' ),
		'google'	=> array( '?q=', '?as_epq=', '?as_q=', '&q=', '&as_epq=', '&as_q=', '#q=', '#as_epq=', '#as_q=' ),
		'msn'		=> array( '?q=', '&q=' ),
		'bing'		=> array( '?q=', '&q=' ),
		'goo'		=> array( '?mt=', '?queryword=', '&mt=', '&queryword=' ),
		'docomo'	=> array( '?mt=', '&mt=' ),
		'ocn'		=> array( '?mt=', '&mt=' ),
		'nifty'		=> array( '?text=', '&text=', '?q=', '&q=' ),
		'biglobe'	=> array( '?q=', '&q=' ),
		'infoseek'	=> array( '?qt=', '&qt=' ),
		'rakuten'	=> array( '?qt=', '&qt=' ),
		'excite'	=> array( '?q=', '&q=', '?search=', '&search=' ),
		'livedoor'	=> array( '?q=', '&q=' ),
		'aol'		=> array( '?query=', '?query_contain=', '&query=', '&query_contain=' ),
		'jword'		=> array( '?name=', '&name=' ),
		'fresheye'	=> array( '?kw=', '&kw=' ),
		'alltheweb'	=> array( '?q=', '&q=' ),
		'hatena'	=> array( '?word=', '&word=' ),
		'marsflag'	=> array( '?phrase=', '&phrase=' ),
		'baidu'		=> array( '?wd=', '&wd=' ),
		'gigablast'	=> array( '?q=', '&q=' ),
		'sagool'	=> array( '?q=', '&q=' ),
		'technorati'	=> array( 'search/' ),
		'ceek'		=> array( '?q=', '&q=' ),
		'luna'		=> array( '?q=', '&q=' ),
		'ask'		=> array( '?searchfor=', '&searchfor=' ),
		'fmworld'	=> array( '?Text=', '&Text=', '?text=', '&text=' ),
		'aok-net'	=> array( '?key=', '&key=' ),
		'default'	=> array(
			'?q=',
			'?search=',
			'?searchfor=',
			'?query=',
			'?word=',
			'?words=',
			'?keyword=',
			'?keywords=',
			'?mt=',
			'&q=',
			'&search=',
			'&searchfor=',
			'&query=',
			'&word=',
			'&words=',
			'&keyword=',
			'&keywords=',
			'&mt='
		)
	);
}
