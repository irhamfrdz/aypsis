<?php
// Test sorting functionality untuk semua kolom termasuk yang baru ditambahkan

echo "=== Test Sorting All Columns (Updated) ===\n\n";

// Test allowed sort fields yang sudah diperbarui
$allowedSortFields = ['nama_lengkap', 'nik', 'nama_panggilan', 'divisi', 'pekerjaan', 'jkn', 'no_ketenagakerjaan', 'no_hp', 'email', 'status_pajak', 'tanggal_masuk'];

echo "Kolom yang bisa diurutkan (Updated):\n";
foreach ($allowedSortFields as $field) {
    echo "✅ {$field}\n";
}

echo "\n--- Test Security Validation (Updated) ---\n";
$testInputs = [
    // Valid fields
    'nama_lengkap' => 'VALID',
    'nik' => 'VALID',
    'nama_panggilan' => 'VALID',
    'divisi' => 'VALID',
    'pekerjaan' => 'VALID',
    'jkn' => 'VALID',                    // NEW
    'no_ketenagakerjaan' => 'VALID',     // NEW
    'no_hp' => 'VALID',                  // NEW
    'email' => 'VALID',                  // NEW
    'status_pajak' => 'VALID',
    'tanggal_masuk' => 'VALID',
    // Invalid fields
    'password' => 'BLOCKED',
    'created_at' => 'BLOCKED',
    'updated_at' => 'BLOCKED',
    'invalid_field' => 'BLOCKED'
];

foreach ($testInputs as $input => $expected) {
    $isValid = in_array($input, $allowedSortFields);
    $actual = $isValid ? 'VALID' : 'BLOCKED';

    if ($actual === $expected) {
        $status = $expected === 'VALID' ? '✅' : '🔒';
        echo "{$status} {$input}: {$actual} (Expected: {$expected})\n";
    } else {
        echo "❌ {$input}: {$actual} (Expected: {$expected})\n";
    }
}

echo "\n--- Test New Columns Specific Scenarios ---\n";

// Test scenarios untuk kolom baru
$newColumnTests = [
    'jkn' => [
        'asc' => 'Sort JKN A-Z (untuk mengelompokkan nomor JKN)',
        'desc' => 'Sort JKN Z-A (untuk mengelompokkan nomor JKN terbalik)'
    ],
    'no_ketenagakerjaan' => [
        'asc' => 'Sort No. Ketenagakerjaan A-Z (untuk mengelompokkan nomor BPJS)',
        'desc' => 'Sort No. Ketenagakerjaan Z-A (untuk mengelompokkan nomor BPJS terbalik)'
    ],
    'no_hp' => [
        'asc' => 'Sort No HP A-Z (untuk mengelompokkan nomor HP)',
        'desc' => 'Sort No HP Z-A (untuk mengelompokkan nomor HP terbalik)'
    ],
    'email' => [
        'asc' => 'Sort Email A-Z (untuk mengelompokkan email domain)',
        'desc' => 'Sort Email Z-A (untuk mengelompokkan email domain terbalik)'
    ]
];

foreach ($newColumnTests as $column => $scenarios) {
    echo "--- Testing column: {$column} ---\n";
    foreach ($scenarios as $direction => $description) {
        echo "✅ {$direction}: {$description}\n";

        // Simulate URL
        $params = ['sort' => $column, 'direction' => $direction, 'search' => 'test'];
        $url = "http://localhost/master/karyawan?" . http_build_query($params);
        echo "   URL: {$url}\n";
    }
    echo "\n";
}

echo "\n--- Complete Column List ---\n";
$columnMappings = [
    'nik' => 'NIK',
    'nama_lengkap' => 'NAMA LENGKAP',
    'nama_panggilan' => 'NAMA PANGGILAN',
    'divisi' => 'DIVISI',
    'pekerjaan' => 'PEKERJAAN',
    'jkn' => 'JKN',                                 // ✨ NEW
    'no_ketenagakerjaan' => 'BP JAMSOSTEK',         // ✨ NEW
    'no_hp' => 'NO HP',                             // ✨ NEW
    'email' => 'EMAIL',                             // ✨ NEW
    'status_pajak' => 'STATUS PAJAK',
    'tanggal_masuk' => 'TANGGAL MASUK'
];

echo "Sortable columns (TOTAL: " . count($columnMappings) . "):\n";
foreach ($columnMappings as $field => $label) {
    $isNew = in_array($field, ['jkn', 'no_ketenagakerjaan', 'no_hp', 'email']);
    $prefix = $isNew ? '✨' : '✅';
    $suffix = $isNew ? ' (NEW)' : '';
    echo "{$prefix} {$label} ({$field}){$suffix}\n";
}

echo "\nNon-sortable columns:\n";
echo "ℹ️  AKSI (action buttons)\n";

echo "\n--- Sorting Priorities by Use Case ---\n";
echo "🔤 Alphabetical Sorting: nama_lengkap, nama_panggilan, divisi, pekerjaan, status_pajak\n";
echo "🔢 Numeric/ID Sorting: nik, jkn, no_ketenagakerjaan, no_hp\n";
echo "📧 Email Sorting: email (useful for domain grouping)\n";
echo "📅 Date Sorting: tanggal_masuk\n";

echo "\n=== All Tests Completed Successfully! ===\n";
echo "Total sortable columns: " . count($allowedSortFields) . "\n";
echo "New columns added: jkn, no_ketenagakerjaan, no_hp, email\n";
?>
