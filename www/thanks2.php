<?
	include("includes/init.txt");

	include("includes/head.txt");
?>

		<td colspan="3">
			<div class="padded">

				<h1>Thanks!</h1>
				<br />

				Thanks for editing your details - your changes have now been saved.<br />
				<br />
				To continue editing your details, <a href="doedit.php?id=<?=$id?>&pw=<?=$pw?>">click here</a>.<br />
				<br />
				To return to the main map, <a href="./">click here</a>.<br />
				<br />
				<br />

<? insert_user_browse_code($id); ?>

			</div>
		</td>

<?
	include("includes/foot.txt");
?>
