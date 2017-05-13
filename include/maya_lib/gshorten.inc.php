<?php

function gshorten($long_url) {


	if (!isset($long_url) ) {

		return null;
	}

	$api_url = 'https://www.googleapis.com/urlshortener/v1/url';
	$api_key = GOOGLE_SHORTEN_API_KEY;
	$curl = curl_init("$api_url?key=$api_key");
	curl_setopt($curl, CURLOPT_HTTPHEADER, 
		array('Content-type: application/json'));
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, 
		'{"longUrl":"' . $long_url . '"}');
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

	$res = curl_exec($curl);
	curl_close($curl);

	$json = json_decode($res);
	$tiny_url = $json->id;

	return $tiny_url;

}

?>