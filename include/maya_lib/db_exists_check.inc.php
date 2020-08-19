<?php 

function db_exists_check ($post_array) {

	$db = new SQLite3 (DB_FILE);

	$query = 'SELECT post_id FROM ff14forum_post ' . 
		'WHERE post_id=:id';

	$stmt = $db -> prepare ($query);

	$exists_flg_array = array();

	foreach ($post_array as $post_1_array) {
		$db_res = null;

		$post_id = (int) $post_1_array ['id'];

		$stmt -> bindValue (':id', $post_id, SQLITE3_INTEGER);

		$db_res = $stmt -> execute();

		$row_array = null;

		$row_array = $db_res -> fetchArray (SQLITE3_ASSOC);

		// 存在しない場合
		if(is_bool ($row_array)) {
			$exists_flg_array[] = 0;
		}
		else {	// 存在する場合
			$exists_flg_array[] = 1;
		}
	}

	unset($post_id);
	unset($post_1_array);
	unset($stmt);
	unset($db_res);

	return $exists_flg_array;
}
