<?
	include("includes/init.txt");

	include("includes/head.txt");

	$line_row = mysql_fetch_array(mysql_query("SELECT * FROM tube_lines WHERE id=$line",$db));
	$line_row[name] = str_replace("National Rail - ", "", $line_row[name]);
	$line_row[color] = ($line_row[color])?$line_row[color]:'transparent';
?>

		<td colspan="3">
			<div class="padded">
				<div style="border: 1px solid black; background-color: <?=$line_row[color]?>;">&nbsp;</div>
				<h1><?=$line_row[name]?></h1>
				<div style="border: 1px solid black; background-color: <?=$line_row[color]?>;">&nbsp;</div>
			</div>

<table cellpadding="0" cellspacing="0" border="0" width="450">
	<tr valign="top">
		<td width="220" class="padded">
			<h2>Stations</h2>
			<br>

<?
	$result = mysql_query("SELECT s.* FROM tube_stations AS s, tube_connections AS c WHERE c.line_id='$line' AND (c.station_id_1=s.id OR (c.station_id_2=s.id AND c.one_way=0)) GROUP BY s.id ORDER BY s.name ASC",$db);
	while($row = mysql_fetch_array($result)){
		echo "<b>&bull;</b> <a href=\"station.php?id=$row[id]\">".StripSlashes($row[name])."</a><br />";
	}
?>

		</td>
		<td width="10"><img src="images/space.gif" width="10"  height="10"></td>
		<td width="220" class="padded">
			<h2>Weblogs</h2>
			<br>

<?
	$result = mysql_query("SELECT w.*,s.station_id FROM tube_weblogs AS w, tube_weblog_stations AS s, tube_connections AS c WHERE w.id=s.weblog_id AND (s.station_id=c.station_id_1 OR s.station_id=c.station_id_2) AND c.line_id=$line GROUP BY w.id ORDER BY w.blog_name ASC",$db);
	while($row = mysql_fetch_array($result)){
		echo "<b>&bull;</b> <a href=\"weblog.php?id=$row[id]\">".htmlentities(StripSlashes($row[blog_name]))."</a><br />\n";
	}
	if (mysql_num_rows($result) == 0){
		echo "<b>&bull;</b> <i>none</i><br />\n";
	}
?>

		</td>
	</tr>
</table>

		</td>

<?
	include("includes/foot.txt");
?>
