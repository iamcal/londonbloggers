<?php
	######################################################################

	function get_stations_within($x, $y, $range){

		$range_sq = $range * $range;
		$rows = array();

		$ret = db_fetch("SELECT * FROM tube_stations WHERE real_x>=$x-$range AND real_x<=$x+$range AND real_y>=$y-$range AND real_y<=$y+$range");
		foreach ($ret['rows'] as $row){

			$dif_x = abs($x - $row['real_x']);
			$dif_y = abs($y - $row['real_y']);
			if ($dif_x && $dif_y){
				$dist = ($dif_x * $dif_x) + ($dif_y * $dif_y);
				if ($dist <= $range_sq){
					$rows[] = $row;
				}
			}
		}

		return $rows;
	}

	######################################################################

	function blog_signature($id){

		$ip = $_SERVER['REMOTE_ADDR'];
		$ua = $_SERVER['HTTP_USER_AGENT'];

		return hash_hmac('sha256', $id.$ip.$ua, $GLOBALS['cfg']['crumb_secret']);
	}

	######################################################################

	function blog_ip_crumb($id){

		$ip = $_SERVER['REMOTE_ADDR'];
		$ua = $_SERVER['HTTP_USER_AGENT'];

		return hash_hmac('sha256', $ip.$id.$ua, $GLOBALS['cfg']['crumb_secret']);
	}

	######################################################################

	function blog_hash_password($password){

		return password_hash($password, PASSWORD_BCRYPT, array(
			'cost' => 13,
		));
	}

	function blog_check_password($hash, $input){

		return password_verify($input, $hash);
	}
