<?
	include("include/init.php");

	loadlib('blogs');


	#
	# old style url?
	#

	if (!$_SERVER['REDIRECT_URL']){
		header("location: /weblogs/$_GET[id]/");
		exit;
	}


	#
	# grab blog
	#

	$weblog = db_single(db_fetch("SELECT * FROM tube_weblogs WHERE id=".intval($_GET['id'])));

	if (!$weblog['id']) error_404();

	if (!preg_match("/^http\:\/\//i", $weblog['blog_url'])){
		$weblog['blog_url'] = "http://". $weblog['blog_url'];
	}

	$smarty->assign('weblog', $weblog);


	#
	# just added?
	#

	if ($_GET['added'] == blog_signature($weblog['id'])){

		$smarty->assign('added', 1);
	}

	if ($_GET['updated'] == blog_signature($weblog['id'])){

		$smarty->assign('updated', 1);
	}


	#
	# build widget code
	#

	$station_ids = array();

	$ret = db_fetch("SELECT station_id FROM tube_weblog_stations WHERE weblog_id=$weblog[id]");
	foreach ($ret['rows'] as $row){
		$station_ids[] = $row['station_id'];
	}

	$smarty->assign('station_ids', implode(',', $station_ids));


	#
	# output
	#

	$smarty->display('page_weblog.txt');
?>