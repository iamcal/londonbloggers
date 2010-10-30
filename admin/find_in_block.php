<?
	#
	# $Id$
	#

	include('../include/init.php');


	#
	# get station target data
	#

	$stations = array();

	$ret = db_fetch("SELECT * FROM tube_stations");
	foreach ($ret['rows'] as $row){

		#
		# only deal with stations we've positioned
		#

		$location = unserialize($row['location']);
		if (!is_array($location['centers'])) continue;


		#
		# offset, because these positions were set with our old map
		#

		$xs = array();
		$ys = array();

		foreach (array_keys($location['centers']) as $k){
			$location['centers'][$k][0] -= 76;
			$location['centers'][$k][1] -= 110;

			$xs[] = $location['centers'][$k][0];
			$ys[] = $location['centers'][$k][1];
		}

		sort($xs);
		sort($ys);

		$x = ($xs[0] + array_pop($xs))/2;
		$y = ($ys[0] + array_pop($ys))/2;

		$stations[$row['id']] = array(
			'name'	=> $row['name'],
			'x'	=> $x,
			'y'	=> $y,
		);
	}


	#
	# bounds
	#

	$lo_x = $stations[616]['x']; # wood lane
	$hi_x = $stations[109]['x']; # aldgate

	$lo_y = $stations[39]['y']; # marylebone
	$hi_y = $stations[44]['y']; # embankment


	#
	# filter
	#

	$matched = array();

	foreach ($stations as $k => $row){
		if ($row['x'] >= $lo_x && $row['x'] <= $hi_x){
			if ($row['y'] >= $lo_y && $row['y'] <= $hi_y){

				$matched[$k] = $row['name'];
			}
		}
	}


	dumper($matched);
	echo implode(',', array_keys($matched));
?>