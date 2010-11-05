<?
	include('include/init.php');

	loadlib('blogs');


	$ret = db_fetch("SELECT id, password FROM tube_weblogs");

	foreach ($ret['rows'] as $row){

		db_update('tube_weblogs', array(

			'password_hash' => AddSlashes(blog_hash_password($row['password'])),
		), "id=$row[id]");
	}

	echo "done";
?>