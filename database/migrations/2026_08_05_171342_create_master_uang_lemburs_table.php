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
        if (!Schema::hasTable('master_uang_lemburs')) {
            Schema::create('master_uang_lemburs', function (Blueprint $table) {
                $table->id();
                $table->string('group')->default('Lembur');
                $table->string('sub_group');
                $table->timestamps();
            });
        }

        if (Schema::hasTable('uang_lembur_rules')) {
            try {
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE uang_lembur_rules DROP FOREIGN KEY uang_lembur_rules_uang_lembur_id_foreign');
            } catch (\Exception $e) {
                // Ignore if foreign key doesn't exist
            }

            try {
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE uang_lembur_rules ADD CONSTRAINT uang_lembur_rules_uang_lembur_id_foreign FOREIGN KEY (uang_lembur_id) REFERENCES master_uang_lemburs(id) ON DELETE CASCADE');
            } catch (\Exception $e) {
                // Ignore if constraint already exists
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_uang_lemburs');
    }
};
