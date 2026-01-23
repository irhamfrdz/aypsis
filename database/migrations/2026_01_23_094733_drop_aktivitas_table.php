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
        Schema::dropIfExists('aktivitas');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Table structure is unknown, so we cannot recreate it accurately.
    }
};
