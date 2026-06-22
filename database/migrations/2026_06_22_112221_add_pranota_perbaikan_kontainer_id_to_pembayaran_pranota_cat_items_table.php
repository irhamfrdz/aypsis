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
        if (Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            try {
                Schema::table('pembayaran_pranota_cat_items', function (Blueprint $table) {
                    $table->dropForeign('pembayaran_pranota_cat_items_pranota_tagihan_cat_id_foreign');
                });
            } catch (\Exception $e) {}

            try {
                Schema::table('pembayaran_pranota_cat_items', function (Blueprint $table) {
                    $table->dropUnique('pp_cat_items_unique');
                });
            } catch (\Exception $e) {}
            
            // Clean up from previous failed run if column exists
            if (Schema::hasColumn('pembayaran_pranota_cat_items', 'pranota_perbaikan_kontainer_id')) {
                try {
                    Illuminate\Support\Facades\DB::statement('ALTER TABLE pembayaran_pranota_cat_items DROP FOREIGN KEY pp_cat_items_pranota_perbaikan_foreign');
                } catch (\Exception $e) {}
                Schema::table('pembayaran_pranota_cat_items', function (Blueprint $table) {
                    $table->dropColumn('pranota_perbaikan_kontainer_id');
                });
            }
        }

        Schema::table('pembayaran_pranota_cat_items', function (Blueprint $table) {
            $table->foreignId('pranota_tagihan_cat_id')->nullable()->change();
            
            if (Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
                $table->foreign('pranota_tagihan_cat_id', 'pembayaran_pranota_cat_items_pranota_tagihan_cat_id_foreign')
                    ->references('id')
                    ->on('pranota_tagihan_cat')
                    ->onDelete('cascade');
            }
            
            $table->unsignedBigInteger('pranota_perbaikan_kontainer_id')->nullable()->after('pranota_tagihan_cat_id');
            $table->foreign('pranota_perbaikan_kontainer_id', 'pp_cat_items_pranota_perbaikan_foreign')
                ->references('id')
                ->on('pranota_perbaikan_kontainers')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_pranota_cat_items', function (Blueprint $table) {
            $table->dropForeign('pp_cat_items_pranota_perbaikan_foreign');
            $table->dropColumn('pranota_perbaikan_kontainer_id');
            $table->foreignId('pranota_tagihan_cat_id')->nullable(false)->change();
            
            if (Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
                $table->unique(['pembayaran_pranota_cat_id', 'pranota_tagihan_cat_id'], 'pp_cat_items_unique');
            }
        });
    }
};
