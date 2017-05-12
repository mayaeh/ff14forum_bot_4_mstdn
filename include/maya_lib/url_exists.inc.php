<?php 

function url_exists($url) {

// http://php.net/manual/ja/function.file-exists.php#85246
// Version 4.x supported
	$handle = curl_init($url);

	if (false === $handle) {
		return false;
	}

//	curl_setopt($handle, CURLOPT_HEADER, false);
	curl_setopt($handle, CURLOPT_FAILONERROR, true);  // this works
	curl_setopt($handle, CURLOPT_HTTPHEADER, Array("Mozilla/5.0 (Windows NT 6.3; WOW64; Trident/7.0; rv:11.0) like Gecko") );	
	curl_setopt($handle, CURLOPT_NOBODY, true);
//	curl_setopt($handle, CURLOPT_RETURNTRANSFER, false);

	// キャッシュを使用しない (使用すると存在するものも取得できない場合あり)
//	curl_setopt($handle, CURLOPT_FRESH_CONNECT, true);

// for debug
//	curl_setopt($handle, CURLOPT_VERBOSE, true);

	$connectable = curl_exec($handle);
	curl_close($handle);

	return $connectable;

}
?>