<?php

// 連想配列の要素が存在するかチェックする関数
require_once (SCRIPT_DIR . "include/unoh_lib/array_get_value.inc.php");

require_once (MAYALIB_DIR . 'access_log_writer.inc.php');

if (! function_exists ('sqlite_escape_string')) {
	require_once (MAYALIB_DIR . 'sqlite_escape_string.inc.php');
}

require_once (MAYALIB_DIR . 'toDB.inc.php');

require_once (MAYALIB_DIR . 'db_exists_check.inc.php');

require_once (MAYALIB_DIR . 'toot_post.inc.php');

require_once (MAYALIB_DIR . 'nazrin_shorten.inc.php');
