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
            $table->string('nomor_kontainer')->nullable()->after('nominal_uang_jalan');
            $table->string('ukuran')->nullable()->after('nomor_kontainer');
            $table->string('tipe_kontainer')->nullable()->after('ukuran');
            $table->string('vendor_item')->nullable()->after('tipe_kontainer');
            $table->string('kondisi')->nullable()->after('vendor_item');
            $table->text('catatan_kondisi')->nullable()->after('kondisi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalan_kontainer_sewas', function (Blueprint $table) {
            $table->dropColumn(['nomor_kontainer', 'ukuran', 'tipe_kontainer', 'vendor_item', 'kondisi', 'catatan_kondisi']);
        });
    }
};
