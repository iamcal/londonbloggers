<?
	include("include/init.php");

	loadlib('blogs');


	#
	# old style url?
	#

	if (!$_SERVER['REDIRECT_URL']){
		header("location: /edit/");
		exit;
	}


	#
	# login?
	#

	if ($_POST['login']){

		$ok = 1;
		$email_enc = AddSlashes(StrToLower($_POST['email']));
		$password_enc = AddSlashes(StrToLower($_POST['password']));

		if (!strlen($email_enc) || !strlen($password_enc)){

			$ok = 0;
			$smarty->assign('error_missingfields', 1);
		}

		if ($ok){
			$weblog = db_single(db_fetch("SELECT * FROM tube_weblogs WHERE email='$email_enc' AND password='$password_enc'"));

			if (!$weblog['id']){

				$ok = 0;
				$smarty->assign('error_badlogin', 1);
			}
		}

		if ($ok){
			$sig = blog_signature($weblog['id']);

			header("location: /weblogs/$weblog[id]/edit/$sig/");
			exit;
		}
	}


	#
	# send password reminder?
	#

	if ($_POST['remind']){

		$ok = 1;
		$email_enc = AddSlashes(StrToLower($_POST['remind_email']));

		if (!strlen($email_enc)){
			$ok = 0;
			# no need for an error
		}

		if ($ok){
			$weblog = db_single(db_fetch("SELECT * FROM tube_weblogs WHERE email='$email_enc'"));

			if (!$weblog['id']){

				$ok = 0;
				$smarty->assign('error_remind_notfound', 1);
			}
		}

		if ($ok){

			$smarty->assign('row', $weblog);

			#mail($row[email], "[LondonBloggers] Login Details", $msg, "From: LondonBloggers <cal@londonbloggers.co.uk>\r\nReturn-path: cal@londonbloggers.co.uk");

			$smarty->display('page_edit_sent.txt');
			exit;
		}
	}


	#
	# output
	#

	$smarty->display('page_edit.txt');
?>