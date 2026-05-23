<?php

$dir = __DIR__ . '/../database/migrations';
$files = scandir($dir);

foreach ($files as $file) {
    if ($file === '.' || $file === '..') {
        continue;
    }
    
    $path = $dir . '/' . $file;
    $content = file_get_contents($path);
    
    if (strpos($content, 'MODIFY') !== false) {
        $hasSqliteCheck = (strpos($content, 'sqlite') !== false) || (strpos($content, 'getDriverName') !== false);
        if (!$hasSqliteCheck) {
            echo "File without sqlite check: {$file}\n";
        }
    }
}
