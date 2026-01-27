<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\NamaStockBan;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure the model knows the table even during migration if needed, 
        // but here we just use Eloquent or DB to insert.
        // Check if 'Velg' exists, if not create it.
        if (!NamaStockBan::where('nama', 'Velg')->exists()) {
            NamaStockBan::create([
                'nama' => 'Velg',
                'status' => 'active' // Assuming 'active' is default or required, checking usage
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optional: delete matches
        // NamaStockBan::where('nama', 'Velg')->delete(); 
        // Better not to delete in down() to avoid data loss if manually added
    }
};
