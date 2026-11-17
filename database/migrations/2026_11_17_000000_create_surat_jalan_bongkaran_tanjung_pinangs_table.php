<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_jalan_bongkaran_tanjung_pinangs', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_surat_jalan');
            $table->string('nomor_surat_jalan')->unique();
            $table->string('nama_kapal')->nullable();
            $table->unsignedBigInteger('kapal_id')->nullable()->index();
            $table->string('no_voyage')->nullable();
            $table->string('no_bl')->nullable();
            $table->unsignedBigInteger('bl_id')->nullable()->index();
            $table->unsignedBigInteger('manifest_id')->nullable()->index();
            $table->string('pengirim')->nullable();
            $table->string('penerima')->nullable();
            $table->text('jenis_barang')->nullable();
            $table->string('tujuan_pengambilan')->nullable();
            $table->string('tujuan_pengiriman')->nullable();
            $table->string('supir')->nullable();
            $table->string('no_plat')->nullable();
            $table->string('kenek')->nullable();
            $table->string('no_kontainer')->nullable();
            $table->string('no_seal')->nullable();
            $table->string('size')->nullable();
            $table->string('tipe_kontainer')->nullable();
            $table->string('term')->nullable();
            $table->text('aktifitas')->nullable();
            $table->string('input_by')->nullable();
            $table->dateTime('input_date')->nullable();
            $table->timestamps();
            $table->index(['tanggal_surat_jalan', 'no_voyage'], 'sjb_tp_tanggal_voyage_index');
            $table->index(['nama_kapal', 'no_voyage'], 'sjb_tp_kapal_voyage_index');
        });

        // Keep existing Tanjung Pinang records while separating future data from the shared table.
        if (Schema::hasTable('surat_jalan_bongkarans')) {
            $columns = [
                'id', 'tanggal_surat_jalan', 'nomor_surat_jalan', 'nama_kapal', 'kapal_id', 'no_voyage', 'no_bl', 'bl_id', 'manifest_id',
                'pengirim', 'penerima', 'jenis_barang', 'tujuan_pengambilan', 'tujuan_pengiriman', 'supir', 'no_plat',
                'kenek', 'no_kontainer', 'no_seal', 'size', 'tipe_kontainer', 'term', 'aktifitas', 'input_by', 'input_date',
                'created_at', 'updated_at',
            ];
            $columns = array_values(array_filter($columns, fn ($column) => Schema::hasColumn('surat_jalan_bongkarans', $column)));
            if (in_array('nomor_surat_jalan', $columns, true) && in_array('tanggal_surat_jalan', $columns, true)) {
                $rows = DB::table('surat_jalan_bongkarans')->where('lokasi', 'tanjung_pinang')->select($columns)->get();
                foreach ($rows->chunk(500) as $chunk) {
                    DB::table('surat_jalan_bongkaran_tanjung_pinangs')->insertOrIgnore($chunk->map(fn ($row) => (array) $row)->all());
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_jalan_bongkaran_tanjung_pinangs');
    }
};
