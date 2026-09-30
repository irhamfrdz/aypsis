<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('langsir_batams', function (Blueprint $table) {
            $table->string('sumber_chasis', 3)->nullable()->after('no_plat');
            $table->foreignId('chasis_mobil_id')->nullable()->after('sumber_chasis')->constrained('mobils')->nullOnDelete();
            $table->string('no_chasis')->nullable()->after('chasis_mobil_id');
        });
    }

    public function down(): void
    {
        Schema::table('langsir_batams', function (Blueprint $table) {
            $table->dropConstrainedForeignId('chasis_mobil_id');
            $table->dropColumn(['sumber_chasis', 'no_chasis']);
        });
    }
};
