<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;

echo "Current group status in database:\n\n";

// Check current groups
$withGroup = DaftarTagihanKontainerSewa::whereNotNull('group')
    ->where('group', '!=', '')
    ->count();

$withoutGroup = DaftarTagihanKontainerSewa::where(function($q) {
    $q->whereNull('group')->orWhere('group', '');
})->count();

$total = DaftarTagihanKontainerSewa::count();

echo "Total records: {$total}\n";
echo "Records with group: {$withGroup}\n";
echo "Records without group: {$withoutGroup}\n\n";

// Show sample of current data
echo "Sample of current data:\n";
$samples = DaftarTagihanKontainerSewa::orderBy('nomor_kontainer')
    ->orderBy('periode')
    ->limit(10)
    ->get();

foreach ($samples as $record) {
    $group = $record->group ?: '(empty)';
    echo "  {$record->nomor_kontainer} Periode {$record->periode} - Group: {$group}\n";
}
