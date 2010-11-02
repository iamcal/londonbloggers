<?
	include("include/init.php");

	loadlib('blogs');


	#
	# grab station
	#

	$id = intval($_GET['id']);

	$station = db_single(db_fetch("SELECT * FROM tube_stations WHERE id=$id"));
	if (!$station['id']) error_404();

	$smarty->assign_by_ref('station', $station);


	#
	# get blogs
	#

	$ret2 = db_fetch("SELECT w.*,s.station_id FROM tube_weblogs AS w, tube_weblog_stations AS s WHERE w.id=s.weblog_id AND s.station_id=$id ORDER BY w.blog_name ASC");

	$smarty->assign_by_ref('blogs', $ret2['rows']);


	#
	# output
	#

	$smarty->display('page_station_all.txt');
?>
