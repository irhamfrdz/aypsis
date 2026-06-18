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
        Schema::table('manifests', function (Blueprint $table) {
            $table->decimal('tonnage_perincian', 12, 3)->nullable()->after('tonnage');
            $table->decimal('volume_perincian', 12, 3)->nullable()->after('volume');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manifests', function (Blueprint $table) {
            $table->dropColumn(['tonnage_perincian', 'volume_perincian']);
        });
    }
};
