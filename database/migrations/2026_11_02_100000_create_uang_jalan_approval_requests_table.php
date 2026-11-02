<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uang_jalan_approval_requests', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_surat_jalan', 20);
            $table->unsignedBigInteger('surat_jalan_id');
            $table->string('nomor_surat_jalan')->nullable();
            $table->string('supir')->nullable();
            $table->unsignedInteger('jumlah_tugas_berjalan');
            $table->json('data_uang_jalan');
            $table->string('status', 20)->default('pending');
            $table->unsignedBigInteger('requested_by')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('catatan_approval')->nullable();
            $table->unsignedBigInteger('uang_jalan_id')->nullable();
            $table->timestamps();
            $table->index(['jenis_surat_jalan', 'surat_jalan_id', 'status'], 'uj_approval_target_status_idx');
            $table->index(['status', 'created_at'], 'uj_approval_status_date_idx');
        });

        DB::table('permissions')->insertOrIgnore([
            'name' => 'uang-jalan-approve',
            'description' => 'Menyetujui pengajuan uang jalan supir dengan tugas berjalan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('uang_jalan_approval_requests');
    }
};
