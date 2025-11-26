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
        Schema::table('surat_jalan_bongkarans', function (Blueprint $table) {
            // Add nama_kapal column if it doesn't exist
            if (!Schema::hasColumn('surat_jalan_bongkarans', 'nama_kapal')) {
                $table->string('nama_kapal')->nullable()->after('aktifitas');
            }
            
            // Drop kapal_id column if it exists
            if (Schema::hasColumn('surat_jalan_bongkarans', 'kapal_id')) {
                $table->dropColumn('kapal_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalan_bongkarans', function (Blueprint $table) {
            // Add back kapal_id
            if (!Schema::hasColumn('surat_jalan_bongkarans', 'kapal_id')) {
                $table->unsignedBigInteger('kapal_id')->nullable()->after('aktifitas');
            }
            
            // Drop nama_kapal
            if (Schema::hasColumn('surat_jalan_bongkarans', 'nama_kapal')) {
                $table->dropColumn('nama_kapal');
            }
        });
    }
};
