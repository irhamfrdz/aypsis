<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║ TEST APPROVAL SURAT JALAN SJ00006                                            ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";

// ID Surat Jalan yang akan di-approve
$suratJalanId = 11; // SJ00006

// Ambil data surat jalan
$suratJalan = DB::table('surat_jalans')->where('id', $suratJalanId)->first();

if (!$suratJalan) {
    echo "❌ Surat Jalan tidak ditemukan!\n";
    exit;
}

echo "📋 SURAT JALAN YANG AKAN DI-APPROVE:\n";
echo "────────────────────────────────────────────────────────────────────────────────\n";
echo "ID              : " . $suratJalan->id . "\n";
echo "No. Surat Jalan : " . $suratJalan->no_surat_jalan . "\n";
echo "Tanggal         : " . date('d/m/Y', strtotime($suratJalan->tanggal_surat_jalan)) . "\n";
echo "Supir           : " . $suratJalan->supir . "\n";
echo "Kegiatan        : " . $suratJalan->kegiatan . "\n";
echo "No. Kontainer   : " . ($suratJalan->no_kontainer ?? 'Belum diisi') . "\n";
echo "No. Seal        : " . ($suratJalan->no_seal ?? 'Belum diisi') . "\n";
echo "Status Sekarang : " . $suratJalan->status . "\n";
echo "────────────────────────────────────────────────────────────────────────────────\n\n";

// Cek approval record
$approval = DB::table('surat_jalan_approvals')
    ->where('surat_jalan_id', $suratJalanId)
    ->where('approval_level', 'approval')
    ->first();

if (!$approval) {
    echo "❌ Approval record tidak ditemukan!\n";
    exit;
}

echo "✅ Ditemukan Pending Approval:\n";
echo "   - Approval ID : " . $approval->id . "\n";
echo "   - Level       : " . $approval->approval_level . "\n";
echo "   - Status      : " . $approval->status . "\n";
echo "   - Created     : " . date('d/m/Y H:i:s', strtotime($approval->created_at)) . "\n\n";

// Cek apakah sudah ada tanda terima
$tandaTerima = DB::table('tanda_terimas')->where('surat_jalan_id', $suratJalanId)->first();

if ($tandaTerima) {
    echo "⚠️  Tanda terima sudah ada (ID: {$tandaTerima->id}), tidak akan dibuat ulang.\n\n";
} else {
    echo "✅ Belum ada tanda terima, akan dibuat otomatis setelah approval.\n\n";
}

echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║ PROSES APPROVAL DIMULAI                                                      ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";

DB::beginTransaction();

