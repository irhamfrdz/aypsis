<?php

// Test the string matching
$permissionName = 'pranota-rit-kenek-view';
echo "Testing permission: {$permissionName}\n";

$parts = explode('-', $permissionName, 2);
echo "Parts: " . print_r($parts, true) . "\n";

if (count($parts) == 2) {
    $module = $parts[0];
    $action = $parts[1];
    echo "Module: {$module}\n";
    echo "Action: {$action}\n";
    
    if ($module === 'pranota' && strpos($action, 'rit-') === 0) {
        echo "✓ Matches pranota module with rit- action\n";
        
        if (strpos($action, 'rit-kenek-') === 0) {
            echo "✓ Matches rit-kenek- pattern\n";
            $newAction = str_replace('rit-kenek-', '', $action);
            echo "New action: {$newAction}\n";
            echo "New module: pranota-rit-kenek\n";
        } else {
            echo "✗ Does not match rit-kenek- pattern\n";
            $newAction = str_replace('rit-', '', $action);
            echo "New action: {$newAction}\n";
            echo "New module: pranota-rit\n";
        }
    } else {
        echo "✗ Does not match pranota module with rit- action\n";
    }
}

echo "\n=== DEBUG COMPLETE ===\n";