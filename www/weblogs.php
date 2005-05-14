<?
	include("includes/init.txt");

	include("includes/head.txt");
?>

		<td colspan="3">
			<div class="padded">

				<h2>All The Weblogs</h2>
				<br />
				There are weblogs at the following stations:<br />
				<br />

<?
	$result = mysql_query("SELECT s.*,COUNT(ws.id) AS count FROM tube_stations AS s, tube_weblog_stations AS ws WHERE s.id=ws.station_id GROUP BY ws.station_id ORDER BY s.name ASC",$db);
	while($row = mysql_fetch_array($result)){
		$s = ($row[count]==1)?'':'s';
		echo "<a href=\"station.php?id=$row[id]\">$row[name]</a> ($row[count] weblog$s)<br />\n";
	}
?>

			</div>
		</td>

<?
	include("includes/foot.txt");
?>
