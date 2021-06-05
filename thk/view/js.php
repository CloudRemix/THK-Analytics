<?php
require_once( '..' . DIRECTORY_SEPARATOR . 'initialize.php' );

$js_min = '';
$javascripts = glob( THK_CORE_DIR. 'application' . DIRECTORY_SEPARATOR . 'javascripts' . DIRECTORY_SEPARATOR . '*' );
foreach( $javascripts as $js ) {
	if( strpos( $js, 'jquery.min.js' ) !== false ) {
		$js_min .= "\n" . file_get_contents( $js );
	}
}
foreach( $javascripts as $js ) {
	if( is_file( $js ) && strrchr( $js, '.js' ) === '.js' ) {
		if( strpos( $js, 'jquery.min.js' ) !== false ) continue;
		$content = file_get_contents( $js );
		if( class_exists('JSMin') ) {
			$content = trim( JSMin::minify( $content ) );
		}
		$js_min .= "\n" . $content;
	}
}

if( !ThkUtil::isHttp() && ThkUtil::isWindows() ) {
	$js_min = ThkUtil::convertEncoding( $js_min, ThkConfig::CHARSET, 'SJIS' );
}

header( 'Content-type: application/x-javascript' );
echo trim( $js_min );
?>
