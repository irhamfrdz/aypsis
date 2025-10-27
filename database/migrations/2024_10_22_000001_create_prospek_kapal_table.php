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
        if (!Schema::hasTable('prospek_kapal')) {
            Schema::create('prospek_kapal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pergerakan_kapal_id')->nullable()->constrained('pergerakan_kapal')->onDelete('set null');
            $table->string('voyage')->index();
            $table->string('nama_kapal');
            $table->datetime('tanggal_loading');
            $table->datetime('estimasi_departure')->nullable();
            $table->integer('jumlah_kontainer_terjadwal')->default(0);
            $table->integer('jumlah_kontainer_loaded')->default(0);
            $table->enum('status', ['draft', 'scheduled', 'loading', 'completed', 'cancelled'])->default('draft');
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['voyage', 'status']);
            $table->index('tanggal_loading');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospek_kapal');
    }
};
