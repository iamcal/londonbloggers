<?php
	include("../include/init.php");


	#
	# get years/months
	#

	$years = array();
	$series = array();

	$ret = db_fetch("SELECT month_create, COUNT(*) AS num FROM tube_weblogs GROUP BY month_create ASC");
	foreach ($ret['rows'] as $row){

		$series[] = $row['num'];

		list($y, $m) = explode('-', $row['month_create']);

		$y = intval($y);
		$m = intval($m);

		if (!$years[$y]){
			$years[$y] = array(
				'total' => 0,
				'months' => array(),
			);
		}

		$years[$y]['total'] += $row['num'];
		$years[$y]['months'][$m] = array(
			'label'	=> date('F', mktime(0,0,0,$m,1,2000)),
			'link'	=> sprintf('%04d-%02d', $y, $m),
			'num'	=> $row['num'],
		);
	}

	krsort($years);

	$smarty->assign('years', $years);


	#
	# data series
	#

	array_shift($series); # hide first (huge) value
	$max = max($series);
	$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

	$data = '';
	foreach ($series as $val){
		$v = floor(61 * $val / $max);
		$data .= substr($chars, $v, 1);
	}


	#
	# google chart url
	#

	$spark_w = 768;
	$spark_h = 40;

	$params = array(
		'chs'	=> "{$spark_w}x{$spark_h}",
		'cht'	=> 'lc:nda',
		'chco'	=> '113B92',
		'chd'	=> 's:'.$data,
		'chm'	=> 'B,113B92,0,0,0',
	);

	$pairs = array();
	foreach ($params as $k => $v) $pairs[] = "$k=$v";
	$spark_url = 'http://chart.apis.google.com/chart?'.implode('&', $pairs);

	$smarty->assign('spark_w', $spark_w);
	$smarty->assign('spark_h', $spark_h);
	$smarty->assign('spark_url', $spark_url);


	#
	# output
	#

	$smarty->display('page_weblogs.txt');
