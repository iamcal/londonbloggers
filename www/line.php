<?php
	include("../include/init.php");


	#
	# fetch line details
	#

	$slug_enc = AddSlashes($_GET['id']);
	$line = db_single(db_fetch("SELECT * FROM tube_lines WHERE slug='$slug_enc'"));
	if (!$line['id']) error_404();

	$smarty->assign('line', $line);


	#
	# fetch stations
	#

	$ret = db_fetch("SELECT s.* FROM tube_stations AS s, tube_connections AS c WHERE c.line_id='$line[id]' AND (c.station_id_1=s.id OR (c.station_id_2=s.id AND c.one_way=0)) GROUP BY s.id ORDER BY s.name ASC");

	$smarty->assign('stations', $ret['rows']);

	$station_ids = array();
	foreach ($ret['rows'] as $row) $station_ids[] = $row['id'];
	$smarty->assign('station_ids', $station_ids);


	#
	# fetch weblogs
	#

	$limit = 10;
	$has_more = 0;

	$ret = db_fetch("SELECT w.*, s.station_id FROM tube_weblogs AS w, tube_weblog_stations AS s, tube_connections AS c WHERE w.id=s.weblog_id AND (s.station_id=c.station_id_1 OR s.station_id=c.station_id_2) AND c.line_id=$line[id] GROUP BY w.id ORDER BY w.date_create DESC LIMIT ".($limit+1));

	if (count($ret['rows']) == $limit + 1){
		$has_more = 1;
		$ret['rows'] = array_slice($ret['rows'], 0, $limit);
	}

	$smarty->assign('weblogs', $ret['rows']);
	$smarty->assign('has_more', $has_more);



	#
	# output
	#

	$smarty->display('page_line.txt');
