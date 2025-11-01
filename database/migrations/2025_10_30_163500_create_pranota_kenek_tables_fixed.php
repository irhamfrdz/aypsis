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
        // Skip users table - already exists
        
        // Check if surat_jalans table needs columns updated
        if (Schema::hasTable('surat_jalans')) {
            // Add missing columns if they don't exist
            Schema::table('surat_jalans', function (Blueprint $table) {
                if (!Schema::hasColumn('surat_jalans', 'uang_rit_kenek')) {
                    $table->decimal('uang_rit_kenek', 15, 2)->default(50000);
                }
                if (!Schema::hasColumn('surat_jalans', 'status_pembayaran_uang_rit_kenek')) {
                    $table->enum('status_pembayaran_uang_rit_kenek', ['belum_dibayar', 'sudah_masuk_pranota', 'sudah_dibayar'])->default('belum_dibayar');
                }
            });
        }

        // Create pranota_uang_keneks table (updated structure)
        if (!Schema::hasTable('pranota_uang_keneks')) {
            Schema::create('pranota_uang_keneks', function (Blueprint $table) {
                $table->id();
                $table->string('no_pranota')->unique();
                $table->date('tanggal');
                $table->integer('jumlah_surat_jalan')->default(0);
                $table->integer('jumlah_kenek')->default(0);
                $table->decimal('total_uang_kenek', 15, 2)->default(0);
                $table->decimal('total_hutang', 15, 2)->default(0);
                $table->decimal('total_tabungan', 15, 2)->default(0);
                $table->decimal('grand_total', 15, 2)->default(0);
                $table->decimal('total_uang', 15, 2)->default(0); // Keep for backward compatibility
                $table->text('keterangan')->nullable();
                $table->enum('status', ['draft', 'submitted', 'approved', 'paid', 'cancelled'])->default('draft');
                $table->date('tanggal_bayar')->nullable();
                $table->unsignedBigInteger('created_by');
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamps();

                // Foreign key constraints (only if users table exists)
                if (Schema::hasTable('users')) {
                    $table->foreign('created_by')->references('id')->on('users');
                    $table->foreign('updated_by')->references('id')->on('users');
                    $table->foreign('approved_by')->references('id')->on('users');
                }

                // Indexes
                $table->index('tanggal');
                $table->index('status');
                $table->index('created_by');
            });
        }

        // Create pranota_uang_kenek_details table
        if (!Schema::hasTable('pranota_uang_kenek_details')) {
            Schema::create('pranota_uang_kenek_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pranota_uang_kenek_id');
                $table->unsignedBigInteger('surat_jalan_id');
                $table->string('no_surat_jalan');
                $table->string('supir_nama')->nullable();
                $table->string('kenek_nama');
                $table->string('no_plat');
                $table->decimal('uang_rit_kenek', 15, 2)->default(50000);
                $table->timestamps();

                // Foreign key constraints
                $table->foreign('pranota_uang_kenek_id')->references('id')->on('pranota_uang_keneks')->onDelete('cascade');
                $table->foreign('surat_jalan_id')->references('id')->on('surat_jalans')->onDelete('cascade');

                // Indexes
                $table->index('pranota_uang_kenek_id');
                $table->index('surat_jalan_id');
                $table->index('kenek_nama');
            });
        }

        // Create pranota_uang_kenek_summary table
        if (!Schema::hasTable('pranota_uang_kenek_summary')) {
            Schema::create('pranota_uang_kenek_summary', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pranota_uang_kenek_id');
                $table->string('kenek_nama');
                $table->integer('jumlah_surat_jalan')->default(0);
                $table->decimal('total_uang_kenek', 15, 2)->default(0);
                $table->decimal('hutang', 15, 2)->default(0);
                $table->decimal('tabungan', 15, 2)->default(0);
                $table->decimal('grand_total_kenek', 15, 2)->default(0);
                $table->timestamps();

                // Foreign key constraints
                $table->foreign('pranota_uang_kenek_id')->references('id')->on('pranota_uang_keneks')->onDelete('cascade');

                // Indexes
                $table->index('pranota_uang_kenek_id');
                $table->index('kenek_nama');
                
                // Unique constraint to prevent duplicate kenek in same pranota
                $table->unique(['pranota_uang_kenek_id', 'kenek_nama'], 'unique_pranota_kenek');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_uang_kenek_summary');
        Schema::dropIfExists('pranota_uang_kenek_details');
        Schema::dropIfExists('pranota_uang_keneks');
    }
};