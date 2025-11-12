<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PricelistUangJalanBatam;

class UpdatePricelistTarifBaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update tarif_base untuk semua pricelist yang belum memiliki tarif_base
        $pricelists = PricelistUangJalanBatam::whereNull('tarif_base')->get();
        
        foreach ($pricelists as $pricelist) {
            $pricelist->update(['tarif_base' => $pricelist->tarif]);
        }
        
        $this->command->info("Updated {$pricelists->count()} pricelist records with base tarif.");
    }
}
