<?php

function toot_post($text) {

    if (is_null($text) || !defined('MSTDN_URL') || !defined('MSTDN_OAUTH_TOKEN')) {

        return;
    }


    $text = rawurlencode($text);

    $query  = "curl -X POST";
    $query .= " -d 'status=" . $text . "'";
    $query .= " -d 'visibility=direct'";
    $query .= " --header 'Authorization: Bearer " . MSTDN_OAUTH_TOKEN . "'";
//    $query .= " --header 'Content-Type:application/json'";
    $query .= " -sS https://" . MSTDN_URL . "/api/v1/statuses";


// for debug
return $query;

$result_json = `$query`;
$result = print_r($result_json);

return $result;

}
