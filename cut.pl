#!/usr/bin/perl

use warnings;
use strict;
use Data::Dumper;

$|++;

my $outdir = '/var/www/cal/gnespy.com/tiles';
my $convert = '/usr/bin/convert';

my $zoom_stats = {};

#
# first things first - calculate the final canvas width
#

my $size_w = 3944;
my $size_h = 3108;

print "canvas size is $size_w x $size_h\n";
print "\n";


#
# zoom1 is a copy of full, with borders (maybe)
#

print "creating zoom level 1 ($size_w x $size_h)...";
`cp $outdir/full.png $outdir/zoom1.png`;
print "ok\n";

my ($z1_w, $z1_h) = &center_in_tilespace('zoom1.png', $size_w, $size_h, 1);

my $levels = [];

push @{$levels}, ['zoom1.png', 1, $z1_w, $z1_h];

#
# scale it down?
#

for (my $z=2; $z<=4; $z++){

	my $sx = int($size_w / (2 ** ($z - 1)));
	my $sy = int($size_h / (2 ** ($z - 1)));

	print "creating zoom level $z ($sx x $sy)...";
	`$convert $outdir/full.png -resize ${sx}x${sy} $outdir/zoom$z.png`;
	print "ok\n";

	my ($s2x, $s2y) = &center_in_tilespace("zoom$z.png", $sx, $sy, $z);

	push @{$levels}, ["zoom${z}.png", $z, $s2x, $s2y];
}

print "\n";

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
print "zoom data:\n";
for my $k(keys %{$zoom_stats}){
	my $z = $zoom_stats->{$k};
	print "\t$k : [$z->[0], $z->[1], $z->[2], $z->[3]],\n";
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
                `$convert -size ${w2}x${h2} xc:white $outdir/temp_$src`;
                `composite -geometry +${x_offset}+${y_offset} $outdir/$src $outdir/temp_$src $outdir/temp_$src`;
		`mv $outdir/temp_$src $outdir/$src`;
                print "ok\n";
		print "\toffset: [$x_offset, $y_offset],\n";

		$zoom_stats->{$level} = [0, 0, $x_offset, $y_offset];

		return ($w2, $h2);
	}
}
