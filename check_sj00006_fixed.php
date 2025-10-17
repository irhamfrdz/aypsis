<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║ CEK DATA SURAT JALAN SJ00006                                                 ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";

// Cari surat jalan dengan nomor SJ00006
$suratJalan = DB::table('surat_jalans')
    ->where('no_surat_jalan', 'LIKE', '%SJ00006%')
    ->orWhere('no_surat_jalan', 'LIKE', '%SJ0006%')
    ->first();

if (!$suratJalan) {
    echo "❌ Surat Jalan SJ00006 tidak ditemukan!\n\n";

    // Tampilkan semua nomor surat jalan yang ada
    echo "📋 DAFTAR SEMUA NOMOR SURAT JALAN:\n";
    echo "────────────────────────────────────────────────────────────────────────────────\n";

    $allSJ = DB::table('surat_jalans')
        ->select('id', 'no_surat_jalan', 'status')
        ->orderBy('id')
        ->get();

    foreach ($allSJ as $sj) {
        echo "ID: {$sj->id} | No: {$sj->no_surat_jalan} | Status: {$sj->status}\n";
    }

    exit;
}

echo "📋 DATA SURAT JALAN:\n";
echo "────────────────────────────────────────────────────────────────────────────────\n";
echo "ID              : " . $suratJalan->id . "\n";
echo "No. Surat Jalan : " . $suratJalan->no_surat_jalan . "\n";
echo "Tanggal         : " . $suratJalan->tanggal_surat_jalan . "\n";
echo "Kegiatan        : " . $suratJalan->kegiatan . "\n";
echo "No. Kontainer   : " . ($suratJalan->no_kontainer ?? 'NULL') . "\n";
echo "No. Seal        : " . ($suratJalan->no_seal ?? 'Belum diisi') . "\n";
echo "Status          : " . $suratJalan->status . "\n";
echo "Created At      : " . $suratJalan->created_at . "\n";
echo "Updated At      : " . $suratJalan->updated_at . "\n";
echo "────────────────────────────────────────────────────────────────────────────────\n\n";

// Cek approval records
echo "📋 APPROVAL RECORDS:\n";
echo "────────────────────────────────────────────────────────────────────────────────\n";

$approvals = DB::table('surat_jalan_approvals')
    ->where('surat_jalan_id', $suratJalan->id)
    ->orderBy('approval_level')
    ->get();

if ($approvals->isEmpty()) {
    echo "❌ Tidak ada approval records untuk surat jalan ini!\n";
} else {
    foreach ($approvals as $approval) {
        echo "\nApproval ID: " . $approval->id . "\n";
        echo "  Level       : " . $approval->approval_level . "\n";
        echo "  Status      : " . $approval->status . "\n";
        echo "  Approved By : " . ($approval->approved_by ?? 'NULL') . "\n";
        echo "  Approved At : " . ($approval->approved_at ?? 'NULL') . "\n";
        echo "  Notes       : " . ($approval->approval_notes ?? '-') . "\n";
    }
}

echo "────────────────────────────────────────────────────────────────────────────────\n\n";

// Cek apakah ada tanda terima yang sudah dibuat
echo "📋 CEK TANDA TERIMA:\n";
echo "────────────────────────────────────────────────────────────────────────────────\n";

$tandaTerima = DB::table('tanda_terimas')
    ->where('nomor_surat_jalan', $suratJalan->no_surat_jalan)
    ->first();

if ($tandaTerima) {
    echo "✅ Tanda Terima sudah dibuat:\n";
    echo "  ID              : " . $tandaTerima->id . "\n";
    echo "  No. Surat Jalan : " . $tandaTerima->nomor_surat_jalan . "\n";
    echo "  Status          : " . $tandaTerima->status . "\n";
    echo "  Created At      : " . $tandaTerima->created_at . "\n";
} else {
    echo "❌ Belum ada tanda terima untuk surat jalan ini.\n";
}

echo "────────────────────────────────────────────────────────────────────────────────\n\n";

// Cek kontainer status
if ($suratJalan->no_kontainer) {
    echo "📦 CEK STATUS KONTAINER: " . $suratJalan->no_kontainer . "\n";
    echo "────────────────────────────────────────────────────────────────────────────────\n";

    $kontainerMaster = DB::table('kontainers')
        ->where('nomor_seri_gabungan', $suratJalan->no_kontainer)
        ->first();

    $kontainerStock = DB::table('stock_kontainers')
        ->where('nomor_seri_gabungan', $suratJalan->no_kontainer)
        ->first();

    if ($kontainerMaster) {
        echo "Master Kontainer: " . $kontainerMaster->status . "\n";
    } else {
        echo "Master Kontainer: Tidak ditemukan\n";
    }

    if ($kontainerStock) {
        echo "Stock Kontainer : " . $kontainerStock->status . "\n";
    } else {
        echo "Stock Kontainer : Tidak ditemukan\n";
    }
    echo "────────────────────────────────────────────────────────────────────────────────\n";
}
