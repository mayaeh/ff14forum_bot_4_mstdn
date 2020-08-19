<?php 

function toDB ($post_array) {

	if(!$post_array || !is_array($post_array)) {
		return null;
	}

	$return_flg = 0;

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

		$row_array = array();

		$row_array = $db_res -> fetchArray (SQLITE3_ASSOC);

		// 存在しない場合
		if(is_bool ($row_array)) {
			$exists_flg_array[] = 0;
		}
		else {	// 存在する場合
			$exists_flg_array[] = 1;
		}

	}

	unset($post_1_array);
	unset($stmt);
	unset($db_res);

	try {
		$db -> exec("BEGIN DEFERRED;");

		$query = "INSERT INTO ff14forum_post (". 
			"post_id". 
			") VALUES (". 
			":post_id)";

		$stmt = $db -> prepare ($query);

		for ($i = 0; $i < count($exists_flg_array); $i++) {
			$post_1_array = $post_array [$i];

			if (0 == $exists_flg_array[$i]) {
				$stmt -> bindValue (':post_id', 
					(int)$post_1_array ['id'], 
					SQLITE3_INTEGER);

				$db_res = $stmt -> execute();

				$return_flg++;
			}
		}

		unset($post_1_array);
	}
	catch (Exception $e) {
		// ロールバック
		$db -> exec("ROLLBACK;");
		$message = 'SQLの実行でエラーが発生しました。<br>';
		$message .= $e -> getTraceAsString ();
		return;
	}

	// コミット
	$db_res = $db -> exec("COMMIT;");

	$db -> close();

	if (isset($message)) {
		return array($return_flg, $message);
	}
	else {
		return $return_flg;
	}
}
