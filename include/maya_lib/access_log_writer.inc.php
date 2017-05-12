<?php
// written by maya minatsuki
// make this file	2010.09.30
// last mod		2016.05.03



// ---------------------------------------------------------------
//	エラー発生時等にアクセスログに記録する。
//	$chtime : 日時 , $words : ログファイルに記録するメッセージ
// ---------------------------------------------------------------
function access_log_writer ($chtime, $words)
{
	if (is_null ($chtime))
	{
		$chtime = time() ;
	}

	if (is_null ($words))
	{
		$words = "null" ;
	}

	// 日時を読みやすく変換
	$chtime = date ( "Y/m/d H:i:s" , $chtime ) ;
	// 日時とエラー内容を一行にまとめる
	$message = $chtime . " , " . $words . "\n" ;
	if ( $fp_w = fopen ( SCRIPT_ERR_LOGFILE , "a" ) )
	{
		// ファイルロックを使用
		flock ( $fp_w , LOCK_EX ) ;
		// 内容をまとめてファイルに書き込む
		fwrite ( $fp_w , $message ) ;
		flock ( $fp_w , LOCK_UN ) ;
		fclose ( $fp_w ) ;
		return 1 ;
	}
	else
	{
		exit ( "ログファイルが存在しないか、書き込めません！<br />\n" ) ;
	}
}

?>
