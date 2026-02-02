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
        Schema::create('master_pricelist_kanisir_bans', function (Blueprint $table) {
            $table->id();
            $table->string('vendor');
            $table->decimal('harga_1000_kawat', 15, 2)->default(0);
            $table->decimal('harga_1000_benang', 15, 2)->default(0);
            $table->decimal('harga_900_kawat', 15, 2)->default(0);
            $table->string('status')->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_pricelist_kanisir_bans');
    }
};
