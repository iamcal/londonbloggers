<?php
	include("../include/init.php");

	loadlib('blogs');


	#
	# grab blog
	#

	$weblog = db_single(db_fetch("SELECT * FROM tube_weblogs WHERE id=".intval($_GET['id'] ?? 0)));

	if (!($weblog['id'] ?? null)) error_404();

	$smarty->assign('weblog', $weblog);


	#
	# check auth
	#

	if (($_GET['sig'] ?? null) != blog_signature($weblog['id'])){

		error_404();
	}


	#
	# done?
	#

	if ($_POST['done'] ?? null){

		$password = trim($_POST['password'] ?? '');

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
