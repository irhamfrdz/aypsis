<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manifest_sugengs', function (Blueprint $table) {
            $table->id();
            $table->string('source_type', 4);
            $table->unsignedBigInteger('source_id');
            $table->text('nomor_tanda_terima')->nullable();
            $table->date('tanggal_tanda_terima')->nullable();
            $table->string('nomor_kontainer')->nullable();
            $table->text('pengirim')->nullable();
            $table->text('tujuan_pengiriman')->nullable();
            $table->unsignedBigInteger('shipper_jb_id')->nullable();
            $table->json('manifest_goods')->nullable();
            $table->unsignedBigInteger('dikirim_oleh')->nullable();
            $table->timestamp('dikirim_pada')->nullable();
            $table->timestamps();
            $table->unique(['source_type', 'source_id'], 'manifest_sugeng_source_unique');
            $table->index('shipper_jb_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manifest_sugengs');
    }
};
