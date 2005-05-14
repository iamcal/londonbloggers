<?
	include("includes/init.txt");

	include("includes/head.txt");
?>

		<td colspan="3">
			<div class="padded">
				Click on the map to zoom in and find your station, or choose from the list of lines on the right for a list of stations along that line.<br />
				<br />
				To see a list of stations with weblogs already registered, <a href="weblogs.php">click here</a>.<br />
				<br />
			</div>
			<img src="images/outline.gif" width="450" height="321" border="0" usemap="#mainmap" alt="" /><br>
		</td>

<map name="mainmap" id="mainmap">
<?
	$mul = 450/7;
	for($y=1; $y<=5; $y++){
		for($x=1; $x<=7; $x++){
			$xp = round($mul*($x-1));
			$yp = round($mul*($y-1));
			$xp2 = round($mul*($x));
			$yp2 = round($mul*($y));
			echo "<area coords=\"$xp,$yp,$xp2,$yp2\" href=\"segment.php?x=$x&amp;y=$y\" alt=\"\" />\n";
		}
	}
?>
</map>


<?
	include("includes/foot.txt");
?>
