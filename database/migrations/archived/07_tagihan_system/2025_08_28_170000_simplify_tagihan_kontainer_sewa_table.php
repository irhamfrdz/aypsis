<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SimplifyTagihanKontainerSewaTable extends Migration
{
    /**
     * Run the migrations.
     * Remove unused columns and normalize names to the required set.
     *
     * Keputusan asumsi singkat:
     * - Menjaga kolom `id`, `created_at`, `updated_at` untuk integritas Eloquent.
     * - Menganggap `tanggal_harga_awal` adalah "tanggal awal sewa" dan
     *   `tanggal_harga_akhir` adalah "tanggal akhir sewa" (biarkan nama kolom ini).
     * - Ganti kolom `massa` (typo) menjadi `masa`.
     */
    public function up()
    {
        Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
            // Rename massa -> masa if present
            if (Schema::hasColumn('tagihan_kontainer_sewa', 'massa') && !Schema::hasColumn('tagihan_kontainer_sewa', 'masa')) {
                try {
                    $table->renameColumn('massa', 'masa');
                } catch (\Exception $e) {
                    // Some DB drivers (SQLite) may not support rename in this context; fallback handled later
                }
            }

            // Add masa if neither massa nor masa existed
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'masa')) {
                try {
                    $table->decimal('masa', 12, 2)->nullable()->after('periode');
                } catch (\Exception $e) {
                    // Ignore duplicate column or unsupported operations when running against
                    // certain DB engines or when migration partially applied previously.
                }
            }

            // Ensure the required columns exist (create if missing)
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'vendor')) $table->string('vendor')->nullable()->after('id');
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'nomor_kontainer')) $table->string('nomor_kontainer')->nullable();
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'group')) $table->string('group')->nullable();
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'tanggal_harga_awal')) $table->date('tanggal_harga_awal')->nullable();
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'tanggal_harga_akhir')) $table->date('tanggal_harga_akhir')->nullable();
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'periode')) $table->string('periode')->nullable();
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'dpp')) $table->decimal('dpp', 15, 2)->nullable();
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'dpp_nilai_lain')) $table->decimal('dpp_nilai_lain', 15, 2)->nullable();
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'ppn')) $table->decimal('ppn', 15, 2)->nullable();
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'pph')) $table->decimal('pph', 15, 2)->nullable();
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'grand_total')) $table->decimal('grand_total', 18, 2)->nullable();

            // Drop columns that should no longer exist according to requirement
            $toDrop = [
                'group_code',
                'ukuran_kontainer',
                'tarif',
                'harga',
                'status_pembayaran',
                'nomor_pranota',
                'is_pranota',
                'tanggal_checkpoint_supir',
                // 'massa' already renamed above if present; if still present drop it
                'massa',
            ];

            foreach ($toDrop as $col) {
                if (Schema::hasColumn('tagihan_kontainer_sewa', $col)) {
                    try {
                        $table->dropColumn($col);
                    } catch (\Exception $e) {
                        // Some DB drivers may not allow dropColumn inside a closure for compound operations.
                        // We'll ignore errors here; user can run a separate raw SQL migration if needed.
                    }
                }
            }
        });

        // For DB engines that don't support rename/drop inside the closure (e.g. SQLite in memory),
        // try a safe fallback using raw statements where possible. This block is best-effort.
        try {
            // Attempt to remove 'massa' if still exists (fallback)
            if (Schema::hasColumn('tagihan_kontainer_sewa', 'massa') && !Schema::hasColumn('tagihan_kontainer_sewa', 'masa')) {
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE tagihan_kontainer_sewa RENAME COLUMN massa TO masa");
            }
        } catch (\Exception $e) {
            // ignore
        }
    }

    /**
     * Reverse the migrations.
     * This will try to restore dropped columns with conservative types.
     */
    public function down()
    {
        Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'group_code')) $table->string('group_code')->nullable();
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'ukuran_kontainer')) $table->string('ukuran_kontainer')->nullable();
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'tarif')) $table->string('tarif')->nullable();
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'harga')) $table->decimal('harga', 15, 2)->nullable();
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'status_pembayaran')) $table->string('status_pembayaran')->nullable();
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'nomor_pranota')) $table->string('nomor_pranota')->nullable();
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'is_pranota')) $table->boolean('is_pranota')->default(false);
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'tanggal_checkpoint_supir')) $table->timestamp('tanggal_checkpoint_supir')->nullable();

            // If masa exists, rename back to massa
            if (Schema::hasColumn('tagihan_kontainer_sewa', 'masa') && !Schema::hasColumn('tagihan_kontainer_sewa', 'massa')) {
                try {
                    $table->renameColumn('masa', 'massa');
                } catch (\Exception $e) {
                    // ignore
                }
            }
        });
    }
}
