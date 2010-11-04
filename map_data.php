<?
	include('include/init.php');


	#
	# get station target data
	#

	$click_boxes = array();
	$box_size = 25;
	$half_box = $box_size / 2;

	$ret = db_fetch("SELECT * FROM tube_stations ORDER BY name ASC");
	foreach ($ret['rows'] as $row){

		$location = unserialize($row['location']);

		$xs = array();
		$ys = array();

		#
		# offset, because these positions were set with our old map
		#

		if (is_array($location['centers'])){

			foreach (array_keys($location['centers']) as $k){
				$location['centers'][$k][0] -= 76;
				$location['centers'][$k][1] -= 110;

				$xs[] = $location['centers'][$k][0];
				$ys[] = $location['centers'][$k][1];

				$x = $location['centers'][$k][0] - $half_box;
				$y = $location['centers'][$k][1] - $half_box;
				$click_boxes[] = array($x, $y, $x+$box_size, $y+$box_size, $row['id']);
			}
		}

		if (is_array($location['label'])){

			$l = $location['label'];
			$click_boxes[] = array($l['l'], $l['t'], $l['r'], $l['b'], $row['id']);
		}


		#
		# find center point
		#

		sort($xs);
		sort($ys);
		$x = ($xs[0] + array_pop($xs))/2;
		$y = ($ys[0] + array_pop($ys))/2;


		#
		# the row we need
		#

		$data[$row['id']] = array(
			'name'	=> $row['name'],
			'x'	=> $x,
			'y'	=> $y,
		);
	}


	#
	# output long cached data
	#

	header("Content-Type: text/plain; charset=utf-8");
	header("Expires: Fri, 10 Jan 2020 23:30:00 GMT");
	header("Cache-Control: max-age=315360000");

	echo "var g_stations = ".JSON_encode($data).";\n";
	echo "var g_click_boxes = ".JSON_encode($click_boxes).";\n";
?>