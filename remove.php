<?
	include('include/init.php');


	#
	# get weblogs to cull
	#

	$ret = db_fetch("SELECT * FROM tube_weblogs WHERE remove=1 LIMIT 10000");
	foreach ($ret['rows'] as $row){

		db_write("DELETE FROM tube_weblog_stations WHERE weblog_id=$row[id]");
		db_write("DELETE FROM tube_weblogs WHERE id=$row[id]");
		echo '. ';
	}


	echo "ok!";
?>