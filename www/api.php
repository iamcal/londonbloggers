<?php
	include('../include/init.php');


	if ($_REQUEST['method'] == 'weblog_count'){

		$id = intval($_REQUEST['station']);

		list($num) = db_list(db_fetch("SELECT COUNT(w.id) FROM tube_weblogs AS w, tube_weblog_stations AS s WHERE w.id=s.weblog_id AND s.station_id=$id"));

		api_reply(array(
			'ok'		=> 1,
			'station'	=> $id,
			'count'		=> intval($num),
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

