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
        Schema::table('manifests', function (Blueprint $table) {
            // Add all missing columns to match bls table structure
            $table->enum('status_bongkar', ['Sudah Bongkar', 'Belum Bongkar'])->default('Belum Bongkar')->after('id');
            $table->boolean('sudah_ob')->default(false)->after('status_bongkar');
            $table->boolean('sudah_tl')->default(false)->after('sudah_ob');
            $table->unsignedBigInteger('prospek_id')->nullable()->after('nomor_bl')->index();
            $table->string('size_kontainer', 20)->nullable()->after('tipe_kontainer');
            $table->string('pelabuhan_asal')->nullable()->after('no_voyage');
            $table->string('pelabuhan_tujuan')->nullable()->after('pelabuhan_asal');
            $table->date('tanggal_berangkat')->nullable()->after('nama_kapal');
            $table->text('nama_barang')->nullable()->change();
            $table->string('asal_kontainer')->nullable()->after('nama_barang');
            $table->string('ke')->nullable()->after('asal_kontainer');
            $table->string('pengirim')->nullable()->after('ke');
            $table->string('penerima')->nullable()->after('pengirim');
            $table->text('alamat_pengiriman')->nullable()->after('penerima');
            $table->string('contact_person')->nullable()->after('alamat_pengiriman');
            $table->decimal('volume', 10, 3)->nullable()->after('tonnage');
            $table->string('satuan')->nullable()->after('volume');
            $table->string('term', 100)->nullable()->after('satuan');
            $table->date('penerimaan')->nullable()->after('kuantitas');
            $table->string('supir_ob')->nullable()->after('penerimaan');
            $table->unsignedBigInteger('supir_id')->nullable()->after('updated_at')->index();
            $table->timestamp('tanggal_ob')->nullable()->after('supir_id');
            $table->text('catatan_ob')->nullable()->after('tanggal_ob');
            $table->unsignedBigInteger('created_by')->nullable()->after('catatan_ob')->index();
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by')->index();
            
            // Foreign keys
            $table->foreign('prospek_id')->references('id')->on('prospek')->onDelete('set null');
            $table->foreign('supir_id')->references('id')->on('karyawans')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manifests', function (Blueprint $table) {
            $table->dropForeign(['prospek_id']);
            $table->dropForeign(['supir_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            
            $table->dropColumn([
                'status_bongkar',
                'sudah_ob',
                'sudah_tl',
                'prospek_id',
                'size_kontainer',
                'pelabuhan_asal',
                'pelabuhan_tujuan',
                'tanggal_berangkat',
                'asal_kontainer',
                'ke',
                'pengirim',
                'penerima',
                'alamat_pengiriman',
                'contact_person',
                'volume',
                'satuan',
                'term',
                'penerimaan',
                'supir_ob',
                'supir_id',
                'tanggal_ob',
                'catatan_ob',
                'created_by',
                'updated_by',
            ]);
        });
    }
};
