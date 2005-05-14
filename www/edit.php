<?
	include("includes/init.txt");

	if ($done){
		if ($row = mysql_fetch_array(mysql_query("SELECT * FROM tube_weblogs WHERE email LIKE '$email' AND password LIKE '$password'",$db))){
			$password_enc = base64_encode($password);

			if ($station){
				header("location: doedit.php?id=$row[id]&pw=$password_enc&station=$station#add");
			}else{
				header("location: doedit.php?id=$row[id]&pw=$password_enc");
			}
			exit;
		}
	}

	if ($done_password){
		if ($row = mysql_fetch_array(mysql_query("SELECT * FROM tube_weblogs WHERE email LIKE '$email'",$db))){

			$msg = "Hi $row[name],\n";
			$msg .= "\n";
			$msg .= "You requested your login details for londonbloggers.co.uk:\n";
			$msg .= "\n";
			$msg .= "Email: $row[email]\n";
			$msg .= "Password: $row[password]\n";
			$msg .= "\n";
			$msg .= "Your weblog: $row[blog_name]\n";
			$msg .= "             $row[blog_url]\n";
			$msg .= "\n";
			$msg .= "You can log in here:\n";
			$msg .= "    http://www.londonbloggers.co.uk/edit.php\n";
			$msg .= "\n";
			$msg .= "\n";
			$msg .= "------------------------------------------------------------------\n";
			$msg .= "http://www.londonbloggers.co.uk/\n";

			mail($row[email], "[LondonBloggers] Login Details", $msg, "From: LondonBloggers <cal@londonbloggers.co.uk>\r\nReturn-path: cal@londonbloggers.co.uk");

			header("location: thanks3.php?email=$row[email]&station=$station");
			exit;
		}
	}

	include("includes/head.txt");
?>

		<td colspan="3">
			<div class="padded">

<h1>Edit Your Details</h1>
<br />
If you have not yet added your weblog, <a href="map.php">choose a station</a> to do so.<br />
Use this form to change your details or add/remove a station for your weblog.<br />
<br />
<? if ($done){ ?>
<img src="images/warning.gif" width="14" height="11"> <span style="color: #cc0000; font-weight: bold;">The login details you entered were not recognised. Try using the form below if you have forgotten your password.</span><br />
<br />
<? } ?>
<form action="edit.php" method="post">
<input type="hidden" name="done" value="1">
<input type="hidden" name="station" value="<?=$station?>">

	<b>Your email address:</b><br />
	<input type="text" name="email" value="<?=htmlentities($email)?>"><br />
	<br />

	<b>Your password</b><br />
	<input type="password" name="password" value="<?=htmlentities($password)?>"><br />
	<br />

	<input type="submit" value="Login!" style="background-color: #ccccff;">
</form>

<br />
<br />
<br />

<a name="password"></a><h2>Forgotten your password?</h2>

<? if ($done_password){ ?>
<img src="images/warning.gif" width="14" height="11"> <span style="color: #cc0000; font-weight: bold;">The email address you entered is not registered. Try <a href="mailto:cal@londonbloggers.co.uk">mailing cal</a>.</span><br />
<br />
<? } ?>

<form action="edit.php#password" method="post">
<input type="hidden" name="done_password" value="1">

	<b>Your email address:</b><br />
	<input type="text" name="email" value="<?=htmlentities($email)?>"><br />
	<br />

	<input type="submit" value="Mail me my password!" style="background-color: #ccccff;">
</form>

<br />
<br />
<br />

<h2>Forgotten your email address too?</h2>

<p>Then <a href="mailto:cal@londonbloggers.co.uk">mail cal</a>.</p>


			</div>
		</td>

<?
	include("includes/foot.txt");
?>
