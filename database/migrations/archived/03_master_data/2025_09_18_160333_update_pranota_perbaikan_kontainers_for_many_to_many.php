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
        Schema::table('pranota_perbaikan_kontainers', function (Blueprint $table) {
            // Drop the old foreign key column since we're moving to many-to-many
            $table->dropForeign(['perbaikan_kontainer_id']);
            $table->dropColumn('perbaikan_kontainer_id');
        });
    }

    public function down(): void
    {
        Schema::table('pranota_perbaikan_kontainers', function (Blueprint $table) {
            // Restore the column if rolling back
            $table->foreignId('perbaikan_kontainer_id')->constrained('perbaikan_kontainers')->onDelete('cascade');
        });
    }
};
