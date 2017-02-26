<?php
	include('../include/init.php');


	#
	# recently added
	#

	$ret = db_fetch("SELECT * FROM tube_weblogs ORDER BY date_create DESC LIMIT 5");
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
	# pick a random station to highlight
	#

	$random_ids = array(1,3,4,6,8,14,15,16,23,36,37,38,39,40,41,42,43,44,59,60,61,62,63,64,65,66,67,68,
			69,70,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,122,123,124,
			125,160,161,162,163,218,219,220,221,259,261,343,360,616);
	$id = $random_ids[rand(0, count($random_ids)-1)];

	list($num) = db_list(db_fetch("SELECT COUNT(w.id) FROM tube_weblogs AS w, tube_weblog_stations AS s WHERE w.id=s.weblog_id AND s.station_id=$id"));

	$smarty->assign('highlight_id', $id);
	$smarty->assign('highlight_num', $num);


	#
	# output
	#

	$smarty->display('page_index.txt');
