<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Bl;
use Illuminate\Support\Facades\DB;

$voyage = 'AP05PJ26';
$records = Bl::where('no_voyage', $voyage)->orderBy('id')->get();

$seen = [];
$duplicatesToDelete = [];

foreach ($records as $record) {
    // Group by core attributes
    $hash = md5(json_encode([
        trim($record->nomor_bl),
        trim($record->nomor_kontainer),
        trim($record->nama_barang),
        $record->kuantitas,
        $record->satuan,
        $record->pengirim,
        $record->penerima,
        $record->volume,
        $record->tonnage
    ]));
    
    if (isset($seen[$hash])) {
        $duplicatesToDelete[] = $record->id;
    } else {
        $seen[$hash] = $record->id;
    }
}

if (count($duplicatesToDelete) > 0) {
    echo "Ditemukan " . count($duplicatesToDelete) . " data duplikat untuk voyage $voyage.\n";
    
    // Check if these IDs are used in surat_jalan_bongkaran
    $usedInSjb = DB::table('surat_jalan_bongkarans')->whereIn('bl_id', $duplicatesToDelete)->pluck('bl_id')->toArray();
    
    if (count($usedInSjb) > 0) {
        echo "PERINGATAN: " . count($usedInSjb) . " data duplikat sudah dibuatkan Surat Jalan Bongkaran.\n";
        echo "Data tersebut akan dilewati (tidak dihapus) demi keamanan relasi data.\n";
        $duplicatesToDelete = array_diff($duplicatesToDelete, $usedInSjb);
    }
    
    if (count($duplicatesToDelete) > 0) {
        echo "Menghapus " . count($duplicatesToDelete) . " data duplikat...\n";
        Bl::whereIn('id', $duplicatesToDelete)->delete();
        echo "Berhasil menghapus data duplikat.\n";
    } else {
        echo "Tidak ada data duplikat yang aman untuk dihapus.\n";
    }
} else {
    echo "Tidak ditemukan data duplikat pada voyage $voyage.\n";
}
