<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('beritas', function (Blueprint $table) {
            if (! Schema::hasColumn('beritas', 'aspect_ratio')) {
                $table->string('aspect_ratio', 20)->default('2.2:1')->after('gambar')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('beritas', function (Blueprint $table) {
            if (Schema::hasColumn('beritas', 'aspect_ratio')) {
                $table->dropColumn('aspect_ratio');
            }
        });
    }
};
