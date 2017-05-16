<?php 

require_once('config.php');



if (!defined(FORUM_HTML)) {

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

	$forum_base_url = 'http://forum.square-enix.com/ffxiv/';

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

		elseif (preg_match("/span\sclass=\"date\">([0-9\/]+)&nbsp;<span\sclass=\"time\">([0-9:]+)<\/span><\/span>/iu", $line, $matches)) {

//var_dump(array($i, $line, $matches));
//exit;

			$post_array[$j]['datetime'] = 
				$matches[1]. " ". 
				$matches[2];

		}

		elseif (preg_match("/span\sclass=\"date\">(今日)&nbsp;<span\sclass=\"time\">([0-9:]+)<\/span><\/span>/iu", $line, $matches)) {

//var_dump(array($i, $line, $matches));
//exit;

			$post_array[$j]['datetime'] = 
				date("Y/m/d"). " ". 
				$matches[2];

// for debug
//$post_array[$j]['datetime'] = "2017/05/13 ". 
//	$matches[2];
//var_dump($post_array[$j]);
//exit;

		}

		elseif (preg_match("/span\sclass=\"date\">(昨日)&nbsp;<span\sclass=\"time\">([0-9:]+)<\/span><\/span>/iu", $line, $matches)) {

//var_dump(array($i, $line, $matches));
//exit;

			$post_array[$j]['datetime'] = 
				date("Y/m/d", mktime(0, 0, 0, date("m"), date("d")-1)). " ". 
				$matches[2];

// for debug
//$post_array[$j]['datetime'] = "2017/05/12 ". 
//	$matches[2];
//var_dump($post_array[$j]);
//exit;

		}

		elseif (preg_match("/投稿者\s<a\shref=\"members\/[0-9]+\-[0-9a-z_\-]+\">([0-9a-z_\-]+)<\/a>/iu", $line, $matches)) {

//var_dump(array($i, $line, $matches));

			$post_array[$j]['user'] = $matches[1];
		}

		elseif (preg_match("/^(\t+\s【\固\定】)?\s+<a\shref=\"(threads\/[0-9]+\-[0-9A-Z%\.\-_=]+\?p=[0-9]+#post[0-9]+)\">/iu", $line, $matches)) {

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

				if (mb_strlen($text) > TOOT_MAX_LENGTH) {

					$text = mb_substr($text, 0, TOOT_MAX_LENGTH -4) . 
					" ...";
				}

				$post_array[$j]['text'] = $text;

				$text = null;
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
$recv = 
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


$t = new \theCodingCompany\Mastodon();

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

		if($reverse_array[$i]['url']) {

			$url = gshorten($reverse_array[$i]['url']);
		}

// for debug
//var_dump($url);
//exit;


//		$text = $reverse_array[$i]['text']. 
//			" (". $reverse_array[$i]['user']. 
//			") \n". $url ;

		$text = 
			$reverse_array[$i]['datetime']. "\n" . 
			$reverse_array[$i]['user']. ":\n" . 
			$reverse_array[$i]['text']. "\n". 
			$url ;

		$recv = $t -> postStatus 
			($text, null, null);


// for debug
//var_dump($text);
//var_dump($recv);
//$recv['id'] = 1;
//exit;


		if (is_int($recv['id'])) {

			$tooted_array[] = $reverse_array[$i];
		}

// for debug
//if($i >20) {
//break;
//}


		sleep(1);

		$url = 
		$recv = null;
	}
}

// for debug
//var_dump($tooted_array);
//var_dump(db_exists_check($tooted_array));
//exit;

if ($tooted_array) {

	var_dump(toDB($tooted_array));
}


exit;


?>