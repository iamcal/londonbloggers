<?
	include('../include/init.php');


	#
	# data fetched from: http://www.tfl.gov.uk/tfl/syndication/feeds/stations.kml
	# details at: http://data.london.gov.uk/datastore/package/tfl-station-locations
	#


	#
	# static map of their names to our names
	#

	$static_map = array(
		"Shepherd's Bush Central"		=> "Shepherd's Bush",
		"Shepherd's Bush Hammersmith & City"	=> "Shepherd's Bush Market",
		"King's Cross St. Pancras"		=> "King's Cross",
		"Harrow on the Hill"			=> "Harrow-on-the-Hill",
		"Edgware Road Bakerloo"			=> "Edgware Road (Bakerloo)",
		"Edgware Road Circle"			=> "Edgware Road (Circle)",
		"Crossharbour & London Arena"		=> "Crossharbour",
		"Bethnal Green"				=> "Bethnal Green (Tube)",
	);


	#
	# parse the XML
	#

	$xmlstr = file_get_contents('stations.kml');
	$xml = new SimpleXMLElement($xmlstr);

	foreach ($xml->Document->Placemark as $place){

		$name = trim($place->name);
		$desc = trim($place->description);
		$ll = trim($place->Point->coordinates);
		list($lat, $lon) = explode(',', $ll);

		#
		# remove the 'Station' suffix
		#

		if (preg_match('!^(.*)\s+Station$!', $name, $m)){
			$name = $m[1];
		}

		if ($static_map[$name]) $name = $static_map[$name];


		#
		# update database
		#

		$name_enc = AddSlashes($name);

		$temp = db_single(db_fetch("SELECT * FROM tube_stations WHERE name='$name_enc'"));

		if (!$temp['id']){
			echo "no match on $name<br />\n";
		}

		db_update('tube_stations', array(
			'lat'	=> AddSlashes($lat),
			'lon'	=> AddSlashes($lon),
		), "name='$name_enc'");
	}

	echo "done";

?>