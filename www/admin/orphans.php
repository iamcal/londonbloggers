<?php
	include('../../include/init.php');


	#
	# get blogs
	#

	$blogs = array();

	$ret = db_fetch("SELECT * FROM tube_weblogs");
	foreach ($ret['rows'] as $row){
		$row['stations'] = 0;
		$blogs[$row['id']] = $row;
	}


	#
	# get stations
	#

	$stations = array();

	$ret = db_fetch("SELECT * FROM tube_stations");
	foreach ($ret['rows'] as $row){
		$stations[$row['id']] = $row;
	}


	#
	# get links
	#

	$links_to_dead_weblogs = array();
	$links_to_dead_stations = array();

	$ret = db_fetch("SELECT * FROM tube_weblog_stations");
	foreach ($ret['rows'] as $row){

		if (!$blogs[$row['weblog_id']]){
			$links_to_dead_weblogs[] = $row['id'];
			continue;
		}

		if (!$stations[$row['station_id']]){
			$links_to_dead_stations[] = $row['id'];
			continue;
		}

		$blogs[$row['weblog_id']]['stations']++;
	}


	#
	# find orphaned blogs
	#

	$blogs_without_stations = array();

	foreach ($blogs as $row){
		if ($row['stations'] == 0){
			$blogs_without_stations[] = $row['id'];
		}
	}


	#
	# output
	#

	echo "Blogs without stations: ";
	foreach ($blogs_without_stations as $id){
		echo "<a href=\"/weblogs/{$id}/\">$id</a>, ";
	}
	echo "<hr>\n";

	echo "links_to_dead_weblogs: ";
	foreach ($links_to_dead_weblogs as $id){
		echo "$id, ";
	}
	echo "<hr>\n";

	echo "links_to_dead_stations: ";
	foreach ($links_to_dead_stations as $id){
		echo "$id, ";
	}

