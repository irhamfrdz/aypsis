<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('master_kapals', function (Blueprint $table) {
            if (! Schema::hasColumn('master_kapals', 'deadweight_tonnage')) {
                $table->decimal('deadweight_tonnage', 12, 2)->nullable()->after('gross_tonnage')->comment('Deadweight tonnage kapal dalam ton');
            }
            if (! Schema::hasColumn('master_kapals', 'length_overall')) {
                $table->decimal('length_overall', 12, 2)->nullable()->after('deadweight_tonnage')->comment('Length overall kapal dalam meter');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_kapals', function (Blueprint $table) {
            $table->dropColumn(['deadweight_tonnage', 'length_overall']);
        });
    }
};
