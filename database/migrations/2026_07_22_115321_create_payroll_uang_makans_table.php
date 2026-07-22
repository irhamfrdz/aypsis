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
        Schema::create('payroll_uang_makans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id');
            $table->date('periode_start');
            $table->date('periode_end');
            $table->integer('total_kehadiran')->default(0);
            $table->integer('multiplier')->default(1);
            $table->decimal('nominal_per_hari', 15, 2)->default(50000);
            $table->decimal('total_payout', 15, 2)->default(0);
            $table->string('status')->default('draft'); // draft, paid
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_uang_makans');
    }
};
