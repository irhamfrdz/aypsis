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
        Schema::table('bls', function (Blueprint $table) {
            if (!Schema::hasColumn('bls', 'supir_id')) {
                $table->foreignId('supir_id')->nullable()->constrained('karyawans')->after('supir_ob');
            }
            if (!Schema::hasColumn('bls', 'tanggal_ob')) {
                $table->timestamp('tanggal_ob')->nullable()->after('supir_id');
            }
            if (!Schema::hasColumn('bls', 'catatan_ob')) {
                $table->text('catatan_ob')->nullable()->after('tanggal_ob');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bls', function (Blueprint $table) {
            if (Schema::hasColumn('bls', 'supir_id')) {
                $table->dropForeign(['supir_id']);
                $table->dropColumn('supir_id');
            }
            if (Schema::hasColumn('bls', 'tanggal_ob')) {
                $table->dropColumn('tanggal_ob');
            }
            if (Schema::hasColumn('bls', 'catatan_ob')) {
                $table->dropColumn('catatan_ob');
            }
        });
    }
};
