<?php
$str = "GUNDUL BUNTUT SEREP 40FT / P260800265";
preg_match('/(P\d{9}|P-?\d{2}-?\d{2}-?\d{4})/i', $str, $matches);
print_r($matches);
