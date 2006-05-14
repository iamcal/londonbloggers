<?
	include("includes/init.txt");

	$password = base64_decode($pw);
	if ($weblog_row = mysql_fetch_array(mysql_query("SELECT * FROM tube_weblogs WHERE id='$id' AND password LIKE '$password'",$db))){
	}else{
		header("location: edit.php?done=1");
		exit;
	}

	if ($done){
		$ok = 1;
		if (!$blog_name){$ok = 0;}
		if (!$blog_url){$ok = 0;}
		if (!$name){$ok = 0;}
		if (!$email){$ok = 0;}
		if (!$about){$ok = 0;}
		if (!$password){$ok = 0;}
		if ($ok){
			$time = time();
			$email_public = ($email_public)?1:0;
			$email_spam = ($email_spam)?1:0;
			mysql_query("UPDATE tube_weblogs SET blog_name='$blog_name', blog_url='$blog_url', name='$name', email='$email', email_public='$email_public', email_spam='$email_spam', about='$about', password='$password', approved=0 WHERE id=$id",$db);
			
			header("location: thanks2.php?id=$id&pw=$pw");
			exit;
		}	
	}

	if ($done_stations){
		#first do removal and editing
		$result = mysql_query("SELECT * FROM tube_weblog_stations WHERE weblog_id=$id",$db);
		while($row = mysql_fetch_array($result)){
			if ($HTTP_POST_VARS["relation_$row[id]"]){
				#update
				$relation = $HTTP_POST_VARS["relation_$row[id]"];
				$station_id = $HTTP_POST_VARS["station_$row[id]"];
				mysql_query("UPDATE tube_weblog_stations SET station_id='$station_id', relation='$relation' WHERE id=$row[id]",$db);
			}else{
				#remove
				mysql_query("DELETE FROM tube_weblog_stations WHERE id=$row[id]",$db);
			}
		}
		#now do adding
		if ($HTTP_POST_VARS["relation_0"]){
			$relation = $HTTP_POST_VARS["relation_0"];
			$station_id = $HTTP_POST_VARS["station_0"];
			mysql_query("INSERT INTO tube_weblog_stations (weblog_id,station_id,relation) VALUES ($id,'$station_id','$relation')",$db);
		}

		header("location: thanks2.php?id=$id&pw=$pw");
		exit;
	}

	include("includes/head.txt");
?>

		<td colspan="3">
			<div class="padded">

<h1>Edit Your Details</h1>
<br />
<? if ($done){ ?>
<img src="images/warning.gif" width="14" height="11"> <span style="color: #cc0000; font-weight: bold;">Please fill out all the fields!</span><br />
<br />
<? } ?>
<form action="doedit.php" method="post">
<input type="hidden" name="done" value="1">
<input type="hidden" name="id" value="<?=$id?>">
<input type="hidden" name="pw" value="<?=$pw?>">

	<b>The name of your weblog:</b><br />
	<input type="text" name="blog_name" value="<?=htmlentities(StripSlashes($weblog_row[blog_name]))?>"><br />
	<br />

	<b>The URL to your weblog:</b><br />
	<input type="text" name="blog_url" value="<?=htmlentities(StripSlashes($weblog_row[blog_url]))?>"><br />
	<br />

	<b>Your name:</b><br />
	<input type="text" name="name" value="<?=htmlentities(StripSlashes($weblog_row[name]))?>"><br />
	<br />

	<b>Your email address:</b><br />
	<input type="text" name="email" value="<?=htmlentities(StripSlashes($weblog_row[email]))?>"><br />
	<input type="checkbox" name="email_public"<?=($weblog_row[email_public])?' checked':''?>> Display your email address on this site<br />
	<input type="checkbox" name="email_spam"<?=($weblog_row[email_spam])?' checked':''?>> Allow us to send you exciting non-spam emails about this site<br />
	<br />

	<b>A little bit about your site:</b><br />
	<textarea name="about" style="width: 300px; height: 100px;" wrap="virtual"><?=htmlentities(StripSlashes($weblog_row[about]))?></textarea><br />
	(HTML will not work - just plain text please!)<br />
	<br />

	<b>Your password</b><br />
	<input type="password" name="password" value="<?=htmlentities(StripSlashes($weblog_row[password]))?>"><br />
	<br />

	<input type="submit" value="Update Your Details" style="background-color: #ccccff;">
</form>

<br />
<br />

<h1>Edit Your Stations</h1>
<br />
<form action="doedit.php" method="post">
<input type="hidden" name="done_stations" value="1">
<input type="hidden" name="id" value="<?=$id?>">
<input type="hidden" name="pw" value="<?=$pw?>">

	<b>Existing Stations</b><br/>
	(To delete an existing station, set the relationship to blank and click update)<br />
	<br />
<?
	$result = mysql_query("SELECT * FROM tube_weblog_stations WHERE weblog_id=$id",$db);
	while($row = mysql_fetch_array($result)){
		insert_station_row($row[id],$row[station_id],$row[relation]);
	}
	echo "<br />\n";
	if ($station){
		echo "<div style=\"border-top: 1px solid #000000; border-left: 1px solid #000000; border-bottom: 3px solid #000000; border-right: 3px solid #000000; padding: 16px; background-color: #f5f5f5;\">\n";
		echo "<a name=\"add\"></a><h2>Add Another Station</h2>\n";
	}else{
		echo "<b>Add Another Station</b><br/>\n";
	}
	echo "<br />\n";
	insert_station_row(0,$station,'');

	if ($station){
		echo "</div><br />";
	}

	function insert_station_row($id,$station_sel,$relation){
		global $db;

		echo "Station:<br/><select name=\"station_$id\">\n";
		$result = mysql_query("SELECT * FROM tube_stations ORDER BY name ASC",$db);
		while($row = mysql_fetch_array($result)){
			$s = ($row[id] == $station_sel)?' selected':'';
			echo "<option value=\"$row[id]\"$s>$row[name]</option>\n";
		}
		echo "</select><br />\n";
		echo "Relationship to weblog:<br /><input type=\"text\" name=\"relation_$id\" value=\"".htmlentities(StripSlashes($relation))."\"><br />(e.g. &quot;home&quot; or &quot;work&quot;)<br /><br />";
	}
?>


	<input type="submit" value="Update Your Stations" style="background-color: #ccccff;">
</form>

			</div>
		</td>

<?
	include("includes/foot.txt");
?>
