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
        Schema::table('surat_jalan_kontainer_sewas', function (Blueprint $table) {
            $table->dropColumn(['kondisi', 'catatan_kondisi']);
        });

        Schema::table('surat_jalan_kontainer_sewa_items', function (Blueprint $table) {
            $table->dropColumn(['kondisi', 'catatan_kondisi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalan_kontainer_sewas', function (Blueprint $table) {
            $table->string('kondisi')->nullable()->after('vendor_item');
            $table->text('catatan_kondisi')->nullable()->after('kondisi');
        });

        Schema::table('surat_jalan_kontainer_sewa_items', function (Blueprint $table) {
            $table->enum('kondisi', ['baik', 'rusak_ringan', 'rusak_berat'])->default('baik')->after('vendor');
            $table->text('catatan_kondisi')->nullable()->after('kondisi');
        });
    }
};
