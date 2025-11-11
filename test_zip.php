<?php
echo "PHP Version: " . phpversion() . "\n";
echo "Loaded Extensions:\n";
$extensions = get_loaded_extensions();
foreach ($extensions as $ext) {
    echo "- " . $ext . "\n";
}

echo "\nZipArchive class exists: " . (class_exists('ZipArchive') ? 'YES' : 'NO') . "\n";

if (class_exists('ZipArchive')) {
    echo "ZipArchive is working!\n";
} else {
    echo "ZipArchive is NOT available!\n";
    
    // Check if extension_loaded
    echo "Extension zip loaded: " . (extension_loaded('zip') ? 'YES' : 'NO') . "\n";
    
    // Check php.ini settings
    echo "Extension dir: " . ini_get('extension_dir') . "\n";
}