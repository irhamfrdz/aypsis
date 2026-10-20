<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = 'pembayaran_pranota_stock_akun_items';

        if (! Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->foreignId('pembayaran_pranota_stock_id')
                    ->constrained('pembayaran_pranota_stocks', 'id', 'fk_ppsa_payment')->cascadeOnDelete();
                $table->foreignId('akun_coa_id')
                    ->constrained('akun_coa', 'id', 'fk_ppsa_coa')->restrictOnDelete();
                $table->decimal('nominal', 15, 2);
                $table->string('keterangan')->nullable();
                $table->timestamps();
            });

            return;
        }

        // MySQL can leave the table behind when CREATE TABLE succeeds but adding a foreign key fails.
        $foreignKeys = Schema::getForeignKeys($tableName);
        $hasPaymentKey = collect($foreignKeys)->contains(fn ($key) => in_array('pembayaran_pranota_stock_id', $key['columns']));
        $hasCoaKey = collect($foreignKeys)->contains(fn ($key) => in_array('akun_coa_id', $key['columns']));

        Schema::table($tableName, function (Blueprint $table) use ($hasPaymentKey, $hasCoaKey) {
            if (! $hasPaymentKey) {
                $table->foreign('pembayaran_pranota_stock_id', 'fk_ppsa_payment')
                    ->references('id')->on('pembayaran_pranota_stocks')->cascadeOnDelete();
            }
            if (! $hasCoaKey) {
                $table->foreign('akun_coa_id', 'fk_ppsa_coa')
                    ->references('id')->on('akun_coa')->restrictOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_pranota_stock_akun_items');
    }
};
