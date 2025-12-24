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
        Schema::create('master_pelayanan_pelabuhans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelayanan');
            $table->text('deskripsi')->nullable();
            $table->decimal('biaya', 15, 2)->nullable();
            $table->string('satuan')->nullable(); // per kontainer, per ton, per unit, dll
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_pelayanan_pelabuhans');
    }
};
