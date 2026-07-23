<?php

$file = 'app/Http/Controllers/BlController.php';
$content = file_get_contents($file);

// The pattern to match the redundant permission check
$pattern = '/\s*\/\/\s*Check permission[^\n]*\s*if\s*\(\!\s*in_array\(\$user->role,\s*\[\'admin\',\s*\'user_admin\'\]\)\)\s*\{\s*\$hasPermission\s*=\s*DB::table\(\'user_permissions\'\)[^}]*?exists\(\);\s*if\s*\(\!\s*\$hasPermission\)\s*\{\s*(abort\(403,[^)]+\);|return\s+response\(\)->json\(\[\s*\'success\'\s*=>\s*false,\s*\'message\'\s*=>\s*\'Tidak memiliki akses[^\']+\'\s*\],\s*403\);)\s*\}\s*\}/s';

// Replace with empty string
$newContent = preg_replace($pattern, '', $content);

// Also handle the one in index which might be slightly different:
// // Check permission (you may want to adjust this based on your permission system)
$pattern2 = '/\s*\/\/\s*Check permission \(you may want to adjust this based on your permission system\)\s*if\s*\(\!\s*in_array\(\$user->role,\s*\[\'admin\',\s*\'user_admin\'\]\)\)\s*\{\s*\/\/\s*Check specific permissions if needed\s*\$hasPermission\s*=\s*DB::table\(\'user_permissions\'\)[^}]*?exists\(\);\s*if\s*\(\!\s*\$hasPermission\)\s*\{\s*abort\(403,[^)]+\);\s*\}\s*\}/s';

$newContent = preg_replace($pattern2, '', $newContent);

file_put_contents($file, $newContent);
echo "Cleaned up redundant permission checks in BlController.php\n";
