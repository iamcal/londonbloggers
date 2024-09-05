<?php
	include('../include/init.php');


	#
	# get data
	#

	#$tag_enc = AddSlashes($_GET['tag']);
	#$tag_row = db_single(db_fetch("SELECT * FROM tube_photocache WHERE tag='$tag_enc'"));

	$refresh = 1;
	$cache_age = 60 * 60; # 1 hour in seconds

	if ($tag_row['id']){
		if ($tag_row['date_update'] + $cache_age > time()){
			$refresh = 0;
		}
	}


	#
	# refresh data from flickr is necessary
	#

	if ($refresh){

		$url = "http://www.flickr.com/services/feeds/photos_public.gne?tags=".urlencode($_GET['tag'])."&format=rss_200";
		$feed = implode('', file($url));

		if ($tag_row['id']){
		#	db_update('tube_photocache', array(
		#		'date_update'	=> time(),
		#		'data'		=> AddSlashes($feed),
		#	), "tag='$tag_enc'");
		}else{
		#	db_insert('tube_photocache', array(
		#		'tag'		=> $tag_enc,
		#		'date_update'	=> time(),
		#		'data'		=> AddSlashes($feed),
		#	));
		}
	}else{
		$feed = $tag_row['data'];
	}


	#
	# parse data
	#

	preg_match_all('!<item>.*?</item>!si', $feed, $matches);

	$photos = array();

	foreach ($matches[0] as $match){
		preg_match('!<link>(.*?)</link>!', $match, $m);
		$link = $m[1];

		preg_match('!<title>(.*?)</title>!', $match, $m);
		$title = $m[1];

		preg_match('!http://farm(.*?)_m\.jpg!', $match, $m);
		$src = "http://farm$m[1]_s.jpg";

		$photos[] = array(
			"title"	=> $title,
			"link"	=> $link,
			"src"	=> $src,
		);

	}

	$smarty->assign('photos', $photos);
	$smarty->assign('tag', $_GET['tag']);


	#
	# test mode just returns the html. easier for seeing the raw html
	#

	if ($_GET['test']){
		$smarty->display('inc_photos_html.txt');
		exit;
	}


	#
	# output
	#

	$smarty->display('js_photos.txt');
