<?
	include('include/init.php');


	#
	# get station target data
	#

	$ret = db_fetch("SELECT * FROM tube_stations ORDER BY name ASC");
	foreach ($ret['rows'] as $row){

		$location = unserialize($row['location']);

		if ($_GET['offset'] && is_array($location['centers'])){
			
			foreach (array_keys($location['centers']) as $k){
				$location['centers'][$k][0] -= 76;
				$location['centers'][$k][1] -= 110;
			}
		}

		$data[$row['id']] = array(
			'name'	=> $row['name'],
			'pts'	=> is_array($location['centers']) ? $location['centers'] : array(),
		);
	}


	#
	# output long cached data
	#

	header("Content-Type: text/plain; charset=utf-8");
	header("Expires: Fri, 10 Jan 2020 23:30:00 GMT");
	header("Cache-Control: max-age=315360000");

	echo "var g_station_positions = ";
	echo JSON_encode($data);
	echo ";\n";
?>