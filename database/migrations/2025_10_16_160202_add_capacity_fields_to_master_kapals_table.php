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
        Schema::table('master_kapals', function (Blueprint $table) {
            $table->integer('kapasitas_kontainer_palka')->nullable()->after('pelayaran')->comment('Kapasitas kontainer di palka kapal');
            $table->integer('kapasitas_kontainer_deck')->nullable()->after('kapasitas_kontainer_palka')->comment('Kapasitas kontainer di deck kapal');
            $table->decimal('gross_tonnage', 12, 2)->nullable()->after('kapasitas_kontainer_deck')->comment('Gross tonnage kapal dalam ton');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_kapals', function (Blueprint $table) {
            $table->dropColumn(['kapasitas_kontainer_palka', 'kapasitas_kontainer_deck', 'gross_tonnage']);
        });
    }
};
