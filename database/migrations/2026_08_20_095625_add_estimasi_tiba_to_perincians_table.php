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
        Schema::table('perincians', function (Blueprint $table) {
            $table->date('estimasi_tiba')->nullable()->after('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perincians', function (Blueprint $table) {
            $table->dropColumn('estimasi_tiba');
        });
    }
};
