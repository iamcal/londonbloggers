<?php
	include("includes/init.txt");

	$sx = 400*($x-1);
	$sy = 400*($y-1);

	$bdr = 2;

	$max_x = 7;
	$max_y = 5;

	$ymin = $y-1;
	$xmin = $x-1;
	$yadd = $y+1;
	$xadd = $x+1;

	$up_link = ($y==1)?"&nbsp;":"<a href=\"segment.php?x=$x&amp;y=$ymin\"><img src=\"images/arrow_up.gif\" width=\"13\" height=\"13\" border=\"0\" alt=\"Up\" /></a>";
	$down_link = ($y==$max_y)?"&nbsp;":"<a href=\"segment.php?x=$x&amp;y=$yadd\"><img src=\"images/arrow_down.gif\" width=\"13\" height=\"13\" border=\"0\" alt=\"Down\" /></a>";

	$left_link = ($x==1)?"&nbsp;":"<a href=\"segment.php?x=$xmin&amp;y=$y\"><img src=\"images/arrow_left.gif\" width=\"13\" height=\"13\" border=\"0\" alt=\"Left\" /></a>";
	$right_link = ($x==$max_x)?"&nbsp;":"<a href=\"segment.php?x=$xadd&amp;y=$y\"><img src=\"images/arrow_right.gif\" width=\"13\" height=\"13\" border=\"0\" alt=\"Right\" /></a>";

	$segment_row = mysql_fetch_array(mysql_query("SELECT * FROM tube_segments WHERE seg_x=$x AND seg_y=$y",$db));

	include("includes/head.txt");
?>

		<td colspan="3">
			<div class="padded">
				Click on the name of your nearest station. You can move around the map using the directional arrows.<br />
				<br />
				To return to the full map view, <a href="map.php">click here</a>.<br />
				<br />
			</div>
	<table border="0">
		<tr>
			<td></td>
			<td align="center"><?=$up_link?></td>
			<td></td>
		</tr>
		<tr valign="middle">
			<td><?=$left_link?></td>
			<td><div style="position: relative;">
				<div style="position: relative; top: 0px; left: 0px; z-index: 1;"><img src="images/<?=sprintf('%02d',$x)?>_<?=sprintf('%02d',$y)?>.gif" border="0" usemap="#mainmap" width="<?=$segment_row['w']?>" height="<?=$segment_row['h']?>" style="border: 2px solid #000000;" alt="" /></div>
<?php
	if ($highlight){
		$row = mysql_fetch_array(mysql_query("SELECT * FROM tube_stations WHERE id='$highlight'",$db));
		$row = get_row_coords($row);
		$w = $row['x_2'] - $row['x'];
		$h = $row['y_2'] - $row['y'];
?>
				<div style="position: absolute; left: <?=$row['x']?>px; top: <?=$row['y']?>px; width: <?=$w?>px; height: <?=$h?>px; z-index: 2; border: 1px solid #0000ff;"><a href="station.php?id=<?=$row['id']?>"><img src="images/space.gif" width="<?=$w?>" height="<?=$h?>" border="0" alt="" /></a></div>
<?php
	}
?>
			</div></td>
			<td><?=$right_link?></td>
		</tr>
		<tr>
			<td></td>
			<td align="center"><?=$down_link?></td>
			<td></td>
		</tr>
	</table>
			<div class="padded">
				<br />
				<!-- la la la -->
			</div>
		</td>

<map name="mainmap" id="mainmap">
<?php
	$result = mysql_query("SELECT * FROM tube_stations WHERE main_x+main_w>$sx AND main_x<=$sx+$segment_row[w] AND main_y+main_h>$sy AND main_y<=$sy+$segment_row[h]",$db);
	while($row = mysql_fetch_array($result)){

		#set
		$row['x'] = $row['main_x'] - $sx;
		$row['y'] = $row['main_y'] - $sy;
		$row['x_2'] = $row['x'] + $row['main_w'];
		$row['y_2'] = $row['y'] + $row['main_h'];

		#expand
		$row['x']-=$bdr;
		$row['y']-=$bdr;
		$row['x_2']+=$bdr;
		$row['y_2']+=$bdr;

		#restrict
		if ($row['x'] < 0){$row['x'] = 0;}
		if ($row['y'] < 0){$row['y'] = 0;}
		if ($row['x_2'] > $segment_row['w']){$row['x_2'] = $segment_row['w'];}
		if ($row['y_2'] > $segment_row['h']){$row['y_2'] = $segment_row['h'];}

		list($count) = mysql_fetch_array(mysql_query("SELECT COUNT(w.id) FROM tube_weblogs AS w, tube_weblog_stations AS s WHERE w.id=s.weblog_id AND s.station_id=$row[id]",$db));
		$s = ($count==1)?'':'s';
		$extra = ($count)?" ($count weblog$s)":'';

		echo "<area coords=\"$row[x],$row[y],$row[x_2],$row[y_2]\" href=\"station.php?id=$row[id]\" alt=\"$row[name]$extra\" title=\"$row[name]$extra\"/>\n";
	}


	function get_row_coords($row){
		global $sx, $sy, $bdr, $segment_row;

		#set
		$row['x'] = $row['main_x'] - $sx;
		$row['y'] = $row['main_y'] - $sy;
		$row['x_2'] = $row['x'] + $row['main_w'];
		$row['y_2'] = $row['y'] + $row['main_h'];

		#expand
		$row['x']-=$bdr;
		$row['y']-=$bdr;
		$row['x_2']+=$bdr;
		$row['y_2']+=$bdr;

		#restrict
		if ($row['x'] < 0){$row['x'] = 0;}
		if ($row['y'] < 0){$row['y'] = 0;}
		if ($row['x_2'] > $segment_row['w']){$row['x_2'] = $segment_row['w'];}
		if ($row['y_2'] > $segment_row['h']){$row['y_2'] = $segment_row['h'];}

		return $row;
	}
?>
</map>

<?php
	include("includes/foot.txt");
?>
