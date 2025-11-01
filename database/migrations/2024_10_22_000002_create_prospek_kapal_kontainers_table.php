<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if table already exists to prevent conflicts
        if (!Schema::hasTable('prospek_kapal_kontainers')) {
            Schema::create('prospek_kapal_kontainers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospek_kapal_id')->constrained('prospek_kapal')->onDelete('cascade');
            $table->unsignedBigInteger('tanda_terima_id')->nullable(); // Will add foreign key later
            $table->unsignedBigInteger('tanda_terima_tanpa_sj_id')->nullable();
            $table->string('nomor_kontainer');
            $table->string('ukuran_kontainer'); // 20ft, 40ft, etc.
            $table->string('no_seal')->nullable();
            $table->datetime('tanggal_loading')->nullable();
            $table->integer('loading_sequence')->nullable();
            $table->enum('status_loading', ['pending', 'ready', 'loading', 'loaded', 'problem'])->default('pending');
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Foreign key constraint with shorter name
            $table->foreign('tanda_terima_tanpa_sj_id', 'pk_kontainers_tt_tanpa_sj_foreign')
                  ->references('id')->on('tanda_terima_tanpa_surat_jalan')
                  ->onDelete('cascade');

            $table->index(['prospek_kapal_id', 'status_loading']);
            $table->index('nomor_kontainer');
            $table->index('loading_sequence');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospek_kapal_kontainers');
    }
};
