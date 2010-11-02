<?
	include("include/init.php");


	#
	# get years/months
	#

	$years = array();

	$ret = db_fetch("SELECT month_create, COUNT(*) AS num FROM tube_weblogs GROUP BY month_create ASC");
	foreach ($ret['rows'] as $row){

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
	# output
	#

	$smarty->display('page_weblogs.txt');
?>