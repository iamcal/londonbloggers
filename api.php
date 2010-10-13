<?
	include('include/init.php');



	if ($_REQUEST['method'] == 'get_stations'){

		$ret = db_fetch("SELECT id, name FROM tube_stations ORDER BY name ASC");

		$names = array();
		foreach ($ret['rows'] as $row) $names[$row['id']] = $row['name'];

		api_reply(array(
			'ok'	=> 1,
			'names'	=> $names,
		));
	}


	if ($_REQUEST['method'] == 'get_station'){

		$id_enc = intval($_REQUEST['id']);

		$row = db_single(db_fetch("SELECT * FROM tube_stations WHERE id=$id_enc"));

		if (!$row['id']) api_error("station $id_enc not found");

		$row['location'] = unserialize($row['location']);

		$line_ids = array();
		$ret2 = db_fetch("SELECT line_id FROM tube_connections WHERE station_id_1=$id_enc OR station_id_2=$id_enc");
		foreach ($ret2['rows'] as $row2){
			$line_ids[$row2['line_id']] = 1;
		}
		$line_ids = implode(', ', array_keys($line_ids));
		$ret3 = db_fetch("SELECT * FROM tube_lines WHERE id IN ($line_ids)");

		api_reply(array(
			'ok'	=> 1,
			'row'	=> $row,
			'lines'	=> $ret3['rows'],
		));
	}

	if ($_REQUEST['method'] == 'get_all_centers'){

		$ret = db_fetch("SELECT location FROM tube_stations");
		$centers = array();

		foreach ($ret['rows'] as $row){
			$location = unserialize($row['location']);
			if (count($location['centers'])){
				$centers = array_merge($centers, $location['centers']);
			}
		}

		api_reply(array(
			'ok'	=> 1,
			'centers'=> $centers,
		));

	}



	if ($_REQUEST['method'] == 'save_station'){

		$id_enc = intval($_REQUEST['id']);

		$location = array(
			'centers' => array(),
		);

		$center_pairs = explode('|', $_REQUEST['centers']);
		foreach ($center_pairs as $pair){
			list($x, $y) = explode(',', $pair);
			$location['centers'][] = array(intval($x), intval($y));
		}

		db_update('tube_stations', array(
			'location' => AddSlashes(serialize($location)),
		), "id=$id_enc");

		api_reply(array(
			'ok'	=> 1,
		));
	}


	api_error("Method \"$_REQUEST[method]\" not found");


	############################################################

	function api_error($msg){
		api_reply(array(
			'ok'	=> 0,
			'error'	=> $msg,
		));
	}

	function api_reply($obj){
		header('Content-type: text/plain');
		echo JSON_encode($obj);
		exit;
	}

?>