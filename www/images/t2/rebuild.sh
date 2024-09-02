#!/bin/bash

convert big.gif -background '#eeeeee' -extent 4096x4096 zoom3.png
convert zoom3.png -resize 2048x2048 zoom2.png
convert zoom3.png -resize 1024x1024 zoom1.png
convert zoom3.png -resize 512x512 zoom0.png

convert zoom0.png +repage -crop 256x256 -set filename:tile "00%[fx:page.x/256]_00%[fx:page.y/256]" tile_0_%[filename:tile].png
convert zoom1.png +repage -crop 256x256 -set filename:tile "00%[fx:page.x/256]_00%[fx:page.y/256]" tile_1_%[filename:tile].png
convert zoom2.png +repage -crop 256x256 -set filename:tile "00%[fx:page.x/256]_00%[fx:page.y/256]" tile_2_%[filename:tile].png
convert zoom3.png +repage -crop 256x256 -set filename:tile "00%[fx:page.x/256]_00%[fx:page.y/256]" tile_3_%[filename:tile].png

php fix_names.php

