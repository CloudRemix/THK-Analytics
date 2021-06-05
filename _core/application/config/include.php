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
require_once( THK_CORE_DIR . THK_CTRL_DIR . 'base' . DIRECTORY_SEPARATOR . 'BaseController.php' );
require_once( THK_CORE_DIR . THK_HELPER_DIR . 'base' . DIRECTORY_SEPARATOR . 'BaseHelper.php' );
if( file_exists( SETTING_SITEURL_FILE ) ) require_once( SETTING_SITEURL_FILE );
if( file_exists( SETTING_DATABASE_FILE ) ) require_once( SETTING_DATABASE_FILE );
?>
