<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('langsir_batams', function (Blueprint $table): void {
            $table->foreignId('operator_lolo_1_id')->nullable()->constrained('karyawans')->nullOnDelete();
            $table->foreignId('operator_lolo_2_id')->nullable()->constrained('karyawans')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('langsir_batams', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('operator_lolo_1_id');
            $table->dropConstrainedForeignId('operator_lolo_2_id');
        });
    }
};
