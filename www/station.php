<?php
	include("../include/init.php");

	loadlib('blogs');


	#
	# old style url?
	#

	if (!$_SERVER['REDIRECT_URL']){
		header("location: /stations/$_GET[id]/");
		exit;
	}


	#
	# grab station
	#

	$id = intval($_GET['id']);

	$station = db_single(db_fetch("SELECT * FROM tube_stations WHERE id=$id"));
	if (!$station['id']) error_404();

	$station['tag'] = preg_replace("![^a-z]!", "", StrToLower($station['name']));

	$smarty->assign_by_ref('station', $station);

	#$segx = floor($station_row[main_x]/400)+1;
	#$segy = floor($station_row[main_y]/400)+1;


	#
	# lines that go through this station
	#

	$lines = array();
	$smarty->assign_by_ref('lines', $lines);

	$ret = db_fetch("SELECT * FROM tube_connections WHERE (station_id_1=$id OR station_id_2=$id) GROUP BY line_id");
	foreach ($ret['rows'] as $row){

		$line = db_single(db_fetch("SELECT * FROM tube_lines WHERE id=$row[line_id]"));
		$lines[] = $line;
	}


	#
	# get blogs
	#

	$ret2 = db_fetch("SELECT w.*,s.station_id FROM tube_weblogs AS w, tube_weblog_stations AS s WHERE w.id=s.weblog_id AND s.station_id=$id ORDER BY w.date_create DESC");

	$smarty->assign_by_ref('blogs', $ret2['rows']);


	#
	# get nearby stations
	#

	$nearby_stations = get_stations_within($station['real_x'], $station['real_y'], 1609);

	$smarty->assign_by_ref('nearby_stations', $nearby_stations);


	#
	# output
	#

	$smarty->display('page_station.txt');
