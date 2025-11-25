<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test-zip', function () {
    $result = [];
    
    // Test 1: Check if extension is loaded
    $result['extension_loaded'] = extension_loaded('zip');
    
    // Test 2: Check if class exists
    $result['class_exists'] = class_exists('ZipArchive');
    
    // Test 3: Try to instantiate ZipArchive
    try {
        $zip = new ZipArchive();
        $result['instantiate'] = 'Success';
    } catch (Exception $e) {
        $result['instantiate'] = 'Error: ' . $e->getMessage();
    }
    
    // Test 4: Check PHP info
    ob_start();
    phpinfo();
    $phpinfo = ob_get_clean();
    $result['zip_in_phpinfo'] = (strpos($phpinfo, 'zip') !== false);
    
    // Test 5: Check loaded extensions
    $result['loaded_extensions'] = get_loaded_extensions();
    $result['zip_in_extensions'] = in_array('zip', $result['loaded_extensions']);
    
    return response()->json($result, 200, [], JSON_PRETTY_PRINT);
});