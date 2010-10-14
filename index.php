<?
	include('include/init.php');


	#
	# get station target data
	#

	$ret = db_fetch("SELECT * FROM tube_stations ORDER BY name ASC");
	foreach ($ret['rows'] as $row){

		$location = unserialize($row['location']);

		$data[$row['id']] = array(
			'name'	=> $row['name'],
			'pts'	=> is_array($location['centers']) ? $location['centers'] : array(),
		);
	}
?>
<html>
<head>
<title></title>
<style>

#map {
	background-color: #cccccc;
	background-image: url(/images/stripes.gif);
	border: 1px solid black;
	width: 800px;
	height: 400px;
	position: relative;
	overflow: hidden;
}

#sidebar {
	position: absolute;
	left: 850px;
	width: 300px;
	border: 1px solid black;
	padding: 1em;
}

.overbox {
	display: none;
	position: absolute;
	left: 860px;
	width: 280px;
	height: 200px;
	top: 100px;
	border: 1px solid black;
	padding: 1em;
	background-color: #eee;
}

.section {
	margin-top: 1em;
	padding-top: 1em;
	border-top: 1px solid black;
}

.center-marker {
	width: 25px;
	height: 25px;
	position: absolute;
	background-color: black;
	z-index: 2;
	opacity: 0.5;
}

a.secret {
	color: #000;
	text-decoration: none;
}

a.secret:hover {
	text-decoration: underline;
}

#infobox {
	display: block;
	position: absolute;
	width: 100px;
	height: 100px;
	position: absolute;
	background-color: pink;
	z-index: 2;
}

</style>
</head>
<body>

<p>This goes above the map.</p>

<div id="map"></div>
<div id="infobox">Hello</div>

<p>And this goes below it.</p>

<script src="/js/map.js"></script>
<script src="/js/core.js"></script>
<script>

var g_map = new map();
var g_markers = null;

var g_station_positions = <?=JSON_encode($data)?>;
var g_click_boxes = [];

window.onload = function(){

	g_map.show_crosshairs = false;
	g_map.init(g_map_data.path, g_map_data.zooms);
	g_map.create(document.getElementById('map'), 800, 400);
	g_map.set_zoom_level(3);
	g_map.center_on_pos(445, 447);

	g_map.get_slab().appendChild(document.getElementById('infobox'));


	g_map.onpan = function(){
		// something?
	};

	g_map.onzoomchange = function(){
		// maintain selection here
		calculate_click_boxes();
	};

	g_map.onclick = function(x, y){

		// find any matching boxes
		var matches = [];

		for (var i=0; i<g_click_boxes.length; i++){
			var box = g_click_boxes[i];
			if (x >= box[0] && y >= box[1] && x <= box[2] && y <= box[3]){
				matches.push(box);
			}
		}

		if (!matches.length) return;


		//
		// find shortest distance to each station
		//

		var distances = {};

		for (var i=0; i<matches.length; i++){
			var cx = matches[i][0] + ((matches[i][2] - matches[i][0]) / 2);
			var cy = matches[i][1] + ((matches[i][3] - matches[i][1]) / 2);
			var dx = Math.abs(x - cx);
			var dy = Math.abs(y - cy);
			var d = Math.sqrt(dx * dx + dy * dy);

			if (!distances[matches[i][4]] || d < distances[matches[i][4]]){

				distances[matches[i][4]] = d;
			}
		}


		//
		// pick the closest station
		//

		var best_d = 1000;
		var best_id = null;

		for (var i in distances){
			if (distances[i] < best_d){
				best_d = distances[i];
				best_id = i;
			}
		}
		if (!best_id) return;

		select_station(best_id);
	}

	// set up initial click targets
	calculate_click_boxes();
};

function calculate_click_boxes(){

	g_click_boxes = [];
	var z = g_map.zoom_level;

	var box_size = 35;

	if (z == 2) box_size /= 2;
	if (z == 3) box_size /= 4;
	if (z == 4) box_size /= 8;
	var half_box = box_size / 2; // TODO: make smaller for other zooms

	for (var station_id in g_station_positions){
		var station = g_station_positions[station_id];
		for (var i=0; i<station.pts.length; i++){
			var pt = station.pts[i];

			if (z != 1) pt = g_map.zoom1_to_current(pt);

			var x = pt[0]-half_box;
			var y = pt[1]-half_box;

			g_click_boxes.push([x, y, x+box_size, y+box_size, station_id]);
		}
	}
}

function select_station(id){

	var station = g_station_positions[id];
	//console.log("Closest station is "+station.name);	

	var box = document.getElementById('infobox');

	var xs = [];
	var ys = [];
	for (var i=0; i<station.pts.length; i++){
		xs.push(station.pts[i][0]);
		ys.push(station.pts[i][1]);
	}
	xs.sort();
	ys.sort();
	var x = (xs[0]+xs.pop())/2;
	var y = (ys[0]+ys.pop())/2;

	var pt = g_map.zoom1_to_current([x, y]);

	g_map.slide_to_pos(pt[0], pt[1]);

	box.style.display = 'block';
	box.innerHTML = station.name;
	box.style.left = (pt[0])+'px';
	box.style.top = (pt[1]-100)+'px';
}

</script>

</body>
</html>