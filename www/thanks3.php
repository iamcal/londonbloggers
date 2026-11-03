<?php
	include("includes/init.txt");

	include("includes/head.txt");
?>

		<td colspan="3">
			<div class="padded">

				<h1>Thanks!</h1>
				<br />

				Your login details have been mailed to you at <?=$email?>.<br />
				<br />
				To log in, <a href="edit.php?email=<?=$email?>&station=<?=$station?>">click here</a>.<br />


			</div>
		</td>

<?php
	include("includes/foot.txt");
?>
