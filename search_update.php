<?php
$lines = file('app/Http/Controllers/BiayaKapalController.php');
foreach ($lines as $i => $line) {
    if (stripos($line, 'function update(') !== false) {
        echo "Found at line: " . ($i + 1) . "\n";
    }
}
