<?php
	$GLOBALS['cfg']['duo_host'] = 'api-21417761.duosecurity.com';
	$GLOBALS['cfg']['duo_ikey'] = 'DIVTIYDFWX2G0JUJEXQH';
	$GLOBALS['cfg']['duo_skey'] = trim(file_get_contents(dirname(__FILE__).'/../secrets/duo_secret_key'));
	$GLOBALS['cfg']['duo_akey'] = trim(file_get_contents(dirname(__FILE__).'/../secrets/duo_app_key'));

	$GLOBALS['cfg']['title'] = 'London Bloggers Admin';
	$GLOBALS['cfg']['auth_app'] = 'londonbloggers';
