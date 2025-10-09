<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PembayaranDpOb;
use Illuminate\Support\Facades\DB;

try {
    echo "Testing insert pembayaran DP OB...\n\n";

    // Get first available Kas/Bank account
    $kasBank = \App\Models\Coa::where('tipe_akun', 'Kas/Bank')->first();
    if (!$kasBank) {
        die("ERROR: No Kas/Bank account found in akun_coa table\n");
    }

    echo "Using Kas/Bank: {$kasBank->id} - {$kasBank->nomor_akun} - {$kasBank->nama_akun}\n\n";

    // Get first available karyawan
    $karyawan = \App\Models\Karyawan::where('status', 'aktif')->limit(2)->pluck('id')->toArray();
    if (empty($karyawan)) {
        die("ERROR: No active karyawan found\n");
    }

    echo "Using Karyawan IDs: " . implode(', ', $karyawan) . "\n\n";

    DB::beginTransaction();

    $data = [
        'nomor_pembayaran' => 'TEST-' . date('YmdHis'),
        'tanggal_pembayaran' => date('Y-m-d'),
        'kas_bank_id' => $kasBank->id,
        'jenis_transaksi' => 'kredit',
        'supir_ids' => $karyawan, // Will be cast to JSON automatically
        'jumlah_per_supir' => 100000,
        'total_pembayaran' => count($karyawan) * 100000,
        'keterangan' => 'Test pembayaran from script',
        'status' => 'dp_belum_terpakai',
        'dibuat_oleh' => 1,
        'disetujui_oleh' => 1,
        'tanggal_persetujuan' => now(),
    ];

    echo "Data to insert:\n";
    print_r($data);
    echo "\n";

    $pembayaran = PembayaranDpOb::create($data);

    DB::commit();

    echo "SUCCESS! Created pembayaran with ID: " . $pembayaran->id . "\n";
    echo "Nomor Pembayaran: " . $pembayaran->nomor_pembayaran . "\n";
    echo "Status: " . $pembayaran->status . "\n";

} catch (\Exception $e) {
    DB::rollback();
    echo "ERROR: " . $e->getMessage() . "\n\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
