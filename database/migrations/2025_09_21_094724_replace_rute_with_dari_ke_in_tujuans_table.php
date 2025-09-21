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
        Schema::table('tujuans', function (Blueprint $table) {
            // Add new columns
            $table->string('dari')->nullable()->after('wilayah');
            $table->string('ke')->nullable()->after('dari');
        });

        // Copy data from rute to dari and ke if needed
        // You might need to parse the rute data here if it contains both origin and destination

        Schema::table('tujuans', function (Blueprint $table) {
            // Drop the old rute column
            $table->dropColumn('rute');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tujuans', function (Blueprint $table) {
            // Add back the rute column
            $table->string('rute')->nullable()->after('wilayah');
        });

        // You might need to reconstruct rute data from dari and ke here

        Schema::table('tujuans', function (Blueprint $table) {
            // Drop the new columns
            $table->dropColumn(['dari', 'ke']);
        });
    }
};
