<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('approval_tanda_terima_2_goods', function (Blueprint $table) {
            $table->json('manifest_goods')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('approval_tanda_terima_2_goods', function (Blueprint $table) {
            $table->dropColumn('manifest_goods');
        });
    }
};
