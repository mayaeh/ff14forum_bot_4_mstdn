<?php 

require_once('config.php');





// for debug
$html = file_get_contents('search.html');
$html_array = null;

if($html) {

	$array = explode("\r\n", $html);

	$html_array = array_slice($array, 230, 2000);
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


		elseif (preg_match("/投稿者\s<a\shref=\"members\/[0-9]+\-[0-9a-z_\-]+\">([0-9a-z_\-]+)<\/a>/iu", $line, $matches)) {

//var_dump(array($i, $line, $matches));

			$post_array[$j]['user'] = $matches[1];
		}

		elseif (preg_match("/^\s+<a\shref=\"(threads\/[0-9]+\-[0-9A-Z%]+\?p=[0-9]+#post[0-9]+)\">/iu", $line, $matches)) {

//var_dump(array($i, $line, $matches));

//var_dump($forum_base_url.$matches[1]);


			$url = $forum_base_url. $matches[1];

			$post_array[$j]['url'] = gshorten($url);
//			$post_array[$j]['url'] = $url;

			sleep(1);

		}


		elseif (preg_match ("/<blockquote\sclass=\"postcontent\srestore\">/iu", $line, $matches)) {


//var_dump(array($i, $line, $matches));

			$content_flag = 1;
		}

		elseif ($content_flag) {

			if (preg_match ("/<\/blockquote>/iu", $line, $matches)) {

				$post_array[$j]['text'] = preg_replace ( 
					"/\n{2,}/u", "\n", $text);

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
		if ($j>2) {

			break;
		}
	}



}

var_dump ($post_array);







exit;


$t = new \theCodingCompany\Mastodon();

$text ='';
$visibility = $in_reply_to_id = null;

$recv = $t -> postStatus 
	($text, $visibility, $in_reply_to_id);

var_dump($recv);

exit;


?>