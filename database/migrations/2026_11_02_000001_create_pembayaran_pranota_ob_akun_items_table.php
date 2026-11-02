<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = 'pembayaran_pranota_ob_akun_items';

        if (! Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->foreignId('pembayaran_pranota_ob_id')
                    ->constrained('pembayaran_pranota_obs', 'id', 'fk_ppob_akun_payment')->cascadeOnDelete();
                $table->foreignId('akun_coa_id')
                    ->constrained('akun_coa', 'id', 'fk_ppob_akun_coa')->restrictOnDelete();
                $table->decimal('nominal', 15, 2);
                $table->timestamps();
            });
        }

        // MySQL can leave the table behind when it rejects a foreign key name.
        $foreignKeys = Schema::getForeignKeys($tableName);
        $hasPaymentKey = collect($foreignKeys)->contains(fn ($key) => in_array('pembayaran_pranota_ob_id', $key['columns']));
        $hasCoaKey = collect($foreignKeys)->contains(fn ($key) => in_array('akun_coa_id', $key['columns']));
        if (! $hasPaymentKey || ! $hasCoaKey) {
            Schema::table($tableName, function (Blueprint $table) use ($hasPaymentKey, $hasCoaKey) {
                if (! $hasPaymentKey) {
                    $table->foreign('pembayaran_pranota_ob_id', 'fk_ppob_akun_payment')
                        ->references('id')->on('pembayaran_pranota_obs')->cascadeOnDelete();
                }
                if (! $hasCoaKey) {
                    $table->foreign('akun_coa_id', 'fk_ppob_akun_coa')
                        ->references('id')->on('akun_coa')->restrictOnDelete();
                }
            });
        }

        if (! Schema::hasColumn('pembayaran_pranota_obs', 'akun_dp_id')) {
            Schema::table('pembayaran_pranota_obs', function (Blueprint $table) {
                $table->foreignId('akun_dp_id')->nullable()
                    ->constrained('akun_coa', 'id', 'fk_ppob_dp_coa')->nullOnDelete();
            });
        } else {
            $hasDpKey = collect(Schema::getForeignKeys('pembayaran_pranota_obs'))
                ->contains(fn ($key) => in_array('akun_dp_id', $key['columns']));
            if (! $hasDpKey) {
                Schema::table('pembayaran_pranota_obs', function (Blueprint $table) {
                    $table->foreign('akun_dp_id', 'fk_ppob_dp_coa')
                        ->references('id')->on('akun_coa')->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pembayaran_pranota_obs', 'akun_dp_id')) {
            $dpKey = collect(Schema::getForeignKeys('pembayaran_pranota_obs'))
                ->first(fn ($key) => in_array('akun_dp_id', $key['columns']));
            Schema::table('pembayaran_pranota_obs', function (Blueprint $table) use ($dpKey) {
                if ($dpKey) {
                    $table->dropForeign(Schema::getConnection()->getDriverName() === 'sqlite' ? ['akun_dp_id'] : $dpKey['name']);
                }
                $table->dropColumn('akun_dp_id');
            });
        }
        Schema::dropIfExists('pembayaran_pranota_ob_akun_items');
    }
};
