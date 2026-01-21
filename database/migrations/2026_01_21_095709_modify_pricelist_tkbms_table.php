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
        Schema::table('pricelist_tkbms', function (Blueprint $table) {
            // Rename cargo to nama_barang if it exists, otherwise create it
            if (Schema::hasColumn('pricelist_tkbms', 'cargo')) {
                $table->renameColumn('cargo', 'nama_barang');
            } else {
                $table->string('nama_barang')->after('id')->nullable();
            }

            // Add new tarif column
            $table->decimal('tarif', 15, 2)->default(0)->after('status');

            // Drop old columns
            $table->dropColumn(['tarif_20f', 'tarif_40f', 'tarif_20m', 'tarif_40m', 'tuslag']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pricelist_tkbms', function (Blueprint $table) {
            $table->renameColumn('nama_barang', 'cargo');
            $table->dropColumn('tarif');
            $table->decimal('tarif_20f', 15, 2)->default(0);
            $table->decimal('tarif_40f', 15, 2)->default(0);
            $table->decimal('tarif_20m', 15, 2)->default(0);
            $table->decimal('tarif_40m', 15, 2)->default(0);
            $table->decimal('tuslag', 15, 2)->default(0);
        });
    }
};
