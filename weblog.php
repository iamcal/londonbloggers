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

	if (!preg_match("/^http\:\/\//i", $weblog['blog_url'])){
		$weblog['blog_url'] = "http://". $weblog['blog_url'];
	}

	$smarty->assign('weblog', $weblog);


	#
	# output
	#

	$smarty->display('page_weblog.txt');
?>