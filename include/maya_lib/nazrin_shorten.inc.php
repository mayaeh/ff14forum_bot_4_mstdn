<?php

function nazrin_shorten($url) {
	if (!$url || !defined('NAZRIN_URL')) {
		return null;
	}

	$post_data = ['url' => $url];

	$nazrin_url = NAZRIN_URL . '/api/short_links';

	$curl = curl_init($nazrin_url);

	$curl_options = [
//		CURLOPT_HTTPHEADER => array($header),
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => http_build_query($post_data),
	];

	curl_setopt_array($curl, $curl_options);

	$result = curl_exec($curl);

	curl_close($curl);

	$json = json_decode($result);

	if (property_exists($json, 'error')) {
		if ($json -> error) {
			access_log_writer('', $json -> error);
		}
	}
	
	if (property_exists($json, 'shortURL')) {
		if ($json -> shortURL) {
			return $json -> shortURL;
		}
	}
	return null;
}
