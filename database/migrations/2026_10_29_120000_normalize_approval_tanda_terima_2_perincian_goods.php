<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('approval_tanda_terima_2_goods', function (Blueprint $table) {
            $table->json('goods_legacy')->nullable()->after('goods');
        });

        $allowed = array_fill_keys(['nama_barang', 'jumlah', 'satuan', 'ukuran', 'keterangan_barang'], true);
        DB::table('approval_tanda_terima_2_goods')->select('id', 'goods')->orderBy('id')
            ->chunkById(100, function ($records) use ($allowed): void {
                foreach ($records as $record) {
                    $original = json_decode($record->goods, true);
                    if (! is_array($original) || ! array_is_list($original) || $original === []) {
                        continue;
                    }

                    $first = is_array($original[0]) ? array_intersect_key($original[0], $allowed) : ['nama_barang' => (string) $original[0]];
                    $normalized = [$first];
                    if ($original === $normalized) {
                        continue;
                    }

                    DB::table('approval_tanda_terima_2_goods')->where('id', $record->id)->update([
                        'goods_legacy' => $record->goods,
                        'goods' => json_encode($normalized, JSON_UNESCAPED_UNICODE),
                    ]);
                }
            });
    }

    public function down(): void
    {
        DB::table('approval_tanda_terima_2_goods')->select('id', 'goods_legacy')->whereNotNull('goods_legacy')
            ->orderBy('id')->chunkById(100, function ($records): void {
                foreach ($records as $record) {
                    DB::table('approval_tanda_terima_2_goods')->where('id', $record->id)
                        ->update(['goods' => $record->goods_legacy]);
                }
            });

        Schema::table('approval_tanda_terima_2_goods', function (Blueprint $table) {
            $table->dropColumn('goods_legacy');
        });
    }
};
