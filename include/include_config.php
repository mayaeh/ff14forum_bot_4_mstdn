<?php
// written by maya minatsuki
// made this file : 2017.05.12
// last mod. : 2017.05.12
//


// 連想配列の要素が存在するかチェックする関数
require_once ( SCRIPT_DIR . "include/unoh_lib/array_get_value.inc.php" ) ;

// Include MastodonOAuthPHP and abraham-twitteroauth Library, thanks
require_once ( SCRIPT_DIR . 'include/composer/vendor/autoload.php' ) ;

require_once ( MAYALIB_DIR . 'access_log_writer.inc.php' ) ;

if ( ! function_exists ('sqlite_escape_string') ) {
	require_once ( MAYALIB_DIR . 'sqlite_escape_string.inc.php' ) ;
}



?>
