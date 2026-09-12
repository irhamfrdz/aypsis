<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_kapal_trucking', function (Blueprint $table) {
            $table->decimal('adjustment', 15, 2)->default(0)->after('pph');
            $table->text('notes_adjustment')->nullable()->after('adjustment');
        });
    }

    public function down(): void
    {
        Schema::table('biaya_kapal_trucking', function (Blueprint $table) {
            $table->dropColumn(['adjustment', 'notes_adjustment']);
        });
    }
};
