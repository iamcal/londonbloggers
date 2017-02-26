<?php
	include("../include/init.php");

	loadlib('blogs');


	#
	# get blogs
	#

	$y = intval($_GET['y']);
	$m = intval($_GET['m']);

	if ($m < 1 || $m > 12) error_404();

	if ($y < 2000 || $y > 2038) error_404();

	$ts = mktime(0,0,0,$m,1,$y);

	$smarty->assign('display_month', date('F Y', $ts));
	$smarty->assign('is_future', $ts > time());

	$d = sprintf('%04d-%02d-01', $y, $m);

	$ret = db_fetch("SELECT * FROM tube_weblogs WHERE month_create='$d' ORDER BY blog_name ASC");

	$smarty->assign_by_ref('blogs', $ret['rows']);


	#
	# output
	#

	$smarty->display('page_weblogs_month.txt');
