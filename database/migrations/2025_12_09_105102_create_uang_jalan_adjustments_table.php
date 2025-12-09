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
        Schema::create('uang_jalan_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uang_jalan_id')->constrained('uang_jalans')->onDelete('cascade');
            $table->date('tanggal_penyesuaian');
            $table->enum('jenis_penyesuaian', ['penambahan', 'pengurangan', 'pengembalian_penuh', 'pengembalian_sebagian']);
            $table->decimal('jumlah_penyesuaian', 15, 2);
            $table->string('alasan_penyesuaian');
            $table->text('memo')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uang_jalan_adjustments');
    }
};
