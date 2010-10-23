<?
	include('include/init.php');


	while (1){

		#
		# check some emails
		#

		$ret = db_fetch("SELECT * FROM tube_weblogs WHERE email_status=0 ORDER BY id ASC LIMIT 100");

		$count = count($ret['rows']);
		if (!$count) break;
		echo "Batch of $count: ";

		foreach ($ret['rows'] as $row){

			list($junk, $domain) = explode('@', $row['email']);

			$status = dns_status($domain) == 'x' ? 1 : 2;

			db_write("UPDATE tube_weblogs SET email_status=$status WHERE id=$row[id]");

			echo '.'; flush();
		}

		echo "\n";
	}

	echo "all done!";



	function dns_status($domain){

		if (!strlen($domain)) return 'x';
		if (@dns_get_record($domain, DNS_MX)) return 'mx';
		if (@dns_get_record($domain, DNS_A)) return 'a';
		return 'x';
	}
?>