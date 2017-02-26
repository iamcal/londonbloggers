<?php
	$GLOBALS['cfg'] = array();

	$GLOBALS['cfg']['environment'] = 'dev';

	$GLOBALS['cfg']['abs_root_url']		= 'http://londonbloggers.iamcal.com/';
	$GLOBALS['cfg']['safe_abs_root_url']	= $GLOBALS['cfg']['abs_root_url'];

	$GLOBALS['cfg']['abs_root_path']	= realpath(dirname(__FILE__) . '/../');

	$GLOBALS['cfg']['smarty_compile'] = 1;
	$GLOBALS['cfg']['check_notices'] = 1;

	$GLOBALS['cfg']['http_timeout'] = 3;

	$GLOBALS['cfg']['db_main'] = array(
		'host'	=> 'localhost',
		'user'	=> 'londonbloggers',
		'name'	=> 'londonbloggers',
		'pass'	=> trim(file_get_contents($cfg['abs_root_path'].'/secrets/mysql_password')),
	);

	$GLOBALS['cfg']['rewrite_static_urls'] = array(
		'/map.php' => '/',
	);

	$GLOBALS['cfg']['crumb_secret'] = trim(file_get_contents($cfg['abs_root_path'].'/secrets/crumb_secret'));

	$GLOBALS['cfg']['email_from_name']	= 'London Bloggers';
	$GLOBALS['cfg']['email_from_email']	= 'londonbloggers@iamcal.com';
	$GLOBALS['cfg']['auto_email_args']	= '-flondonbloggers@iamcal.com';

	$GLOBALS['cfg']['admin_email'] = 'cal@iamcal.com';

	$GLOBALS['cfg']['data_rev'] = 2;
