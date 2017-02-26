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
	# done?
	#

	if ($_POST['done']){

		$password = trim($_POST['password']);

		if (strlen($password)){

			db_update('tube_weblogs', array(
				'password_hash'	=> AddSlashes(blog_hash_password($password)),
			), "id=$weblog[id]");


			$sig = blog_signature($weblog['id']);

			header("location: /weblogs/$weblog[id]/edit/$sig/?newpass=1");
			exit;
		}
	}


	#
	# output
	#

	$smarty->display('page_reset.txt');
