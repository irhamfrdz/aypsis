<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_kas_pinangs', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_invoice')->unique();
            $table->date('tanggal_transaksi');
            $table->string('arus_kas', 10);
            $table->foreignId('akun_kas_id')->constrained('akun_coa');
            $table->foreignId('akun_lawan_id')->constrained('akun_coa');
            $table->string('pihak_terkait')->nullable();
            $table->string('referensi')->nullable();
            $table->decimal('total', 15, 2)->default(0);
            $table->string('status', 20)->default('draft');
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['tanggal_transaksi', 'status']);
            $table->index('pihak_terkait');
        });

        Schema::create('invoice_kas_pinang_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_kas_pinang_id')->constrained('invoice_kas_pinangs')->cascadeOnDelete();
            $table->string('uraian');
            $table->decimal('jumlah', 12, 2);
            $table->string('satuan', 50)->nullable();
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });

        foreach (['view', 'create', 'update', 'delete'] as $action) {
            DB::table('permissions')->updateOrInsert(
                ['name' => "invoice-kas-pinang-{$action}"],
                ['description' => ucfirst($action).' Invoice Kas Pinang', 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    public function down(): void
    {
        DB::table('permissions')->whereIn('name', array_map(
            fn ($action) => "invoice-kas-pinang-{$action}",
            ['view', 'create', 'update', 'delete']
        ))->delete();
        Schema::dropIfExists('invoice_kas_pinang_items');
        Schema::dropIfExists('invoice_kas_pinangs');
    }
};
