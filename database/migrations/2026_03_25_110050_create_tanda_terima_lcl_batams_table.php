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
        Schema::create('tanda_terima_lcl_batams', function (Blueprint $table) {
            $table->id();
            
            // Basic Information
            $table->string('nomor_tanda_terima')->unique();
            $table->date('tanggal_tanda_terima');
            $table->string('no_surat_jalan_customer')->nullable();
            
            // Term Reference
            $table->foreignId('term_id')->constrained('terms')->onDelete('cascade');
            
            // Receiver Information  
            $table->string('nama_penerima');
            $table->string('pic_penerima')->nullable();
            $table->string('telepon_penerima')->nullable();
            $table->text('alamat_penerima');
            
            // Sender Information
            $table->string('nama_pengirim');
            $table->string('pic_pengirim')->nullable();
            $table->string('telepon_pengirim')->nullable();
            $table->text('alamat_pengirim');
            
            // Goods Information
            $table->string('nama_barang');
            $table->foreignId('jenis_barang_id')->constrained('jenis_barangs')->onDelete('cascade');
            $table->integer('kuantitas')->default(1);
            $table->text('keterangan_barang')->nullable();
            
            // Driver Information
            $table->string('supir');
            $table->string('no_plat');
            
            // Destination
            $table->foreignId('tujuan_pengiriman_id')->constrained('tujuan_kegiatan_utamas')->onDelete('cascade');
            
            // Container Type (always LCL for this table)
            $table->string('tipe_kontainer')->default('lcl');
            
            // Status
            $table->enum('status', ['draft', 'confirmed', 'delivered', 'cancelled'])->default('draft');
            
            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['nomor_tanda_terima']);
            $table->index(['tanggal_tanda_terima']);
            $table->index(['status']);
            $table->index(['tipe_kontainer']);
        });

        Schema::create('tanda_terima_lcl_batam_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tanda_terima_lcl_batam_id')
                  ->constrained('tanda_terima_lcl_batams', 'id', 'fk_tt_lcl_batam_idx')
                  ->onDelete('cascade');
            
            $table->integer('item_number')->default(1);
            $table->decimal('panjang', 10, 2)->nullable()->comment('Length in cm');
            $table->decimal('lebar', 10, 2)->nullable()->comment('Width in cm');  
            $table->decimal('tinggi', 10, 2)->nullable()->comment('Height in cm');
            $table->decimal('meter_kubik', 15, 6)->nullable()->comment('Volume in m³');
            $table->decimal('tonase', 10, 2)->nullable()->comment('Weight in tons');
            
            $table->timestamps();
            $table->index(['tanda_terima_lcl_batam_id'], 'idx_tt_lcl_batam_items');
            $table->index(['item_number']);
        });

        Schema::create('tt_lcl_batam_kontainer_pivot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tt_lcl_batam_id')
                  ->constrained('tanda_terima_lcl_batams', 'id', 'fk_tt_lcl_batam_pivot')
                  ->onDelete('cascade');
            
            $table->foreignId('kontainer_id')->nullable()->constrained('kontainers')->onDelete('cascade');
            
            $table->string('nomor_kontainer')->nullable();
            $table->string('nomor_seal')->nullable();
            $table->date('tanggal_seal')->nullable();
            
            $table->boolean('is_split')->default(false);
            $table->string('split_from_nomor')->nullable();
            $table->decimal('split_volume', 15, 6)->nullable();
            $table->decimal('split_tonase', 10, 2)->nullable();
            $table->text('split_keterangan')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            $table->index(['tt_lcl_batam_id'], 'idx_tt_lcl_batam_pivot');
            $table->index(['nomor_kontainer']);
            $table->index(['nomor_seal']);
            $table->index(['is_split']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tt_lcl_batam_kontainer_pivot');
        Schema::dropIfExists('tanda_terima_lcl_batam_items');
        Schema::dropIfExists('tanda_terima_lcl_batams');
    }
};
