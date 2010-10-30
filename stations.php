<?
	include("include/init.php");


	#
	# old style url?
	#

	if (!$_SERVER['REDIRECT_URL']){
		header("location: /stations/");
		exit;
	}


	#
	# fetch stations with blogs
	#

	$stations = array();

	$ret = db_fetch("SELECT * FROM tube_stations ORDER BY name ASC");
	foreach ($ret['rows'] as $row){
		$row['count'] = 0;
		$stations[$row['id']] = $row;
	}


	$ret = db_fetch("SELECT station_id, COUNT(id) AS num FROM tube_weblog_stations GROUP BY station_id");
	foreach ($ret['rows'] as $row){
		if ($stations[$row['station_id']]){
			$stations[$row['station_id']]['count'] = $row['num'];
		}
	}

	$smarty->assign('stations', $stations);


	#
	# output
	#

	$smarty->display('page_stations.txt');
?>
