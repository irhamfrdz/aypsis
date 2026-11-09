<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tanda_terima_tanpa_surat_jalan', function (Blueprint $table) {
            $table->dropUnique('tanda_terima_tanpa_surat_jalan_no_tanda_terima_unique');
            $table->dropIndex('tanda_terima_tanpa_surat_jalan_no_tanda_terima_index');
            $table->dropIndex('tanda_terima_tanpa_surat_jalan_pengirim_index');
            $table->dropIndex('tanda_terima_tanpa_surat_jalan_penerima_index');
        });

        Schema::table('tanda_terima_tanpa_surat_jalan', function (Blueprint $table) {
            $table->longText('no_tanda_terima')->nullable()->change();
            $table->longText('nomor_tanda_terima')->nullable()->change();
            $table->longText('pengirim')->nullable()->change();
            $table->longText('penerima')->nullable()->change();
            $table->longText('notify_party')->nullable()->change();
            $table->longText('alamat_pengirim')->nullable()->change();
            $table->longText('alamat_penerima')->nullable()->change();
            $table->longText('alamat_notify_party')->nullable()->change();
            $table->longText('tujuan_pengiriman')->nullable()->change();
            $table->longText('no_kontainer')->nullable()->change();
            $table->longText('size_kontainer')->nullable()->change();
            $table->longText('no_seal')->nullable()->change();
            $table->longText('nama_barang')->nullable()->change();
            $table->longText('jenis_barang')->nullable()->change();
            $table->longText('satuan_barang')->nullable()->change();
            $table->longText('keterangan_barang')->nullable()->change();
            $table->char('no_tanda_terima_digest', 64)->nullable()->unique();
        });

        DB::table('tanda_terima_tanpa_surat_jalan')->select('id', 'no_tanda_terima')
            ->orderBy('id')->chunkById(500, function ($receipts): void {
                foreach ($receipts as $receipt) {
                    DB::table('tanda_terima_tanpa_surat_jalan')->where('id', $receipt->id)->update([
                        'no_tanda_terima_digest' => $receipt->no_tanda_terima === null
                            ? null : hash('sha256', mb_strtolower($receipt->no_tanda_terima)),
                    ]);
                }
            });

        Schema::table('manifests', function (Blueprint $table) {
            $table->longText('nomor_tanda_terima')->nullable()->change();
            $table->longText('nama_barang')->nullable()->change();
            $table->longText('hs_code')->nullable()->change();
            $table->longText('satuan')->nullable()->change();
            $table->longText('pengirim')->nullable()->change();
            $table->longText('penerima')->nullable()->change();
            $table->longText('notify_party')->nullable()->change();
            $table->longText('alamat_pengirim')->nullable()->change();
            $table->longText('alamat_penerima')->nullable()->change();
            $table->longText('alamat_notify_party')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('manifests', function (Blueprint $table) {
            $table->string('nomor_tanda_terima')->nullable()->change();
            $table->text('nama_barang')->nullable()->change();
            $table->string('hs_code')->nullable()->change();
            $table->string('satuan')->nullable()->change();
            $table->string('pengirim')->nullable()->change();
            $table->string('penerima')->nullable()->change();
            $table->string('notify_party')->nullable()->change();
            $table->text('alamat_pengirim')->nullable()->change();
            $table->text('alamat_penerima')->nullable()->change();
            $table->text('alamat_notify_party')->nullable()->change();
        });

        Schema::table('tanda_terima_tanpa_surat_jalan', function (Blueprint $table) {
            $table->dropUnique('tanda_terima_tanpa_surat_jalan_no_tanda_terima_digest_unique');
            $table->dropColumn('no_tanda_terima_digest');
            $table->string('no_tanda_terima')->nullable()->change();
            $table->string('nomor_tanda_terima')->nullable()->change();
            $table->string('pengirim')->nullable()->change();
            $table->string('penerima')->nullable()->change();
            $table->string('notify_party')->nullable()->change();
            $table->text('alamat_pengirim')->nullable()->change();
            $table->text('alamat_penerima')->nullable()->change();
            $table->text('alamat_notify_party')->nullable()->change();
            $table->string('tujuan_pengiriman')->nullable()->change();
            $table->string('no_kontainer')->nullable()->change();
            $table->string('size_kontainer')->nullable()->change();
            $table->string('no_seal')->nullable()->change();
            $table->string('nama_barang')->nullable()->change();
            $table->text('jenis_barang')->nullable()->change();
            $table->string('satuan_barang')->default('unit')->change();
            $table->text('keterangan_barang')->nullable()->change();
            $table->unique('no_tanda_terima');
            $table->index('no_tanda_terima');
            $table->index('pengirim');
            $table->index('penerima');
        });
    }
};
