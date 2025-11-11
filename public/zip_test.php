<?php
echo "<h1>ZipArchive Test</h1>";
echo "PHP Version: " . phpversion() . "<br>";
echo "ZipArchive class exists: " . (class_exists('ZipArchive') ? '<span style="color:green">YES</span>' : '<span style="color:red">NO</span>') . "<br>";

if (class_exists('ZipArchive')) {
    echo "<p style='color:green'>ZipArchive is working!</p>";
    
    // Test creating a zip
    $zip = new ZipArchive();
    echo "ZipArchive object created: " . (is_object($zip) ? '<span style="color:green">YES</span>' : '<span style="color:red">NO</span>') . "<br>";
} else {
    echo "<p style='color:red'>ZipArchive is NOT available!</p>";
    
    // Check if extension_loaded
    echo "Extension zip loaded: " . (extension_loaded('zip') ? '<span style="color:green">YES</span>' : '<span style="color:red">NO</span>') . "<br>";
}

echo "<h2>Loaded Extensions:</h2>";
$extensions = get_loaded_extensions();
$zipFound = false;
foreach ($extensions as $ext) {
    if (strtolower($ext) === 'zip') {
        echo "<span style='color:green; font-weight:bold'>- " . $ext . "</span><br>";
        $zipFound = true;
    } else {
        echo "- " . $ext . "<br>";
    }
}

if (!$zipFound) {
    echo "<p style='color:red; font-weight:bold'>ZIP extension NOT found in loaded extensions!</p>";
}
?>