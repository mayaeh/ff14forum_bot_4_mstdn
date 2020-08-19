<?php 

require_once('config.php');

if (defined('FORUM_HTML')) {
	$html = file_get_contents(FORUM_HTML);
}
else {
	exit('FORUM_HTML not defined. please check environment.inc.php. terminated.'."\n");
}

$html_array = null;

if($html) {
	$array = explode("\r\n", $html);

	$html_array = array_slice($array, 230, 2000);
}
else {
	exit('$html is null or false. terminated.'."\n");
}

if(is_array($html_array)) {
	$forum_base_url = 'https://forum.square-enix.com/ffxiv/';

	$i =
	$j = 0;

	$text =
	$content_flag = null;

	for ($i = 0; $i < count($html_array); $i++) {
		$line = $html_array[$i];

		if (preg_match("/<li\sclass=\"imodselector\spostbit\spostbitcontainer\"\sid=\"post_([0-9]+)\">/iu", $line, $matches)) {
			$post_array[$j]['id'] = $matches[1];
		}
		elseif (preg_match("/span\sclass=\"date\">([0-9]{2})\-([0-9]{2})\-([0-9]{4})&nbsp;<span\sclass=\"time\">([0-9]{2}):([0-9]{2})\s([A|P]M)<\/span><\/span>/iu", $line, $matches)) {
			$datetime = $matches[3]. "/". $matches[1]. "/". $matches[2]. 
				" ". $matches[4]. ":". $matches[5]. " ". $matches[6];

			$post_array[$j]['datetime'] = 
				date("Y/m/d H:i", strtotime($datetime));
		}
		elseif (preg_match("/span\sclass=\"date\">(Today)&nbsp;<span\sclass=\"time\">([0-9]{2}):([0-9]{2})\s([A|P]M)<\/span><\/span>/iu", $line, $matches)) {
			$datetime = date("Y/m/d"). " ". 
				$matches[2]. ":". $matches[3]. " ". $matches[4];

			$post_array[$j]['datetime'] = 
				date("Y/m/d H:i", strtotime($datetime));
		}
		elseif (preg_match("/span\sclass=\"date\">(Yesterday)&nbsp;<span\sclass=\"time\">([0-9]{2}):([0-9]{2})\s([A|P]M)<\/span><\/span>/iu", $line, $matches)) {
			$datetime = date("Y/m/d", mktime(0, 0, 0, date("m"), date("d")-1)). 
				" ". $matches[2]. ":". $matches[3]. " ". $matches[4];

			$post_array[$j]['datetime'] = 
				date("Y/m/d H:i", strtotime($datetime));
		}
		elseif (preg_match("/by\s<a\shref=\"members\/[0-9]+\-[0-9a-z_\-]+\">([0-9a-z_\-]+)<\/a>/iu", $line, $matches)) {
			$post_array[$j]['user'] = $matches[1];
		}
		elseif (preg_match("/^(\t+Sticky:)?\s+<a\shref=\"(threads\/[0-9]+\-[0-9A-Z%\.\-_=]+\?p=[0-9]+#post[0-9]+)\">/iu", $line, $matches)) {
			$url = $forum_base_url. $matches[2];

			$post_array[$j]['url'] = $url;
		}
		elseif (preg_match ("/<blockquote\sclass=\"postcontent\srestore\">/iu", $line, $matches)) {
			$content_flag = 1;
		}
		elseif ($content_flag) {

			if (preg_match ("/<\/blockquote>/iu", $line, $matches)) {

				$text = preg_replace ("/\n{2,}/u", "\n", $text);

				$text = preg_replace ("/&lt;iframe\swidth=&quot;560&quot;\sheight=&quot;315&quot;\ssrc=&quot;(https:\/\/www\.youtube\.com\/embed\/[a-zA-Z0-9]+)&quot;\sframeborder=&quot;0&quot;/u", ' $1 ', $text);

				$post_array[$j]['text'] = $text;

				$text =
				$content_flag = null;

				$j++;
			}
			else {
				$text .= preg_replace (
					"/\s?<br\s\/>/iu", "\n", preg_replace ( 
					"/\t+/u", "", $line));
			}
		}
	}
}

$url =
$result =
$nazrin_result =
$reverse_array =
$tooted_array = null;

$reverse_array = array_reverse($post_array);

$db_exists_flg = db_exists_check($reverse_array);

for ($i = 0; $i < count($reverse_array); $i++) {

	if($db_exists_flg[$i]<1) {
		if($reverse_array[$i]['url'] && defined('NAZRIN_URL')) {
			$reverse_array[$i]['shorten_url'] = 
				nazrin_shorten($reverse_array[$i]['url']);
		}

		$reverse_array[$i]['text_length'] = 
			mb_strlen($reverse_array[$i]['text']);

		if (array_get_value($reverse_array[$i], 'shorten_url')) {
			if ($reverse_array[$i]['text_length'] + mb_strlen($reverse_array[$i]['shorten_url']) > TOOT_MAX_LENGTH) {
				$reverse_array[$i]['text_short'] = 
					mb_substr($reverse_array[$i]['text'], 0, 
					TOOT_MAX_LENGTH - mb_strlen($reverse_array[$i]['shorten_url']) -4) . 
					" ...";
			}
			else {
				$reverse_array[$i]['text_short'] = 
					$reverse_array[$i]['text'];
			}
		}
		else {
			$reverse_array[$i]['url_length'] = 
				mb_strlen($reverse_array[$i]['url']);

			if ($reverse_array[$i]['text_length'] + $reverse_array[$i]['url_length'] > TOOT_MAX_LENGTH) {

				$reverse_array[$i]['text_short'] = 
					mb_substr($reverse_array[$i]['text'], 0, 
					TOOT_MAX_LENGTH - $reverse_array[$i]['url_length'] -4) . 
					" ...";
			}
			else {
				$reverse_array[$i]['text_short'] = 
					$reverse_array[$i]['text'];
			}
		}
	}

	if(array_get_value($reverse_array[$i], 'shorten_url')) {
		$text = 
			$reverse_array[$i]['datetime']. "\n" . 
			$reverse_array[$i]['user']. ":\n" . 
			$reverse_array[$i]['text_short']. "\n". 
			$reverse_array[$i]['shorten_url'];
	}
	else {
		$text = 
			$reverse_array[$i]['datetime']. "\n" . 
			$reverse_array[$i]['user']. ":\n" . 
			$reverse_array[$i]['text_short']. "\n". 
			$reverse_array[$i]['url'];
	}

	$result = toot_post($text);

	if ($result) {
		$result_json = json_decode($result);

		if (property_exists($result_json, 'id')) {
			if ($result_json -> id) {
				$tooted_array[] = $reverse_array[$i];
			}
		}
	}
	elseif (is_null($result)) {
		exit('result IS NULL');
	}
	else {
		access_log_writer('', 'result error: '. $result);
		exit('result error');
	}

	// 20 件以上ある場合は処理を中断する
	if($i >20) {
	break;
	}
	
	sleep(1);

	$url =
	$result =
	$result_json = null;
}

if ($tooted_array) {
	var_dump(toDB($tooted_array));
}

exit;
