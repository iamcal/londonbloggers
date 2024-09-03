<?php
	include('../include/init.php');


	#
	# get station data
	#

	$max_x = 0;
	$max_y = 0;

	$data = [];

	$ret = db_fetch("SELECT * FROM tube_stations ORDER BY name ASC");
	foreach ($ret['rows'] as $row){

		$row['location'] = unserialize($row['location']);

		$data[$row['id']] = $row;

		$max_x = max($max_x, $row['location']['label']['r']);
		$max_y = max($max_x, $row['location']['label']['b']);
	}
?>
<html>
<head>
<title>Map Test</title>
<script>

var stations = <?php echo json_encode($data); ?>;

window.onload = function(){

	var r = 2;

	var d = document.getElementById('wrapper');

	d.style.minWidth = <?php echo $max_x; ?>;
	d.style.minHeight = <?php echo $max_y; ?>;

	for (var i in stations){
		var s = stations[i];
		//console.log(s);

		var lbl = document.createElement('DIV');
		lbl.style.position = 'absolute';
		lbl.style.left = s.location.label.l + 'px';
		lbl.style.top = s.location.label.t + 'px';
		lbl.style.width = (s.location.label.r - s.location.label.l) + 'px';
		lbl.style.height = (s.location.label.b - s.location.label.t) + 'px';
		lbl.style.backgroundColor = 'rgba(100,100,255,0.5)';
		lbl.style.fontFamily = 'Arial';
		lbl.appendChild(document.createTextNode(s.name));

		d.appendChild(lbl);

		for (var j=0; j<s.location.centers.length; j++){
			var c = s.location.centers[j];

			var dot = document.createElement('DIV');
			dot.style.position = 'absolute';
			// the map_data.php file also uses these offset (-76, -110) for the centers only,
			// not for the labels. evidently they came from different maps to begin with!!
			// (or perhaps just offset differently)
			dot.style.left = (c[0]-(76+r)) + 'px';
			dot.style.top = (c[1]-(110+r)) + 'px';
			dot.style.width = (r+r) + 'px';
			dot.style.height = (r+r) + 'px';
			dot.style.backgroundColor = 'rgba(255,100,100,1)';

			d.appendChild(dot);

		}
	}
}

</script>
</head>
<body>

<div id="wrapper" style="position: relative">
	<img src="images/src/bigger.png"/>
</div>

</body>
</html>
