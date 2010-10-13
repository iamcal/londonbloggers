<?
	include('include/init.php');


	$ret = db_fetch("SELECT * FROM tube_lines");
?>

<table border="1" cellpadding="10">
<? foreach ($ret['rows'] as $row){ ?>
	<tr>
<? if ($row['has_line']){ ?>
		<td><div style="height: 4px; width: 100px; background-color: #fff; border-top: 6px solid <?=$row['color']?>; border-bottom: 6px solid <?=$row['color']?>"></div></td>
<? }else{ ?>
		<td><div style="height: 16px; width: 100px; background-color: <?=$row['color']?>"></div></td>
<? } ?>
		<td><?=$row['name']?>
<?
	$ret2 = db_fetch("SELECT s.* FROM tube_stations AS s, tube_connections AS c WHERE c.line_id=$row[id] AND (c.station_id_1=s.id OR c.station_id_2=s.id) GROUP BY s.id LIMIT 5;");
	$names = array();
	foreach ($ret2['rows'] as $row2) $names[] = $row2['name'];
?>
		<td><?=implode(', ', $names)?></td>
	</tr>
<? } ?>
</table>