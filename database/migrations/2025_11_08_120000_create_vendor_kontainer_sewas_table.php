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
        // Check if table already exists before creating
        if (!Schema::hasTable('vendor_kontainer_sewas')) {
            Schema::create('vendor_kontainer_sewas', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('npwp')->nullable();
                $table->decimal('tax_ppn_percent', 5, 2)->default(11.00);
                $table->decimal('tax_pph_percent', 5, 2)->default(2.00);
                $table->timestamps();

            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_kontainer_sewas');
    }
};