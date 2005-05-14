<?
	include("includes/init.txt");

	include("includes/head.txt");
?>

		<td colspan="3">
			<div class="padded">

				<h2>London Weblog #<?=$id?></h2>
				<br>

<?
	$row = mysql_fetch_array(mysql_query("SELECT * FROM tube_weblogs WHERE id='$id'", $db));

	#fix url?
	if (!preg_match("/^http\:\/\//i",$row[blog_url])){
		$row[blog_url] = "http://$row[blog_url]";
	}

	echo "<b style=\"font-size: 13px;\"><a href=\"$row[blog_url]\" target=\"_blank\">".htmlentities($row[blog_name])."</a></b> ";
	if ($row[email_public]){
		echo "by <a href=\"mailto:$row[email]\">".htmlentities($row[name])."</a><br />\n";
	}else{
		echo "by ".htmlentities($row[name])."<br />\n";
	}
	$station_list = get_StationList($row[id]);
	echo "Stations: $station_list<br />\n";
	echo "<i>&quot;".htmlentities($row[about])."&quot;</i><br />\n";
	echo "<br />\n";
?>
				<a href="weblogs.php">Click here</a> to see all the weblogs.

			</div>
		</td>

<?
	include("includes/foot.txt");
?>
