<?php
$content = file_get_contents(__DIR__ . '/resources/views/surat-jalan-bongkaran-batam/index.blade.php');

$patterns = [
    '/function editSuratJalan\(suratJalanId\).*?\}\s*\/\/ Close/s',
    '/function openEditModal\(suratJalanId\).*?\}\s*\/\/ Close/s',
    '/let editModalJustOpened = false;\s*function openEditModal\(suratJalanId\).*?\}\s*\/\//s',
    '/function handleEditFormSubmit\(event\).*?\}\s*\/\//s',
    '/function setupEditModalSupirAutoFill\(\).*?\}\s*\/\//s',
    '/function setupEditModalUangJalanCalculation\([^)]*\).*?\}\s*\n\s*\n/s'
];

foreach ($patterns as $pattern) {
    $content = preg_replace($pattern, '// Close', $content, 1);
}

file_put_contents(__DIR__ . '/resources/views/surat-jalan-bongkaran-batam/index.blade.php', $content);
echo "Done.\n";
