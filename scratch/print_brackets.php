<?php

$lines = explode("\n", file_get_contents('resources/views/stock-ban/create.blade.php'));
foreach ($lines as $i => $line) {
    if (strpos($line, '[') !== false || strpos($line, ']') !== false) {
        printf("%4d: %s\n", $i + 1, trim($line));
    }
}
