<?
	include("includes/init.txt");
	include("lib.txt");

	include("includes/head.txt");

	$set_labels = array();
	$set_extras = array();

	function add_set($label,$extra){
		global $set_labels, $set_extras;
		$set_labels[] = $label;
		$set_extras[] = $extra;
	}

	add_set("Underground (inc. DLR and Tramlink)","line_id<15");
	add_set("National Rail","line_id>14");
	add_set("Underground &amp; National Rail","1");
?>

<form action="paths.php" method="post">

Using <select name="set">
<?
	foreach(array_keys($set_labels) as $k){
		$s = ($k == $set)?' selected':'';
		echo "<option value=\"$k\"$s>$set_labels[$k]</option>";
	}
?>
</select>,<br>
<br>
find the path between <select name="from">
<?
	$result = mysql_query("SELECT * FROM tube_stations ORDER BY name ASC",$db);
	while($row = mysql_fetch_array($result)){
		$s = ($row[id] == $from)?' selected':'';
		echo "<option value=\"$row[id]\"$s>$row[name]</option>\n";
	}
?>
</select> and <select name="to">
<?
	$result = mysql_query("SELECT * FROM tube_stations ORDER BY name ASC",$db);
	while($row = mysql_fetch_array($result)){
		$s = ($row[id] == $to)?' selected':'';
		echo "<option value=\"$row[id]\"$s>$row[name]</option>\n";
	}
?>
</select> <input type="submit" value="go"><br>
</form>
<br>
<br>

<?

	if ($from && $to){


		$tree = tree_Load();
		$start_nodes = tree_NodesAt($from); #west croydon
		$end_nodes = tree_NodesAt($to); #waterloo

		$paths = tree_ShortestPaths($tree, $start_nodes, $end_nodes);

		$count = count($paths);
		echo "<b>Found $count routes from $from to $to:</b><br><br>";

		foreach($paths as $path){
			foreach($path[steps] as $step){
				list($station,$line) = explode('_',$step);
				$station = tree_GetNodeName($station);
				$line = tree_GetLineName($line);
				echo "$station on $line<br>\n";
			}
			echo "Approx journey time: $path[distance]<br>\n";
			echo "<br>\n";
		}
	}

	include("includes/foot.txt");
?>
