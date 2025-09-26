<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\Permohonan;
use Illuminate\Support\Facades\DB;

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Memperbaiki status data yang sudah di-approve system 1 tapi belum system 2...\n";
echo "================================================================\n";

// Cari data yang sudah di-approve system 1 tapi belum system 2, dan status masih "Selesai"
$recordsToFix = Permohonan::where('approved_by_system_1', true)
    ->where('approved_by_system_2', false)
    ->where('status', 'Selesai')
    ->get();

echo "Ditemukan " . $recordsToFix->count() . " data yang perlu diperbaiki:\n\n";

foreach ($recordsToFix as $record) {
    echo "ID: {$record->id} - Status: {$record->status} - Sys1: " . ($record->approved_by_system_1 ? 'true' : 'false') . " - Sys2: " . ($record->approved_by_system_2 ? 'true' : 'false') . "\n";

    // Update status kembali ke "Pending"
    $record->status = 'Pending';
    $record->save();

    echo "  -> Status diubah ke: {$record->status}\n";
}

echo "\nSelesai! Semua data sudah diperbaiki.\n";

// Verifikasi hasil
$fixedRecords = Permohonan::where('approved_by_system_1', true)
    ->where('approved_by_system_2', false)
    ->where('status', 'Pending')
    ->count();

echo "Verifikasi: {$fixedRecords} data sekarang memiliki status 'Pending' dan siap untuk Approval Tugas 2.\n";
