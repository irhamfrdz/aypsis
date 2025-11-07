<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            // Drop all foreign key constraints that reference pranotalist
            $foreignKeys = [
                'pembayaran_pranota_items' => ['pranota_id'],
                'pembayaran_pranota' => ['pranota_id'],
                'pranota_tagihan_cat_items' => ['pranota_id'],
            ];

            foreach ($foreignKeys as $tableName => $columns) {
                try {
                    Schema::table($tableName, function (Blueprint $table) use ($columns) {
                        foreach ($columns as $column) {
                            $table->dropForeign([$column]);
                        }
                    });
                    echo "Dropped foreign key constraints from: $tableName\n";
                } catch (Exception $e) {
                    echo "Warning: Could not drop foreign key from $tableName - " . $e->getMessage() . "\n";
                }
            }

            // Now drop the tables
            Schema::dropIfExists('pranotalist');
            Schema::dropIfExists('pranota_tagihan_kontainer');

            echo "Successfully dropped unused pranota tables.\n";
            echo "Kept: pranota_tagihan_kontainer_sewa (the one currently in use).\n";

        } finally {
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: This down migration would recreate basic table structure
        // but data will be lost. Only use if you have backups.

        // Recreate pranotalist table (basic structure)
        Schema::create('pranotalist', function (Blueprint $table) {
            $table->id();
            $table->string('no_invoice');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('adjustment', 15, 2)->default(0);
            $table->text('alasan_adjustment')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('supplier')->nullable();
            $table->string('no_invoice_vendor')->nullable();
            $table->date('tgl_invoice_vendor')->nullable();
            $table->enum('status', ['unpaid', 'paid'])->default('unpaid');
            $table->json('tagihan_ids')->nullable();
            $table->integer('jumlah_tagihan')->default(0);
            $table->date('tanggal_pranota');
            $table->date('due_date')->nullable();
            $table->timestamps();
        });

        // Recreate pranota_tagihan_kontainer table (basic structure)
        Schema::create('pranota_tagihan_kontainer', function (Blueprint $table) {
            $table->id();
            $table->string('no_invoice');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->enum('status', ['unpaid', 'paid', 'cancelled'])->default('unpaid');
            $table->json('tagihan_kontainer_ids')->nullable();
            $table->integer('jumlah_tagihan')->default(0);
            $table->date('tanggal_pranota');
            $table->date('due_date')->nullable();
            $table->timestamps();
        });
    }
};
