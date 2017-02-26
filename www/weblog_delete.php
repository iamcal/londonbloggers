<?php
	include("../include/init.php");

	loadlib('blogs');


	#
	# grab blog
	#

	$weblog = db_single(db_fetch("SELECT * FROM tube_weblogs WHERE id=".intval($_GET['id'])));

	if (!$weblog['id']) error_404();

	$smarty->assign('weblog', $weblog);


	#
	# check auth
	#

	if ($_GET['sig'] != blog_signature($weblog['id'])){

		error_404();
	}



	#
	# delete?
	#

	if ($_POST['done']){

		db_write("DELETE FROM tube_weblogs WHERE id=$weblog[id]");
		db_write("DELETE FROM tube_weblog_stations WHERE weblog_id=$weblog[id]");

		$smarty->display('page_weblog_delete_done.txt');
		exit;
	}


	#
	# output
	#

	$smarty->display('page_weblog_delete.txt');
