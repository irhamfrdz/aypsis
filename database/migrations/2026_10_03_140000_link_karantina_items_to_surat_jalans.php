<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('biaya_kapal_perijinan_karantinas')
            ->where('source_type', 'surat_jalan')
            ->orderBy('id')
            ->chunkById(500, function ($items) {
                foreach ($items as $item) {
                    $suratJalanId = DB::table('tanda_terimas')
                        ->where('id', $item->source_id)
                        ->value('surat_jalan_id');

                    if (! $suratJalanId) {
                        continue;
                    }

                    $existingItem = DB::table('biaya_kapal_perijinan_karantinas')
                        ->where('biaya_kapal_perijinan_id', $item->biaya_kapal_perijinan_id)
                        ->where('source_type', 'surat_jalan')
                        ->where('source_id', $suratJalanId)
                        ->where('id', '!=', $item->id)
                        ->first();

                    if ($existingItem) {
                        DB::table('biaya_kapal_perijinan_karantinas')
                            ->where('id', $existingItem->id)
                            ->update(['nominal' => (float) $existingItem->nominal + (float) $item->nominal]);
                        DB::table('biaya_kapal_perijinan_karantinas')->where('id', $item->id)->delete();

                        continue;
                    }

                    DB::table('biaya_kapal_perijinan_karantinas')
                        ->where('id', $item->id)
                        ->update(['source_id' => $suratJalanId]);

                    DB::table('biaya_kapal_perijinan')
                        ->where('id', $item->biaya_kapal_perijinan_id)
                        ->where('karantina_source_type', 'surat_jalan')
                        ->where('karantina_source_id', $item->source_id)
                        ->update(['karantina_source_id' => $suratJalanId]);
                }
            });
    }

    public function down(): void
    {
        DB::table('biaya_kapal_perijinan_karantinas')
            ->where('source_type', 'surat_jalan')
            ->orderBy('id')
            ->chunkById(500, function ($items) {
                foreach ($items as $item) {
                    $tandaTerimaId = DB::table('tanda_terimas')
                        ->where('surat_jalan_id', $item->source_id)
                        ->orderBy('id')
                        ->value('id');

                    if (! $tandaTerimaId) {
                        continue;
                    }

                    DB::table('biaya_kapal_perijinan_karantinas')
                        ->where('id', $item->id)
                        ->update(['source_id' => $tandaTerimaId]);

                    DB::table('biaya_kapal_perijinan')
                        ->where('id', $item->biaya_kapal_perijinan_id)
                        ->where('karantina_source_type', 'surat_jalan')
                        ->where('karantina_source_id', $item->source_id)
                        ->update(['karantina_source_id' => $tandaTerimaId]);
                }
            });
    }
};
