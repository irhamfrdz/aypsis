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
        Schema::create('biaya_kapal_opp_opt_manifest', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opp_opt_id')->constrained('biaya_kapal_opp_opts')->onDelete('cascade');
            $table->foreignId('manifest_id')->constrained('manifests')->onDelete('cascade');
            $table->timestamps();
        });

        // Migrate existing data if any
        DB::statement('INSERT INTO biaya_kapal_opp_opt_manifest (opp_opt_id, manifest_id, created_at, updated_at) SELECT id, manifest_id, NOW(), NOW() FROM biaya_kapal_opp_opts WHERE manifest_id IS NOT NULL');

        Schema::table('biaya_kapal_opp_opts', function (Blueprint $table) {
            $table->dropForeign(['manifest_id']); // if there is a foreign key
            $table->dropColumn('manifest_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapal_opp_opts', function (Blueprint $table) {
            $table->foreignId('manifest_id')->nullable()->after('pricelist_opp_opt_id')->constrained('manifests')->onDelete('set null');
        });

        // Try to recover data (only 1 per opp_opt)
        DB::statement('UPDATE biaya_kapal_opp_opts b JOIN (SELECT opp_opt_id, manifest_id FROM biaya_kapal_opp_opt_manifest GROUP BY opp_opt_id) AS p ON b.id = p.opp_opt_id SET b.manifest_id = p.manifest_id');

        Schema::dropIfExists('biaya_kapal_opp_opt_manifest');
    }
};
