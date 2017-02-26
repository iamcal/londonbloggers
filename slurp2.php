<?php
	include('include/init.php');

	$dh = opendir('/root/wayback/websites/londonbloggers.iamcal.com');
	while (($file = readdir($dh)) !== false){
		if (is_file("/root/wayback/websites/londonbloggers.iamcal.com/$file") && preg_match('!^weblog.php\?id=(\d+)$!', $file, $m)){

			slurp_file(file_get_contents("/root/wayback/websites/londonbloggers.iamcal.com/$file"), $m[1]);
		}
	}


	function slurp_file($html, $id){

		#echo "$id: ".strlen($html)."\n";
		#return;

		$hash = array(
			'id'		=> $id,
		#	'date_create'	=> 0,
		#	'date_update'	=> 0,
		#	'month_create'	=> '0000-00-00',
			'name'		=> '',
			'email'		=> "missing-{$id}@iamcal.com",
			'email_public'	=> 0,
			'email_spam'	=> 0,
			'password_hash'	=> '',
			'blog_name'	=> '',
			'blog_url'	=> '',
			'about'		=> '',
			'approved'	=> 0,
		);

		preg_match('!<h2>London Weblog #\d+</h2>(.*?)</div>!s', $html, $m);
		$html = $m[0];
		if (!strlen($html)) return;


		if (preg_match('!<a href="(.*?)" target="_blank">(.*?)</a></b> by !', $html, $m)){
			$hash['blog_url'] = htmlspecialchars_decode($m[1]);
			$hash['blog_name'] = htmlspecialchars_decode($m[2]);
		}

		if (preg_match('! by <a href="mailto:(.*?)">(.*?)</a><br />!', $html, $m)){
			$hash['email'] = htmlspecialchars_decode($m[1]);
			$hash['email_public'] = 1;
			$hash['name'] = htmlspecialchars_decode($m[2]);
		}else{
			if (preg_match('! by (.*?)<br />!', $html, $m)){
				$hash['name'] = htmlspecialchars_decode($m[1]);
			}
		}

		if (preg_match('!<i>&quot;(.*?)&quot;</i><br />!', $html, $m)){
			$hash['about'] = htmlspecialchars_decode($m[1]);
		}


		#
		# find station IDs
		#

		$stations = array();

		if (preg_match('!Stations: <a href="(.*?)<br />!', $html, $m)){

			preg_match_all('!station.php\?id=(\d+)!', $m[1], $m2);
			$stations = $m2[1];
		}


		if (!strlen($hash['blog_url'])) return;
		if ($hash['blog_url'] == 'http://') return; # no idea


		#print_r($hash);
		#print_r($stations);
		#exit;


		foreach ($hash as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		db_insert_dupe('tube_weblogs', $hash, $hash);


		foreach ($stations as $station){
			db_insert_dupe('tube_weblog_stations', array(
				'weblog_id'	=> AddSlashes($id),
				'station_id'	=> AddSlashes($station),
			), array(
				'station_id'	=> AddSlashes($station),
			));
		}

		echo '.';
	}

	echo "\nALL DONE\n";
