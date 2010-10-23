<?
	include("../include/init.php");


	#
	# approve a batch?
	#

	if ($_POST['ids']){

		$ids = explode(',', $_POST['ids']);

		foreach ($ids as $id){

			$id = intval($id);

			if ($_POST["ok$id"] == 'ok'){
				db_write("UPDATE tube_weblogs SET approved=1 WHERE id=$id");
			}

			if ($_POST["ok$id"] == 'spam'){
				db_write("DELETE FROM tube_weblog_stations WHERE weblog_id=$id");
				db_write("DELETE FROM tube_weblogs WHERE id=$id");
			}
		}

		header('Location: review.php?done=1');
		exit;
	}


	#
	# fetch a batch for approval
	#

	$rows = array();
	$ids = array();

	list($count) = db_list(db_fetch("SELECT COUNT(*) FROM tube_weblogs WHERE approved=0"));

	$ret = db_fetch("SELECT * FROM tube_weblogs WHERE approved=0 ORDER BY date_create DESC LIMIT 20");
	foreach ($ret['rows'] as $row){

		$row['stations'] = array();

		$ret2 = db_fetch("SELECT * FROM tube_weblog_stations WHERE weblog_id=$row[id]");
		foreach ($ret2['rows'] as $row2){

			$row['stations'][] = $row2['station_id'];
		}

		$rows[] = $row;
		$ids[] = $row['id'];
	}

	$smarty->assign('rows', $rows);
	$smarty->assign('ids', implode(',', $ids));
	$smarty->assign('count', $count);


	#
	# output
	#

	$smarty->display('page_admin_review.txt');
?>