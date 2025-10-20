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
        // Drop foreign key dan kolom service_id dari tabel gate_ins
        Schema::table('gate_ins', function (Blueprint $table) {
            if (Schema::hasColumn('gate_ins', 'service_id')) {
                $table->dropForeign(['service_id']);
                $table->dropColumn('service_id');
            }
        });

        // Drop foreign key dan kolom service_id dari tabel kontainers
        Schema::table('kontainers', function (Blueprint $table) {
            if (Schema::hasColumn('kontainers', 'service_id')) {
                $table->dropForeign(['service_id']);
                $table->dropColumn('service_id');
            }
        });

        // Drop tabel master_services jika ada
        Schema::dropIfExists('master_services');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate master_services table
        Schema::create('master_services', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique()->comment('Kode service unik');
            $table->string('nama_service')->comment('Nama service');
            $table->text('deskripsi')->nullable()->comment('Deskripsi service');
            $table->string('status')->default('aktif')->comment('Status aktif/non-aktif');
            $table->timestamps();
            $table->softDeletes();
        });

        // Add service_id back to gate_ins
        Schema::table('gate_ins', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->constrained('master_services')->onDelete('set null');
        });

        // Add service_id back to kontainers
        Schema::table('kontainers', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->constrained('master_services')->onDelete('set null');
        });
    }
};
