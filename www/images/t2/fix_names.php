<?php
	$dh = opendir(__DIR__);
	while (($file = readdir($dh)) !== false){
		if (preg_match('!^tile_(\d+)_00(\d+)_00(\d+)\.png$!', $file, $m)){
			if (strlen($m[2]) > 1 || strlen($m[3]) > 1){
				$orig = $file;
				$fixd = sprintf('tile_%d_%03d_%03d.png', $m[1], $m[2], $m[3]);
				echo "Needs fixing: $file -> $fixd ... ";
				rename(__DIR__.'/'.$orig, __DIR__.'/'.$fixd);
				echo "OK\n";
			}
		}
	}
	closedir($dh);
