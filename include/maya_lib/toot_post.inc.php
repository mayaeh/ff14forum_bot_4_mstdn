<?php

function toot_post($text) {

	if (!$text || !defined('MSTDN_URL') || !defined('MSTDN_OAUTH_TOKEN')) {
		return null;
	}

//for debug
//return $text;

//	$text = rawurlencode($text);

	$post_data = [
		'status' => $text,
		'visibility' => 'public',
	];

	$url = "https://".  MSTDN_URL . '/api/v1/statuses';

	$curl = curl_init($url);

	$header = 'Authorization: Bearer '. MSTDN_OAUTH_TOKEN;

	$curl_options = [
		CURLOPT_HTTPHEADER => array($header),
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => http_build_query($post_data),
	];

	curl_setopt_array($curl, $curl_options);

	$result = curl_exec($curl);

	curl_close($curl);

//	$query  = "curl -X POST";
//	$query .= " -d 'status=" . $text . "'";
//	$query .= " -d 'visibility=direct'";
//	$query .= " --header 'Authorization: Bearer " . MSTDN_OAUTH_TOKEN . "'";
//	$query .= " --header 'Content-Type:application/json'";
//	$query .= " -sS https://" . MSTDN_URL . "/api/v1/statuses";


// for debug
//return $query;

//$result_json = `$query`;
//$result = print_r($result_json);

	return $result;

}
