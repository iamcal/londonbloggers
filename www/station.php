<?
	include("includes/init.txt");

	$station_row = mysql_fetch_array(mysql_query("SELECT * FROM tube_stations WHERE id=$id",$db));

	$station_row[tag] = preg_replace("![^a-z]!", "", StrToLower($station_row[name]));

	$segx = floor($station_row[main_x]/400)+1;
	$segy = floor($station_row[main_y]/400)+1;

	include("includes/head.txt");
?>

		<td width="220" class="padded">

			<h2><?=$station_row[name]?> Station</h2>
			<br />

			If this is your nearest station, then click the button below.<br />
			<br />
			<form action="submit.php" action="get">
			<input type="hidden" name="station" value="<?=$id?>">
			<span style="padding: 10px; background-color: #ccccff; border: 2px solid #9999cc; width: 300px; text-align: center;">
				<input type="submit" value="Add My Weblog!">
			</span>
			</form>
			<br />
			<br />

			<table border="0" cellpadding="0" cellspacing="0">
				<tr valign="middle">
					<td><img src="images/icon_tube.gif" width="16" height="16"></td>
					<td>&nbsp;</td>
					<td><a href="segment.php?x=<?=$segx?>&amp;y=<?=$segy?>&highlight=<?=$station_row[id]?>">See this station on the tube map.</a></td>
				</tr>
<? if ($station_row[real_x]){ ?>
				<tr>
					<td colspan="3">&nbsp;</td>
				</td>
				<tr valign="middle">
					<td><img src="images/icon_street.gif" width="16" height="16"></td>
					<td>&nbsp;</td>
					<td><a href="http://www.streetmap.co.uk/newmap.srf?x=<?=$station_row[real_x]?>&y=<?=$station_row[real_y]?>&z=1" target="_blank">See this station on a street map.</a></td>
				</tr>
<? } ?>
			</table>
			<br />
			The following lines pass through this station:<br />
			<table border="0" cellpadding="0" cellspacng="0" width="206">
<?
	$result = mysql_query("SELECT * FROM tube_connections WHERE (station_id_1=$id OR station_id_2=$id) GROUP BY line_id",$db);
	while($row = mysql_fetch_array($result)){
		$line_row = mysql_fetch_array(mysql_query("SELECT * FROM tube_lines WHERE id=$row[line_id]",$db));
		insert_line_row($line_row);
	}
?>
			</table>

			<br />
			<br />
			<h2>Weblog Browser</h2>
			<br>
			The weblog browser shows a small menu of sites at this tube station. To use the weblog browser on your own site, copy and paste this code:<br>
			<br>
			<textarea style="width: 208px; height: 100px;" wrap="none">&lt;script src="<?=$cfg[base_url]?>browser.php?ids=<?=$id?>" type="text/javascript"&gt;&lt;/script&gt;</textarea><br>
			<br>
			To see a demo of the browser in action, <a href="browser_demo.php">click here</a>. The demo also includes instructions for getting the browser to display multiple stations and showing a smaller version of the browser.<br>
			<br>

			<h2>Photos</h2>
			<br>
<script language="Javascript" src="photos.php?tag=<?=urlencode($station_row[tag])?>"></script>

		</td>
		<td width="10"><img src="images/space.gif" width="10"  height="10"></td>
		<td width="220" class="padded">

<?
	$result = mysql_query("SELECT w.*,s.station_id FROM tube_weblogs AS w, tube_weblog_stations AS s WHERE w.id=s.weblog_id AND s.station_id=$id ORDER BY w.blog_name ASC",$db);
	$count = mysql_num_rows($result);
	$count_str = ($count)?" ($count)":'';
?>
			<h2>Weblogs At This Station<?=$count_str?></h2>
			<br />
<?
	if (mysql_num_rows($result)){
?>
			The following weblogs are located at <i><?=$station_row[name]?></i>:<br />
			<br />
<?
		while($row = mysql_fetch_array($result)){
			output_blog_info($row);
		}
	}else{
?>
			There are no weblogs located at <i><?=$station_row[name]?></i>. To add your own, click the button on the left.<br />
<?
	}
?>
			<br />
			<h2>Stations Nearby</h2>
			<br />
<?
	$stations = get_stations_within($station_row[real_x], $station_row[real_y], 1609);
	if (count($stations)){

		echo "The following stations are located within a mile of $station_row[name]:<br>\n<br>\n";

		foreach($stations as $station){
			echo "<a href=\"station.php?id=$station[id]\">$station[name]</a><br>\n";
		}
	}else{
		echo "There are no stations within a mile of $station_row[name].<br>\n";
	}
?>
<!--
			<br />
			<i>Note - this data relies on geo-coding information for the stations. At present, not all stations have been geo-coded.</i><br>
-->
		</td>
<?
	include("includes/foot.txt");
?>
