<?php
require '..' . DIRECTORY_SEPARATOR . 'initialize.php';

$js_dir = 'ccchart';

$js_min = '';
$javascripts = glob( THK_CORE_DIR . THK_APP_DIR . 'javascripts' . DIRECTORY_SEPARATOR . $js_dir . DIRECTORY_SEPARATOR . '*' );
foreach( $javascripts as $js ) {
	if( is_file( $js ) && strrchr( $js, '.js' ) === '.js' ) {
		$content = file_get_contents( $js );
		if( class_exists( 'JSMin' ) ) {
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
