<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manifests', function (Blueprint $table) {
            $table->string('tonnage', 32)->nullable()->change();
        });

        // Existing DECIMAL(12,3) values keep their three-decimal representation.
        DB::table('manifests')->whereNotNull('tonnage')->orderBy('id')->chunkById(500, function ($manifests) {
            foreach ($manifests as $manifest) {
                DB::table('manifests')->where('id', $manifest->id)->update([
                    'tonnage' => number_format((float) $manifest->tonnage, 3, '.', ''),
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('manifests', function (Blueprint $table) {
            $table->decimal('tonnage', 12, 3)->nullable()->change();
        });
    }
};
