<?
	#
	# $Id$
	#

	include("include/init.php");


	#
	# fetch line details
	#

	$line = db_single(db_fetch("SELECT * FROM tube_lines WHERE id=".intval($_GET['id'])));
	$line['name'] = str_replace("National Rail - ", "", $line['name']);
	$line['color'] = $line['color'] ? $line['color'] : 'transparent';

	$smarty->assign('line', $line);


	#
	# fetch stations
	#

	$ret = db_fetch("SELECT s.* FROM tube_stations AS s, tube_connections AS c WHERE c.line_id='$line[id]' AND (c.station_id_1=s.id OR (c.station_id_2=s.id AND c.one_way=0)) GROUP BY s.id ORDER BY s.name ASC");

	$smarty->assign('stations', $ret['rows']);


	#
	# fetch weblogs
	#

	$ret = db_fetch("SELECT w.*, s.station_id FROM tube_weblogs AS w, tube_weblog_stations AS s, tube_connections AS c WHERE w.id=s.weblog_id AND (s.station_id=c.station_id_1 OR s.station_id=c.station_id_2) AND c.line_id=$line[id] GROUP BY w.id ORDER BY w.blog_name ASC");

	$smarty->assign('weblogs', $ret['rows']);



	#
	# output
	#

	$smarty->display('page_stations.txt');
?>