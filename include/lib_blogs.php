<?
	######################################################################

	function get_StationList($id){

		$id = intval($id);

		$out = array();
		$ret = db_fetch("SELECT s.* FROM tube_weblog_stations AS ws, tube_stations AS s WHERE ws.station_id=s.id AND ws.weblog_id=$id ORDER BY s.name ASC");

		foreach ($ret['rows'] as $row){
			$out[] = "<a href=\"/stations/$row[id]/\">$row[name]</a>";
		}
		return implode(", ",$out);
	}

	######################################################################

	function insert_line_row($row){

		$row[name] = str_replace("National Rail - ", "", $row[name]);
		if ($row['color']){
?>
					<tr>
						<td width="40"><a href="stations.php?line=<?=$row[id]?>" style="text-decoration: none;"><span style="background-color: <?=$row[color]?>;"><img src="images/space.gif" width="40" height="4" border="0" alt="<?=$row[name]?>"></span></a></td>
						<td width="4">&nbsp;</td>
						<td><a href="stations.php?line=<?=$row[id]?>"><?=$row[name]?></a></td>
					</tr>
<?
		}else{
?>
					<tr>
						<td colspan="3"><b>&bull;</b> <a href="stations.php?line=<?=$row[id]?>"><?=$row[name]?></a></td>
					</tr>
<?
		}
	}

	######################################################################

	function get_stations_within($x, $y, $range){

		$range_sq = $range * $range;
		$rows = array();

		$ret = db_fetch("SELECT * FROM tube_stations WHERE real_x>=$x-$range AND real_x<=$x+$range AND real_y>=$y-$range AND real_y<=$y+$range");
		foreach ($ret['rows'] as $row){

			$dif_x = abs($x - $row['real_x']);
			$dif_y = abs($y - $row['real_y']);
			if ($dif_x && $dif_y){
				$dist = ($dif_x * $dif_x) + ($dif_y * $dif_y);
				if ($dist <= $range_sq){
					$rows[] = $row;
				}
			}
		}

		return $rows;
	}

	######################################################################
?>