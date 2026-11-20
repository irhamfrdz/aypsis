<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_kartu_bensin_batam_histories', function (Blueprint $table) {
            $table->foreignId('penerima_id')->nullable()->constrained('karyawans')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('master_kartu_bensin_batam_histories', function (Blueprint $table) {
            $table->dropConstrainedForeignId('penerima_id');
        });
    }
};
