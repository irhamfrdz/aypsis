<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manifests', function (Blueprint $table) {
            $table->index('nomor_tanda_terima', 'manifests_nomor_tanda_terima_approval_idx');
        });
    }

    public function down(): void
    {
        Schema::table('manifests', function (Blueprint $table) {
            $table->dropIndex('manifests_nomor_tanda_terima_approval_idx');
        });
    }
};
