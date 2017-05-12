<?php

// written by maya 2012.11.08
// sqlite_escape_string for SQLite3

function sqlite_escape_string ( $str ) {

	$db = null ;
	$db = new SQLite3 ( ':memory:' ) ;

	$escape_string = $db -> escapeString ( $str ) ;

	$db -> close () ;

	return $escape_string ;
}
?>
