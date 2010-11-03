<?
	#
	# $Id$
	#

	include("include/init.php");


	#
	# fetch line details
	#

	$slug_enc = AddSlashes($_GET['id']);
	$line = db_single(db_fetch("SELECT * FROM tube_lines WHERE slug='$slug_enc'"));
	if (!$line['id']) error_404();

	$smarty->assign('line', $line);


	#
	# fetch weblogs
	#

	$ret = db_fetch("SELECT w.*, s.station_id FROM tube_weblogs AS w, tube_weblog_stations AS s, tube_connections AS c WHERE w.id=s.weblog_id AND (s.station_id=c.station_id_1 OR s.station_id=c.station_id_2) AND c.line_id=$line[id] GROUP BY w.id ORDER BY w.date_create DESC");

	$smarty->assign('count', count($ret['rows']));

	$weblogs = array();
	foreach ($ret['rows'] as $row){
		list($y) = explode('-', $row['month_create']);
		$weblogs[$y][] = $row;
	}

	$smarty->assign('weblogs', $weblogs);



	#
	# output
	#

	$smarty->display('page_line_all.txt');
?>