<?php
	include("includes/init.txt");


	#
	# count block matches?
	#

	if ($_POST['word']){

		$id_list = find_queue_matches(StripSlashes($_POST['word']), $_POST['all']);
		$queue_matches = count($id_list);
	}


	#
	# save a new block?
	#

	if ($_POST['word2']){

		$id_list = find_queue_matches(StripSlashes($_POST['word2']), $_POST['all']);

		foreach ($id_list as $id){

			mysql_query("DELETE FROM tube_weblog_stations WHERE weblog_id=$id");
			mysql_query("DELETE FROM tube_weblogs WHERE id=$id");
		}

		mysql_query("INSERT INTO tube_blocks (`word`, `all`) VALUES ('$_POST[word2]', '$_POST[all]')", $db);

		header('location: block.php?added=1');
		exit;
	}


	#
	# remove an old block?
	#

	if ($_GET['remove']){

		mysql_query("DELETE FROM tube_blocks WHERE id='$_GET[remove]'");
		
		header('location: block.php?removed=1');
		exit;
	}


	#
	# fetch the block list
	#

	$blocks = array();

	$result = mysql_query("SELECT * FROM tube_blocks ORDER BY id ASC");
	while ($row = mysql_fetch_array($result)){

		$blocks[] = $row;
	}



	function find_queue_matches($word, $all){

		$word_rx = preg_quote($word, '!');
		$ids = array();

		$result = mysql_query("SELECT * FROM tube_weblogs WHERE approved=0");
		while ($row = mysql_fetch_array($result)){

			if ($all){
				$check = $row['blog_url'].$row['blog_name'].$row['name'].$row['about'];
			}else{
				$check = $row['blog_url'];
			}

			if (preg_match("!$word_rx!i", $check)){

				$ids[] = $row['id'];
			}
		}

		return $ids;
	}
?>
<html>
<head>
<title>Admin - Block</title>
</head>
<body>

<?php if ($_POST['word']){ ?>

<p>
<?php if ($queue_matches){ ?>
	There are currently <?=$queue_matches?> items in the review queue which will be auto-removed by this block.
<?php } ?>
	Are you sure you want to add this block?
</p>

<p>
	<b>Phrase:</b> <?=HtmlSpecialChars(StripSlashes($_POST['word']))?><br />
	<b>Scope:</b> <?=$_POST['all']?'All Fields':'URL'?><br />
</p>

<form action="block.php" method="post">
<input type="hidden" name="word2" value="<?=HtmlSpecialChars(StripSlashes($_POST['word']))?>" />
<input type="hidden" name="all" value="<?=HtmlSpecialChars($_POST['all'])?>" />
<p><input type="submit" value="Save" /> or <a href="block.php">cancel</a></p>
</form>

<?php }else{ ?>

<?php if ($_GET['added']){ ?><p>Your new block was added.</p><?php } ?>
<?php if ($_GET['removed']){ ?><p>Your block was removed.</p><?php } ?>


<p>There are <?=count($blocks)?> blocks in effect.</p>

<table border="1">
	<tr>
		<th>Phrase</th>
		<th>Scope</th>
		<th>Admin</th>
	</tr>
<?php foreach ($blocks as $row){ ?>
	<tr>
		<td><?=HtmlSpecialChars(StripSlashes($row['word']))?></td>
		<td><?=$row['all']?'All Fields':'URL'?></td>
		<td><a href="block.php?remove=<?=$row['id']?>">remove</a></td>
	</tr>
<?php } ?>
</table>

<p>Add a new block...</p>

<form action="block.php" method="post">
<input type="text" name="word" value="" /> <select name="all">
<option value="0">URL</option>
<option value="1">All Fields</option>
</select> <input type="submit" value="Save" />
</form>

<p>Blocks are case insensitive, but otherwise the exact phrase is matched for blocking. If the phrase occurs anywhere in the submission, it doesn't get saved.</p>

<?php } ?>

</body>
</html>