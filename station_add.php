<?
	include("include/init.php");

	loadlib('blogs');


	#
	# grab station
	#

	$id = intval($_GET['id']);

	$station = db_single(db_fetch("SELECT * FROM tube_stations WHERE id=$id"));
	if (!$station['id']) error_404();

	$station['tag'] = preg_replace("![^a-z]!", "", StrToLower($station['name']));

	$smarty->assign_by_ref('station', $station);


	#
	# submit!
	#

	if ($_POST['done']){


		#
		# check we got the required fields
		#

		$hash = array(
			'blog_name'	=> AddSlashes(trim($_POST['blog_name'])),
			'blog_url'	=> AddSlashes(trim($_POST['blog_url'])),
			'name'		=> AddSlashes(trim($_POST['name'])),
			'email'		=> AddSlashes(trim(StrToLower($_POST['email']))),
			'about'		=> AddSlashes(trim($_POST['about'])),
			'password'	=> AddSlashes(trim($_POST['password'])),
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

		if (!preg_match('!^http://!i', StripSlashes($hash['blog_url']))){
			$ok = 0;
			$smarty->assign('error_url_http', 1);
		}


		#
		# set up the other fields
		#

		$hash['email_public']	= $_POST['email_public'] ? 1 : 0;
		$hash['email_spam']	= $_POST['email_spam'] ? 1 : 0;
		$hash['date_create']	= time();


		#
		# check email address isn't taken
		#

		if ($ok){

			$temp = db_single(db_fetch("SELECT * FROM tube_weblogs WHERE email='$hash[email]'"));
			if ($temp['id']){

				$ok = 0;
				$smarty->assign('error_email_taken', 1);
			}
		}


		#
		# ok! add it!
		#

		if ($ok){

			$ret = db_insert('tube_weblogs', $hash);
			$id = $ret['insert_id'];

			db_insert('tube_weblog_stations', array(
				'weblog_id'	=> $id,
				'station_id'	=> $station['id'],
			));


			$sig = blog_signature($id);

			header("location: /weblogs/{$id}/?added=".$sig);
			exit;
		}
	}


	#
	# output
	#

	$smarty->display('page_station_add.txt');
?>