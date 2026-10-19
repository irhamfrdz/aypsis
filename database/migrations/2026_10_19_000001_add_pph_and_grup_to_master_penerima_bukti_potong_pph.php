<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_penerima_bukti_potong_pph', function (Blueprint $table) {
            $table->string('nik_npwp', 64)->nullable();
            $table->string('pph', 100)->nullable();
            $table->string('grup', 100)->nullable();
        });

        DB::table('master_penerima_bukti_potong_pph')
            ->orderBy('id')
            ->chunkById(500, function ($penerimas) {
                foreach ($penerimas as $penerima) {
                    $identitas = trim((string) ($penerima->npwp ?: $penerima->nik));

                    if ($identitas !== '') {
                        DB::table('master_penerima_bukti_potong_pph')
                            ->where('id', $penerima->id)
                            ->update(['nik_npwp' => $identitas]);
                    }
                }
            });
    }

    public function down(): void
    {
        Schema::table('master_penerima_bukti_potong_pph', function (Blueprint $table) {
            $table->dropColumn(['nik_npwp', 'pph', 'grup']);
        });
    }
};
