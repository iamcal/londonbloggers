<?php
	include('include/init.php');

	$dh = opendir('/root/wayback/websites/londonbloggers.iamcal.com/weblogs');
	while (($file = readdir($dh)) !== false){
		if (is_dir("/root/wayback/websites/londonbloggers.iamcal.com/weblogs/$file") && preg_match('!^\d+$!', $file)
			&& file_exists("/root/wayback/websites/londonbloggers.iamcal.com/weblogs/$file/index.html")){

				slurp_file(file_get_contents("/root/wayback/websites/londonbloggers.iamcal.com/weblogs/$file/index.html"), $file);
		}
	}


	function slurp_file($html, $id){

		if (preg_match('!The requested URL /weblogs/.*? was not found on this server!', $html)){
			return;
		}

		#echo "$id: ".strlen($html)."\n";

		$hash = array(
			'id'		=> $id,
			'date_create'	=> 0,
			'date_update'	=> 0,
			'month_create'	=> '0000-00-00',
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

		if (preg_match('!<h1><a href="(.*?)" class="stealth">(.*?)</a></h1>!', $html, $m)){
			$hash['blog_url'] = htmlspecialchars_decode($m[1]);
			$hash['blog_name'] = htmlspecialchars_decode($m[2]);
		}

		if (preg_match('!Written by <a href="mailto:(.*?)">(.*?)</a><br />!', $html, $m)){
			$hash['email'] = htmlspecialchars_decode($m[1]);
			$hash['email_public'] = 1;
			$hash['name'] = htmlspecialchars_decode($m[2]);
		}else{
			if (preg_match('!Written by (.*?)<br />!', $html, $m)){
				$hash['name'] = htmlspecialchars_decode($m[1]);
			}
		}

		if (preg_match('!Added ((\d+)(st|nd|rd|th) (\S+) 20\d\d)!', $html, $m)){
			$ts = StrToTime($m[1]);
			$hash['date_create'] = $ts;
			$hash['month_create'] = date('Y-m-01', $ts);
		}

		if (preg_match('!<p style="margin-bottom: 3em"><i>(.*?)</i></p>!', $html, $m)){
			$hash['about'] = htmlspecialchars_decode($m[1]);
		}


		#
		# find station IDs
		#

		$stations = array();

		if (preg_match('!<p><b>Stations:</b> (.*?)</p>!', $html, $m)){

			preg_match_all('!/stations/(\d+)/!', $m[1], $m2);
			$stations = $m2[1];
		}




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
