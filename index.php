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
	# output
	#

	$smarty->display('page_temp.txt');
?>
