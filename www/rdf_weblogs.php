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
	$result = mysql_query("SELECT * FROM tube_weblogs", $db);
	while($row = mysql_fetch_array($result)){
?>
	<tube:weblog>
		<rdf:Description rdf:about="<?=htmlspecialchars($row[blog_url])?>"/>
		<rdfs:seeAlso rdf:resource="<?=htmlspecialchars($row[blog_url])?>"/>

		<tube:dateadded><?=$row[date_create]?></tube:dateadded>
		<weblog:name><?=htmlspecialchars($row[blog_name])?></weblog:name>
		<weblog:url><?=htmlspecialchars($row[blog_url])?></weblog:url>
		<weblog:authorname><?=htmlspecialchars($row[name])?></weblog:authorname>
<?php if ($row[email_public]){ ?>
		<weblog:authoremail><?=str_replace('@',' AT ',htmlspecialchars($row[email]))?></weblog:authoremail>
<?php } ?>
		<weblog:about><?=htmlspecialchars($row[about])?></weblog:about>

<?php
		$subresult = mysql_query("SELECT * FROM tube_weblog_stations WHERE weblog_id=$row[id]", $db);
		while($subrow = mysql_fetch_array($subresult)){
?>
		<tube:near>
			<tube:stationid><?=$subrow[station_id]?></tube:stationid>
			<tube:relationship><?=htmlspecialchars($subrow[relation])?></tube:relationship>
		</tube:near>

<?php
		}
?>
	</tube:weblog>

<?php
	}
?>

</rdf:RDF>
