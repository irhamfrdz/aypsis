<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('langsir_batams', function (Blueprint $table) {
            $table->string('tipe_operator_lolo', 10)->nullable();
            $table->string('operator_lolo_vendor')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('langsir_batams', fn (Blueprint $table) => $table->dropColumn(['tipe_operator_lolo', 'operator_lolo_vendor']));
    }
};
