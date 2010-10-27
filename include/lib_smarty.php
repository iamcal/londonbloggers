<?
	#
	# $Id$
	#

	$GLOBALS['timings']['smarty_comp_count']	= 0;
	$GLOBALS['timings']['smarty_comp_time']	= 0;

	define('SMARTY_DIR', INCLUDE_DIR.'/smarty_2.6.26/');

	require(SMARTY_DIR . 'Smarty.class.php');

	$GLOBALS['smarty'] = new Smarty();

	$GLOBALS['smarty']->template_dir = INCLUDE_DIR.'/../templates/';
	$GLOBALS['smarty']->compile_dir  = INCLUDE_DIR.'/../templates_c/';
	$GLOBALS['smarty']->compile_check = $GLOBALS['cfg']['smarty_compile'];;
	$GLOBALS['smarty']->force_compile = $GLOBALS['cfg']['smarty_compile'];;

	$GLOBALS['smarty']->assign_by_ref('cfg', $GLOBALS['cfg']);


	#######################################################################################

	function smarty_timings(){

		$GLOBALS['timings']['smarty_timings_out'] = microtime_ms();

		echo "<table class=\"debugtimings\" border=\"1\" align=\"center\">\n";
		echo "<tr>\n";
		echo "<th>Item</th>";
		echo "<th>Count</th>";
		echo "<th>Time</th>";
		echo "</tr>\n";

		# we add this one last so it goes at the bottom of the list
		$GLOBALS['timing_keys']['smarty_comp'] = 'Templates Compiled';

		foreach ($GLOBALS['timing_keys'] as $k => $v){
			$c = intval($GLOBALS['timings']["{$k}_count"]);
			$t = intval($GLOBALS['timings']["{$k}_time"]);
			echo "<tr><td>$v</td><td>$c</td><td>$t ms</td></tr>\n";
		}

		$map2 = array(
			array("Startup &amp; Libraries", $GLOBALS['timings']['init_end'] - $GLOBALS['timings']['execution_start']),
			array("Page Execution", $GLOBALS['timings']['smarty_start_output'] - $GLOBALS['timings']['init_end']),
			array("Smarty Output", $GLOBALS['timings']['smarty_timings_out'] - $GLOBALS['timings']['smarty_start_output']),
			array("<b>Total</b>", $GLOBALS['timings']['smarty_timings_out'] - $GLOBALS['timings']['execution_start']),
		);

		foreach ($map2 as $a){
			echo "<tr><td colspan=\"2\">$a[0]</td><td>$a[1] ms</td></tr>\n";
		}

		echo "</table>";
	}

	$GLOBALS['smarty']->register_function('timings', 'smarty_timings');

	#######################################################################################

	function versionify($path){

		$full_path = $GLOBALS['cfg']['abs_root_path'].$path;
		$stat = @stat($full_path);

		$bits = explode('.', $path);

		$ext = array_pop($bits);
		$bits[] = "v$stat[9]";
		$bits[] = $ext;

		return implode('.', $bits);
	}

	#######################################################################################

	function station_list($id, $trim=2){

		$id = intval($id);
		$ret = db_fetch("SELECT s.* FROM tube_weblog_stations AS ws, tube_stations AS s WHERE ws.station_id=s.id AND ws.weblog_id=$id ORDER BY s.name ASC");

		$out = array();
		foreach ($ret['rows'] as $row){
			$out[] = "<a href=\"/stations/$row[id]/\">".HtmlSpecialChars($row['name'])."</a>";
		}

		if (count($out) > $trim+1){
			$out = array_slice($out, 0, $trim);		
			$out[] = '...';
		}

		return implode(", ",$out);
	}

	#######################################################################################
?>