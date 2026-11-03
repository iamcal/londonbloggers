<?php
	include("includes/init.txt");

	include("includes/head.txt");
?>

		<td colspan="3">
			<div class="padded">

				<h1>Thanks!</h1>
				<br />

				Thanks for adding your weblog to the directory.<br />
				<br />
				To see your weblog listing, <a href="station.php?id=<?=$station?>">click here</a>.<br />
				<br />
				To return to the main map, <a href="./">click here</a>.<br />
				<br />
				To add more stations to your weblog, <a href="edit.php">click here</a>.<br />
				<br />
				<br />

<?php insert_user_browse_code($id); ?>

			</div>
		</td>


<?php
	include("includes/foot.txt");
?>
