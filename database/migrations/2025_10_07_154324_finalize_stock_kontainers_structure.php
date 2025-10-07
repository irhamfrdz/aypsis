<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Exception;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pastikan kolom-kolom baru ada terlebih dahulu
        Schema::table('stock_kontainers', function (Blueprint $table) {
            // Tambahkan kolom baru jika belum ada
            if (!Schema::hasColumn('stock_kontainers', 'awalan_kontainer')) {
                $table->string('awalan_kontainer', 10)->nullable()->after('keterangan');
            }
            if (!Schema::hasColumn('stock_kontainers', 'nomor_seri_kontainer')) {
                $table->string('nomor_seri_kontainer', 20)->nullable()->after('awalan_kontainer');
            }
            if (!Schema::hasColumn('stock_kontainers', 'akhiran_kontainer')) {
                $table->string('akhiran_kontainer', 5)->nullable()->after('nomor_seri_kontainer');
            }
            if (!Schema::hasColumn('stock_kontainers', 'nomor_seri_gabungan')) {
                $table->string('nomor_seri_gabungan', 50)->nullable()->after('akhiran_kontainer');
            }
        });

        // Migrasi data dari nomor_kontainer ke format baru jika ada data
        if (Schema::hasColumn('stock_kontainers', 'nomor_kontainer')) {
            DB::table('stock_kontainers')->whereNotNull('nomor_kontainer')->orderBy('id')->chunk(100, function ($stockKontainers) {
                foreach ($stockKontainers as $stock) {
                    if (strlen($stock->nomor_kontainer) == 11) {
                        // Asumsikan format ABCD123456X (4 awalan + 6 nomor seri + 1 akhiran)
                        $awalan = substr($stock->nomor_kontainer, 0, 4);
                        $nomor_seri = substr($stock->nomor_kontainer, 4, 6);
                        $akhiran = substr($stock->nomor_kontainer, 10, 1);
                        
                        DB::table('stock_kontainers')
                            ->where('id', $stock->id)
                            ->update([
                                'awalan_kontainer' => $awalan,
                                'nomor_seri_kontainer' => $nomor_seri,
                                'akhiran_kontainer' => $akhiran,
                                'nomor_seri_gabungan' => $stock->nomor_kontainer
                            ]);
                    }
                }
            });
        }
        
        // Setelah data dimigrasikan dan kolom ada, hapus kolom lama dan buat unique constraint
        Schema::table('stock_kontainers', function (Blueprint $table) {
            // Cek apakah kolom nomor_kontainer masih ada sebelum menghapus
            if (Schema::hasColumn('stock_kontainers', 'nomor_kontainer')) {
                $table->dropColumn('nomor_kontainer');
            }
            
            // Buat unique constraint pada nomor_seri_gabungan jika belum ada
            try {
                $table->unique('nomor_seri_gabungan');
            } catch (Exception $e) {
                // Index mungkin sudah ada, abaikan error
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan kolom nomor_kontainer terlebih dahulu
        Schema::table('stock_kontainers', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_kontainers', 'nomor_kontainer')) {
                $table->string('nomor_kontainer')->nullable()->after('id');
            }
        });
        
        // Migrasi data kembali dari format baru ke nomor_kontainer
        DB::table('stock_kontainers')->whereNotNull('nomor_seri_gabungan')->orderBy('id')->chunk(100, function ($stockKontainers) {
            foreach ($stockKontainers as $stock) {
                DB::table('stock_kontainers')
                    ->where('id', $stock->id)
                    ->update([
                        'nomor_kontainer' => $stock->nomor_seri_gabungan
                    ]);
            }
        });
        
        Schema::table('stock_kontainers', function (Blueprint $table) {
            // Hapus unique constraint jika ada
            try {
                $table->dropUnique(['nomor_seri_gabungan']);
            } catch (Exception $e) {
                // Index mungkin tidak ada, abaikan error
            }
            
            // Hapus kolom baru jika ada
            if (Schema::hasColumn('stock_kontainers', 'nomor_seri_gabungan')) {
                $table->dropColumn('nomor_seri_gabungan');
            }
            if (Schema::hasColumn('stock_kontainers', 'akhiran_kontainer')) {
                $table->dropColumn('akhiran_kontainer');
            }
            if (Schema::hasColumn('stock_kontainers', 'nomor_seri_kontainer')) {
                $table->dropColumn('nomor_seri_kontainer');
            }
            if (Schema::hasColumn('stock_kontainers', 'awalan_kontainer')) {
                $table->dropColumn('awalan_kontainer');
            }
            
            // Kembalikan unique constraint ke nomor_kontainer
            try {
                $table->unique('nomor_kontainer');
            } catch (Exception $e) {
                // Index mungkin sudah ada, abaikan error
            }
        });
    }
};
