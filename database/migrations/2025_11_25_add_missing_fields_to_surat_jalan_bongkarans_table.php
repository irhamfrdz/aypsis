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
        Schema::table('surat_jalan_bongkarans', function (Blueprint $table) {
            // Add missing fields from form
            $table->unsignedBigInteger('kapal_id')->nullable()->after('id');
            $table->string('no_voyage')->nullable()->after('kapal_id');
            $table->string('no_bl')->nullable()->after('no_voyage');
            
            // Rename columns to match form names
            $table->renameColumn('no_surat_jalan', 'nomor_surat_jalan');
            
            // Add missing shipping information
            $table->string('jenis_pengiriman')->nullable()->after('tujuan_pengiriman');
            $table->date('tanggal_ambil_barang')->nullable()->after('jenis_pengiriman');
            
            // Add missing personnel field
            $table->string('krani')->nullable()->after('kenek');
            
            // Add missing financial fields
            $table->enum('uang_jalan_type', ['full', 'setengah'])->nullable()->after('rit');
            $table->decimal('uang_jalan_nominal', 15, 2)->default(0)->after('uang_jalan_type');
            
            // Add billing fields
            $table->boolean('tagihan_ayp')->default(false)->after('uang_jalan_nominal');
            $table->boolean('tagihan_atb')->default(false)->after('tagihan_ayp');
            $table->boolean('tagihan_pb')->default(false)->after('tagihan_atb');
            
            // Rename alamat to tujuan_alamat to match form
            $table->renameColumn('alamat', 'tujuan_alamat');
            
            // Add indexes for new fields
            $table->index(['kapal_id']);
            $table->index(['no_voyage']);
            $table->index(['no_bl']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalan_bongkarans', function (Blueprint $table) {
            // Drop new fields
            $table->dropColumn([
                'kapal_id', 
                'no_voyage', 
                'no_bl',
                'jenis_pengiriman',
                'tanggal_ambil_barang',
                'krani',
                'uang_jalan_type',
                'uang_jalan_nominal',
                'tagihan_ayp',
                'tagihan_atb',
                'tagihan_pb'
            ]);
            
            // Rename columns back
            $table->renameColumn('nomor_surat_jalan', 'no_surat_jalan');
            $table->renameColumn('tujuan_alamat', 'alamat');
        });
    }
};