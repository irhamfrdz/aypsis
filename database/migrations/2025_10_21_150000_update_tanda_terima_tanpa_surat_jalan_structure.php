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
        Schema::table('tanda_terima_tanpa_surat_jalan', function (Blueprint $table) {
            // Add missing fields only if they don't exist
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'nama_barang')) {
                $table->string('nama_barang')->nullable()->after('jenis_barang');
            }
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'telepon')) {
                $table->string('telepon', 50)->nullable()->after('pengirim');
            }
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'pic')) {
                $table->string('pic')->nullable()->after('telepon');
            }
        });

        // Remove fields that are no longer needed (check if they exist first)
        Schema::table('tanda_terima_tanpa_surat_jalan', function (Blueprint $table) {
            $columnsToRemove = [];

            if (Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'tujuan_pengambilan')) {
                $columnsToRemove[] = 'tujuan_pengambilan';
            }
            if (Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'supir')) {
                $columnsToRemove[] = 'supir';
            }
            if (Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'no_plat')) {
                $columnsToRemove[] = 'no_plat';
            }

            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
        });

        // Create table for dimensi items
        if (!Schema::hasTable('tanda_terima_dimensi_items')) {
            Schema::create('tanda_terima_dimensi_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tanda_terima_tanpa_surat_jalan_id');
                $table->decimal('panjang', 8, 2)->nullable();
                $table->decimal('lebar', 8, 2)->nullable();
                $table->decimal('tinggi', 8, 2)->nullable();
                $table->decimal('meter_kubik', 12, 6)->nullable();
                $table->decimal('tonase', 8, 2)->nullable();
                $table->integer('item_order')->default(0);
                $table->timestamps();

                // Custom foreign key with shorter name
                $table->foreign('tanda_terima_tanpa_surat_jalan_id', 'tttsj_dimensi_fk')
                      ->references('id')
                      ->on('tanda_terima_tanpa_surat_jalan')
                      ->onDelete('cascade');

                $table->index(['tanda_terima_tanpa_surat_jalan_id', 'item_order'], 'tttsj_dimensi_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanda_terima_dimensi_items');

        Schema::table('tanda_terima_tanpa_surat_jalan', function (Blueprint $table) {
            // Restore removed fields
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'tujuan_pengambilan')) {
                $table->string('tujuan_pengambilan')->nullable()->after('tujuan_pengiriman');
            }
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'supir')) {
                $table->string('supir')->nullable();
            }
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'no_plat')) {
                $table->string('no_plat')->nullable();
            }

            // Remove added fields
            $columnsToRemove = [];
            if (Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'nama_barang')) {
                $columnsToRemove[] = 'nama_barang';
            }
            if (Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'telepon')) {
                $columnsToRemove[] = 'telepon';
            }
            if (Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'pic')) {
                $columnsToRemove[] = 'pic';
            }

            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
        });
    }
};
