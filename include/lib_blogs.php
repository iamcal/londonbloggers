<?

	function get_StationList($id){

		$id = intval($id);

		$out = array();
		$ret = db_fetch("SELECT s.* FROM tube_weblog_stations AS ws, tube_stations AS s WHERE ws.station_id=s.id AND ws.weblog_id=$id ORDER BY s.name ASC");

		foreach ($ret['rows'] as $row){
			$out[] = "<a href=\"station.php?id=$row[id]\">$row[name]</a>";
		}
		return implode(", ",$out);
	}

?>