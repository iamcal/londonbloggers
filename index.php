<?
	#
	# $Id$
	#

	include('include/init.php');


	#
	# recently added
	#

	$ret = db_fetch("SELECT * FROM tube_weblogs WHERE approved=1 ORDER BY date_create DESC LIMIT 5");
	$smarty->assign('weblogs', $ret['rows']);


	#
	# counts
	#

	list($c1) = db_list(db_fetch("SELECT COUNT(*) FROM tube_weblogs"));
	list($c2) = db_list(db_fetch("SELECT COUNT(*) FROM tube_stations"));
	list($c3) = db_list(db_fetch("SELECT COUNT(*) FROM tube_lines"));

	$smarty->assign('count_weblogs', $c1);
	$smarty->assign('count_stations', $c2);
	$smarty->assign('count_lines', $c3);


	#
	# output
	#

	$smarty->display('page_index.txt');
?>
