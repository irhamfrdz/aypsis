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
        Schema::table('tanda_terimas_lcl', function (Blueprint $table) {
            // Cek apakah kolom sudah ada sebelum menambahkannya
            if (!Schema::hasColumn('tanda_terimas_lcl', 'nomor_kontainer')) {
                $table->string('nomor_kontainer')->nullable()->after('tujuan_pengiriman_id');
            }
            
            if (!Schema::hasColumn('tanda_terimas_lcl', 'size_kontainer')) {
                $table->string('size_kontainer')->nullable()->after('nomor_kontainer');
            }
            
            if (!Schema::hasColumn('tanda_terimas_lcl', 'tipe_kontainer')) {
                $table->string('tipe_kontainer')->nullable()->after('size_kontainer');
            }
            
            if (!Schema::hasColumn('tanda_terimas_lcl', 'nomor_seal')) {
                $table->string('nomor_seal')->nullable()->after('tipe_kontainer');
            }
            
            if (!Schema::hasColumn('tanda_terimas_lcl', 'tanggal_seal')) {
                $table->date('tanggal_seal')->nullable()->after('nomor_seal');
            }
            
            // Add index for better query performance when grouping by container
            if (!Schema::hasColumn('tanda_terimas_lcl', 'nomor_kontainer')) {
                $table->index('nomor_kontainer', 'idx_nomor_kontainer');
            }
            
            // Add composite index for container grouping queries
            $table->index(['nomor_kontainer', 'tanggal_tanda_terima'], 'idx_kontainer_tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tanda_terimas_lcl', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_kontainer_tanggal');
            $table->dropIndex('idx_nomor_kontainer');
            
            // Drop columns if they exist
            if (Schema::hasColumn('tanda_terimas_lcl', 'tanggal_seal')) {
                $table->dropColumn('tanggal_seal');
            }
            
            if (Schema::hasColumn('tanda_terimas_lcl', 'nomor_seal')) {
                $table->dropColumn('nomor_seal');
            }
            
            if (Schema::hasColumn('tanda_terimas_lcl', 'tipe_kontainer')) {
                $table->dropColumn('tipe_kontainer');
            }
            
            if (Schema::hasColumn('tanda_terimas_lcl', 'size_kontainer')) {
                $table->dropColumn('size_kontainer');
            }
            
            if (Schema::hasColumn('tanda_terimas_lcl', 'nomor_kontainer')) {
                $table->dropColumn('nomor_kontainer');
            }
        });
    }
};
