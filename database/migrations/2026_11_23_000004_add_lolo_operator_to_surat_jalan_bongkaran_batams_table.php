<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_jalan_bongkaran_batams', function (Blueprint $table): void {
            $table->string('tipe_operator_lolo')->nullable()->after('menggunakan_lolo');
            $table->foreignId('operator_lolo_karyawan_id')->nullable()->after('tipe_operator_lolo')->constrained('karyawans')->nullOnDelete();
            $table->string('operator_lolo_vendor')->nullable()->after('operator_lolo_karyawan_id');
        });
    }

    public function down(): void
    {
        Schema::table('surat_jalan_bongkaran_batams', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('operator_lolo_karyawan_id');
            $table->dropColumn(['tipe_operator_lolo', 'operator_lolo_vendor']);
        });
    }
};
