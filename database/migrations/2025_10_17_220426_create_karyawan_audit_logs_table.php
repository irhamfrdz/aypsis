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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // User yang melakukan aksi
            $table->string('user_name')->nullable(); // Nama user (backup jika user dihapus)

            // Polymorphic relationship - bisa untuk model apapun
            $table->string('auditable_type'); // App\Models\Karyawan, App\Models\User, dll
            $table->unsignedBigInteger('auditable_id'); // ID dari model yang diaudit

            $table->string('action'); // 'created', 'updated', 'deleted', 'viewed'
            $table->string('module')->nullable(); // 'karyawan', 'user', 'divisi', 'pricelist', dll
            $table->string('description')->nullable(); // Deskripsi aksi yang dilakukan

            // Data perubahan
            $table->longText('old_values')->nullable(); // Data sebelum perubahan (JSON)
            $table->longText('new_values')->nullable(); // Data setelah perubahan (JSON)

            // Tracking info tambahan
            $table->string('ip_address', 45)->nullable(); // IP address user
            $table->text('user_agent')->nullable(); // Browser/device info
            $table->string('url')->nullable(); // URL yang diakses

            // Timestamps
            $table->timestamp('created_at')->useCurrent();

            // Indexes untuk performance
            $table->index(['auditable_type', 'auditable_id']);
            $table->index(['user_id', 'created_at']);
            $table->index(['module', 'action']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
