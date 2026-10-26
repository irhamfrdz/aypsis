<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tanda_terimas', function (Blueprint $table) {
            $table->string('approval_pelabuhan_status')->default('pending');
            $table->index('approval_pelabuhan_status');
        });
    }

    public function down(): void
    {
        Schema::table('tanda_terimas', function (Blueprint $table) {
            $table->dropIndex(['approval_pelabuhan_status']);
            $table->dropColumn('approval_pelabuhan_status');
        });
    }
};
