<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('prospek')
            ->where(function ($query) {
                $query->whereNull('no_surat_jalan')->orWhere('no_surat_jalan', '');
            })
            ->where(function ($query) {
                $query->where('keterangan', 'like', 'Kontainer LCL dengan % tanda terima')
                    ->orWhere('keterangan', 'Synced from LCL Stuffing');
            })
            ->whereNotNull('nomor_kontainer')->where('nomor_kontainer', '!=', '')
            ->whereNotNull('no_seal')->where('no_seal', '!=', '')
            ->chunkById(100, function ($prospeks) {
                foreach ($prospeks as $prospek) {
                    $numbers = DB::table('tanda_terima_lcl_kontainer_pivot as pivot')
                        ->join('tanda_terimas_lcl as receipt', 'receipt.id', '=', 'pivot.tanda_terima_lcl_id')
                        ->where('pivot.nomor_kontainer', $prospek->nomor_kontainer)
                        ->where('pivot.nomor_seal', $prospek->no_seal)
                        ->orderBy('pivot.id')
                        ->pluck('receipt.nomor_tanda_terima')
                        ->filter()->unique()->implode(', ');

                    if ($numbers !== '') {
                        DB::table('prospek')->where('id', $prospek->id)
                            ->where(function ($query) {
                                $query->whereNull('no_surat_jalan')->orWhere('no_surat_jalan', '');
                            })
                            ->update(['no_surat_jalan' => Str::limit($numbers, 255, '')]);
                    }
                }
            });
    }

    public function down(): void
    {
        // Preserve recovered receipt numbers; they cannot be distinguished from later edits.
    }
};
