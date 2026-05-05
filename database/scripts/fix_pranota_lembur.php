<?php

define('LARAVEL_START', microtime(true));

require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';

use App\Models\PranotaLembur;
use Illuminate\Support\Facades\DB;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

DB::beginTransaction();

try {
    echo "Memulai perbaikan data Pranota Lembur...\n";

    // 1. Update biaya_lembur yang masih 0 tapi is_lembur = 1
    $updatedLembur = DB::table('pranota_lembur_surat_jalan')
        ->where('is_lembur', 1)
        ->where('biaya_lembur', 0)
        ->update(['biaya_lembur' => 50000]);
    echo "- Update biaya lembur: $updatedLembur baris diperbarui.\n";

    // 2. Update biaya_nginap yang masih 0 tapi is_nginap = 1
    $updatedNginap = DB::table('pranota_lembur_surat_jalan')
        ->where('is_nginap', 1)
        ->where('biaya_nginap', 0)
        ->update(['biaya_nginap' => 50000]);
    echo "- Update biaya nginap: $updatedNginap baris diperbarui.\n";

    // 3. Update total_biaya per baris di pivot
    $updatedPivotTotal = DB::table('pranota_lembur_surat_jalan')
        ->update(['total_biaya' => DB::raw('biaya_lembur + biaya_nginap')]);
    echo "- Update total biaya per baris pivot: $updatedPivotTotal baris diperbarui.\n";

    // 4. Update total_biaya di header PranotaLembur
    $pranotas = PranotaLembur::all();
    foreach ($pranotas as $p) {
        $newTotal = DB::table('pranota_lembur_surat_jalan')
            ->where('pranota_lembur_id', $p->id)
            ->sum('total_biaya');
        
        $p->total_biaya = $newTotal;
        $p->total_setelah_adjustment = $newTotal + ($p->adjustment ?? 0);
        $p->save();
        
        echo "  * Pranota {$p->nomor_pranota} diperbarui: Total Rp " . number_format($p->total_setelah_adjustment, 0, ',', '.') . "\n";
    }

    DB::commit();
    echo "Perbaikan data selesai dengan sukses!\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "Gagal memperbaiki data: " . $e->getMessage() . "\n";
}
