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
        Schema::table('surat_jalan_bongkarans', function (Blueprint $table) {
            $table->unsignedBigInteger('manifest_id')->nullable()->after('id');
            $table->foreign('manifest_id')->references('id')->on('manifests')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalan_bongkarans', function (Blueprint $table) {
            $table->dropForeign(['manifest_id']);
            $table->dropColumn('manifest_id');
        });
    }
};
