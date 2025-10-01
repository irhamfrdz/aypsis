<?php

// Script untuk mengekstrak semua module dan permission dari UI Matrix
$blade_content = file_get_contents('resources/views/master-user/edit.blade.php');

// Regex untuk menemukan semua permissions[module][action]
preg_match_all('/permissions\[([^\]]+)\]\[([^\]]+)\]/', $blade_content, $matches, PREG_SET_ORDER);

$ui_matrix_modules = [];
foreach ($matches as $match) {
    $module = trim($match[1], '"\'');
    $action = trim($match[2], '"\'');

    if (!isset($ui_matrix_modules[$module])) {
        $ui_matrix_modules[$module] = [];
    }
    if (!in_array($action, $ui_matrix_modules[$module])) {
        $ui_matrix_modules[$module][] = $action;
    }
}

ksort($ui_matrix_modules);

echo "=== MODULES YANG ADA DI UI MATRIX ===\n";
foreach ($ui_matrix_modules as $module => $actions) {
    echo "- $module: " . implode(', ', $actions) . "\n";
}

echo "\n=== TOTAL MODULES: " . count($ui_matrix_modules) . " ===\n";

// Simpan hasil untuk analisis lebih lanjut
file_put_contents('ui_matrix_modules.json', json_encode($ui_matrix_modules, JSON_PRETTY_PRINT));
echo "\nHasil disimpan ke ui_matrix_modules.json\n";
