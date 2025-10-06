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
        Schema::table('tipe_akuns', function (Blueprint $table) {
            $table->string('tipe_akun')->nullable()->after('id');
            $table->text('catatan')->nullable()->after('tipe_akun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipe_akuns', function (Blueprint $table) {
            $table->dropColumn(['tipe_akun', 'catatan']);
        });
    }
};
