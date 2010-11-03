<?
	include("include/init.php");

	loadlib('blogs');


	#
	# grab blog
	#

	$weblog = db_single(db_fetch("SELECT * FROM tube_weblogs WHERE id=".intval($_GET['id'])));

	if (!$weblog['id']) error_404();

	$smarty->assign('weblog', $weblog);


	#
	# crumb
	#

	$report_slug = blog_ip_crumb($weblogs['id']);
	$smarty->assign('report_slug', $report_slug);


	#
	# submitted?
	#

	if ($_POST['done'] == $report_slug){

		email_send(array(
			'to_email'	=> $cfg['admin_email'],
			'template'	=> 'email_report.txt',
		));

		$smarty->assign('done', 1);
	}


	#
	# output
	#

	$smarty->display('page_weblog_report.txt');
?>