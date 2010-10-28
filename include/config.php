<?
	$GLOBALS['cfg'] = array();

	$GLOBALS['cfg']['db_main'] = array(
		'host'	=> 'localhost',
		'user'	=> 'root',
		'pass'	=> 'root',
		'name'	=> 'cal_london',
	);

	$GLOBALS['cfg']['abs_root_url']		= 'http://london.local/';
	$GLOBALS['cfg']['safe_abs_root_url']	= $GLOBALS['cfg']['abs_root_url'];

	$GLOBALS['cfg']['abs_root_path']	= realpath(dirname(__FILE__) . '/../');

	$GLOBALS['cfg']['smarty_compile'] = 1;
	$GLOBALS['cfg']['check_notices'] = 1;

	$GLOBALS['cfg']['http_timeout'] = 3;
?>
