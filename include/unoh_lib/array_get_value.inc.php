<?php
/**
 *  配列から値を取り出す
 *
 *  配列から値を取り出します。もし連想キーが存在しない場合、
 *  第3引数の値を返します。
 *
 *  @param array $array 値を取得したい配列
 *  @param mixed $key 配列から値を取得したい連想キー
 *  @return mixed 配列から取り出した値、$keyが存在しなければ
 *                $defaultを返す
 *
 * http://unoh.github.io/2006/11/01/e_notice.html
 */
function array_get_value ( $array , $key , $default = NULL )
{
    return isset ( $array [$key] ) ? $array[$key]: $default ;
}
?>
