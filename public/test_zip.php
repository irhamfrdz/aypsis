<?php
echo "PHP Version: " . phpversion() . "<br>";
echo "Loaded extensions: " . implode(", ", get_loaded_extensions()) . "<br><br>";

if (class_exists('ZipArchive')) {
    echo "✅ ZipArchive class is available<br>";
    $zip = new ZipArchive();
    echo "✅ ZipArchive object created successfully<br>";
} else {
    echo "❌ ZipArchive class is NOT available<br>";
    echo "Available classes with 'zip' in name: ";
    $classes = get_declared_classes();
    $zipClasses = array_filter($classes, function($class) {
        return stripos($class, 'zip') !== false;
    });
    echo implode(", ", $zipClasses) . "<br>";
}

echo "<br>Extension zip loaded: " . (extension_loaded('zip') ? "Yes" : "No") . "<br>";

// Check if specific functions are available
$zipFunctions = ['zip_open', 'zip_read', 'zip_close'];
foreach ($zipFunctions as $func) {
    echo "Function {$func}: " . (function_exists($func) ? "Available" : "Not available") . "<br>";
}

phpinfo();
?>