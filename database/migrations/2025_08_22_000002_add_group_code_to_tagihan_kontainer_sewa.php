<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'group_code')) {
                $table->string('group_code')->nullable()->index()->after('vendor');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
            if (Schema::hasColumn('tagihan_kontainer_sewa', 'group_code')) {
                $table->dropColumn('group_code');
            }
        });
    }
};
