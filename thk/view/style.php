<?php
require_once( '..' . DIRECTORY_SEPARATOR . 'initialize.php' );

$css_min = '';
$stylesheets = glob( THK_CORE_DIR . 'application' . DIRECTORY_SEPARATOR . 'stylesheets' . DIRECTORY_SEPARATOR . '*' );
foreach( $stylesheets as $css ) {
	if( is_file( $css ) && strrchr( $css, '.css' ) === '.css' ) {
		$content = file_get_contents( $css );
		if( class_exists( 'CSSmin' ) ) {
			$minify = new CSSmin();
			if( method_exists( $minify, 'run' ) ) {
				$content = trim( $minify->run( $content ) );
			}
		}
		$css_min .= "\n" . $content;
	}
}
if( !ThkUtil::isHttp() && ThkUtil::isWindows() ) {
	$css_min = ThkUtil::convertEncoding( $css_min, ThkConfig::CHARSET, 'SJIS' );
}

header( 'Content-type: text/css' );
echo trim( $css_min );
?>
