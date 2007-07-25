<?php
	include("includes/init.txt");
	header("content-type: text/xml");
?>
<rdf:RDF
    xmlns:tube="http://www.iamcal.com/xml/tube/0.1/"
    xmlns:weblog="http://www.iamcal.com/xml/weblog/0.1/"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#"
>

<?php
	$result = mysql_query("SELECT * FROM tube_stations", $db);
	while($row = mysql_fetch_array($result)){
?>
	<tube:station>
		<rdf:Description rdf:about="http://www.iamcal.com/misc/londonbloggers/station.php?id=<?=$row[id]?>"/>
		<rdfs:seeAlso rdf:resource="http://www.iamcal.com/misc/londonbloggers/station.php?id=<?=$row[id]?>"/>

		<tube:stationid><?=$row[id]?></tube:stationid>
		<tube:stationname><?=htmlentities($row[name])?></tube:stationname>

		<tube:geox><?=$row[real_x]?></tube:geox>
		<tube:geoy><?=$row[real_y]?></tube:geoy>

		<tube:mapx><?=$row[main_x]?></tube:mapx>
		<tube:mapy><?=$row[main_y]?></tube:mapy>
		<tube:mapw><?=$row[main_w]?></tube:mapw>
		<tube:maph><?=$row[main_h]?></tube:maph>

<?php
		$subresult = mysql_query("SELECT * FROM tube_connections WHERE (station_id_1=$row[id]) OR (station_id_2=$row[id] AND one_way=0)", $db);
		while($subrow = mysql_fetch_array($subresult)){
			$station = ($subrow[station_id_1] == $row[id])?$subrow[station_id_2]:$subrow[station_id_1];
?>
		<tube:connection>
			<tube:stationid><?=$station?></tube:stationid>
			<tube:lineid><?=$subrow[line_id]?></tube:lineid>
		</tube:connection>

<?php
	}
?>
	</tube:station>

<?php
	}
?>

</rdf:RDF>
