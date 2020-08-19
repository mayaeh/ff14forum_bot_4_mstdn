<?php

// デフォルトのタイムゾーンを設定します。PHP 5.1 以降で使用可能です
date_default_timezone_set ('Asia/Tokyo');

mb_internal_encoding ("UTF-8");

$message = null;

// 環境設定ファイルからスクリプト設置ディレクトリの設定を読み込む
if ( is_readable ('environment.inc.php')) {
	include ('environment.inc.php');
}
else {
	$message = "environment.inc.php.sample を environment.inc.php ". 
		"と改名し各種設定をしてください。\n";
}

if ( ! defined ('SCRIPT_DIR')) {
	$message .= "environment.inc.php 内設定を読み込めませんでした。\n";
	exit ($message) ;
}

if ( ! defined ('DB_FILE')) {
	$message .= "DB_FILE の設定を読み込めませんでした。environment.inc.php を確認してください。\n";
	exit ($message);
}
else {
	if ( ! is_writable(DB_FILE)) {
		$message .= "DB_FILE に書き込めないようです。確認してください。\n";
		exit ($message);
	}
}

// file_get_contents でのアクセス時用にユーザーエージェントを設定
ini_set ('user_agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:53.0) Gecko/20100101 Firefox/53.0');


// エラーログのパス及びファイル名
define ('SCRIPT_ERR_LOGFILE', SCRIPT_DIR . "error.log");

define ('MAYALIB_DIR', SCRIPT_DIR . "include/maya_lib/");

require_once (SCRIPT_DIR . 'include/include_config.php');
