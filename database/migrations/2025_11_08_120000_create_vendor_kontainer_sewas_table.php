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
        // Check if table already exists before creating
        if (!Schema::hasTable('vendor_kontainer_sewas')) {
            Schema::create('vendor_kontainer_sewas', function (Blueprint $table) {
                $table->id();
                $table->string('kode')->unique()->comment('Kode unik vendor kontainer sewa');
                $table->string('nama_vendor')->comment('Nama vendor kontainer sewa');
                $table->text('catatan')->nullable()->comment('Catatan tambahan vendor');
                $table->enum('status', ['aktif', 'nonaktif'])->default('aktif')->comment('Status vendor');
                $table->timestamps();
                
                // Indexes
                $table->index(['status'], 'idx_vendor_kontainer_sewas_status');
                $table->index(['kode'], 'idx_vendor_kontainer_sewas_kode');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_kontainer_sewas');
    }
};