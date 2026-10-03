<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biaya_kapal_perijinan_karantinas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('biaya_kapal_perijinan_id')
                ->constrained('biaya_kapal_perijinan')
                ->cascadeOnDelete();
            $table->string('source_type', 50);
            $table->unsignedBigInteger('source_id');
            $table->string('nomor_dokumen');
            $table->decimal('nominal', 15, 2);
            $table->timestamps();

            $table->unique(
                ['biaya_kapal_perijinan_id', 'source_type', 'source_id'],
                'perijinan_karantina_source_unique'
            );
            $table->index(['source_type', 'source_id']);
        });

        DB::table('biaya_kapal_perijinan')
            ->where('mode', 'karantina')
            ->whereNotNull('karantina_source_type')
            ->whereNotNull('karantina_source_id')
            ->orderBy('id')
            ->chunkById(500, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('biaya_kapal_perijinan_karantinas')->insert([
                        'biaya_kapal_perijinan_id' => $row->id,
                        'source_type' => $row->karantina_source_type,
                        'source_id' => $row->karantina_source_id,
                        'nomor_dokumen' => $row->karantina_nomor_dokumen,
                        'nominal' => $row->jumlah_biaya,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('biaya_kapal_perijinan_karantinas');
    }
};
