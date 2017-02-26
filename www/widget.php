<?php
	include("../include/init.php");


	#
	# fetch stations / weblogs
	#

	$stations = array();

	$ids = explode(',', $_GET['ids']);
	foreach ($ids as $id){
		$id = intval($id);

		$station = db_single(db_fetch("SELECT * FROM tube_stations WHERE id=$id"));
		if (!$station['id']) continue;


		#
		# weblogs
		#

		$limit = 5;

		$ret2 = db_fetch("SELECT w.*,s.station_id FROM tube_weblogs AS w, tube_weblog_stations AS s WHERE w.id=s.weblog_id AND s.station_id=$station[id] ORDER BY w.date_create DESC LIMIT $limit");
		list($total) = db_list(db_fetch("SELECT COUNT(w.id) FROM tube_weblogs AS w, tube_weblog_stations AS s WHERE w.id=s.weblog_id AND s.station_id=$station[id]"));

		$station['weblogs'] = $ret2['rows'];
		$station['more'] = max(0, $total - $limit);


		$stations[] = $station;
	}

	$smarty->assign('stations', $stations);


	#
	# uid allows multiple widgets per page, somehow!
	#

	$smarty->assign('uid', time().rand(100,999));



	#
	# test mode just return the html, with no script to make
	# the menu open - just easier for seeing the raw html
	#

	if ($_GET['test']){
		$smarty->display('inc_widget_html.txt');
		exit;
	}


	#
	# output
	#

	$smarty->display('js_widget.txt');
