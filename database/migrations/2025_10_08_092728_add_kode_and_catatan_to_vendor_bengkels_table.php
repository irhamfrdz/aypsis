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
        Schema::table('vendor_bengkel', function (Blueprint $table) {
            $table->string('kode')->nullable()->after('id')->index();
            $table->text('catatan')->nullable()->after('keterangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_bengkel', function (Blueprint $table) {
            $table->dropColumn(['kode', 'catatan']);
        });
    }
};
