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

//var_dump(count($html_array));
//exit;

if(is_array($html_array)) {

//var_dump($html_array);

	$forum_base_url = 'https://forum.square-enix.com/ffxiv/';

	$i = 
	$j = 0;

	$text = 
	$content_flag = null;

	for ($i = 0; $i < count($html_array); $i++) {

		$line = $html_array[$i];

		if (preg_match("/<li\sclass=\"imodselector\spostbit\spostbitcontainer\"\sid=\"post_([0-9]+)\">/iu", $line, $matches)) {

//var_dump(array($i, $line, $matches));

			$post_array[$j]['id'] = 
				$matches[1];

		}

		elseif (preg_match("/span\sclass=\"date\">([0-9]{2})\-([0-9]{2})\-([0-9]{4})&nbsp;<span\sclass=\"time\">([0-9]{2}):([0-9]{2})\s([A|P]M)<\/span><\/span>/iu", $line, $matches)) {

//var_dump(array($i, $line, $matches));
//exit;

			$datetime = $matches[3]. "/". $matches[1]. "/". $matches[2]. 
				" ". $matches[4]. ":". $matches[5]. " ". $matches[6];

			$post_array[$j]['datetime'] = 
				date("Y/m/d H:i", strtotime($datetime));
		}

		elseif (preg_match("/span\sclass=\"date\">(Today)&nbsp;<span\sclass=\"time\">([0-9]{2}):([0-9]{2})\s([A|P]M)<\/span><\/span>/iu", $line, $matches)) {

//var_dump(array($i, $line, $matches));
//exit;

			$datetime = date("Y/m/d"). " ". 
				$matches[2]. ":". $matches[3]. " ". $matches[4];

			$post_array[$j]['datetime'] = 
				date("Y/m/d H:i", strtotime($datetime));

// for debug
//$post_array[$j]['datetime'] = "2017/05/13 ". 
//	$matches[2];
//var_dump($post_array[$j]);
//exit;

		}

		elseif (preg_match("/span\sclass=\"date\">(Yesterday)&nbsp;<span\sclass=\"time\">([0-9]{2}):([0-9]{2})\s([A|P]M)<\/span><\/span>/iu", $line, $matches)) {

//var_dump(array($i, $line, $matches));
//exit;

			$datetime = date("Y/m/d", mktime(0, 0, 0, date("m"), date("d")-1)). 
				" ". $matches[2]. ":". $matches[3]. " ". $matches[4];

			$post_array[$j]['datetime'] = 
				date("Y/m/d H:i", strtotime($datetime));

// for debug
//$post_array[$j]['datetime'] = "2017/05/12 ". 
//	$matches[2];
//var_dump($post_array[$j]);
//exit;

		}

		elseif (preg_match("/by\s<a\shref=\"members\/[0-9]+\-[0-9a-z_\-]+\">([0-9a-z_\-]+)<\/a>/iu", $line, $matches)) {

//var_dump(array($i, $line, $matches));

			$post_array[$j]['user'] = $matches[1];
		}

		elseif (preg_match("/^(\t+Sticky:)?\s+<a\shref=\"(threads\/[0-9]+\-[0-9A-Z%\.\-_=]+\?p=[0-9]+#post[0-9]+)\">/iu", $line, $matches)) {

//var_dump(array($i, $line, $matches));

//var_dump($forum_base_url.$matches[1]);

			$url = $forum_base_url. $matches[2];

			$post_array[$j]['url'] = $url;

		}

		elseif (preg_match ("/<blockquote\sclass=\"postcontent\srestore\">/iu", $line, $matches)) {


//var_dump(array($i, $line, $matches));

			$content_flag = 1;
		}

		elseif ($content_flag) {

			if (preg_match ("/<\/blockquote>/iu", $line, $matches)) {

				$text = preg_replace ("/\n{2,}/u", "\n", $text);

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

		// for debug
//		if ($j>2) {

//			break;
//		}

	}
}

// for debug
//var_dump ($post_array);
//var_dump(toDB($post_array));
//var_dump(db_exists_check($post_array));
//exit;


$url = 
$result = 
$reverse_array = 
$tooted_array = null;

$reverse_array = array_reverse($post_array);

// for debug
//var_dump($reverse_array);
//exit;

$db_exists_flg = db_exists_check($reverse_array);

// for debug
//var_dump($db_exists_flg);
//exit;

for ($i = 0; $i < count($reverse_array); $i++) {

// for debug
//if($i>1) {
//var_dump($db_exists_flg[$i]);
//exit;
//}

	if($db_exists_flg[$i]<1) {

// for debug
//var_dump($db_exists_flg[$i]);
//var_dump ($reverse_array[$i]['url']);
//exit;

		$reverse_array[$i]['text_length'] = 
			mb_strlen($reverse_array[$i]['text']);

		$reverse_array[$i]['url_length'] = 
			mb_strlen($reverse_array[$i]['url']);

		if ($reverse_array[$i]['text_length'] + $reverse_array[$i]['url_length'] > TOOT_MAX_LENGTH) {

			$reverse_array[$i]['text_short'] = 
				mb_substr($reverse_array[$i]['text'], 0, 
				TOOT_MAX_LENGTH - $reverse_array[$i]['url_length'] -4) . 
				" ...";
		}
		else {

			$reverse_array[$i]['text_short'] = $reverse_array[$i]['text'];
		}
	}

// for debug
var_dump($reverse_array);
//var_dump($reverse_array[$i]['url_length']);
//exit;


//	$text = $reverse_array[$i]['text']. 
//		" (". $reverse_array[$i]['user']. 
//		") \n". $url ;

	$text = 
		$reverse_array[$i]['datetime']. "\n" . 
		$reverse_array[$i]['user']. ":\n" . 
		$reverse_array[$i]['text_short']. "\n". 
		$reverse_array[$i]['url'];

// for debug
//var_dump($text);
//exit;

    $result = toot_post($text);

// for debug
var_dump($result);
exit;

	if (is_int($result['id'])) {

		$tooted_array[] = $reverse_array[$i];
	}
	elseif (is_null($result)) {

        exit('result IS NULL');
    }

// for debug
//if($i >20) {
//break;
//}

	sleep(1);

	$url = 
	$result = null;
}

// for debug
//var_dump($tooted_array);
//var_dump(db_exists_check($tooted_array));
exit;

if ($tooted_array) {

	var_dump(toDB($tooted_array));
}

exit;
