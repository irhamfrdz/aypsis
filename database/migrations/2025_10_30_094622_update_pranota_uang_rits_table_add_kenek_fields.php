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
        Schema::table('pranota_uang_rits', function (Blueprint $table) {
            // Tambah field untuk kenek
            $table->string('kenek_nama')->nullable()->after('no_plat');
            
            // Pisahkan uang rit menjadi supir dan kenek
            $table->decimal('uang_rit_supir', 15, 2)->default(85000)->after('uang_jalan');
            $table->decimal('uang_rit_kenek', 15, 2)->default(50000)->after('uang_rit_supir');
            
            // Update total calculation (akan dihitung sebagai uang_jalan + uang_rit_supir + uang_rit_kenek)
            $table->decimal('total_rit', 15, 2)->default(135000)->after('uang_rit_kenek'); // 85000 + 50000
            
            // Rename uang_rit menjadi total_uang_rit untuk clarity
            $table->renameColumn('uang_rit', 'uang_rit_old');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_uang_rits', function (Blueprint $table) {
            $table->dropColumn(['kenek_nama', 'uang_rit_supir', 'uang_rit_kenek', 'total_rit']);
            $table->renameColumn('uang_rit_old', 'uang_rit');
        });
    }
};
