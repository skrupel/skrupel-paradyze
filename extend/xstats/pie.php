<?php
/**
 * Autor: SHE
 *
 * Display a small pie percentage image
 */

$perc=$_GET["perc"];
if( is_null($perc)) {
    die("Parameter perc erwartet.");
}
$size=$_GET["size"];
if( is_null($size)) {
    die("Parameter size erwartet.");
}
$padding = 2;
$myImage = ImageCreate($size,$size);
//hex 45 = dec 69
$background = ImageColorAllocate($myImage, 69, 69, 69);
// hex 1BAC2C = dec 27 172 44
$green = ImageColorAllocate ($myImage, 27, 172, 44);
//hex 54 = dec 84
$backgroundLight = ImageColorAllocate ($myImage, 84, 84, 84);
$black = ImageColorAllocate ($myImage, 00, 00, 00);


ImageFilledArc($myImage, $size/2, $size/2, $size-$padding, $size-$padding, 270, 270+(360*$perc/100), $green, IMG_ARC_PIE);
if( $perc != 100){
    ImageFilledArc($myImage, $size/2, $size/2, $size-$padding, $size-$padding, 270+(360*$perc/100), 270, $backgroundLight, IMG_ARC_PIE);
}
imagearc($myImage, $size/2, $size/2, $size-$padding, $size-$padding,  0, 360, $black);


header ("Content-type: image/png");
ImagePNG($myImage);

ImageDestroy($myImage);

?>
