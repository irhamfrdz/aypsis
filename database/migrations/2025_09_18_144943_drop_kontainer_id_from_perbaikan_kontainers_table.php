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
                $table->dropIndex('perbaikan_kontainers_kontainer_id_status_perbaikan_index');
            });
        } catch (\Exception $e) {
            // Ignore if index doesn't exist or fails
        }

        Schema::table('perbaikan_kontainers', function (Blueprint $table) {
            try {
                $table->dropForeign(['kontainer_id']);
            } catch (\Exception $e) {
                // Ignore if foreign key doesn't exist
            }
            $table->dropColumn('kontainer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perbaikan_kontainers', function (Blueprint $table) {
            $table->foreignId('kontainer_id')->constrained('kontainers')->onDelete('cascade');
        });
    }
};
