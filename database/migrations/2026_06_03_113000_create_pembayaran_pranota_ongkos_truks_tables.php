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
        // 1. Add status_pembayaran to parent table if not exists
        if (! Schema::hasColumn('pranota_ongkos_truks', 'status_pembayaran')) {
            Schema::table('pranota_ongkos_truks', function (Blueprint $table) {
                $table->string('status_pembayaran')->default('unpaid')->after('status');
            });
        }

        // 2. Create pembayaran_pranota_ongkos_truks table
        Schema::create('pembayaran_pranota_ongkos_truks', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pembayaran')->unique();
            $table->string('nomor_accurate')->nullable();
            $table->string('nomor_cetakan')->nullable();
            $table->date('tanggal_pembayaran');
            $table->string('bank')->nullable();
            $table->enum('jenis_transaksi', ['cash', 'transfer', 'check', 'giro'])->default('cash');
            $table->decimal('total_pembayaran', 15, 2);
            $table->decimal('total_tagihan_penyesuaian', 15, 2)->default(0);
            $table->decimal('total_tagihan_setelah_penyesuaian', 15, 2);
            $table->text('alasan_penyesuaian')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status_pembayaran', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->string('bukti_pembayaran')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['status_pembayaran', 'tanggal_pembayaran'], 'idx_pot_status_tanggal');
        });

        // 3. Create pembayaran_pranota_ongkos_truk_items table (pivot)
        Schema::create('pembayaran_pranota_ongkos_truk_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pembayaran_pranota_ongkos_truk_id');
            $table->unsignedBigInteger('pranota_ongkos_truk_id');
            $table->decimal('subtotal', 15, 2)->comment('Subtotal untuk pranota ini');
            $table->timestamps();

            // Foreign keys
            $table->foreign('pembayaran_pranota_ongkos_truk_id', 'fk_ppot_items_pembayaran')
                ->references('id')
                ->on('pembayaran_pranota_ongkos_truks')
                ->onDelete('cascade');

            $table->foreign('pranota_ongkos_truk_id', 'fk_ppot_items_pranota')
                ->references('id')
                ->on('pranota_ongkos_truks')
                ->onDelete('cascade');

            // Unique constraint
            $table->unique(['pembayaran_pranota_ongkos_truk_id', 'pranota_ongkos_truk_id'], 'unique_pembayaran_pot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_pranota_ongkos_truk_items');
        Schema::dropIfExists('pembayaran_pranota_ongkos_truks');

        if (Schema::hasColumn('pranota_ongkos_truks', 'status_pembayaran')) {
            Schema::table('pranota_ongkos_truks', function (Blueprint $table) {
                $table->dropColumn('status_pembayaran');
            });
        }
    }
};
