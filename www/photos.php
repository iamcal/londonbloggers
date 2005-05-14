<?
	include('includes/init.txt');

	#
	# get data
	#

	$tag_enc = AddSlashes($HTTP_GET_VARS[tag]);

	$tag_row = mysql_fetch_array(mysql_query("SELECT * FROM tube_photocache WHERE tag='$tag_enc'"));

	$refresh = 1;
	$row[cache_age] = 60 * 60; # 1 hour in seconds

	if ($tag_row[id]){
		if ($tag_row[date_update] + $row[cache_age] > time()){
			$refresh = 0;
		}
	}

	if ($refresh){

		$url = "http://www.flickr.com/services/feeds/photos_public.gne?tags=".urlencode($HTTP_GET_VARS[tag])."&format=rss_200";
		$feed = implode('', file($url));

		$feed_enc = AddSlashes($feed);
		$time = time();

		if ($tag_row[id]){
			mysql_query("UPDATE tube_photocache SET date_update='$time', data='$feed_enc' WHERE tag='$tag_enc'");
		}else{
			mysql_query("INSERT INTO tube_photocache (tag, date_update, data) VALUES ('$tag_enc', '$time', '$feed_enc')");
		}
	}else{
		$feed = $tag_row[data];
	}


	#
	# parse data
	#

	preg_match_all('!<item>.*?</item>!si', $feed, $matches);

	$photos = array();

	foreach($matches[0] as $match){
		preg_match('!<link>(.*?)</link>!', $match, $m);
		$link = $m[1];

		preg_match('!<title>(.*?)</title>!', $match, $m);
		$title = $m[1];

		preg_match('!http://photos(.*?)_m\.jpg!', $match, $m);
		$src = "http://photos$m[1]_s.jpg";

		$photos[] = array(
			"title"	=> $title,
			"link"	=> $link,
			"src"	=> $src,
		);

	}

	$tag1 = urlencode($HTTP_GET_VARS[tag]);
	$tag2 = AddSlashes($HTTP_GET_VARS[tag]);

	if (count($photos)){

		echo "document.write('<b>Recent <a href=\"http://www.flickr.com/\">flickr</a> photos for $tag2</b><br /><br />');\n";

		foreach($photos as $photo){
			$link = AddSlashes($photo[link]);
			$src = AddSlashes($photo[src]);
			$title = AddSlashes(HtmlSpecialChars($photo[title]));

			echo "document.write('<a href=\"$link\" title=\"$title\"><img src=\"$src\" width=\"75\" height=\"75\" alt=\"$title\" class=\"flickr\" /></a>');\n";
		}

		echo "document.write('<br /><br /><a href=\"http://www.flickr.com/photos/tags/$tag/\">More...</a>');\n";

	}else{
		echo "document.write('There are no <a href=\"http://www.flickr.com/\">flickr</a> photos for $tag2 - why not <a href=\"http://www.flickr.com/photos/upload/\">upload some</a>?');\n";
	}

?>