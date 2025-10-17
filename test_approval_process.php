<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

use App\Models\SuratJalan;
use App\Models\SuratJalanApproval;
use App\Models\TandaTerima;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║ TEST APPROVAL SURAT JALAN                                                    ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";

// Pilih surat jalan ID 10 untuk testing
$suratJalanId = 10;
$suratJalan = SuratJalan::find($suratJalanId);

if (!$suratJalan) {
    echo "❌ Surat Jalan ID {$suratJalanId} tidak ditemukan!\n";
    exit(1);
}

echo "📋 SURAT JALAN YANG AKAN DI-APPROVE:\n";
echo str_repeat('─', 80) . "\n";
echo "ID              : {$suratJalan->id}\n";
echo "No. Surat Jalan : {$suratJalan->no_surat_jalan}\n";
echo "Tanggal         : " . ($suratJalan->tanggal_surat_jalan ? date('d/m/Y', strtotime($suratJalan->tanggal_surat_jalan)) : '-') . "\n";
echo "Supir           : {$suratJalan->supir}\n";
echo "Kegiatan        : {$suratJalan->kegiatan}\n";
echo "No. Kontainer   : " . ($suratJalan->no_kontainer ?: 'Belum diisi') . "\n";
echo "No. Seal        : " . ($suratJalan->no_seal ?: 'Belum diisi') . "\n";
echo "Status Sekarang : {$suratJalan->status}\n";
echo str_repeat('─', 80) . "\n\n";

// Check pending approval
$pendingApproval = SuratJalanApproval::where('surat_jalan_id', $suratJalan->id)
    ->where('status', 'pending')
    ->first();

if (!$pendingApproval) {
    echo "❌ Tidak ada pending approval untuk surat jalan ini!\n";
    exit(1);
}

echo "✅ Ditemukan Pending Approval:\n";
echo "   - Approval ID : {$pendingApproval->id}\n";
echo "   - Level       : {$pendingApproval->approval_level}\n";
echo "   - Status      : {$pendingApproval->status}\n";
echo "   - Created     : " . $pendingApproval->created_at->format('d/m/Y H:i:s') . "\n\n";

// Check if tanda terima already exists
$existingTandaTerima = TandaTerima::where('surat_jalan_id', $suratJalan->id)->first();
if ($existingTandaTerima) {
    echo "⚠️  Tanda Terima sudah ada (ID: {$existingTandaTerima->id})\n";
    echo "   Tanda terima duplikat tidak akan dibuat.\n\n";
} else {
    echo "✅ Belum ada tanda terima, akan dibuat otomatis setelah approval.\n\n";
}

echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║ PROSES APPROVAL DIMULAI                                                      ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";

DB::beginTransaction();

