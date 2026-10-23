<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $normalize = fn ($value) => match (strtoupper(trim($value ?? ''))) {
            'JAKARTA', 'JKT' => 'JKT',
            'BATAM', 'BTM' => 'BTM',
            'PNG' => 'PNG',
            default => null,
        };

        DB::table('assets')->whereIn('lokasi', ['Jakarta', 'Batam', 'Di kapal', 'Di bawa oleh karyawan'])
            ->orderBy('id')->chunkById(100, function ($assets) use ($normalize) {
                foreach ($assets as $asset) {
                    $lokasi = $normalize($asset->lokasi);
                    if (! $lokasi && $asset->source_table === 'mobils' && $asset->source_id) {
                        $lokasi = $normalize(DB::table('mobils')->where('id', $asset->source_id)->value('lokasi'));
                    }
                    if (! $lokasi && $asset->karyawan_id) {
                        $lokasi = $normalize(DB::table('karyawans')->where('id', $asset->karyawan_id)->value('cabang'));
                    }
                    if ($lokasi) {
                        DB::table('assets')->where('id', $asset->id)->update(['lokasi' => $lokasi]);
                    }
                }
            });
    }

    public function down(): void
    {
        // Location codes remain valid business data after a rollback.
    }
};
