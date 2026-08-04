<?php
$file = __DIR__ . '/images/logo.png';
$img = imagecreatefrompng($file);
imagepalettetotruecolor($img);
imagealphablending($img, false);
imagesavealpha($img, true);
$w = imagesx($img);
$h = imagesy($img);
for ($x = 0; $x < $w; $x++) {
    for ($y = 0; $y < $h; $y++) {
        $c = imagecolorat($img, $x, $y);
        $r = ($c >> 16) & 0xFF;
        $g = ($c >> 8) & 0xFF;
        $b = $c & 0xFF;
        if ($r > 240 && $g > 240 && $b > 240) {
            $alpha = imagecolorallocatealpha($img, 255, 255, 255, 127);
            imagesetpixel($img, $x, $y, $alpha);
        }
    }
}
imagepng($img, __DIR__ . '/images/logo_transparent.png');
echo 'OK';
