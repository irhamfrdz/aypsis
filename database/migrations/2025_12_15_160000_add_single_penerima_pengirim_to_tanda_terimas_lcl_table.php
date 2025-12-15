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
        Schema::table('tanda_terimas_lcl', function (Blueprint $table) {
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('tanda_terimas_lcl', 'nama_penerima')) {
                $table->string('nama_penerima')->nullable()->after('term_id');
            }
            if (!Schema::hasColumn('tanda_terimas_lcl', 'pic_penerima')) {
                $table->string('pic_penerima')->nullable()->after('nama_penerima');
            }
            if (!Schema::hasColumn('tanda_terimas_lcl', 'telepon_penerima')) {
                $table->string('telepon_penerima')->nullable()->after('pic_penerima');
            }
            if (!Schema::hasColumn('tanda_terimas_lcl', 'alamat_penerima')) {
                $table->text('alamat_penerima')->nullable()->after('telepon_penerima');
            }
            
            if (!Schema::hasColumn('tanda_terimas_lcl', 'nama_pengirim')) {
                $table->string('nama_pengirim')->nullable()->after('alamat_penerima');
            }
            if (!Schema::hasColumn('tanda_terimas_lcl', 'pic_pengirim')) {
                $table->string('pic_pengirim')->nullable()->after('nama_pengirim');
            }
            if (!Schema::hasColumn('tanda_terimas_lcl', 'telepon_pengirim')) {
                $table->string('telepon_pengirim')->nullable()->after('pic_pengirim');
            }
            if (!Schema::hasColumn('tanda_terimas_lcl', 'alamat_pengirim')) {
                $table->text('alamat_pengirim')->nullable()->after('telepon_pengirim');
            }
            
            // Fix foreign key name for tujuan_pengiriman
            // Rename column from tujuan_pengiriman to tujuan_pengiriman_id
            if (Schema::hasColumn('tanda_terimas_lcl', 'tujuan_pengiriman') && 
                !Schema::hasColumn('tanda_terimas_lcl', 'tujuan_pengiriman_id')) {
                $table->renameColumn('tujuan_pengiriman', 'tujuan_pengiriman_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tanda_terimas_lcl', function (Blueprint $table) {
            // Rename back
            if (Schema::hasColumn('tanda_terimas_lcl', 'tujuan_pengiriman_id')) {
                $table->renameColumn('tujuan_pengiriman_id', 'tujuan_pengiriman');
            }
        });
    }
};
