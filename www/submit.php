<?
	include("includes/init.txt");

	$station_row = mysql_fetch_array(mysql_query("SELECT * FROM tube_stations WHERE id=$station",$db));

	if ($done){
		$ok = 1;
		$error = "You must fill out all of the fields to continue!";
		if (!$blog_name){$ok = 0;}
		if (!$blog_url){$ok = 0;}
		if (!$name){$ok = 0;}
		if (!$email){$ok = 0;}
		if (!$about){$ok = 0;}
		if (!$password){$ok = 0;}
		if (!$relation){$ok = 0;}
		if ($ok){
			if ($row = mysql_fetch_array(mysql_query("SELECT * FROM tube_weblogs WHERE email LIKE '$email'",$db))){
				$ok = 0;
				$error = 'Someone has already registered a weblog using that email address.<br />To edit your weblog details and add more stations, <a href="edit.php">click here</a>';
			}
		}
		if ($ok){
			$time = time();
			$email_public = ($email_public)?1:0;
			$email_spam = ($email_spam)?1:0;
			mysql_query("INSERT INTO tube_weblogs (date_create, blog_name, blog_url, name, email, email_public, email_spam, about, password) VALUES ($time, '$blog_name', '$blog_url', '$name', '$email', '$email_public', '$email_spam', '$about', '$password')",$db);
			$weblog_id = mysql_insert_id();

			mysql_query("INSERT INTO tube_weblog_stations (weblog_id, station_id, relation) VALUES ($weblog_id, '$station', '$relation')",$db);

			header("location: thanks.php?station=$station&id=$weblog_id");
			exit;
		}
	}

	include("includes/head.txt");
?>

		<td colspan="3">
			<div class="padded">

<h1>Add Your Weblog</h1>
<br />
If you would rather not do this yet, <a href="./">click here</a> to return to the main map.<br />
<br />
If you have already added your weblog, but would like to add this station to it, <a href="edit.php?station=<?=$station?>">click here</a>.<br />
<br />
<br />
<? if ($done){ ?>
<img src="images/warning.gif" width="14" height="11"> <span style="color: #cc0000; font-weight: bold;"><?=$error?></span><br />
<br />
<? } ?>
<form action="submit.php" method="post">
<input type="hidden" name="done" value="1">
<input type="hidden" name="station" value="<?=$station?>">

	<b>Your nearest station:</b><br />
	<i><?=$station_row[name]?></i><br />
	<br />

	<b>Relationship of this station to you:</b><br />
	<input type="text" name="relation" value="<?=htmlentities(StripSlashes($relation))?>"><br />
	(e.g. &quot;home&quot; or &quot;work&quot; - this information does not appear on the site, but is useful if you add more than one station)<br />
	<br />

	<b>The name of your weblog:</b><br />
	<input type="text" name="blog_name" value="<?=htmlentities(StripSlashes($blog_name))?>"><br />
	<br />

	<b>The URL to your weblog:</b><br />
	<input type="text" name="blog_url" value="<?=htmlentities(StripSlashes($blog_url))?>"><br />
	<br />

	<b>Your name:</b><br />
	<input type="text" name="name" value="<?=htmlentities(StripSlashes($name))?>"><br />
	<br />

	<b>Your email address:</b><br />
	<input type="text" name="email" value="<?=htmlentities(StripSlashes($email))?>"><br />
	<input type="checkbox" name="email_public" checked> Display your email address on this site<br />
	<input type="checkbox" name="email_spam" checked> Allow us to send you exciting non-spam emails about this site<br />
	<br />

	<b>A little bit about your site:</b><br />
	<textarea name="about" style="width: 300px; height: 100px;" wrap="virtual"><?=htmlentities(StripSlashes($about))?></textarea><br />
	(HTML will not work - just plain text please!)<br />
	<br />

	<b>Choose a password</b><br />
	<input type="password" name="password" value="<?=htmlentities(StripSlashes($password))?>"><br />
	(You will need to remember this if you want to edit your details in future.)<br />
	<br />

	<input type="submit" value="Add My Weblog!" style="background-color: #ccccff;">
</form>

			</div>
		</td>

<?
	include("includes/foot.txt");
?>
