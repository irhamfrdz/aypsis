<?php
// Simulasi end-to-end: buat kontainer + tagihan sumber, buat pranota (menggunakan Pranota controller action), lakukan pembayaran, verifikasi status
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\TagihanKontainerSewa;
use App\Models\PembayaranPranotaTagihanKontainer;

echo "Starting simulation...\n";
DB::beginTransaction();
try {
    // 1) create kontainer
    $kontainerId = DB::table('kontainers')->insertGetId([
        'awalan_kontainer' => 'SIM',
    // keep serial to 6 chars to match schema
    'nomor_seri_kontainer' => substr(uniqid('S'), 0, 6),
        'tipe_kontainer' => 'DRY',
        'akhiran_kontainer' => 'X',
    'nomor_seri_gabungan' => 'SIM' . substr(uniqid('S'), 0, 3) . 'X',
        'ukuran' => '40',
        'pemilik_kontainer' => 'ZONA',
        'harga_satuan' => 1261261.00,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "Created kontainer id={$kontainerId}\n";

    // 2) create source tagihan (non-pranota)
    $sourceTagihanId = DB::table('tagihan_kontainer_sewa')->insertGetId([
        'vendor' => 'ZONA',
        'tarif' => 'Bulanan',
        'ukuran_kontainer' => '40',
        'harga' => 1261261.00,
        'tanggal_harga_awal' => now()->toDateString(),
        'periode' => '1',
        'group_code' => null,
        'keterangan' => 'Pranota untuk tagihan periode 1',
        'status_pembayaran' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "Created source tagihan id={$sourceTagihanId}\n";

    // attach pivot
    DB::table('tagihan_kontainer_sewa_kontainers')->insert([
        'tagihan_id' => $sourceTagihanId,
        'kontainer_id' => $kontainerId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "Attached kontainer to source tagihan\n";

    // 3) create pranota by inserting a new pranota row and moving pivot
    $pranotaId = DB::table('tagihan_kontainer_sewa')->insertGetId([
        'vendor' => 'ZONA',
        'tarif' => 'Pranota',
        'ukuran_kontainer' => '40',
        'harga' => 1261261.00,
        'tanggal_harga_awal' => now()->toDateString(),
        'periode' => '1',
        'group_code' => null,
        'keterangan' => '-',
        'status_pembayaran' => 'Sudah Masuk Pranota',
    // nomor_pranota column removed; leave null or omit
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "Created pranota id={$pranotaId}\n";

    // move pivot: in real flow, pranota creation copies pivot rows from source; here we will insert another pivot row linking pranota->kontainer
    DB::table('tagihan_kontainer_sewa_kontainers')->insert([
        'tagihan_id' => $pranotaId,
        'kontainer_id' => $kontainerId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "Attached kontainer to pranota via pivot\n";

    // 4) simulate payment creation similar to controller
    $pembayaran = PembayaranPranotaTagihanKontainer::create([
        'nomor_pembayaran' => 'SIM-BTK-'.time(),
        'nomor_cetakan' => 1,
        'bank' => 'BCA',
        'jenis_transaksi' => 'Debit',
        'tanggal_kas' => now()->toDateString(),
        'keterangan' => null,
        'total_pembayaran' => 0,
        'penyesuaian' => 0,
    ]);
    echo "Created pembayaran id={$pembayaran->id}\n";

    // attach pranota tagihan to pembayaran and mark tagihan as Lunas (simulate controller)
    $pembayaran->tagihans()->attach($pranotaId, ['amount' => 1261261.00]);
    $pranota = TagihanKontainerSewa::find($pranotaId);
    $pranota->status_pembayaran = 'Lunas';
    $pranota->keterangan = ($pranota->keterangan ? $pranota->keterangan . ' | ' : '') . 'Dibayar melalui ' . $pembayaran->nomor_pembayaran;
    $pranota->save();
    echo "Marked pranota as Lunas\n";

    DB::commit();

    // 5) Verify final state
    $rows = DB::table('tagihan_kontainer_sewa_kontainers as tkk')
        ->join('tagihan_kontainer_sewa as tk', 'tkk.tagihan_id', '=', 'tk.id')
        ->where('tkk.kontainer_id', $kontainerId)
        ->select('tk.id','tk.tarif','tk.status_pembayaran','tk.keterangan','tkk.tagihan_id')
        ->orderBy('tk.id', 'desc')
        ->get();

    echo "Final tagihan rows for kontainer {$kontainerId}:\n";
    echo json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "Simulation failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Simulation completed successfully.\n";
