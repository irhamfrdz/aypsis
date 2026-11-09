<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL can leave the table in place when a later statement in the
        // migration fails. Allow a retry to complete and be recorded.
        if (Schema::hasTable('pembayaran_pranota_vendor_supir_coa_items')) {
            $indexes = collect(Schema::getIndexes('pembayaran_pranota_vendor_supir_coa_items'));
            if (! $indexes->contains(fn (array $index) => $index['name'] === 'pvs_coa_payment_account_idx')) {
                Schema::table('pembayaran_pranota_vendor_supir_coa_items', function (Blueprint $table) {
                    $table->index(['pembayaran_id', 'akun_coa_id'], 'pvs_coa_payment_account_idx');
                });
            }

            return;
        }

        Schema::create('pembayaran_pranota_vendor_supir_coa_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_id')->constrained('pembayaran_pranota_vendor_supirs')->cascadeOnDelete();
            $table->foreignId('akun_coa_id')->constrained('akun_coa')->restrictOnDelete();
            $table->decimal('nominal', 15, 2);
            $table->string('memo', 500)->nullable();
            $table->timestamps();
            $table->index(['pembayaran_id', 'akun_coa_id'], 'pvs_coa_payment_account_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_pranota_vendor_supir_coa_items');
    }
};
