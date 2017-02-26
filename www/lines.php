<?php
	include('../include/init.php');


	#
	# fetch line
	#

	$lines = array();
	$smarty->assign_by_ref('lines', $lines);

	$ret = db_fetch("SELECT * FROM tube_lines");
	foreach ($ret['rows'] as $row){

		$ret2 = db_fetch("SELECT s.* FROM tube_stations AS s, tube_connections AS c WHERE c.line_id=$row[id] AND (c.station_id_1=s.id OR c.station_id_2=s.id) GROUP BY s.id LIMIT 5;");
		$row['stations'] = $ret2['rows'];

		$key = $row['has_line'] ? 'national' : 'tfl';

		$lines[$key][] = $row;
	}


	#
	# display
	#

	$smarty->display('page_lines.txt');
