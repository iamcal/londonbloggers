#!/usr/bin/perl

use warnings;
use strict;
use Data::Dumper;

$|++;

#
# a map of the mappy bits
#

my $src = '/var/www/html/londonbloggers.iamcal.com/www/images/src/big.gif';
$src = '/var/www/html/londonbloggers.iamcal.com/www/images/src/large-detailed-public-transport-map-of-London-city.jpg';
$src = '/var/www/html/londonbloggers.iamcal.com/www/images/src/tubetrains-and-tube-travelcard-zones-map-001.jpg';
$src = '/var/www/html/londonbloggers.iamcal.com/www/images/src/bigger.png';

my $outdir = '/var/www/html/londonbloggers.iamcal.com/www/images/t1';

my $zoom_stats = {};


#
# first things first - calculate the final canvas width
#

my $size_w = 2700;
my $size_h = 2000;

$size_w = 3200;
$size_h = 2458;

$size_w = 4047;
$size_h = 3113;

$size_w = 3564;
$size_h = 2640;

print "canvas size is $size_w x $size_h\n";
print "\n";


#
# zoom1 is a copy of full, resized to be a good multiple of 256
#

my $levels = [];

my $final_w = 256;
my $final_h = 256;

$final_w *= 2 while $final_w < $size_w;
$final_h *= 2 while $final_h < $size_h;

print "creating zoom level 1 ($final_w x $final_h)...";

`convert -size ${final_w}x${final_h} xc:skyblue $outdir/zoom1.png`;
`composite -geometry -174-73 ${src} $outdir/zoom1.png $outdir/zoom1.png`;

push @{$levels}, ['zoom1.png', $final_w, $final_h];

print "ok\n";


&cut_tiles(3, 'zoom1.png', $final_w, $final_h);





exit;


sub cut_tiles{
	my ($level, $src, $w, $h) = @_;

	my $tiles_x = $w / 256;
	my $tiles_y = $h / 256;

	print "cutting level $level tiles...\n";
	print "\tneed to create $tiles_x x $tiles_y\n";

	print "\tcutting tiles...";
	`convert $outdir/$src +gravity -crop 256x256 $outdir/tiles_%d.jpg`;
	print "ok\n";

	print "\trenaming tiles...";
	my $c = 0;
	for (my $y=1; $y<=$tiles_y; $y++){
	for (my $x=1; $x<=$tiles_x; $x++){

		my $tile = sprintf 'tile_%d_%03d_%03d.jpg', $level, $x, $y;
		`mv $outdir/tiles_$c.jpg $outdir/$tile`;
		$c++;
	}
	}
	print "ok\n";
}




my ($z1_w, $z1_h) = &center_in_tilespace('zoom1.png', $size_w, $size_h, 1);

push @{$levels}, ['zoom1.png', 1, $z1_w, $z1_h];

exit;

#
# scale it down?
#

for (my $z=2; $z<=5; $z++){

	my $sx = int($size_w / (2 ** ($z - 1)));
	my $sy = int($size_h / (2 ** ($z - 1)));

	print "creating zoom level $z ($sx x $sy)...";
	`convert $outdir/full.png -resize ${sx}x${sy} $outdir/zoom$z.png`;
	print "ok\n";

	my ($s2x, $s2y) = &center_in_tilespace("zoom$z.png", $sx, $sy, $z);

	push @{$levels}, ["zoom${z}.png", $z, $s2x, $s2y];
}


#
# how many tiles will we need?
#

for my $lvl(@{$levels}){

	my $tiles_x = $lvl->[2] / 256;
	my $tiles_y = $lvl->[3] / 256;

	$zoom_stats->{$lvl->[1]}->[0] = $tiles_x;
	$zoom_stats->{$lvl->[1]}->[1] = $tiles_y;

	print "cutting level $lvl->[1] tiles...\n";
	print "\tneed to create $tiles_x x $tiles_y\n";

	print "\tcutting tiles...";
	`convert $outdir/$lvl->[0] +gravity -crop 256x256 $outdir/tiles_%d.jpg`;
	print "ok\n";

	print "\trenaming tiles...";
	my $c = 0;
	for (my $y=1; $y<=$tiles_y; $y++){
	for (my $x=1; $x<=$tiles_x; $x++){

		my $tile = sprintf 'tile_%d_%03d_%03d.jpg', $lvl->[1], $x, $y;
		`mv $outdir/tiles_$c.jpg $outdir/$tile`;
		$c++;
	}
	}
	print "ok\n";
}

print "\n";
print "zipping it all up...";
`tar cvf $outdir.tar $outdir/tile_*`;
print "ok\n";

print "\n";
print "zoom data:\n";
for my $k(keys %{$zoom_stats}){
	my $z = $zoom_stats->{$k};
	print "\t$k => array($z->[0], $z->[1], $z->[2], $z->[3]),\n";
}


sub round_up {
	if ($_[0] % 256 > 0){ return 256 * (int($_[0] / 256) + 1); }
	return $_[0];
}

sub center_in_tilespace {

	my ($src, $w, $h, $level) = @_;

	my $w2 = &round_up($w);
	my $h2 = &round_up($h);

	if ($w == $w2 && $h == $h2){

		return ($w, $h);
	}else{
		my $x_offset = int(($w2 - $w) / 2);
		my $y_offset = int(($h2 - $h) / 2);

                print "\trounding to 256 tiles...";
                `convert -size ${w2}x${h2} xc:skyblue $outdir/temp_$src`;
                `composite -geometry +${x_offset}+${y_offset} $outdir/$src $outdir/temp_$src $outdir/temp_$src`;
		`mv $outdir/temp_$src $outdir/$src`;
                print "ok\n";
		print "\toffset: [$x_offset, $y_offset],\n";

		$zoom_stats->{$level} = [0, 0, $x_offset, $y_offset];

		return ($w2, $h2);
	}
}
