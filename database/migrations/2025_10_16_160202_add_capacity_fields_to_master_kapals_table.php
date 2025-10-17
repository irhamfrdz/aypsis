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
            // Check which column exists to determine where to place new columns
            $hasLokasi = Schema::hasColumn('master_kapals', 'lokasi');
            $hasPelayaran = Schema::hasColumn('master_kapals', 'pelayaran');
            $hasCatatan = Schema::hasColumn('master_kapals', 'catatan');
            
            // Only add columns if they don't already exist
            if (!Schema::hasColumn('master_kapals', 'kapasitas_kontainer_palka')) {
                if ($hasPelayaran) {
                    $table->integer('kapasitas_kontainer_palka')->nullable()->after('pelayaran')->comment('Kapasitas kontainer di palka kapal');
                } elseif ($hasLokasi) {
                    $table->integer('kapasitas_kontainer_palka')->nullable()->after('lokasi')->comment('Kapasitas kontainer di palka kapal');
                } elseif ($hasCatatan) {
                    $table->integer('kapasitas_kontainer_palka')->nullable()->after('catatan')->comment('Kapasitas kontainer di palka kapal');
                } else {
                    $table->integer('kapasitas_kontainer_palka')->nullable()->comment('Kapasitas kontainer di palka kapal');
                }
            }
            
            if (!Schema::hasColumn('master_kapals', 'kapasitas_kontainer_deck')) {
                $table->integer('kapasitas_kontainer_deck')->nullable()->after('kapasitas_kontainer_palka')->comment('Kapasitas kontainer di deck kapal');
            }
            
            if (!Schema::hasColumn('master_kapals', 'gross_tonnage')) {
                $table->decimal('gross_tonnage', 12, 2)->nullable()->after('kapasitas_kontainer_deck')->comment('Gross tonnage kapal dalam ton');
            }
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
