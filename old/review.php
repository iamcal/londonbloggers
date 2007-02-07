<?
	include("includes/init.txt");

	#
	# approve a batch?
	#

	if ($HTTP_POST_VARS[ids]){

		$ids = explode(',', $HTTP_POST_VARS[ids]);

		foreach ($ids as $id){

			if ($HTTP_POST_VARS["ok$id"]){

				mysql_query("UPDATE tube_weblogs SET approved=1 WHERE id=$id");
			}else{
				mysql_query("DELETE FROM tube_weblog_stations WHERE weblog_id=$id");
				mysql_query("DELETE FROM tube_weblogs WHERE id=$id");
			}
		}

		header('Location: /review.php?done=1');
		exit;
	}


	#
	# fetch a batch for approval
	#

	$rows = array();
	$ids = array();

	list($count) = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM tube_weblogs WHERE approved=0"));

	$result = mysql_query("SELECT * FROM tube_weblogs WHERE approved=0 ORDER BY date_create ASC LIMIT 50");
	while ($row = mysql_fetch_array($result)){

		$row[stations] = array();

		$result2 = mysql_query("SELECT * FROM tube_weblog_stations WHERE weblog_id=$row[id]");
		while ($row2 = mysql_fetch_array($result2)){

			$row[stations][] = $row2[station_id];
		}

		$rows[] = $row;
		$ids[] = $row[id];
	}
?>
<html>
<head>
<title>Admin - Review</title>
<script>

function highlight(name, col){
	document.getElementById(name).style.backgroundColor = col;
}

</script>
</head>
<body>

<p><?=$count?> entries in the review queue</p>

<form action="/review.php" method="post">
<input type="hidden" name="ids" value="<?=implode(',', $ids)?>" />

<table border="1">
<? foreach ($rows as $row){ ?>
	<tr id="row<?=$row[id]?>">
		<td><?=HtmlSpecialChars(StripSlashes($row[blog_name]))?></td>
		<td><?=HtmlSpecialChars(StripSlashes($row[blog_url]))?></td>
		<td><?=HtmlSpecialChars(StripSlashes($row[name]))?></td>
		<td><?=implode(', ', $row[stations])?></td>
		<td><?=HtmlSpecialChars(StripSlashes($row[about]))?></td>
		<td>
			<input type="radio" name="ok<?=$row[id]?>" id="ok<?=$row[id]?>-1" value="1" onclick="highlight('row<?=$row[id]?>', '#fff')" checked /> <label for="ok<?=$row[id]?>-1" onclick="highlight('row<?=$row[id]?>', '#fff')">OK</label>
			<input type="radio" name="ok<?=$row[id]?>" id="ok<?=$row[id]?>-0" value="0" onclick="highlight('row<?=$row[id]?>', '#f99')" /> <label for="ok<?=$row[id]?>-0" onclick="highlight('row<?=$row[id]?>', '#f99')">Spam</label>
		</td>
	</tr>
<? } ?>
	<tr>
		<td colspan="3" align="left"><input type="submit" value="Next" /></td>
		<td colspan="3" align="right"><input type="submit" value="Next" /></td>
	</tr>
</table>



</form>

</body>
</html>