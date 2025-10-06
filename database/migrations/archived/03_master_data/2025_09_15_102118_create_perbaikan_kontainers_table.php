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
        Schema::create('perbaikan_kontainers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kontainer_id')->constrained('kontainers')->onDelete('cascade');
            $table->date('tanggal_perbaikan');
            $table->string('jenis_perbaikan'); // maintenance, repair, inspection
            $table->text('deskripsi_perbaikan');
            $table->decimal('biaya_perbaikan', 15, 2)->nullable();
            $table->string('status_perbaikan')->default('pending'); // pending, in_progress, completed, cancelled
            $table->string('teknisi')->nullable();
            $table->text('catatan')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->string('prioritas')->default('normal'); // low, normal, high, urgent
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes
            $table->index(['kontainer_id', 'status_perbaikan']);
            $table->index('tanggal_perbaikan');
            $table->index('status_perbaikan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perbaikan_kontainers');
    }
};
