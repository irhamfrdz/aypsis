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
        Schema::create('invoices_kontainer_sewa', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_invoice')->unique();
            $table->date('tanggal_invoice');
            $table->string('vendor_name')->nullable(); // Nama vendor
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('ppn', 15, 2)->default(0);
            $table->decimal('pph', 15, 2)->default(0);
            $table->decimal('adjustment', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->enum('status', ['draft', 'submitted', 'approved', 'paid', 'cancelled'])->default('draft');
            $table->text('keterangan')->nullable();
            $table->text('catatan')->nullable();
            $table->string('file_attachment')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('nomor_invoice');
            $table->index('tanggal_invoice');
            $table->index('status');
            $table->index('created_by');
        });

        // Pivot table untuk relasi many-to-many antara invoice dan tagihan kontainer
        Schema::create('invoice_kontainer_sewa_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices_kontainer_sewa')->onDelete('cascade');
            $table->foreignId('tagihan_id')->constrained('daftar_tagihan_kontainer_sewa')->onDelete('cascade');
            $table->decimal('jumlah', 15, 2); // Nilai tagihan saat ditambahkan ke invoice
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('invoice_id');
            $table->index('tagihan_id');
            
            // Unique constraint: satu tagihan hanya bisa masuk ke satu invoice
            $table->unique('tagihan_id');
        });

        // Update tabel daftar_tagihan_kontainer_sewa untuk menambah kolom invoice_id
        Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
            $table->foreignId('invoice_id')->nullable()->after('status_pranota')->constrained('invoices_kontainer_sewa')->onDelete('set null');
            $table->index('invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key constraint first
        Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
            $table->dropColumn('invoice_id');
        });
        
        Schema::dropIfExists('invoice_kontainer_sewa_items');
        Schema::dropIfExists('invoices_kontainer_sewa');
    }
};
