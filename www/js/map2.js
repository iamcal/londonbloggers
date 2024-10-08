var map_config = {
	path		: '/images/t2/',
	fileExt		: 'png',
	tileSize	: 256,
	defaultZoom	: 3,
	maxZoom		: 3,
	cacheMinutes	: 0,
	debug		: false
};
var weblog_counts = {};
var accept_station_clicks = true;
var g_this_station = null;
var g_marker_image = null;

//
// a very simple projection
//

function LBMapProjection(){}

LBMapProjection.prototype.fromLatLngToPoint = function(latLng){
	var x = latLng.lng() * map_config.tileSize;
	var y = latLng.lat() * map_config.tileSize;
	return new google.maps.Point(x, y);
};

LBMapProjection.prototype.fromPointToLatLng = function(point){
	var lng = point.x * (1.0 / map_config.tileSize);
	var lat = point.y * (1.0 / map_config.tileSize);
	return new google.maps.LatLng(lat, lng);
};


function LatLngToPixels(latLng){

	var pnt = LBMapProjection.prototype.fromLatLngToPoint(latLng);
	return [pnt.x * 8, pnt.y * 8];
}

function PixelsToLatLng(pxs){

	var pnt = {x: pxs[0] / 8, y: pxs[1] / 8};
	return LBMapProjection.prototype.fromPointToLatLng(pnt);
}


//
// the class for our map layer
//

//Zoom map
//0 -> 4
//1 -> 3
//2 -> 2
//3 -> 1

var LBMapOptions = {

	getTileUrl: function(tile, zoom) {

		if (tile.x < 0 || tile.y < 0) return null;
		if ((zoom == 3) && (tile.x > 15 || tile.y > 12)) return null;
		if ((zoom == 2) && (tile.x > 7 || tile.y > 6)) return null;
		if ((zoom == 1) && (tile.x > 3 || tile.y > 3)) return null;
		if ((zoom == 0) && (tile.x > 1 || tile.y > 1)) return null;

		var tx = ""+tile.x;
		var ty = ""+tile.y;
		while (tx.length < 3) tx = "0"+tx;
		while (ty.length < 3) ty = "0"+ty;

		var url = map_config.path+"tile_"+zoom+"_"+tx+"_"+ty+"."+map_config.fileExt;
		return url;
	},
	tileSize: new google.maps.Size(map_config.tileSize, map_config.tileSize),
	maxZoom:  map_config.maxZoom,
	minZoom:  0,
	isPng:    false
};

var LBMapType = new google.maps.ImageMapType(LBMapOptions);
LBMapType.name = "LB Map";
LBMapType.alt = "London Bloggers Map";
LBMapType.projection = new LBMapProjection();


//
// the debug overlay
//

function CoordMapType(){
}

function CoordMapType(tileSize){
	this.tileSize = tileSize;
}

CoordMapType.prototype.getTile = function(coord, zoom, ownerDocument) {
	var div = ownerDocument.createElement('DIV');
	div.innerHTML = "(" + coord.x + ", " + coord.y + ", " + zoom + ")";
	div.innerHTML += "<br />";
	div.innerHTML += LBMapOptions.getTileUrl(coord, zoom);
	div.style.width = this.tileSize.width + 'px';
	div.style.height = this.tileSize.height + 'px';
	div.style.fontSize = '10';
	div.style.borderStyle = 'solid';
	div.style.borderWidth = '1px';
	div.style.borderColor = '#AAAAAA';
	return div;
};


//
// startup code
//

var map;
var info_window;
var markers = [];

function initialize() {
	var mapOptions = {
		backgroundColor: '#eee',
		zoom: map_config.defaultZoom,
		center: new google.maps.LatLng(0.25, 0.25),
		navigationControl: true,
		navigationControlOptions: { style: google.maps.NavigationControlStyle.SMALL }, // still no way to get rid of the dude
		scaleControl: false,
		mapTypeControl: false,
		streetViewControl: false,
		mapTypeId: 'LBmap'
	};
	map = new google.maps.Map(document.getElementById("map"), mapOptions);

	if(map_config.debug) {
		map.overlayMapTypes.insertAt(0, new CoordMapType(new google.maps.Size(map_config.tileSize, map_config.tileSize)));

		google.maps.event.addListener(map, 'click', function(event) {
			console.log("latLng; " + event.latLng.lat() + ", " + event.latLng.lng());

			var pnt = map.getProjection().fromLatLngToPoint(event.latLng);
			console.log("point: ", pnt);

			var pxx = (pnt.x * 8) - 256;
			var pxy = (pnt.y * 8) - 256;
			console.log("pixel: " + pxx + ", " + pxy);
		});
	}

	// Now attach the coordinate map type to the map's registry
	map.mapTypes.set('LBmap', LBMapType);
  
	// We can now set the map to use the 'coordinate' map type
	map.setMapTypeId('LBmap');

	// we'll use this info window later
	info_window = new google.maps.InfoWindow();


	// click hook
	google.maps.event.addListener(map, 'click', function(event){
		var p = LatLngToPixels(event.latLng);
		process_click(p[0], p[1]);
	});
}

