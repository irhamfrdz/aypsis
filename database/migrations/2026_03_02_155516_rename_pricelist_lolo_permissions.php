<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('permissions')
            ->where('name', 'master-pricelist-lolo-read')
            ->update(['name' => 'master-pricelist-lolo-view', 'description' => 'View Pricelist LOLO']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('permissions')
            ->where('name', 'master-pricelist-lolo-view')
            ->update(['name' => 'master-pricelist-lolo-read', 'description' => 'View Pricelist LOLO']);
    }
};
