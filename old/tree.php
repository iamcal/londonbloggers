<?
	include("includes/init.txt");
	include("lib.txt");

	include("includes/head.txt");
?>

<h1>Data Tree</h1>
<br />

<?
	debug_vars(tree_Load());
	
	include("includes/foot.txt");
?>