try {
    echo "1️⃣  Mengupdate status approval menjadi 'approved'...\n";
    $pendingApproval->update([
        'status' => 'approved',
        'approved_at' => now(),
        'approved_by' => 1, // Admin user ID
        'catatan_approval' => 'Test approval via script'
    ]);
    echo "   ✅ Approval status updated\n\n";

    echo "2️⃣  Mengupdate status surat jalan menjadi 'completed'...\n";
    $suratJalan->update([
        'status' => 'completed'
    ]);
    echo "   ✅ Surat jalan status updated to 'completed'\n\n";

    echo "3️⃣  Update status kontainer berdasarkan kegiatan...\n";
    if ($suratJalan->no_kontainer) {
        $kontainers = array_map('trim', explode(',', $suratJalan->no_kontainer));
        echo "   Kontainer yang akan diupdate: " . implode(', ', $kontainers) . "\n";

        // Determine status based on kegiatan
        $statusMapping = [
            'STUFFING' => 'terisi',
            'ANTAR ISI' => 'terkirim',
            'JEMPUT KOSONG' => 'active',
            'ANTAR KOSONG' => 'tersedia',
            'MASUK DEPO' => 'tersedia',
        ];

        $kegiatanUpper = strtoupper($suratJalan->kegiatan);
        $newStatus = $statusMapping[$kegiatanUpper] ?? null;

        if ($newStatus) {
            echo "   Kegiatan: {$kegiatanUpper} → Status baru: {$newStatus}\n";

            foreach ($kontainers as $kontainerNo) {
                // Update di master kontainers
                $updated = DB::table('kontainers')
                    ->where('nomor_seri_gabungan', $kontainerNo)
                    ->update([
                        'status' => $newStatus,
                        'updated_at' => now()
                    ]);

                // Update di stock kontainers
                $stockUpdated = DB::table('stock_kontainers')
                    ->where('nomor_seri_gabungan', $kontainerNo)
                    ->update([
                        'status' => $newStatus,
                        'updated_at' => now()
                    ]);

                echo "   ✅ Kontainer {$kontainerNo}: master=" . ($updated ? 'updated' : 'not found') .
                     ", stock=" . ($stockUpdated ? 'updated' : 'not found') . "\n";
            }
        } else {
            echo "   ⚠️  Kegiatan '{$kegiatanUpper}' tidak ada mapping status\n";
        }
    } else {
        echo "   ⚠️  Tidak ada nomor kontainer\n";
    }
    echo "\n";

    echo "4️⃣  Membuat Tanda Terima otomatis...\n";
    if (!$existingTandaTerima) {
        $tandaTerima = TandaTerima::create([
            'surat_jalan_id' => $suratJalan->id,
            'no_surat_jalan' => $suratJalan->no_surat_jalan,
            'tanggal_surat_jalan' => $suratJalan->tanggal_surat_jalan,
            'supir' => $suratJalan->supir,
            'kegiatan' => $suratJalan->kegiatan,
            'size' => $suratJalan->size,
            'jumlah_kontainer' => $suratJalan->jumlah_kontainer,
            'no_kontainer' => $suratJalan->no_kontainer,
            'no_seal' => $suratJalan->no_seal,
            'tujuan' => $suratJalan->tujuan,
            'pengirim' => $suratJalan->pengirim,
            'gambar_checkpoint' => $suratJalan->gambar_checkpoint,
            'status' => 'draft',
            'created_by' => 1,
        ]);
        echo "   ✅ Tanda Terima created (ID: {$tandaTerima->id})\n";
        echo "   📋 No. Surat Jalan: {$tandaTerima->no_surat_jalan}\n";
        echo "   📊 Status: {$tandaTerima->status}\n\n";
    } else {
        echo "   ⏭️  Skip - Tanda terima sudah ada\n\n";
    }

    DB::commit();

    echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
    echo "║ ✅ APPROVAL BERHASIL!                                                        ║\n";
    echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";

    echo "📊 HASIL AKHIR:\n";
    echo str_repeat('─', 80) . "\n";

    $suratJalan->refresh();
    $pendingApproval->refresh();

    echo "Surat Jalan:\n";
    echo "   - ID: {$suratJalan->id}\n";
    echo "   - No: {$suratJalan->no_surat_jalan}\n";
    echo "   - Status: {$suratJalan->status}\n\n";

    echo "Approval:\n";
    echo "   - ID: {$pendingApproval->id}\n";
    echo "   - Status: {$pendingApproval->status}\n";
    echo "   - Approved At: " . ($pendingApproval->approved_at ? $pendingApproval->approved_at->format('d/m/Y H:i:s') : '-') . "\n";
    echo "   - Approved By: " . ($pendingApproval->approved_by ?: '-') . "\n\n";

    $tandaTerimaCheck = TandaTerima::where('surat_jalan_id', $suratJalan->id)->first();
    echo "Tanda Terima:\n";
    if ($tandaTerimaCheck) {
        echo "   - ID: {$tandaTerimaCheck->id}\n";
        echo "   - No. Surat Jalan: {$tandaTerimaCheck->no_surat_jalan}\n";
        echo "   - Status: {$tandaTerimaCheck->status}\n";
        echo "   - Created: " . $tandaTerimaCheck->created_at->format('d/m/Y H:i:s') . "\n";
    } else {
        echo "   - Tidak ada\n";
    }

    echo str_repeat('─', 80) . "\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
