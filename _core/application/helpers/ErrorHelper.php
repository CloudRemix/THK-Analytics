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
class ErrorHelper extends BaseHelper {
	public function __construct( ThkRequest $request, ThkSession $session, ThkMessage $message, ThkResult $result=null, $controller, $action ) {
		parent::__construct( $request, $session, $message, $result, $controller, $action );
	}

	public function errTag() {
		if( ThkUtil::isHttp() ) echo '<p class="err_msg">';
		$this->messageTag();
		if( ThkUtil::isHttp() ) echo '</p>';
	}

	public function linkTag() {
		$html = '';
		if( ThkUtil::isHttp() ) {
			$html = '<a href="./">TOP</a>';
			$html = '<p class="err_link">'. $html. '</p>';
		}
		echo $html;
	}
}
?>
