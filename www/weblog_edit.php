<?php
	include("../include/init.php");

	loadlib('blogs');


	#
	# grab blog
	#

	$weblog = db_single(db_fetch("SELECT * FROM tube_weblogs WHERE id=".intval($_GET['id'])));

	if (!$weblog['id']) error_404();

	$smarty->assign('weblog', $weblog);


	#
	# check auth
	#

	if ($_GET['sig'] != blog_signature($weblog['id'])){

		error_404();
	}



	#
	# the '$edit' row lets us carry over entered values
	#

	$edit = $weblog;
	$smarty->assign_by_ref('edit', $edit);

	if ($_POST['done']){

		$ok = 1;


		#
		# update the edit hash first, in case we need to show the
		# edit form again.
		#

		$edit['blog_name']	= $_POST['blog_name'];
		$edit['blog_url']	= $_POST['blog_url'];
		$edit['name']		= $_POST['name'];
		$edit['email']		= $_POST['email'];
		$edit['about']		= $_POST['about'];
		$edit['email_public']	= $_POST['email_public'] ? 1 : 0;
		$edit['email_spam']	= $_POST['email_spam'] ? 1 : 0;


		#
		# start to build the update hash
		#

		$hash = array(
			'blog_name'	=> AddSlashes(trim($_POST['blog_name'])),
			'blog_url'	=> AddSlashes(trim($_POST['blog_url'])),
			'name'		=> AddSlashes(trim($_POST['name'])),
			'email'		=> AddSlashes(trim(StrToLower($_POST['email']))),
			'about'		=> AddSlashes(trim($_POST['about'])),
		);

		$ok = 1;
		foreach ($hash as $val){
			if (!strlen($val)){
				$ok = 0;
				$smarty->assign('error_fields', 1);
			}
		}


		#
		# url validation
		#

		if (!preg_match('!^http://.!i', $edit['blog_url'])){
			$ok = 0;
			$smarty->assign('error_url_http', 1);
		}


		#
		# set up the other fields
		#

		$hash['email_public']	= $_POST['email_public'] ? 1 : 0;
		$hash['email_spam']	= $_POST['email_spam'] ? 1 : 0;
		$hash['date_update']	= time();


		#
		# check email address isn't taken
		#

		if ($ok){

			$temp = db_single(db_fetch("SELECT * FROM tube_weblogs WHERE email='$hash[email]' AND id!=$weblog[id]"));
			if ($temp['id']){

				$ok = 0;
				$smarty->assign('error_email_taken', 1);
			}
		}


		#
		# save changes
		#

		if ($ok){

			$hash[approved] = 0;
			db_update('tube_weblogs', $hash, "id=$weblog[id]");

			$sig = blog_signature($weblog[id]);

			header("location: /weblogs/{$weblog[id]}/?updated=".$sig);
			exit;
		}
	}


	#
	# add a station?
	#

	if ($_POST['done_add']){

		#
		# check the station exists and hasn't already been added
		#

		$id = intval($_POST['station']);
		$temp1 = db_single(db_fetch("SELECT * FROM tube_stations WHERE id=$id"));
		$temp2 = db_single(db_fetch("SELECT * FROM tube_weblog_stations WHERE weblog_id=$weblog[id] AND station_id=$id"));

		if ($temp1['id'] && !$temp2['id']){

			db_insert('tube_weblog_stations', array(
				'weblog_id'	=> $weblog['id'],
				'station_id'	=> $id,
			));

			$smarty->assign('added_station', $id);
		}
	}


	#
	# remove a station?
	#

	if ($_GET['remove']){

		$id = intval($_GET['remove']);

		db_write("DELETE FROM tube_weblog_stations WHERE weblog_id=$weblog[id] AND station_id=$id");
	}


	#
	# fetch current stations
	#

	$stations = array();
	$ret = db_fetch("SELECT * FROM tube_weblog_stations WHERE weblog_id=$weblog[id] ORDER BY id ASC");
	foreach ($ret['rows'] as $row){
		$stations[] = db_single(db_fetch("SELECT * FROM tube_stations WHERE id=$row[station_id]"));
	}
	$smarty->assign('stations', $stations);


	#
	# get a list of all stations for the 'add station' dropdown
	#

	$ret = db_fetch("SELECT id, name FROM tube_stations ORDER BY name ASC");
	$smarty->assign('all_stations', $ret['rows']);


	#
	# output
	#

	$smarty->display('page_weblog_edit.txt');
