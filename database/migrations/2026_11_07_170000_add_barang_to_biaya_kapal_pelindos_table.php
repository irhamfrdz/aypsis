<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_kapal_pelindos', function (Blueprint $table) {
            $table->string('status_kontainer')->nullable()->after('no_voyage');
            $table->string('ukuran', 2)->nullable()->after('status_kontainer');
        });
    }

    public function down(): void
    {
        Schema::table('biaya_kapal_pelindos', function (Blueprint $table) {
            $table->dropColumn(['status_kontainer', 'ukuran']);
        });
    }
};
