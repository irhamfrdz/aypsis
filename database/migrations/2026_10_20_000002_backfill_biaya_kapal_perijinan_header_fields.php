<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Preserve the first available legacy section values when the invoice header is empty.
        DB::table('biaya_kapal_perijinan')
            ->select(['id', 'biaya_kapal_id', 'vendor', 'nomor_referensi'])
            ->orderBy('id')
            ->chunkById(500, function ($sections) {
                foreach ($sections as $section) {
                    if (filled($section->vendor)) {
                        DB::table('biaya_kapals')
                            ->where('id', $section->biaya_kapal_id)
                            ->where(function ($query) {
                                $query->whereNull('nama_vendor')->orWhere('nama_vendor', '');
                            })
                            ->update(['nama_vendor' => $section->vendor]);
                    }

                    if (filled($section->nomor_referensi)) {
                        DB::table('biaya_kapals')
                            ->where('id', $section->biaya_kapal_id)
                            ->where(function ($query) {
                                $query->whereNull('nomor_referensi')->orWhere('nomor_referensi', '');
                            })
                            ->update(['nomor_referensi' => $section->nomor_referensi]);
                    }
                }
            });
    }

    public function down(): void
    {
        // Keep copied header values; the original section values remain for legacy records.
    }
};
