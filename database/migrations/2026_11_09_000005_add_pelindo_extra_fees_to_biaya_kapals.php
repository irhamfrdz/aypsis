<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_kapals', function (Blueprint $table) {
            $table->decimal('pelindo_biaya_admin_nota', 15, 2)->nullable();
            $table->decimal('pelindo_biaya_kebersihan', 15, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('biaya_kapals', function (Blueprint $table) {
            $table->dropColumn(['pelindo_biaya_admin_nota', 'pelindo_biaya_kebersihan']);
        });
    }
};
