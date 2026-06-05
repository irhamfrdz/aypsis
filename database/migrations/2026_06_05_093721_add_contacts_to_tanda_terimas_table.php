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
        Schema::table('tanda_terimas', function (Blueprint $table) {
            $table->string('kontak_pengirim')->nullable()->after('alamat_pengirim');
            $table->string('kontak_penerima')->nullable()->after('alamat_penerima');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tanda_terimas', function (Blueprint $table) {
            $table->dropColumn(['kontak_pengirim', 'kontak_penerima']);
        });
    }
};
