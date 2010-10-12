<?
	$GLOBALS['cfg'] = array();

	$GLOBALS['cfg']['db_main'] = array(
		'host'	=> 'localhost',
		'user'	=> 'root',
		'pass'	=> 'root',
		'name'	=> 'cal_london',
	);

	$GLOBALS['cfg']['abs_root_url']		= 'http://www.ourapp.com/';
	$GLOBALS['cfg']['safe_abs_root_url']	= $GLOBALS['cfg']['abs_root_url'];

	$GLOBALS['cfg']['smarty_compile'] = 1;
	$GLOBALS['cfg']['check_notices'] = 1;

	$GLOBALS['cfg']['http_timeout'] = 3;
?>
