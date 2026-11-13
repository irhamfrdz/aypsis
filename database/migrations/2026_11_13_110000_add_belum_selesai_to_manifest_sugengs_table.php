<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manifest_sugengs', function (Blueprint $table) {
            $table->boolean('belum_selesai')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('manifest_sugengs', function (Blueprint $table) {
            $table->dropColumn('belum_selesai');
        });
    }
};