function process_click(x, y){

	if (!accept_station_clicks) return;

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

	select_station(best_id, true);
}

function select_station(id){

	g_selected_station = id;
	var station = g_stations[id];


	//
	// open window
	//

	var ll = PixelsToLatLng([station.x, station.y]);
	map.panTo(ll);

	info_window.close();
	info_window.setPosition(ll);
	set_station_html(id);
	info_window.open(map);

	if (!weblog_counts[id]){

		api_call('weblog_count', { 'station' : id }, function(o){
			weblog_counts[o.station] = o.count == 0 ? 'z' : o.count;
			if (o.station == g_selected_station){
				set_station_html(g_selected_station);
			}
		});
	}
}

function set_station_html(id){

	var station = g_stations[id];

	if (weblog_counts[id]){
		var count = weblog_counts[id] == 'z' ? 0 : weblog_counts[id];
		var weblogs = count==1 ? 'weblog' : 'weblogs';

		if (g_this_station == id){
			if (weblog_counts[id] > 1){
				info_window.setContent("<b>"+station.name+"</b><br /><br />View <a href=\"/stations/"+id+"/all/\">all "+count+" "+weblogs+"</a>");
			}else{
				info_window.setContent("<b>"+station.name+"</b><br /><br />Be the first to <a href=\"/stations/"+id+"/add/\">add your weblog</a>");
			}
		}else{
			info_window.setContent("<b>"+station.name+"</b><br /><br />Home to <a href=\"/stations/"+id+"/\">"+count+" "+weblogs+"</a>");
		}
	}else{
		info_window.setContent("<b>"+station.name+"</b><br /><br />...");
	}
}

function get_station_info(id){

	return g_stations[id];
}

function clear_markers(){

	for (var i=0; i<markers.length; i++){
		markers[i].setMap(null);
	}
	markers = [];
}

function create_markers(stations){

	clear_markers();

	var xs = [];
	var ys = [];
	var marker_img = new google.maps.MarkerImage(g_marker_image);
	var shadow_img = new google.maps.MarkerImage('http://chart.apis.google.com/chart?chst=d_map_pin_shadow');

	for (var i=0; i<stations.length; i++){

		var id = stations[i];
		var station = get_station_info(id);

		xs.push(parseInt(station.x));
		ys.push(parseInt(station.y));

		var marker = new google.maps.Marker({
			position: PixelsToLatLng([station.x, station.y]),
			map: map,
			title: station.name
		});

		if (g_marker_image){
			marker.setIcon(marker_img);
			marker.setShadow(shadow_img);
		}

		google.maps.event.addListener(marker, 'click', function(){
			var cid = id;
			var cmarker = marker;
			return function(event){
				marker_click(cid, cmarker);
			};
		}());

		markers.push(marker);
	}

	xs.sort(function(a,b){return a-b});
	ys.sort(function(a,b){return a-b});

	var lo_x = xs[0];
	var lo_y = ys[0];
	var hi_x = xs.pop();
	var hi_y = ys.pop();


	//
	// for large marker sets (lines), we will pull the edges in to avoid fitToBounds()
	// picking a zoom level too far out. or google could just fix this...
	//

	if (hi_x - lo_x > 1000){
		lo_x += 200;
		hi_x -= 200;
	}
	if (hi_y - lo_y > 1000){
		lo_y += 200;
		hi_y -= 200;
	}


	var bounds = new google.maps.LatLngBounds(PixelsToLatLng([lo_x, hi_y]), PixelsToLatLng([hi_x, lo_y]));

	map.fitBounds(bounds);
}

function marker_click(id, marker){

	select_station(id);
}