try {
    // 1. Update approval status
    echo "1️⃣  Mengupdate status approval menjadi 'approved'...\n";
    DB::table('surat_jalan_approvals')
        ->where('id', $approval->id)
        ->update([
            'status' => 'approved',
            'approved_by' => 1, // Admin user
            'approved_at' => now(),
            'updated_at' => now(),
        ]);
    echo "   ✅ Approval status updated\n\n";

    // 2. Update surat jalan status
    echo "2️⃣  Mengupdate status surat jalan menjadi 'approved'...\n";
    DB::table('surat_jalans')
        ->where('id', $suratJalanId)
        ->update([
            'status' => 'approved',
            'updated_at' => now(),
        ]);
    echo "   ✅ Surat jalan status updated to 'approved'\n\n";

    // 3. Update kontainer status
    echo "3️⃣  Update status kontainer berdasarkan kegiatan...\n";
    if ($suratJalan->no_kontainer) {
        $nomorKontainers = array_map('trim', explode(',', $suratJalan->no_kontainer));

        foreach ($nomorKontainers as $nomorKontainer) {
            if (empty($nomorKontainer)) continue;

            echo "   Kontainer yang akan diupdate: $nomorKontainer\n";

            // Tentukan status baru berdasarkan kegiatan
            $statusBaru = match($suratJalan->kegiatan) {
                'ANTAR ISI' => 'terkirim',
                'ANTAR KOSONG' => 'kosong',
                'AMBIL ISI' => 'penuh',
                'AMBIL KOSONG' => 'kosong',
                'RETUR BARANG' => 'retur',
                'KIRIM BARANG' => 'terkirim',
                default => 'aktif'
            };

            echo "   Kegiatan: {$suratJalan->kegiatan} → Status baru: $statusBaru\n";

            // Update di master kontainers
            $updatedMaster = DB::table('kontainers')
                ->where('nomor_seri_gabungan', $nomorKontainer)
                ->update([
                    'status' => $statusBaru,
                    'updated_at' => now(),
                ]);

            // Update di stock_kontainers
            $updatedStock = DB::table('stock_kontainers')
                ->where('nomor_seri_gabungan', $nomorKontainer)
                ->update([
                    'status' => $statusBaru,
                    'updated_at' => now(),
                ]);

            echo "   ✅ Kontainer $nomorKontainer: master=" . ($updatedMaster ? 'updated' : 'not found') .
                 ", stock=" . ($updatedStock ? 'updated' : 'not found') . "\n";
        }
    } else {
        echo "   ⚠️  Tidak ada nomor kontainer\n";
    }
    echo "\n";

    // 4. Create tanda terima
    echo "4️⃣  Membuat Tanda Terima otomatis...\n";
    if (!$tandaTerima) {
        $tandaTerimaId = DB::table('tanda_terimas')->insertGetId([
            'surat_jalan_id' => $suratJalanId,
            'no_surat_jalan' => $suratJalan->no_surat_jalan,
            'tanggal_surat_jalan' => $suratJalan->tanggal_surat_jalan,
            'supir' => $suratJalan->supir,
            'kegiatan' => $suratJalan->kegiatan,
            'size' => $suratJalan->size,
            'jumlah_kontainer' => $suratJalan->jumlah_kontainer,
            'no_kontainer' => $suratJalan->no_kontainer,
            'no_seal' => $suratJalan->no_seal,
            'tujuan_pengiriman' => $suratJalan->tujuan_pengiriman,
            'pengirim' => $suratJalan->pengirim,
            'gambar_checkpoint' => $suratJalan->gambar_checkpoint,
            'status' => 'draft',
            'created_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        echo "   ✅ Tanda Terima created (ID: $tandaTerimaId)\n";
        echo "   📋 No. Surat Jalan: {$suratJalan->no_surat_jalan}\n";
        echo "   📊 Status: draft\n";
    } else {
        echo "   ⚠️  Tanda terima sudah ada, skip pembuatan\n";
    }

    DB::commit();

    echo "\n╔══════════════════════════════════════════════════════════════════════════════╗\n";
    echo "║ ✅ APPROVAL BERHASIL!                                                        ║\n";
    echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";

    // Tampilkan hasil akhir
    $suratJalanFinal = DB::table('surat_jalans')->where('id', $suratJalanId)->first();
    $approvalFinal = DB::table('surat_jalan_approvals')->where('id', $approval->id)->first();
    $tandaTerimaFinal = DB::table('tanda_terimas')->where('surat_jalan_id', $suratJalanId)->first();

    echo "📊 HASIL AKHIR:\n";
    echo "────────────────────────────────────────────────────────────────────────────────\n";
    echo "Surat Jalan:\n";
    echo "   - ID: {$suratJalanFinal->id}\n";
    echo "   - No: {$suratJalanFinal->no_surat_jalan}\n";
    echo "   - Status: {$suratJalanFinal->status}\n\n";

    echo "Approval:\n";
    echo "   - ID: {$approvalFinal->id}\n";
    echo "   - Status: {$approvalFinal->status}\n";
    echo "   - Approved At: " . date('d/m/Y H:i:s', strtotime($approvalFinal->approved_at)) . "\n";
    echo "   - Approved By: {$approvalFinal->approved_by}\n\n";

    if ($tandaTerimaFinal) {
        echo "Tanda Terima:\n";
        echo "   - ID: {$tandaTerimaFinal->id}\n";
        echo "   - No. Surat Jalan: {$tandaTerimaFinal->no_surat_jalan}\n";
        echo "   - Status: {$tandaTerimaFinal->status}\n";
        echo "   - Created: " . date('d/m/Y H:i:s', strtotime($tandaTerimaFinal->created_at)) . "\n";
    }
    echo "────────────────────────────────────────────────────────────────────────────────\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "\n📋 Stack trace:\n" . $e->getTraceAsString() . "\n";
}
