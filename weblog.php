<?
	include("include/init.php");

	loadlib('blogs');


	#
	# grab blog
	#

	$weblog = db_single(db_fetch("SELECT * FROM tube_weblogs WHERE id=".intval($_GET['id'])));

	if (!preg_match("/^http\:\/\//i", $weblog['blog_url'])){
		$weblog['blog_url'] = "http://". $weblog['blog_url'];
	}

	$smarty->assign('weblog', $weblog);


	#
	# get station list
	#

	$stations = get_StationList($weblog['id']);

	$smarty->assign('stations', $stations);


	#
	# output
	#

	$smarty->display('page_weblog.txt');
?>