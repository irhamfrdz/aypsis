<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PranotaSuratJalan;
use Carbon\Carbon;

echo "🔍 Testing Pranota Surat Jalan Show View Data...\n\n";

try {
    // Find the first pranota or create a test one
    $pranota = PranotaSuratJalan::with([
        'suratJalans.pengirimRelation',
        'suratJalans.jenisBarangRelation',
        'suratJalans.tujuanPengambilanRelation',
        'creator'
    ])->first();

    if (!$pranota) {
        echo "❌ No pranota surat jalan found in database\n";
        exit(1);
    }

    echo "✅ Found pranota: {$pranota->nomor_pranota}\n";
    echo "📅 Tanggal: " . ($pranota->formatted_tanggal_pranota ?? 'No accessor found') . "\n";
    echo "💰 Total: " . ($pranota->formatted_total_amount ?? 'No accessor found') . "\n";
    echo "👤 Creator: " . ($pranota->creator->name ?? 'No creator') . "\n";
    echo "📊 Status: {$pranota->status}\n";
    echo "📝 Catatan: " . ($pranota->catatan ?? 'No catatan') . "\n";
    echo "📦 Surat Jalans count: " . $pranota->suratJalans->count() . "\n\n";

    if ($pranota->suratJalans->count() > 0) {
        echo "📋 First Surat Jalan Details:\n";
        $firstSJ = $pranota->suratJalans->first();

        echo "  📄 Nomor: {$firstSJ->no_surat_jalan}\n";
        echo "  📅 Tanggal: " . ($firstSJ->formatted_tanggal_surat_jalan ?? 'No accessor') . "\n";
        echo "  👤 Pengirim: " . ($firstSJ->pengirim ?? 'No pengirim') . "\n";
        echo "  📦 Jenis Barang: " . ($firstSJ->jenis_barang ?? 'No jenis barang') . "\n";
        echo "  🎯 Tujuan: " . ($firstSJ->tujuan_pengambilan ?? 'No tujuan') . "\n";
        echo "  📦 Nomor Kontainer: " . ($firstSJ->no_kontainer ?? 'No kontainer') . "\n";
        echo "  🚗 Supir: " . ($firstSJ->supir ?? 'No supir') . "\n";
        echo "  🚗 Supir 2: " . ($firstSJ->supir2 ?? 'No supir2') . "\n";
        echo "  💰 Uang Jalan: " . ($firstSJ->formatted_uang_jalan ?? 'No accessor') . "\n";
        echo "  ✅ Approval Status: " . ($firstSJ->isFullyApproved() ? 'Fully Approved' : 'Pending Approval') . "\n";
    }

    echo "\n✅ All data accessible for show view!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
