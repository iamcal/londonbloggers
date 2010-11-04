<?
	include('include/init.php');


	#
	# get station target data
	#

	$click_boxes = array();
	$box_size = 35;
	$half_box = $box_size / 2;

	$ret = db_fetch("SELECT * FROM tube_stations ORDER BY name ASC");
	foreach ($ret['rows'] as $row){

		$location = unserialize($row['location']);

		# offset, because these positions were set with our old map
		if (is_array($location['centers'])){
			
			foreach (array_keys($location['centers']) as $k){
				$location['centers'][$k][0] -= 76;
				$location['centers'][$k][1] -= 110;

				$x = $location['centers'][$k][0] - $half_box;
				$y = $location['centers'][$k][1] - $half_box;
				$click_boxes[] = array($x, $y, $x+$box_size, $y+$box_size, $row['id']);
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

	echo "var g_station_positions = ".JSON_encode($data).";\n";
	echo "var g_click_boxes = ".JSON_encode($click_boxes).";\n";
?>