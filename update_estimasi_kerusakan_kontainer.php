<?php

// File: update_estimasi_kerusakan_kontainer.php
// Script untuk mengupdate field estimasi_kerusakan_kontainer yang kosong

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\PerbaikanKontainer;

echo "=== Update Estimasi Kerusakan Kontainer ===\n\n";

// Cari data yang estimasi_kerusakan_kontainer nya kosong
$emptyRecords = PerbaikanKontainer::whereNull('estimasi_kerusakan_kontainer')
    ->orWhere('estimasi_kerusakan_kontainer', '')
    ->with('kontainer.permohonans')
    ->get();

echo "Ditemukan " . $emptyRecords->count() . " record dengan estimasi_kerusakan_kontainer kosong\n\n";

$updated = 0;
foreach ($emptyRecords as $record) {
    $newValue = '';

    // Prioritas 1: Ambil dari catatan jika ada
    if (!empty($record->catatan)) {
        $newValue = $record->catatan;
    }
    // Prioritas 2: Generate dari data permohonan
    elseif ($record->kontainer && $record->kontainer->permohonans->count() > 0) {
        $permohonan = $record->kontainer->permohonans->first();
        $newValue = 'Perbaikan kontainer berdasarkan permohonan ID: ' . $permohonan->id;
    }
    // Prioritas 3: Default value
    else {
        $newValue = 'Perbaikan kontainer - ' . $record->nomor_memo_perbaikan;
    }

    // Update record
    $record->update(['estimasi_kerusakan_kontainer' => $newValue]);

    echo "Updated ID {$record->id}: {$newValue}\n";
    $updated++;
}

echo "\n=== Summary ===\n";
echo "Total records updated: {$updated}\n";
echo "Script completed successfully!\n";
