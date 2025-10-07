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
        // Migrasi data dari nomor_kontainer ke format baru jika ada data
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
        
        // Setelah data dimigrasikan, hapus kolom lama dan buat unique constraint
        Schema::table('stock_kontainers', function (Blueprint $table) {
            // Cek apakah kolom nomor_kontainer masih ada sebelum menghapus
            if (Schema::hasColumn('stock_kontainers', 'nomor_kontainer')) {
                $table->dropColumn('nomor_kontainer');
            }
            
            // Cek apakah unique constraint sudah ada sebelum menambah
            if (!Schema::hasIndex('stock_kontainers', 'stock_kontainers_nomor_seri_gabungan_unique')) {
                $table->unique('nomor_seri_gabungan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_kontainers', function (Blueprint $table) {
            // Kembalikan kolom nomor_kontainer
            $table->string('nomor_kontainer')->nullable()->after('id');
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
            // Hapus unique constraint dan kolom baru
            if (Schema::hasIndex('stock_kontainers', 'stock_kontainers_nomor_seri_gabungan_unique')) {
                $table->dropUnique(['nomor_seri_gabungan']);
            }
            
            $table->dropColumn([
                'awalan_kontainer',
                'nomor_seri_kontainer', 
                'akhiran_kontainer',
                'nomor_seri_gabungan'
            ]);
            
            // Kembalikan unique constraint ke nomor_kontainer
            $table->unique('nomor_kontainer');
        });
    }
};
