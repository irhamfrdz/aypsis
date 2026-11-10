<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengisian_tanggal_dokumens', function (Blueprint $table) {
            $table->foreignId('cabang_id')->nullable()->constrained('cabangs')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pengisian_tanggal_dokumens', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cabang_id');
        });
    }
};
