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
        Schema::create('pranota_lembur_karyawan_headers', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pranota', 50)->unique();
            $table->integer('nomor_cetakan')->default(1);
            $table->date('tanggal_pranota');
            $table->decimal('total_biaya', 15, 2)->default(0);
            $table->decimal('adjustment', 15, 2)->default(0);
            $table->decimal('total_setelah_adjustment', 15, 2)->default(0);
            $table->string('status', 20)->default('draft');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Rename the column in the detail table if it exists
        if (Schema::hasColumn('pranota_lembur_karyawans', 'pranota_lembur_id')) {
            Schema::table('pranota_lembur_karyawans', function (Blueprint $table) {
                $table->renameColumn('pranota_lembur_id', 'pranota_lembur_karyawan_header_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('pranota_lembur_karyawans', 'pranota_lembur_karyawan_header_id')) {
            Schema::table('pranota_lembur_karyawans', function (Blueprint $table) {
                $table->renameColumn('pranota_lembur_karyawan_header_id', 'pranota_lembur_id');
            });
        }
        Schema::dropIfExists('pranota_lembur_karyawan_headers');
    }
};
