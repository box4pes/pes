<?php

/* 
 * Copyright (C) http://php.net/manual/en/function.openssl-random-pseudo-bytes.php#113214
 */

//Here's an example to show the distribution of random numbers as an image. Credit to Hayley Watson at the mt_rand page for the original comparison between rand and mt_rand.
//
//rand is red, mt_rand is green and openssl_random_pseudo_bytes is blue.
//
//NOTE: This is only a basic representation of the distribution of the data. Has nothing to do with the strength of the algorithms or their reliability.

header("Content-type: image/png");
$sizex=800;
$sizey=800;

$img = imagecreatetruecolor(3 * $sizex,$sizey);
$r = imagecolorallocate($img,255, 0, 0);
$g = imagecolorallocate($img,0, 255, 0);
$b = imagecolorallocate($img,0, 0, 255);
imagefilledrectangle($img, 0, 0, 3 * $sizex, $sizey, imagecolorallocate($img, 255, 255, 255));

$p = 0;
for($i=0; $i < 100000; $i++) {
    $np = rand(0,$sizex);
    imagesetpixel($img, $p, $np, $r);
    $p = $np;
}

$p = 0;
for($i=0; $i < 100000; $i++) {
    $np = mt_rand(0,$sizex);
    imagesetpixel($img, $p + $sizex, $np, $g);
    $p = $np;
}

$p = 0;
for($i=0; $i < 100000; $i++) {
    $np = floor($sizex*(hexdec(bin2hex(openssl_random_pseudo_bytes(4)))/0xffffffff));
    imagesetpixel($img, $p + (2*$sizex), $np, $b);
    $p = $np;
}

imagepng($img);
imagedestroy($img);
?>