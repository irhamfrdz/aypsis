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
        Schema::table('biaya_kapal_opp_opts', function (Blueprint $table) {
            $table->foreignId('manifest_id')->nullable()->after('pricelist_opp_opt_id')->constrained('manifests')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapal_opp_opts', function (Blueprint $table) {
            $table->dropForeign(['manifest_id']);
            $table->dropColumn('manifest_id');
        });
    }
};
