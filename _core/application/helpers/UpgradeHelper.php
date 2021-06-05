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
class UpgradeHelper extends BaseHelper {

	public function __construct( ThkRequest $request, ThkSession $session, ThkMessage $message, ThkResult $result=null, $controller, $action ) {
		parent::__construct( $request, $session, $message, $result, $controller, $action );
	}

	public function upgradeDisp() {
		$html = '';
		$version = $this->result->get('version');
		$coreVersion = File::replaceCrlf( File::readFile( THK_CORE_DIR. 'thk_version.txt' ), '' );
		if( $version === Config::RA_LITE_VERSION ) {
			$version = '0.00';
		}

		$foundVersion = false;
		$grade = 'アップグレード';

		if( $version != $coreVersion ) {
			if( SystemUtil::versionCompareThk( $version, $coreVersion, '<' ) ) {
				$foundVersion = true;
			}
			else {
				$grade = 'ダウングレード';
				if( SystemUtil::versionCompareThk( $coreVersion, Config::ENABLE_DOWNGRADE_VERSION, '<' ) ) {
					$html .= '<p class="version">ver.' . $this->escapeHtml( $coreVersion ) . ' への' . $grade . 'はできません。</p>';
				}
				else {
					$foundVersion = true;
				}
			}
		}

		if( $foundVersion ) {
			$html .= '<p class="version">ver.' . $this->escapeHtml( $coreVersion ) . ' への<br />' . $grade . 'を行ないます。</p>';
			$html .= '<p class="msg">処理を実行する場合は、以下のボタンを押してください。</p>';
			$html .= '<div class="install">';
			$html .= '<form action="' . $this->getIndexUrl('upgrade', 'upgrade', '&amp;select=' . $this->escapeHtml( $coreVersion ) ) . '" method="post">';
			$html .= '<input class="button" type="submit" value=" ver.' . $this->escapeHtml( $coreVersion ) . ' へ' . $grade . 'する" onclick="disableButton(this.form);" />';
			$html .= '</form>';
			$html .= '</div>';
		}
		else {
			$html .= '<p class="version">現在のバージョンは' . $this->escapeHtml( $version ) . 'です。</p>';
			$html .= '<p class="msg">アップロードした' . $grade . '用ファイルを<br />削除してからログインしてください。</p>';
		}
		echo $html;
	}

	public function linkTag() {
		$html = '';
		$html = '<a href="./">ログイン</a>';
		echo $html;
	}

}
?>
