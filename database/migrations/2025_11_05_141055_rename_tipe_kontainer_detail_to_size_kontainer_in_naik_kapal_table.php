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
        Schema::table('naik_kapal', function (Blueprint $table) {
            // Rename column from tipe_kontainer_detail to size_kontainer
            $table->renameColumn('tipe_kontainer_detail', 'size_kontainer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('naik_kapal', function (Blueprint $table) {
            // Rename back from size_kontainer to tipe_kontainer_detail
            $table->renameColumn('size_kontainer', 'tipe_kontainer_detail');
        });
    }
};
