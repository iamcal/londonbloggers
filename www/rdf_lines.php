<?php
	include("includes/init.txt");
	header("content-type: text/plain");
?>
<rdf:RDF
    xmlns:tube="http://www.iamcal.com/xml/tube/0.1/"
    xmlns:weblog="http://www.iamcal.com/xml/weblog/0.1/"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#"
>

<?php
	$result = mysql_query("SELECT * FROM tube_lines", $db);
	while($row = mysql_fetch_array($result)){
?>
	<tube:line>
		<rdf:Description rdf:about="http://www.iamcal.com/misc/londonbloggers/stations.php?line=<?=$row[id]?>"/>
		<rdfs:seeAlso rdf:resource="http://www.iamcal.com/misc/londonbloggers/stations.php?line=<?=$row[id]?>"/>

		<tube:lineid><?=$row[id]?></tube:lineid>
		<tube:linename><?=$row[name]?></tube:linename>
	</tube:line>

<?php
	}
?>

</rdf:RDF>
