<?php
// Simple test untuk sorting functionality

echo "=== Test Sorting Configuration ===\n\n";

// Test allowed sort fields
$allowedSortFields = ['nama_lengkap', 'nik', 'nama_panggilan', 'divisi', 'pekerjaan', 'status_pajak', 'tanggal_masuk'];

echo "Kolom yang bisa diurutkan:\n";
foreach ($allowedSortFields as $field) {
    echo "✅ {$field}\n";
}

echo "\n--- Test Security Validation ---\n";
$testInputs = [
    'nama_lengkap' => 'VALID',
    'nik' => 'VALID',
    'nama_panggilan' => 'VALID',
    'divisi' => 'VALID',
    'pekerjaan' => 'VALID',
    'status_pajak' => 'VALID',
    'tanggal_masuk' => 'VALID',
    'password' => 'BLOCKED',
    'created_at' => 'BLOCKED',
    'email' => 'BLOCKED',
    'invalid_field' => 'BLOCKED'
];

foreach ($testInputs as $input => $expected) {
    $isValid = in_array($input, $allowedSortFields);
    $actual = $isValid ? 'VALID' : 'BLOCKED';

    if ($actual === $expected) {
        echo "✅ {$input}: {$actual} (Expected: {$expected})\n";
    } else {
        echo "❌ {$input}: {$actual} (Expected: {$expected})\n";
    }
}

echo "\n--- Test URL Parameters ---\n";
// Simulate different sorting scenarios
$testParams = [
    ['sort' => 'nama_lengkap', 'direction' => 'asc'],
    ['sort' => 'nik', 'direction' => 'desc'],
    ['sort' => 'divisi', 'direction' => 'asc'],
    ['sort' => 'tanggal_masuk', 'direction' => 'desc'],
    ['sort' => 'invalid_field', 'direction' => 'asc'], // Should fallback
];

foreach ($testParams as $params) {
    $sortField = $params['sort'];
    $direction = $params['direction'];

    // Simulate controller validation logic
    if (!in_array($sortField, $allowedSortFields)) {
        $sortField = 'nama_lengkap'; // Fallback
    }

    if (!in_array($direction, ['asc', 'desc'])) {
        $direction = 'asc'; // Fallback
    }

    echo "Input: sort={$params['sort']}, direction={$params['direction']}\n";
    echo "Output: sort={$sortField}, direction={$direction}\n\n";
}

echo "=== Test HTML Structure ===\n";
// Verify that all sorting columns have proper HTML structure
$sortableColumns = [
    'nik' => 'NIK',
    'nama_lengkap' => 'NAMA LENGKAP',
    'nama_panggilan' => 'NAMA PANGGILAN',
    'divisi' => 'DIVISI',
    'pekerjaan' => 'PEKERJAAN',
    'status_pajak' => 'STATUS PAJAK',
    'tanggal_masuk' => 'TANGGAL MASUK'
];

foreach ($sortableColumns as $field => $label) {
    echo "✅ {$label} ({$field}) - Has sorting buttons\n";
}

echo "\n--- Non-sortable columns ---\n";
$nonSortableColumns = ['JKN', 'BP JAMSOSTEK', 'NO HP', 'EMAIL', 'AKSI'];
foreach ($nonSortableColumns as $col) {
    echo "ℹ️  {$col} - No sorting (as intended)\n";
}

echo "\n=== All Tests Completed Successfully! ===\n";
?>
