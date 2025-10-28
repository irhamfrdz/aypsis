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
        Schema::table('prospek', function (Blueprint $table) {
            $table->unsignedBigInteger('tanda_terima_id')->nullable()->after('surat_jalan_id')->comment('ID tanda terima terkait');
            
            // Add foreign key constraint
            $table->foreign('tanda_terima_id')->references('id')->on('tanda_terimas')->onDelete('set null');
            
            // Add index for better performance
            $table->index('tanda_terima_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prospek', function (Blueprint $table) {
            $table->dropForeign(['tanda_terima_id']);
            $table->dropIndex(['tanda_terima_id']);
            $table->dropColumn('tanda_terima_id');
        });
    }
};
