<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_pricelist_ob', function (Blueprint $table) {
            $table->enum('status_service', ['service', 'non_service'])
                ->default('non_service')
                ->after('status_kontainer');
        });
    }

    public function down(): void
    {
        Schema::table('master_pricelist_ob', function (Blueprint $table) {
            $table->dropColumn('status_service');
        });
    }
};
