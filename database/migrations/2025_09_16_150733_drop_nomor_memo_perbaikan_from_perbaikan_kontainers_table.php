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
        try {
            Schema::table('perbaikan_kontainers', function (Blueprint $table) {
                $table->dropUnique('perbaikan_kontainers_nomor_memo_perbaikan_unique');
            });
        } catch (\Exception $e) {
            // Ignore if index doesn't exist or fails
        }

        Schema::table('perbaikan_kontainers', function (Blueprint $table) {
            $table->dropColumn('nomor_memo_perbaikan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perbaikan_kontainers', function (Blueprint $table) {
            $table->string('nomor_memo_perbaikan')->nullable()->after('id');
        });
    }
};
