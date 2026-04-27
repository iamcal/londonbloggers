<?php
	include('../../include/init.php');



	if (($_REQUEST['method'] ?? null) =='get_stations'){

		$ret = db_fetch("SELECT id, name FROM tube_stations ORDER BY name ASC");

		$names = array();
		foreach ($ret['rows'] as $row) $names[$row['id']] = $row['name'];

		api_reply(array(
			'ok'	=> 1,
			'names'	=> $names,
		));
	}

	if (($_REQUEST['method'] ?? null) =='get_lines'){

		$ret = db_fetch("SELECT id, name FROM tube_lines ORDER BY name ASC");

		$lines = array();
		foreach ($ret['rows'] as $row) $lines[$row['id']] = $row['name'];

		api_reply(array(
			'ok'	=> 1,
			'lines'	=> $lines,
		));
	}


	if (($_REQUEST['method'] ?? null) == 'get_station'){

		$id_enc = intval($_REQUEST['id'] ?? 0);

		$row = db_single(db_fetch("SELECT * FROM tube_stations WHERE id=$id_enc"));

		if (!($row['id'] ?? null)) api_error("station $id_enc not found");

		$row['location'] = unserialize($row['location']);


		#
		# lines
		#

		$line_ids = array();
		$ret2 = db_fetch("SELECT * FROM tube_connections WHERE station_id_1=$id_enc OR station_id_2=$id_enc ORDER BY line_id ASC");
		foreach ($ret2['rows'] as $row2){
			$line_ids[$row2['line_id']] = 1;
		}

		$lines = array();
		$raw_lines = array();

		if (count($line_ids)){

			$line_ids = implode(', ', array_keys($line_ids));
			$ret3 = db_fetch("SELECT * FROM tube_lines WHERE id IN ($line_ids)");

			foreach ($ret3['rows'] as $row3){
				$lines[$row3['id']] = $row3;
			}

			$raw_lines = $ret3['rows'];
		}


		#
		# conns
		#

		$cons = array();
		foreach ($ret2['rows'] as $row2){

			$station_id = $row2['station_id_1'] == $id_enc ? $row2['station_id_2'] : $row2['station_id_1'];
			$station = db_single(db_fetch("SELECT * FROM tube_stations WHERE id=$station_id"));

			$cons[] = array(
				'id' => $row2['id'],
				'remote_id' => $station_id,
				'remote_name' => $station['name'],
				'line_id' => $row2['line_id'],
				'line_name' => $lines[$row2['line_id']]['name'],
			);
		}


		api_reply(array(
			'ok'	=> 1,
			'row'	=> $row,
			'lines'	=> $raw_lines,
			'cons'	=> $cons,
		));
	}

	if (($_REQUEST['method'] ?? null) == 'get_all_centers'){

		$ret = db_fetch("SELECT location FROM tube_stations");
		$centers = array();

		foreach ($ret['rows'] as $row){
			$location = unserialize($row['location']);
			if (count($location['centers'] ?? array())){
				$centers = array_merge($centers, $location['centers']);
			}
		}

		api_reply(array(
			'ok'	=> 1,
			'centers'=> $centers,
		));

	}



	if (($_REQUEST['method'] ?? null) == 'save_station'){

		$id_enc = intval($_REQUEST['id'] ?? 0);

		$location = array(
			'centers' => array(),
		);

		$center_pairs = explode('|', $_REQUEST['centers'] ?? '');
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


	if (($_REQUEST['method'] ?? null) == 'edit_con'){

		$id_enc = intval($_REQUEST['con_id'] ?? 0);
		$row = db_single(db_fetch("SELECT * FROM tube_connections WHERE id=$id_enc"));

		if (!($row['id'] ?? null)) api_error("con not found");

		$update_dst = $row['station_id_1'] == ($_REQUEST['src'] ?? null) ? 'station_id_2' : 'station_id_1';

		db_update('tube_connections', array(
			$update_dst => intval($_REQUEST['dst'] ?? 0),
			'line_id' => intval($_REQUEST['line'] ?? 0),
		), "id=$id_enc");

		api_reply(array(
			'ok'	=> 1,
		));
	}

	if (($_REQUEST['method'] ?? null) == 'delete_con'){

		$id_enc = intval($_REQUEST['con_id'] ?? 0);
		db_write("DELETE FROM tube_connections WHERE id=$id_enc");

		api_reply(array(
			'ok'	=> 1,
		));
	}

	if (($_REQUEST['method'] ?? null) == 'add_con'){

		db_insert('tube_connections', array(
			'station_id_1'	=> intval($_REQUEST['src'] ?? 0),
			'station_id_2'	=> intval($_REQUEST['dst'] ?? 0),
			'line_id'	=> intval($_REQUEST['line'] ?? 0),
		));

		api_reply(array(
			'ok'	=> 1,
		));
	}

	if (($_REQUEST['method'] ?? null) == 'set_station_label'){

		$id_enc = intval($_REQUEST['id'] ?? 0);

		$row = db_single(db_fetch("SELECT * FROM tube_stations WHERE id=$id_enc"));

		$location = unserialize($row['location'] ?? '');
		if (!is_array($location)) $location = array();

		$location['label'] = array(
			'l' => intval($_REQUEST['l'] ?? 0),
			'r' => intval($_REQUEST['r'] ?? 0),
			't' => intval($_REQUEST['t'] ?? 0),
			'b' => intval($_REQUEST['b'] ?? 0),
		);

		db_update('tube_stations', array(
			'location' => AddSlashes(serialize($location)),
		), "id=$id_enc");

		api_reply(array(
			'ok'	=> 1,
		));
	}


	api_error("Method \"".($_REQUEST['method'] ?? '')."\" not found");


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

